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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
	private string $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string $version The current version of this plugin.
	 */
	private string $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register payment gateway.
	 *
	 * @param array<string, mixed> $methods Payment gateway methods.
	 *
	 * @return array<string, mixed>
	 */
	public function register_payment_gateway( array $methods ): array {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return $methods;
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coingate-for-woocommerce-payment-gateway.php';

		if ( ! isset( $methods['Coingate_Payment_Gateway'] ) ) {
			$methods['Coingate_Payment_Gateway'] = new Coingate_For_Woocommerce_Payment_Gateway();
		}

		return $methods;
	}

	/**
	 * Enable Block support
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function woocommerce_gateway_coingate_block_support(): void {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry' ) ) {
			return;
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coingate-for-woocommerce-blocks-support.php';

		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( \Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new Coingate_For_Woocommerce_Blocks_Support() );
			}
		);

		// Add block editor assets
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_block_editor_assets(): void {
		$asset_file = include plugin_dir_path( dirname( __FILE__ ) ) . 'build/index.asset.php';

		wp_enqueue_script(
			'coingate-blocks-editor',
			plugin_dir_url( dirname( __FILE__ ) ) . 'build/index.js',
			$asset_file['dependencies'] ?? array(),
			$asset_file['version'] ?? $this->version,
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'coingate-blocks-editor', 'coingate-for-woocommerce' );
		}
	}
}
