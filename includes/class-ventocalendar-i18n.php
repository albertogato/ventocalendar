<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Internationalization class.
 *
 * Handles loading the plugin text domain for translation support.
 * This makes the plugin ready for translation into multiple languages
 * using WordPress translation functions.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar_I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Loads the plugin's translations from the /languages directory
	 * and makes them available for WordPress translation functions
	 * like __(), _e(), esc_html__(), etc.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
	}
}
