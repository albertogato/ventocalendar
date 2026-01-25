<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core plugin class.
 *
 * This is the main class that orchestrates the entire plugin. It loads all
 * dependencies, defines hooks for admin and public areas, registers custom
 * post types, shortcodes, blocks, and REST API endpoints. This class acts
 * as the central coordinator for all plugin functionality.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      VentoCalendar_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VENTOCALENDAR_VERSION' ) ) {
			$this->version = VENTOCALENDAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ventocalendar';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_cpt();
		$this->define_shortcodes();
		$this->define_blocks();
		$this->define_rest_api();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - VentoCalendar_Loader. Orchestrates the hooks of the plugin.
	 * - VentoCalendar_I18n. Defines internationalization functionality.
	 * - VentoCalendar_Admin. Defines all hooks for the admin area.
	 * - VentoCalendar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ventocalendar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ventocalendar-i18n.php';

		require_once plugin_dir_path( __DIR__ ) . 'includes/cpt/class-ventocalendar-cpt-event.php';

		/**
		 * The class responsible for defining shortcodes.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ventocalendar-shortcodes.php';

		/**
		 * The class responsible for defining REST API endpoints.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ventocalendar-rest-api.php';

		/**
		 * The class responsible for defining blocks.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/blocks/class-ventocalendar-block-event-info.php';
		require_once plugin_dir_path( __DIR__ ) . 'includes/blocks/class-ventocalendar-block-calendar.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ventocalendar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-ventocalendar-public.php';

		$this->loader = new VentoCalendar_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the VentoCalendar_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new VentoCalendar_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new VentoCalendar_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cleanup_admin_menu', 999 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new VentoCalendar_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'display_event_dates' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    VentoCalendar_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register custom post types.
	 *
	 * Instantiates and registers all custom post types used by the plugin,
	 * including the Event post type with its meta fields, meta boxes, and
	 * custom admin columns.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_cpt() {
		$cpt_event = new VentoCalendar_CPT_Event();
		$cpt_event->register_hooks();
	}

	/**
	 * Register all shortcodes.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shortcodes() {
		$shortcodes = new VentoCalendar_Shortcodes();
		$this->loader->add_action( 'init', $shortcodes, 'register_shortcodes' );
	}

	/**
	 * Register all Gutenberg blocks.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_blocks() {
		$block_event_info = new VentoCalendar_Block_Event_Info();
		$this->loader->add_action( 'init', $block_event_info, 'register_block' );

		$block_calendar = new VentoCalendar_Block_Calendar();
		$this->loader->add_action( 'init', $block_calendar, 'register_block' );
	}

	/**
	 * Register REST API endpoints.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_rest_api() {
		$rest_api = new VentoCalendar_REST_API();
		$this->loader->add_action( 'rest_api_init', $rest_api, 'register_routes' );
	}
}
