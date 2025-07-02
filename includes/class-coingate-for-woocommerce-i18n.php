<?php
/**
 * Define the internationalization functionality.
 *
 * @link       https://coingate.com
 * @since      1.0.0
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		$domain = 'coingate-for-woocommerce';
		$locale = determine_locale();
		
		load_plugin_textdomain(
			$domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

		// Load WooCommerce specific translations
		if ( class_exists( 'WooCommerce' ) ) {
			load_plugin_textdomain(
				$domain,
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/woocommerce/'
			);
		}
	}

}
