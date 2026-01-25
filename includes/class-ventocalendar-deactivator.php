<?php
/**
 * Fired during plugin deactivation.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Deactivator class.
 *
 * Defines all code that runs during plugin deactivation.
 * This class handles cleanup tasks like flushing rewrite rules.
 * Note: This does not delete plugin data or options.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Performs deactivation tasks such as flushing rewrite rules
	 * to clean up custom post type permalinks. Does not remove
	 * plugin data, settings, or custom post type content.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Flush rewrite rules to clean up custom post type permalinks.
		flush_rewrite_rules();
	}
}
