<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://coingate.com
 * @since      1.0.0
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/admin
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_Admin {

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
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Load plugin.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_plugin(): bool {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', function() {
				?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'CoinGate for WooCommerce requires WooCommerce to be installed and activated.', 'coingate-for-woocommerce' ); ?></p>
				</div>
				<?php
			} );
			
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( esc_html__( 'WooCommerce is not active', 'coingate-for-woocommerce' ) );
			}
			
			return false;
		}

		return true;
	}

}
