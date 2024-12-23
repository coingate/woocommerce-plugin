<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class Coingate_For_Woocommerce_Blocks_Support extends AbstractPaymentMethodType {
	private $gateway;
	protected $name = 'coingate'; // payment gateway id

	public function initialize() {
		$this->settings = get_option( "woocommerce_coingate_settings", array() );
	}

	public function is_active() {
		return ! empty( $this->settings[ 'enabled' ] ) && 'yes' === $this->settings[ 'enabled' ];
	}

	public function get_payment_method_script_handles() {

		wp_register_script(
			'wc-coingate-blocks-integration',
			plugin_dir_url( __DIR__ ) . 'build/index.js',
			array(
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
			),
			null,
			true
		);

		return array( 'wc-coingate-blocks-integration' );
	}

	public function get_payment_method_data() {
		return [
			'title'        => $this->get_setting( 'title' ),
			'description'  => $this->get_setting( 'description' ),
			// TODO CAN ADD ICON pvz coingate
            //			'icon'         => plugin_dir_url( __DIR__ ) . 'assets/icon.png',
			// 'supports'  => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] ),
		];
	}
}