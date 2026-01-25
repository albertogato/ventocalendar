<?php
/**
 * Fired during plugin activation.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Activator class.
 *
 * Defines all code that runs during plugin activation.
 * This class handles setup tasks like creating database tables,
 * setting default options, and flushing rewrite rules.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Performs activation tasks such as setting up default options,
	 * creating necessary database tables, and flushing rewrite rules
	 * to ensure custom post types are properly registered.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Register the CPT before flushing rewrite rules.
		// We need to manually register it here because the 'init' hook hasn't run yet.
		require_once plugin_dir_path( __FILE__ ) . 'cpt/class-ventocalendar-cpt-event.php';

		$cpt_event = new VentoCalendar_CPT_Event();
		$cpt_event->register_post_type();

		// Flush rewrite rules to register custom post types.
		flush_rewrite_rules();

		// Set default options if they don't exist.
		$default_options = array(
			'show_event_info_automatically' => 0,
			'show_start_time'               => 0,
			'show_end_time'                 => 0,
		);

		// Only add defaults if option doesn't exist yet.
		if ( false === get_option( 'ventocalendar' ) ) {
			add_option( 'ventocalendar', $default_options );
		}
	}
}
