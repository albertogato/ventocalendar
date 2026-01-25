<?php
/**
 * Provide a admin area view for the plugin help/usage
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ventocalendar_nonce_action = 'ventocalendar_tabs';

// Get the current tab safely.
$ventocalendar_help_usage_active_tab = 'quick-start';

if ( isset( $_GET['tab'], $_GET['_wpnonce'] ) ) {
	$ventocalendar_get_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
	$ventocalendar_nonce   = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

	if ( wp_verify_nonce( $ventocalendar_nonce, 'ventocalendar_tabs' ) ) {
		$ventocalendar_help_usage_active_tab = $ventocalendar_get_tab;
	}
}

/**
 * Generates a URL for VentoCalendar help page tabs.
 *
 * Creates a secure admin URL with nonce verification for navigating
 * between different tabs in the VentoCalendar help page.
 *
 * @since 1.0.0
 *
 * @param string $tab The tab identifier to link to.
 * @return string The complete admin URL with tab parameter and nonce.
 */
function ventocalendar_tab_link( $tab ) {
	$ventocalendar_nonce = wp_create_nonce( 'ventocalendar_tabs' );
	return add_query_arg(
		array(
			'page'     => 'ventocalendar-help',
			'tab'      => $tab,
			'_wpnonce' => $ventocalendar_nonce,
		),
		admin_url( 'admin.php' )
	);
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( ventocalendar_tab_link( 'quick-start' ) ); ?>" class="nav-tab <?php echo 'quick-start' === $ventocalendar_help_usage_active_tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Quick start', 'ventocalendar' ); ?>
		</a>
		<a href="<?php echo esc_url( ventocalendar_tab_link( 'blocks' ) ); ?>" class="nav-tab <?php echo 'blocks' === $ventocalendar_help_usage_active_tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Gutenberg blocks', 'ventocalendar' ); ?>
		</a>
		<a href="<?php echo esc_url( ventocalendar_tab_link( 'shortcodes' ) ); ?>" class="nav-tab <?php echo 'shortcodes' === $ventocalendar_help_usage_active_tab ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Shortcodes', 'ventocalendar' ); ?>
		</a>
	</h2>

	<div class="tab-content" style="margin-top: 20px;">
		<?php if ( 'quick-start' === $ventocalendar_help_usage_active_tab ) : ?>
			<!-- Quick Start Tab -->
			<div class="ventocalendar-help-section">
				<h2><?php esc_html_e( 'Welcome to VentoCalendar!', 'ventocalendar' ); ?></h2>

				<p class="description" style="font-size: 15px; line-height: 1.6;">
					<?php esc_html_e( 'VentoCalendar is a simple yet powerful plugin for creating and managing events on your WordPress site. It provides a custom post type for events with start/end dates, color coding, a built-in calendar component to display events, and automatic display of event information on the frontend.', 'ventocalendar' ); ?>
				</p>

				<hr style="margin: 30px 0;">

				<h3><?php esc_html_e( 'Creating Your First Event', 'ventocalendar' ); ?></h3>

				<ol style="font-size: 15px; line-height: 1.8;">
					<li>
						<strong><?php esc_html_e( 'Navigate to Events', 'ventocalendar' ); ?></strong><br>
						<?php esc_html_e( 'In your WordPress admin menu, click on "VentoCalendar" and then "Add new event".', 'ventocalendar' ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Add event details', 'ventocalendar' ); ?></strong><br>
						<?php esc_html_e( 'Fill in the event title and description just like a regular post.', 'ventocalendar' ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Set event dates and times', 'ventocalendar' ); ?></strong><br>
						<?php esc_html_e( 'In the "Event Details" metabox, set the start date (required) and optionally the start time and end time. For multi-day events, check the "All day event" checkbox and specify an end date.', 'ventocalendar' ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Choose event color', 'ventocalendar' ); ?></strong><br>
						<?php esc_html_e( 'Select a color for your event. This color will be used in the calendar view.', 'ventocalendar' ); ?>
					</li>
					<li>
						<strong><?php esc_html_e( 'Publish', 'ventocalendar' ); ?></strong><br>
						<?php esc_html_e( 'Click "Publish" to make your event live.', 'ventocalendar' ); ?>
					</li>
				</ol>

				<hr style="margin: 30px 0;">

				<h3><?php esc_html_e( 'What you can do next', 'ventocalendar' ); ?></h3>

				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h4 style="margin-top: 0;">
						<span class="dashicons dashicons-calendar-alt" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Display a calendar', 'ventocalendar' ); ?>
					</h4>
					<p><?php esc_html_e( 'Show all your events in a beautiful monthly calendar view. You can add it to any post or page using:', 'ventocalendar' ); ?></p>
					<ul style="line-height: 1.8;">
						<li><?php esc_html_e( 'The calendar Gutenberg block (search for "Events Calendar")', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'The shortcode [ventocalendar-calendar]', 'ventocalendar' ); ?></li>
					</ul>
					<p>
						<a href="?page=ventocalendar-help&tab=blocks" class="button">
							<?php esc_html_e( 'Learn about Blocks', 'ventocalendar' ); ?>
						</a>
						<a href="?page=ventocalendar-help&tab=shortcodes" class="button" style="margin-left: 10px;">
							<?php esc_html_e( 'Learn about Shortcodes', 'ventocalendar' ); ?>
						</a>
					</p>
				</div>

				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h4 style="margin-top: 0;">
						<span class="dashicons dashicons-admin-settings" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Configure settings', 'ventocalendar' ); ?>
					</h4>
					<p><?php esc_html_e( 'Go to VentoCalendar > Settings to configure how event information is automatically displayed on event posts.', 'ventocalendar' ); ?></p>
					<p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=ventocalendar-settings' ) ); ?>" class="button">
							<?php esc_html_e( 'Go to Settings', 'ventocalendar' ); ?>
						</a>
					</p>
				</div>

				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h4 style="margin-top: 0;">
						<span class="dashicons dashicons-edit" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Advanced usage: Shortcodes', 'ventocalendar' ); ?>
					</h4>
					<p><?php esc_html_e( 'For advanced users, you can use shortcodes within event content to display specific event information like dates and times with custom formatting.', 'ventocalendar' ); ?></p>
					<p><?php esc_html_e( 'Available shortcodes:', 'ventocalendar' ); ?></p>
					<ul>
						<li><code>[ventocalendar-start-date]</code></li>
						<li><code>[ventocalendar-end-date]</code></li>
						<li><code>[ventocalendar-start-time]</code></li>
						<li><code>[ventocalendar-end-time]</code></li>
					</ul>
					<p>
						<a href="?page=ventocalendar-help&tab=shortcodes" class="button">
							<?php esc_html_e( 'View shortcode documentation', 'ventocalendar' ); ?>
						</a>
					</p>
				</div>

				<div style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0;">
					<p style="margin: 0;">
						<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
						<strong><?php esc_html_e( 'Tip:', 'ventocalendar' ); ?></strong>
						<?php esc_html_e( 'Event information can be displayed automatically on event pages, or you can use blocks and shortcodes for more control over placement and formatting.', 'ventocalendar' ); ?>
					</p>
				</div>
			</div>

		<?php elseif ( 'blocks' === $ventocalendar_help_usage_active_tab ) : ?>
			<!-- Gutenberg Blocks Tab -->
			<div class="ventocalendar-help-section">
				<h2><?php esc_html_e( 'Gutenberg blocks', 'ventocalendar' ); ?></h2>

				<p class="description" style="font-size: 15px;">
					<?php esc_html_e( 'VentoCalendar provides two Gutenberg blocks to help you display event information and calendars on your site.', 'ventocalendar' ); ?>
				</p>

				<hr style="margin: 30px 0;">

				<!-- Events Calendar Block -->
				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h3 style="margin-top: 0;">
						<span class="dashicons dashicons-calendar-alt" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Events Calendar Block', 'ventocalendar' ); ?>
					</h3>

					<p><?php esc_html_e( 'This block displays a monthly calendar view of all your published events with an intuitive and interactive interface.', 'ventocalendar' ); ?></p>

					<h4><?php esc_html_e( 'How to use', 'ventocalendar' ); ?></h4>
					<ol style="line-height: 1.8;">
						<li><?php esc_html_e( 'Edit any post or page in the Block Editor (Gutenberg)', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Click the "+" button to add a new block', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Search for "Events Calendar" in the block inserter', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'The calendar will automatically appear with all your published events', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Customize the calendar settings in the sidebar panel', 'ventocalendar' ); ?></li>
					</ol>

					<h4><?php esc_html_e( 'Block settings', 'ventocalendar' ); ?></h4>
					<table class="widefat" style="max-width: 800px; margin-top: 20px;">
						<thead>
							<tr>
								<th style="width: 30%;"><?php esc_html_e( 'Setting', 'ventocalendar' ); ?></th>
								<th style="width: 70%;"><?php esc_html_e( 'Description', 'ventocalendar' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong><?php esc_html_e( 'View type', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Choose Calendar to display the events of the month in a calendar view, or List to display a list of events for each month', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e( 'First day of week', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Choose whether to start the week on Monday or Sunday', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Initial month', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Set the initial month to display when the calendar loads (format: YYYY-MM, e.g., 2024-06). Leave empty to show the current month', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e( 'Layout', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Choose between three visual layouts: Basic, Compact, and Clean to adjust spacing and button appearance', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e( 'Show start date', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Display the start date when viewing event details in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Show end date', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Display the end date when viewing event details in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e( 'Show start time', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Include the time in the start date display in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Show end time', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Include the time in the end date display in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Show Add to Google Calendar button', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Include the Google add to calendar button in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Show Add to Apple Calendar button', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Include the Apple add to calendar button in the modal popup', 'ventocalendar' ); ?></td>
							</tr>
						</tbody>
					</table>

					<h4><?php esc_html_e( 'Calendar features', 'ventocalendar' ); ?></h4>
					<ul style="line-height: 1.8;">
						<li><strong><?php esc_html_e( 'Monthly view:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Navigate between months using Previous, Next, and Today buttons', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'List view:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Navigate between months and display a simple list of events for each month', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Event colors:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Events are displayed in the colors you assign to them', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Multi-day events:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Events spanning multiple days appear as horizontal bars across the calendar', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Event details modal:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Click on any day to see all events scheduled for that day with links to their full details', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Responsive design:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'The calendar automatically adapts to mobile devices for optimal viewing', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Design integration:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Design integration: Clean design that blends naturally with your WordPress theme.', 'ventocalendar' ); ?></li>
					</ul>

					<div style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0;">
						<p style="margin: 0;">
							<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
							<strong><?php esc_html_e( 'Note:', 'ventocalendar' ); ?></strong>
							<?php esc_html_e( 'The calendar will only display published events. Draft and scheduled events are not shown in the calendar view.', 'ventocalendar' ); ?>
						</p>
					</div>
				</div>


				<hr style="margin: 30px 0;">

				<!-- Event Info Block -->
				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h3 style="margin-top: 0;">
						<span class="dashicons dashicons-calendar-alt" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Event Info Block', 'ventocalendar' ); ?>
					</h3>

					<p><?php esc_html_e( 'This block displays event date and time information with the same styling as the automatic display, but gives you full control over the format and appearance.', 'ventocalendar' ); ?></p>

					<h4><?php esc_html_e( 'How to use', 'ventocalendar' ); ?></h4>
					<ol style="line-height: 1.8;">
						<li><?php esc_html_e( 'Edit an event post in the Block Editor (Gutenberg)', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Click the "+" button to add a new block', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Search for "Event Info" in the block inserter', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'The block will automatically display the event dates and times', 'ventocalendar' ); ?></li>
						<li><?php esc_html_e( 'Customize the block settings in the sidebar panel', 'ventocalendar' ); ?></li>
					</ol>

					<h4><?php esc_html_e( 'Block settings', 'ventocalendar' ); ?></h4>
					<table class="widefat" style="max-width: 800px; margin-top: 20px;">
						<thead>
							<tr>
								<th style="width: 30%;"><?php esc_html_e( 'Setting', 'ventocalendar' ); ?></th>
								<th style="width: 70%;"><?php esc_html_e( 'Description', 'ventocalendar' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong><?php esc_html_e( 'Show start time', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Check this box to include the time in the start date display', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><strong><?php esc_html_e( 'Show end time', 'ventocalendar' ); ?></strong></td>
								<td><?php esc_html_e( 'Check this box to include the time in the end date display', 'ventocalendar' ); ?></td>
							</tr>
						</tbody>
					</table>

					<p style="margin-top: 15px;">
						<?php
						printf(
							/* translators: %s: link to WordPress general settings */
							esc_html__( 'Date and time formats are automatically taken from your WordPress general settings. You can change them in %s.', 'ventocalendar' ),
							'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Settings > General', 'ventocalendar' ) . '</a>'
						);
						?>
					</p>

					<div style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0;">
						<p style="margin: 0;">
							<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
							<strong><?php esc_html_e( 'Important:', 'ventocalendar' ); ?></strong>
							<?php esc_html_e( 'The Event Info block is only available when editing Event posts. If you try to use it in other post types, it will display a message indicating that it only works with events.', 'ventocalendar' ); ?>
						</p>
					</div>

					<h4><?php esc_html_e( 'When to Use', 'ventocalendar' ); ?></h4>
					<ul style="line-height: 1.8;">
						<li><strong><?php esc_html_e( 'Automatic display:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Use the plugin settings to show event info automatically at the beginning of all event posts with consistent formatting.', 'ventocalendar' ); ?></li>
						<li><strong><?php esc_html_e( 'Event info block:', 'ventocalendar' ); ?></strong> <?php esc_html_e( 'Use this block when you need custom formatting for specific events, or want to place the event info in a specific location within your content.', 'ventocalendar' ); ?></li>
					</ul>

					<div style="background: #f9f9f9; border-left: 4px solid #f0b849; padding: 15px; margin: 20px 0;">
						<p style="margin: 0;">
							<span class="dashicons dashicons-lightbulb" style="color: #f0b849;"></span>
							<strong><?php esc_html_e( 'Tip:', 'ventocalendar' ); ?></strong>
							<?php esc_html_e( 'You can use both the automatic display and the block together. The block gives you flexibility to add event information anywhere in your content with different formats.', 'ventocalendar' ); ?>
						</p>
					</div>
				</div>
			</div>

		<?php elseif ( 'shortcodes' === $ventocalendar_help_usage_active_tab ) : ?>
			<!-- Shortcodes Tab -->
			<div class="ventocalendar-help-section">
				<h2><?php esc_html_e( 'Shortcodes', 'ventocalendar' ); ?></h2>

				<p class="description" style="font-size: 15px;">
					<?php esc_html_e( 'VentoCalendar provides several shortcodes that you can use to display event information in your content.', 'ventocalendar' ); ?>
				</p>

				<hr style="margin: 30px 0;">

				<!-- Event Information Shortcodes -->
				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h3 style="margin-top: 0;">
						<span class="dashicons dashicons-shortcode" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Event information shortcodes', 'ventocalendar' ); ?>
					</h3>

					<p><?php esc_html_e( 'These shortcodes can be used within event post content to display specific event information. They only work in event posts.', 'ventocalendar' ); ?></p>

					<h4><?php esc_html_e( 'Available shortcodes', 'ventocalendar' ); ?></h4>

					<table class="widefat" style="max-width: 800px; margin-top: 20px;">
						<thead>
							<tr>
								<th style="width: 40%;"><?php esc_html_e( 'Shortcode', 'ventocalendar' ); ?></th>
								<th style="width: 40%;"><?php esc_html_e( 'Description', 'ventocalendar' ); ?></th>
								<th style="width: 20%;"><?php esc_html_e( 'Example Output', 'ventocalendar' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><code>[ventocalendar-start-date]</code></td>
								<td><?php esc_html_e( 'Displays the event start date using the format configured in WordPress Settings > General.', 'ventocalendar' ); ?></td>
								<td><strong><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( 'now' ) ) ); ?></strong></td>
							</tr>
							<tr class="alternate">
								<td><code>[ventocalendar-end-date]</code></td>
								<td><?php esc_html_e( 'Displays the event end date using the format configured in WordPress Settings > General.', 'ventocalendar' ); ?></td>
								<td><strong><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( 'now' ) ) ); ?></strong></td>
							</tr>
							<tr>
								<td><code>[ventocalendar-start-time]</code></td>
								<td><?php esc_html_e( 'Displays the event start time using the format configured in WordPress Settings > General.', 'ventocalendar' ); ?></td>
								<td><strong><?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( 'now' ) ) ); ?></strong></td>
							</tr>
							<tr class="alternate">
								<td><code>[ventocalendar-end-time]</code></td>
								<td><?php esc_html_e( 'Displays the event end time using the format configured in WordPress Settings > General.', 'ventocalendar' ); ?></td>
								<td><strong><?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( 'now' ) ) ); ?></strong></td>
							</tr>
						</tbody>
					</table>

					<p style="margin-top: 15px;">
						<?php
						printf(
							/* translators: %s: link to WordPress general settings */
							esc_html__( 'All shortcodes use the date and time formats configured in your WordPress general settings. You can change them in %s.', 'ventocalendar' ),
							'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Settings > General', 'ventocalendar' ) . '</a>'
						);
						?>
					</p>

					<h4 style="margin-top: 30px;"><?php esc_html_e( 'Usage examples', 'ventocalendar' ); ?></h4>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Example 1: Display full date and time', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							<?php esc_html_e( 'Event starts on [ventocalendar-start-date] at [ventocalendar-start-time] and ends at [ventocalendar-end-time].', 'ventocalendar' ); ?>
						</code>
						<p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
							<?php
							printf(
								/* translators: 1: example start date, 2: example start time, 3: example end date, 4: example end time */
								esc_html__( 'Result: Event starts on %1$s at %2$s and ends at %3$s.', 'ventocalendar' ),
								'<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( 'now' ) ) ) . '</strong>',
								'<strong>' . esc_html( date_i18n( get_option( 'time_format' ), strtotime( 'now' ) ) ) . '</strong>',
								'<strong>' . esc_html( date_i18n( get_option( 'time_format' ), strtotime( '+2 hours' ) ) ) . '</strong>'
							);
							?>
						</p>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Example 2: Just the date', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							<?php esc_html_e( 'Join us on [ventocalendar-start-date] for this amazing event!', 'ventocalendar' ); ?>
						</code>
						<p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
							<?php
							printf(
								/* translators: %s: example date */
								esc_html__( 'Result: Join us on %s for this amazing event!', 'ventocalendar' ),
								'<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( 'now' ) ) ) . '</strong>'
							);
							?>
						</p>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Example 3: Only times', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							<?php esc_html_e( 'From [ventocalendar-start-time] to [ventocalendar-end-time]', 'ventocalendar' ); ?>
						</code>
						<p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
							<?php
							printf(
								/* translators: 1: example start time, 2: example end time */
								esc_html__( 'Result: From %1$s to %2$s', 'ventocalendar' ),
								'<strong>' . esc_html( date_i18n( get_option( 'time_format' ), strtotime( 'now' ) ) ) . '</strong>',
								'<strong>' . esc_html( date_i18n( get_option( 'time_format' ), strtotime( '+2 hours' ) ) ) . '</strong>'
							);
							?>
						</p>
					</div>

					<div style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0;">
						<p style="margin: 0;">
							<span class="dashicons dashicons-info" style="color: #0073aa;"></span>
							<strong><?php esc_html_e( 'Important:', 'ventocalendar' ); ?></strong>
							<?php esc_html_e( 'These shortcodes only work within event post content. If used in other post types, they will not display anything.', 'ventocalendar' ); ?>
						</p>
					</div>
				</div>

				<hr style="margin: 30px 0;">

				<!-- Calendar Shortcode -->
				<div style="background: #fff; border: 1px solid #ddd; padding: 20px; margin: 20px 0;">
					<h3 style="margin-top: 0;">
						<span class="dashicons dashicons-calendar-alt" style="color: #2271b1;"></span>
						<?php esc_html_e( 'Events calendar shortcode', 'ventocalendar' ); ?>
					</h3>

					<p><?php esc_html_e( 'Display your events in an elegant monthly calendar view. This shortcode can be used in any post, page, or widget area.', 'ventocalendar' ); ?></p>

					<h4><?php esc_html_e( 'Basic usage', 'ventocalendar' ); ?></h4>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd; font-size: 14px;">
							[ventocalendar-calendar]
						</code>
					</div>

					<h4><?php esc_html_e( 'Shortcode parameters', 'ventocalendar' ); ?></h4>

					<table class="widefat" style="max-width: 800px; margin-top: 20px;">
						<thead>
							<tr>
								<th style="width: 25%;"><?php esc_html_e( 'Parameter', 'ventocalendar' ); ?></th>
								<th style="width: 20%;"><?php esc_html_e( 'Values', 'ventocalendar' ); ?></th>
								<th style="width: 15%;"><?php esc_html_e( 'Default', 'ventocalendar' ); ?></th>
								<th style="width: 40%;"><?php esc_html_e( 'Description', 'ventocalendar' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><code>view_type</code></td>
								<td><code>calendar</code><br><code>list</code></td>
								<td><code>calendar</code></td>
								<td><?php esc_html_e( 'Set the calendar view type', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><code>first_day_of_week</code></td>
								<td><code>monday</code><br><code>sunday</code></td>
								<td><code>monday</code></td>
								<td><?php esc_html_e( 'Set the first day of the week in the calendar', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><code>initial_month</code></td>
								<td><?php esc_html_e( 'YYYY-MM format', 'ventocalendar' ); ?></td>
								<td><?php esc_html_e( 'Current month', 'ventocalendar' ); ?></td>
								<td><?php esc_html_e( 'Set the initial month to display (e.g., 2024-06 for June 2024)', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><code>layout</code></td>
								<td><code>basic</code><br><code>compact</code><br><code>compact</code></td>
								<td><code>basic</code></td>
								<td><?php esc_html_e( 'Set the layout in the calendar', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><code>show_start_date</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>true</code></td>
								<td><?php esc_html_e( 'Show the start date in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><code>show_end_date</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>true</code></td>
								<td><?php esc_html_e( 'Show the end date in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
							<tr>
								<td><code>show_start_time</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>false</code></td>
								<td><?php esc_html_e( 'Show the start time in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><code>show_end_time</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>false</code></td>
								<td><?php esc_html_e( 'Show the end time in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><code>show_add_to_calendar_google</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>false</code></td>
								<td><?php esc_html_e( 'Show the Google add to calendar button in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
							<tr class="alternate">
								<td><code>show_add_to_calendar_apple</code></td>
								<td><code>true</code><br><code>false</code></td>
								<td><code>false</code></td>
								<td><?php esc_html_e( 'Show the Apple add to calendar button in the event details modal', 'ventocalendar' ); ?></td>
							</tr>
						</tbody>
					</table>

					<p style="margin-top: 15px;">
						<?php
						printf(
							/* translators: %s: link to WordPress general settings */
							esc_html__( 'The calendar uses the date and time formats configured in your WordPress general settings. You can change them in %s.', 'ventocalendar' ),
							'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Settings > General', 'ventocalendar' ) . '</a>'
						);
						?>
					</p>

					<h4 style="margin-top: 30px;"><?php esc_html_e( 'Usage examples', 'ventocalendar' ); ?></h4>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Basic calendar', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar]
						</code>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Calendar with sunday as first day', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar first_day_of_week="sunday"]
						</code>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Calendar showing times in event details', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar show_start_time="true" show_end_time="true"]
						</code>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Calendar starting in a specific month', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar initial_month="2024-12"]
						</code>
						<p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
							<?php esc_html_e( 'This will display December 2024 when the calendar loads. Users can still navigate to other months.', 'ventocalendar' ); ?>
						</p>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Calendar with custom settings', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar first_day_of_week="sunday" initial_month="2025-06" show_start_time="true"]
						</code>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
						<p style="margin: 0 0 10px 0;"><strong><?php esc_html_e( 'Basic calendar showing events in a list', 'ventocalendar' ); ?></strong></p>
						<code style="display: block; background: white; padding: 10px; border: 1px solid #ddd;">
							[ventocalendar-calendar view_type="list"]
						</code>
					</div>

					<div style="background: #f9f9f9; border-left: 4px solid #f0b849; padding: 15px; margin: 20px 0;">
						<p style="margin: 0;">
							<span class="dashicons dashicons-lightbulb" style="color: #f0b849;"></span>
							<strong><?php esc_html_e( 'Tip:', 'ventocalendar' ); ?></strong>
							<?php esc_html_e( 'You can also use the Events Calendar Gutenberg block for a visual way to add and configure the calendar in the block editor.', 'ventocalendar' ); ?>
						</p>
					</div>
				</div>
			</div>

		<?php endif; ?>
	</div>
</div>
