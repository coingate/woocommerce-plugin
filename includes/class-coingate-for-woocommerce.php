<?php
/**
 * The core plugin class.
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    Coingate_For_Woocommerce_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected Coingate_For_Woocommerce_Loader $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected string $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'COINGATE_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = COINGATE_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coingate-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coingate_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Coingate_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Coingate_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Coingate_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies(): void {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coingate-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coingate-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coingate-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coingate-for-woocommerce-public.php';

		$this->loader = new Coingate_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Coingate_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function set_locale(): void {
		$plugin_i18n = new Coingate_For_Woocommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function define_admin_hooks(): void {
		$plugin_admin = new Coingate_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'load_plugin' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function define_public_hooks(): void {
		$plugin_public = new Coingate_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'woocommerce_payment_gateways', $plugin_public, 'register_payment_gateway' );

		$this->loader->add_action( 'woocommerce_blocks_loaded', $plugin_public, 'woocommerce_gateway_coingate_block_support' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string The name of the plugin.
	 * @since  1.0.0
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Coingate_For_Woocommerce_Loader Orchestrates the hooks of the plugin.
	 * @since  1.0.0
	 */
	public function get_loader(): Coingate_For_Woocommerce_Loader {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 * @since  1.0.0
	 */
	public function get_version(): string {
		return $this->version;
	}

}
