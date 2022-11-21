<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://coingate.com
 * @since             1.0.0
 * @package           Coingate_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Payment Gateway - CoinGate
 * Plugin URI:        https://coingate.com
 * Description:       Accept Bitcoin and 70+ Cryptocurrencies via CoinGate in your WooCommerce store.
 * Version:           2.1.1
 * Author:            CoinGate
 * Author URI:        https://coingate.com
 * License:           MIT License
 * License URI:       https://github.com/coingate/woocommerce-plugin/blob/master/LICENSE
 * Github Plugin URI: https://github.com/coingate/woocommerce-plugin
 * Text Domain:       coingate-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once 'vendor/autoload.php';

/**
 * Currently plugin version.
 */
define( 'COINGATE_FOR_WOOCOMMERCE_VERSION', '2.1.1' );

/**
 * Currently plugin URL.
 */
define( 'COINGATE_FOR_WOOCOMMERCE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-coingate-for-woocommerce-activator.php
 */
function activate_coingate_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coingate-for-woocommerce-activator.php';
	Coingate_For_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-coingate-for-woocommerce-deactivator.php
 */
function remove_coingate_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coingate-for-woocommerce-deactivator.php';
	Coingate_For_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_coingate_for_woocommerce' );
register_uninstall_hook( __FILE__, 'remove_coingate_for_woocommerce' );
register_deactivation_hook( __FILE__, 'remove_coingate_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coingate-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_coingate_for_woocommerce() {

	$plugin = new Coingate_For_Woocommerce();
	$plugin->run();

}
run_coingate_for_woocommerce();
