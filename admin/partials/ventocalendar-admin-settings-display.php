<?php
/**
 * Provide a admin area view for the plugin settings
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( $this->plugin_name );
		do_settings_sections( $this->get_settings_page_slug() );
		submit_button();
		?>
	</form>
</div>
