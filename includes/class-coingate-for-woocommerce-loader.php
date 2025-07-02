<?php
/**
 * Register all actions and filters for the plugin
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
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Coingate_For_Woocommerce
 * @subpackage Coingate_For_Woocommerce/includes
 * @author     CoinGate <support@coingate.com>
 */
class Coingate_For_Woocommerce_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array<string, mixed>    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected array $actions = array();

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array<string, mixed>    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected array $filters = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook             The name of the WordPress action that is being registered.
	 * @param object $component        A reference to the instance of the object on which the action is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 * @return void
	 */
	public function add_action( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook             The name of the WordPress filter that is being registered.
	 * @param object $component        A reference to the instance of the object on which the filter is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 * @return void
	 */
	public function add_filter( string $hook, object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array<string, mixed>  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param  string                $hook             The name of the WordPress filter that is being registered.
	 * @param  object                $component        A reference to the instance of the object on which the filter is defined.
	 * @param  string                $callback         The name of the function definition on the $component.
	 * @param  int                   $priority         The priority at which the function should be fired.
	 * @param  int                   $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return array<string, mixed>                    The collection of actions and filters registered with WordPress.
	 */
	private function add( array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args ): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

}
