<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           VentoCalendar
 *
 * @wordpress-plugin
 * Plugin Name:       VentoCalendar
 * Plugin URI:
 * Description:       A lightweight and intuitive events calendar plugin for WordPress.
 * Version:           1.1.2
 * Author:            Alberto Gato Otero (albertogato)
 * Author URI:        https://profiles.wordpress.org/albertogato/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ventocalendar
 * Domain Path:       /languages
 *
 * VentoCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * VentoCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with VentoCalendar. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VENTOCALENDAR_VERSION', '1.1.2' );

/**
 * Define the plugin base path and URL.
 * These are always relative to the plugin directory in WordPress.
 */
if ( ! defined( 'VENTOCALENDAR_PLUGIN_PATH' ) ) {
	define( 'VENTOCALENDAR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'VENTOCALENDAR_PLUGIN_URL' ) ) {
	define( 'VENTOCALENDAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Define the core path for loading core files.
 */
if ( ! defined( 'VENTOCALENDAR_CORE_PATH' ) ) {
	define( 'VENTOCALENDAR_CORE_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Define the core URL for enqueuing assets.
 * In both development and distribution, URLs are relative to the plugin directory.
 */
if ( ! defined( 'VENTOCALENDAR_CORE_URL' ) ) {
	// URLs are always relative to the plugin directory (works with both symlinks and built versions).
	define( 'VENTOCALENDAR_CORE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in core/includes/class-ventocalendar-activator.php
 */
function ventocalendar_activate() {
	require_once VENTOCALENDAR_CORE_PATH . 'includes/class-ventocalendar-activator.php';
	VentoCalendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in core/includes/class-ventocalendar-deactivator.php
 */
function ventocalendar_deactivate() {
	require_once VENTOCALENDAR_CORE_PATH . 'includes/class-ventocalendar-deactivator.php';
	VentoCalendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ventocalendar_activate' );
register_deactivation_hook( __FILE__, 'ventocalendar_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once VENTOCALENDAR_CORE_PATH . 'includes/class-ventocalendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function ventocalendar_run() {
	$plugin = new VentoCalendar();
	$plugin->run();
}

ventocalendar_run();
