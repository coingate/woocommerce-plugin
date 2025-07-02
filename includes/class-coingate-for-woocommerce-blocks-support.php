<?php
/**
 * Class Coingate_For_Woocommerce_Blocks_Support
 *
 * Provides support for WooCommerce blocks integration with the CoinGate payment gateway.
 *
 * Extends the AbstractPaymentMethodType to implement specific functionality
 * required for handling CoinGate payment methods within WooCommerce Blocks.
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 *
 * @final
 */

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Class Coingate_For_Woocommerce_Blocks_Support
 *
 * Provides support for WooCommerce blocks integration with the CoinGate payment gateway.
 *
 * Extends the AbstractPaymentMethodType to implement specific functionality
 * required for handling CoinGate payment methods within WooCommerce Blocks.
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 *
 * @final
 */
final class Coingate_For_Woocommerce_Blocks_Support extends AbstractPaymentMethodType {

	/**
	 * Payment gateway settings.
	 *
	 * @var array<string, mixed>
	 */
	private array $settings = array();

	/**
	 * Payment method name.
	 *
	 * @var string
	 */
	protected string $name = 'coingate';

	/**
	 * Initialize the payment method.
	 *
	 * @return void
	 */
	public function initialize(): void {
		$this->settings = get_option( 'woocommerce_coingate_settings', array() );
	}

	/**
	 * Check if payment method is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
	}

	/**
	 * Get payment method script handles.
	 *
	 * @return array<string>
	 */
	public function get_payment_method_script_handles(): array {
		$asset_file = include plugin_dir_path( __DIR__ ) . 'build/index.asset.php';

		wp_register_script(
			'wc-coingate-blocks-integration',
			plugin_dir_url( __DIR__ ) . 'build/index.js',
			array(
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
				'wp-components',
				'wp-i18n',
			),
			$asset_file['version'] ?? false,
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-coingate-blocks-integration', 'coingate-for-woocommerce' );
		}

		return array( 'wc-coingate-blocks-integration' );
	}

	/**
	 * Get payment method data.
	 *
	 * @return array<string, mixed>
	 */
	public function get_payment_method_data(): array {
		return array(
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			'supports'    => array(
				'products',
				'refunds',
			),
		);
	}
}
