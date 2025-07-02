<?php
/**
 * Fired during plugin deactivation.
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
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_Deactivator {

	/**
	 * Delete plugin settings.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		delete_option( 'woocommerce_coingate_settings' );
		
		// Clear any existing caches
		wp_cache_flush();
	}

}
