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
	 * @var array
	 */
	private $gateway;

	/**
	 * Payment method name.
	 *
	 * @var string
	 */
	protected $name = 'coingate';

	/**
	 * Init
	 */
	public function initialize(): void {
		$this->settings = get_option( 'woocommerce_coingate_settings', array() );
	}

	/**
	 * Payment method enabled.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'];
	}

	/**
	 * Script to use
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles(): array {
		wp_register_script(
			'wc-coingate-blocks-integration',
			plugin_dir_url( __DIR__ ) . 'build/index.js',
			array(
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
			),
			false,
			true
		);

		return array( 'wc-coingate-blocks-integration' );
	}

	/**
	 * Payment method data
	 *
	 * @return array
	 */
	public function get_payment_method_data(): array {
		return array(
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			// 'icon'         => plugin_dir_url( __DIR__ ) . 'assets/icon.png',
			// 'supports'  => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] ),
		);
	}
}
