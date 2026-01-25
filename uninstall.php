<?php
/**
 * Plugin uninstall handler.
 *
 * This file is executed when the plugin is uninstalled via WordPress.
 * No data is removed on uninstall. Event data remains in the database
 * unless the site owner removes it manually.
 *
 * @package VentoCalendar
 */

// Exit if accessed directly or if uninstall not triggered by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
