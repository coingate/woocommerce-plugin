<?php
/**
 * The functionality of the coingate payment gateway.
 *
 * @link       https://coingate.com
 * @since      1.0.0
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
	return;
}

use CoinGate\Exception\ApiErrorException;
use CoinGate\Client;

/**
 * The functionality of the coingate payment gateway.
 *
 * @since      1.0.0
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_Payment_Gateway extends WC_Payment_Gateway {

	public const ORDER_TOKEN_META_KEY = 'coingate_order_token';

	public const SETTINGS_KEY = 'woocommerce_coingate_settings';

	/**
	 * API auth token.
	 *
	 * @var string
	 */
	public string $api_auth_token;

	/**
	 * List of order statuses.
	 *
	 * @var array
	 */
	public array $order_statuses;

	/**
	 * TRUE, if TEST mode enabled, FALSE otherwise.
	 *
	 * @var bool
	 */
	public bool $test = false;

	/**
	 * API secret key.
	 *
	 * @var string
	 */
	public string $api_secret;

	/**
	 * Coingate_Payment_Gateway constructor.
	 */
	public function __construct() {
		$this->id = 'coingate';
		$this->has_fields = false;
		$this->method_title = 'CoinGate';
		$this->icon = apply_filters( 'woocommerce_coingate_icon', COINGATE_FOR_WOOCOMMERCE_PLUGIN_URL . 'assets/bitcoin.png' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->api_secret = $this->get_option( 'api_secret' );
		$this->api_auth_token = ( empty( $this->get_option( 'api_auth_token' ) ) ? $this->get_option( 'api_secret' ) : $this->get_option( 'api_auth_token' ) );
		$this->order_statuses = $this->get_option( 'order_statuses' );
		$this->test = ( 'yes' === $this->get_option( 'test', 'no' ) );

		add_action( 'woocommerce_update_options_payment_gateways_coingate', array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_coingate', array( $this, 'save_order_statuses' ) );
		add_action( 'woocommerce_thankyou_coingate', array( $this, 'thankyou' ) );
		add_action( 'woocommerce_api_wc_gateway_coingate', array( $this, 'payment_callback' ) );
	}

	/**
	 * Output the gateway settings screen.
	 */
	public function admin_options() {
		?>
		<h3>
			<?php
			esc_html_e( 'CoinGate', 'coingate' );
			?>
		</h3>
		<table class="form-table">
			<?php
				$this->generate_settings_html();
			?>
		</table>
		<?php
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable CoinGate', 'coingate' ),
				'label'       => __( 'Enable Cryptocurrency payments via CoinGate', 'coingate' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'description' => array(
				'title'       => __( 'Description', 'coingate' ),
				'type'        => 'textarea',
				'description' => __( 'The payment method description which a user sees at the checkout of your store.', 'coingate' ),
				'default'     => __( 'Pay with Bitcoin, stablecoins and other cryptocurrencies', 'coingate' ),
			),
			'title' => array(
				'title'       => __( 'Title', 'coingate' ),
				'type'        => 'text',
				'description' => __( 'The payment method title which a customer sees at the checkout of your store.', 'coingate' ),
				'default'     => __( 'Cryptocurrencies via CoinGate', 'coingate' ),
			),
			'api_auth_token' => array(
				'title'       => __( 'API Auth Token', 'coingate' ),
				'type'        => 'password',
				'description' => __( '<a href="https://support.coingate.com/hc/en-us/articles/4402498918546" target="_blank">How to create API token</a>', 'coingate' ),
				'default'     => ( empty( $this->get_option( 'api_secret' ) ) ? '' : $this->get_option( 'api_secret' ) ),
			),
			'order_statuses' => array(
				'type' => 'order_statuses',
			),
			'purchaser_email_status' => array(
				'title'       => __( 'Pre-fill CoinGate invoice email', 'coingate' ),
				'type'        => 'checkbox',
				'label'       => __( 'Pre-fill CoinGate invoice email', 'coingate' ),
				'default'     => 'yes',
				'description' => __(
					'When this feature is enabled, customer email will be passed to CoinGate\'s checkout form automatically. <br>
                    Email will be used to contact customers by the CoinGate team if any payment issues occur.',
					'coingate'
				),
			),
			'test' => array(
				'title'       => __( 'Test (Sandbox)', 'coingate' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Test Mode (Sandbox)', 'coingate' ),
				'default'     => 'no',
				'description' => __(
					'To test on <a href="https://sandbox.coingate.com" target="_blank">CoinGate Sandbox</a>, turn Test Mode "On".
					Please note, for Test Mode you must create a separate account on <a href="https://sandbox.coingate.com" target="_blank">sandbox.coingate.com</a> and generate API credentials there.
					API credentials generated on <a href="https://coingate.com" target="_blank">coingate.com</a> are "Live" credentials and will not work for "Test" mode.',
					'coingate'
				),
			),
		);
	}

	/**
	 * Thank you page.
	 */
	public function thankyou() {
		$description = $this->get_description();
		if ( $description ) {
			echo '<p>' . esc_html( $description ) . '</p>';
		}
	}

	/**
	 * Validate api_auth_token field.
	 *
	 * @param string $key Field key.
	 * @param string $value Field value.
	 * @return string Returns field value.
	 */
	public function validate_api_auth_token_field( $key, $value ) {
		if ( ! empty( $value ) ) {
			$client = new Client();
			$client::setAppInfo( 'Coingate For Woocommerce', COINGATE_FOR_WOOCOMMERCE_VERSION );
			$post_data = $this->get_post_data();
			$mode = isset( $post_data['woocommerce_coingate_test'] ) ? (bool) $post_data['woocommerce_coingate_test'] : false;
			$result = $client::testConnection( $value, $mode );

			if ( $result ) {
				return $value;
			}
		}

		WC_Admin_Settings::add_error( esc_html__( 'API Auth Token is invalid. Your changes have not been saved.', 'coingate' ) );

		return '';
	}

	/**
	 * Payment process.
	 *
	 * @param int $order_id The order ID.
	 * @return array
	 *
	 * @throws Exception Unknown exception type.
	 */
	public function process_payment( int $order_id ): array {
		global $woocommerce, $page, $paged;
		$order = wc_get_order( $order_id );

		$client = $this->init_coingate();

		$description = array();
		foreach ( $order->get_items() as $item ) {
			$description[] = $item['qty'] . ' × ' . $item['name'];
		}

		$params = array(
			'order_id'         => $order->get_id(),
			'price_amount'     => $order->get_total(),
			'price_currency'   => $order->get_currency(),
			'callback_url'     => trailingslashit( get_bloginfo( 'wpurl' ) ) . '?wc-api=wc_gateway_coingate',
			'cancel_url'       => $this->get_cancel_order_url( $order ),
			'success_url'      => add_query_arg( 'order-received', $order->get_id(), add_query_arg( 'key', $order->get_order_key(), $this->get_return_url( $order ) ) ),
			'title'            => get_bloginfo( 'name', 'raw' ) . ' Order #' . $order->get_id(),
			'description'      => implode( ', ', $description ),
		);

		if ( 'yes' === $this->get_option( 'purchaser_email_status' ) ) {
			$params['purchaser_email'] = $order->get_billing_email();
		}

		$response = array( 'result' => 'fail' );

		try {
			$gateway_order = $client->order->create( $params );
			if ( $gateway_order ) {
				$order->add_meta_data( static::ORDER_TOKEN_META_KEY, $gateway_order->token, true );
				$order->save();
				$response['result'] = 'success';
				$response['redirect'] = $gateway_order->payment_url;
			}
		} catch ( ApiErrorException $exception ) {
			error_log( $exception );
		}

		return $response;
	}

	/**
	 * Payment callback.
	 *
	 * @throws Exception Unknown exception type.
	 */
	public function payment_callback(): void {
		$request = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		
		if ( ! isset( $request['order_id'], $request['token'], $request['id'] ) ) {
			throw new Exception( 'Invalid callback data received' );
		}

		$order = wc_get_order( $request['order_id'] );

		if ( ! $this->is_token_valid( $order, preg_replace( '/\s+/', '', $request['token'] ) ) ) {
			throw new Exception( 'CoinGate callback token does not match' );
		}

		if ( ! $order || ! $order->get_id() ) {
			throw new Exception( 'Order #' . $request['order_id'] . ' does not exists' );
		}

		if ( $order->get_payment_method() !== $this->id ) {
			throw new Exception( 'Order #' . $order->get_id() . ' payment method is not ' . $this->method_title );
		}

		// Get payment data from request due to security reason.
		$client = $this->init_coingate();
		$cg_order = $client->order->get( (int) $request['id'] );
		if ( ! $cg_order || $order->get_id() !== (int) $cg_order->order_id ) {
			throw new Exception( 'CoinGate Order #' . $order->get_id() . ' does not exists.' );
		}

		$callback_order_status = sanitize_text_field( $cg_order->status );

		$order_statuses = $this->get_option( 'order_statuses' );
		$wc_order_status = isset( $order_statuses[ $callback_order_status ] ) ? $order_statuses[ $callback_order_status ] : null;
		if ( ! $wc_order_status ) {
			return;
		}

		switch ( $callback_order_status ) {
			case 'paid':
				$status_was = 'wc-' . $order->get_status();

				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment is confirmed on the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.', 'coingate' ) );
				$order->payment_complete();

				$wc_expired_status = $order_statuses['expired'];
				$wc_canceled_status = $order_statuses['canceled'];

				if ( 'processing' === $order->status && ( $status_was === $wc_expired_status || $status_was === $wc_canceled_status ) ) {
					WC()->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger( $order->get_id() );
				}
				if ( ( 'processing' === $order->status || 'completed' === $order->status ) && ( $status_was === $wc_expired_status || $status_was === $wc_canceled_status ) ) {
					WC()->mailer()->emails['WC_Email_New_Order']->trigger( $order->get_id() );
				}
				break;
			case 'confirming':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Shopper transferred the payment for the invoice. Awaiting blockchain network confirmation.', 'coingate' ) );
				break;
			case 'invalid':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment rejected by the network or did not confirm within 7 days.', 'coingate' ) );
				break;
			case 'expired':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Buyer did not pay within the required time and the invoice expired.', 'coingate' ) );
				break;
			case 'canceled':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Buyer canceled the invoice.', 'coingate' ) );
				break;
			case 'refunded':
				$this->handle_order_status( $order, $wc_order_status );
				$order->add_order_note( __( 'Payment was refunded to the buyer.', 'coingate' ) );
				break;
		}
	}

	/**
	 * Generates a URL so that a customer can cancel their (unpaid - pending) order.
	 *
	 * @param WC_Order $order    Order.
	 * @param string   $redirect Redirect URL.
	 * @return string
	 */
	public function get_cancel_order_url( WC_Order $order, string $redirect = '' ): string {
		return apply_filters(
			'woocommerce_get_cancel_order_url',
			wp_nonce_url(
				add_query_arg(
					array(
						'order'    => $order->get_order_key(),
						'order_id' => $order->get_id(),
						'redirect' => $redirect,
					),
					$order->get_cancel_endpoint()
				),
				'woocommerce-cancel_order'
			)
		);
	}

	/**
	 * Generate order statuses.
	 *
	 * @return string|false
	 */
	public function generate_order_statuses_html(): string|false {
		ob_start();

		$cg_statuses = $this->coingate_order_statuses();
		$default_status['ignore'] = __( 'Do nothing', 'coingate' );
		$wc_statuses = array_merge( $default_status, wc_get_order_statuses() );

		$default_statuses = array(
			'paid'       => 'wc-processing',
			'confirming' => 'ignore',
			'invalid'    => 'ignore',
			'expired'    => 'ignore',
			'canceled'   => 'ignore',
			'refunded'   => 'ignore',
		);

		?>
		<tr valign="top">
			<th scope="row" class="titledesc"> <?php esc_html_e( 'Order Statuses:', 'coingate' ); ?></th>
			<td class="forminp" id="coingate_order_statuses">
				<table cellspacing="0">
					<?php
					foreach ( $cg_statuses as $cg_status_name => $cg_status_title ) {
						?>
						<tr>
							<th><?php echo esc_html( $cg_status_title ); ?></th>
							<td>
								<select name="woocommerce_coingate_order_statuses[<?php echo esc_html( $cg_status_name ); ?>]">
									<?php
									$cg_settings = get_option( static::SETTINGS_KEY );
									$order_statuses = $cg_settings['order_statuses'];

									foreach ( $wc_statuses as $wc_status_name => $wc_status_title ) {
										$current_status = isset( $order_statuses[ $cg_status_name ] ) ? $order_statuses[ $cg_status_name ] : null;

										if ( empty( $current_status ) ) {
											$current_status = $default_statuses[ $cg_status_name ];
										}

										if ( $current_status === $wc_status_name ) {
											echo '<option value="' . esc_attr( $wc_status_name ) . '" selected>' . esc_html( $wc_status_title ) . '</option>';
										} else {
											echo '<option value="' . esc_attr( $wc_status_name ) . '">' . esc_html( $wc_status_title ) . '</option>';
										}
									}
									?>
								</select>
							</td>
						</tr>
						<?php
					}
					?>
				</table>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Validate order statuses field.
	 *
	 * @return array|string
	 */
	public function validate_order_statuses_field(): array|string {
		$order_statuses = $this->get_option( 'order_statuses' );

		if ( isset( $_POST[ $this->plugin_id . $this->id . '_order_statuses' ] ) ) {
			return array_map(
				'sanitize_text_field',
				wp_unslash( $_POST[ $this->plugin_id . $this->id . '_order_statuses' ] )
			);
		}

		return $order_statuses;
	}

	/**
	 * Save order statuses.
	 */
	public function save_order_statuses(): void {
		$coingate_order_statuses = $this->coingate_order_statuses();
		$wc_statuses = wc_get_order_statuses();

		if ( isset( $_POST['woocommerce_coingate_order_statuses'] ) ) {
			$cg_settings = get_option( static::SETTINGS_KEY );
			$order_statuses = $cg_settings['order_statuses'];

			foreach ( $coingate_order_statuses as $cg_status_name => $cg_status_title ) {
				if ( ! isset( $_POST['woocommerce_coingate_order_statuses'][ $cg_status_name ] ) ) {
					continue;
				}

				$wc_status_name = sanitize_text_field( wp_unslash( $_POST['woocommerce_coingate_order_statuses'][ $cg_status_name ] ) );

				if ( array_key_exists( $wc_status_name, $wc_statuses ) ) {
					$order_statuses[ $cg_status_name ] = $wc_status_name;
				}
			}

			$cg_settings['order_statuses'] = $order_statuses;
			update_option( static::SETTINGS_KEY, $cg_settings );
		}
	}

	/**
	 * Handle order status.
	 *
	 * @param WC_Order $order  The order.
	 * @param string   $status Order status.
	 */
	protected function handle_order_status( WC_Order $order, string $status ): void {
		if ( 'ignore' !== $status ) {
			$order->update_status( $status );
		}
	}

	/**
	 * List of coingate order statuses.
	 *
	 * @return array<string, string>
	 */
	private function coingate_order_statuses(): array {
		return array(
			'paid'       => 'Paid',
			'confirming' => 'Confirming',
			'invalid'    => 'Invalid',
			'expired'    => 'Expired',
			'canceled'   => 'Canceled',
			'refunded'   => 'Refunded',
		);
	}

	/**
	 * Initial client.
	 *
	 * @return Client
	 * @throws Exception If client initialization fails.
	 */
	private function init_coingate(): Client {
		try {
			$auth_token = ( empty( $this->api_auth_token ) ? $this->api_secret : $this->api_auth_token );
			if ( empty( $auth_token ) ) {
				throw new Exception( __( 'API credentials are not configured.', 'coingate' ) );
			}
			
			$client = new Client( $auth_token, $this->test );
			$client::setAppInfo( 'Coingate For Woocommerce', COINGATE_FOR_WOOCOMMERCE_VERSION );

			return $client;
		} catch ( Exception $e ) {
			error_log( 'CoinGate client initialization failed: ' . $e->getMessage() );
			throw $e;
		}
	}

	/**
	 * Check token match.
	 *
	 * @param WC_Order $order The order.
	 * @param string   $token Token.
	 * @return bool
	 */
	private function is_token_valid( WC_Order $order, string $token ): bool {
		$order_token = $order->get_meta( static::ORDER_TOKEN_META_KEY );

		return ! empty( $order_token ) && hash_equals( $order_token, $token );
	}

}
