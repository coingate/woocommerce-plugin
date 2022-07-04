<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://coingate.com
 * @since      1.0.0
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/public
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register payment gateway.
	 *
	 * @param  array $methods Payment gateway methods.
	 * @return array
	 */
	public function register_payment_gateway( array $methods ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coingate-for-woocommerce-payment-gateway.php';

		if ( ! isset( $methods['Coingate_Payment_Gateway'] ) ) {
			$methods['Coingate_Payment_Gateway'] = new Coingate_For_Woocommerce_Payment_Gateway();
		}

		return $methods;
	}

}
