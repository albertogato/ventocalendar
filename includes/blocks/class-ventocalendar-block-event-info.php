<?php
/**
 * Event Info Block functionality.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Event Info Block class.
 *
 * Handles the registration and rendering of the event information block
 * for displaying event dates and times in the frontend. Manages date/time
 * formatting and display options for single and multi-day events.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/blocks
 * @since      1.0.0
 */
class VentoCalendar_Block_Event_Info {

	/**
	 * Register the block.
	 *
	 * @since    1.0.0
	 */
	public function register_block() {
		$script_path = 'admin/js/blocks/event-info.js';
		$script_file = VENTOCALENDAR_CORE_PATH . $script_path;

		// Register the block editor script.
		wp_register_script(
			'ventocalendar-block-event-info',
			VENTOCALENDAR_CORE_URL . $script_path,
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data' ),
			file_exists( $script_file ) ? filemtime( $script_file ) : '1.0.0',
			true
		);

		wp_set_script_translations(
			'ventocalendar-block-event-info',
			'ventocalendar',
			VENTOCALENDAR_CORE_PATH . 'languages'
		);

		// Register the block.
		register_block_type(
			'ventocalendar/event-info',
			array(
				'editor_script'   => 'ventocalendar-block-event-info',
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'showStartTime' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showEndTime'   => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
			)
		);
	}

	/**
	 * Format date and time for display.
	 *
	 * @since    1.0.0
	 * @param    string $date          The date in Y-m-d format.
	 * @param    string $time          The time in H:i:s format (optional).
	 * @param    string $date_format   The date format to use.
	 * @param    string $time_format   The time format to use.
	 * @param    bool   $show_time     Whether to include time in the output.
	 * @return   string The formatted date/time string.
	 */
	private function format_date_time_display( $date, $time, $date_format, $time_format, $show_time ) {
		if ( empty( $date ) ) {
			return '';
		}

		// If we should show time and time is provided.
		if ( $show_time && ! empty( $time ) ) {
			// Combine date and time for timestamp.
			$datetime_str = $date . ' ' . $time;
			$timestamp    = strtotime( $datetime_str );

			if ( false === $timestamp ) {
				return '';
			}

			// Format with both date and time.
			return date_i18n( $date_format . ' ' . $time_format, $timestamp );
		}

		// Otherwise, just format the date.
		$timestamp = strtotime( $date );

		if ( false === $timestamp ) {
			return '';
		}

		return date_i18n( $date_format, $timestamp );
	}

	/**
	 * Format only time for display (without date).
	 *
	 * @since    1.0.0
	 * @param    string $date          The date in Y-m-d format (needed for timestamp).
	 * @param    string $time          The time in H:i:s format.
	 * @param    string $time_format   The time format to use.
	 * @return   string The formatted time string.
	 */
	private function format_time_only( $date, $time, $time_format ) {
		if ( empty( $time ) || empty( $date ) ) {
			return '';
		}

		// Combine date and time for timestamp.
		$datetime_str = $date . ' ' . $time;
		$timestamp    = strtotime( $datetime_str );

		if ( false === $timestamp ) {
			return '';
		}

		// Format only the time.
		return date_i18n( $time_format, $timestamp );
	}

	/**
	 * Render the block on the frontend.
	 *
	 * @since    1.0.0
	 * @param    array $attributes    Block attributes.
	 * @return   string The rendered block HTML.
	 */
	public function render_block( $attributes ) {
		global $post;

		// Check that we are in a post.
		if ( ! $post ) {
			return '';
		}

		// Check that it is a 'ventocalendar_event' post type.
		if ( 'ventocalendar_event' !== get_post_type( $post ) ) {
			return '';
		}

		// Use WordPress formats.
		$date_format     = get_option( 'date_format' );
		$time_format     = get_option( 'time_format' );
		$show_start_time = isset( $attributes['showStartTime'] ) ? $attributes['showStartTime'] : false;
		$show_end_time   = isset( $attributes['showEndTime'] ) ? $attributes['showEndTime'] : false;

		// Get event dates, times and color.
		$start_date  = get_post_meta( $post->ID, '_start_date', true );
		$end_date    = get_post_meta( $post->ID, '_end_date', true );
		$start_time  = get_post_meta( $post->ID, '_start_time', true );
		$end_time    = get_post_meta( $post->ID, '_end_time', true );
		$event_color = get_post_meta( $post->ID, '_color', true );

		// If there is no start date, return empty.
		if ( empty( $start_date ) ) {
			return '';
		}

		// If there is no color, use default color.
		if ( empty( $event_color ) ) {
			$event_color = '#2271b1';
		}

		// Format start date (with or without time).
		$start_formatted = $this->format_date_time_display(
			$start_date,
			$start_time,
			$date_format,
			$time_format,
			$show_start_time
		);

		// Format end date/time.
		$end_formatted = '';

		// If end_date exists (multi-day event).
		if ( ! empty( $end_date ) && $end_date !== $start_date ) {
			// Multi-day event: show end_date with end_time if exists.
			$end_formatted = $this->format_date_time_display(
				$end_date,
				$end_time, // Include time if exists.
				$date_format,
				$time_format,
				$show_end_time // Show time according to configuration.
			);
		} elseif ( ! empty( $end_time ) && $show_end_time ) { // Check if there is NO end_date (or it equals start_date) but there is end_time.
			// Same-day event: only show time.
			$end_formatted = $this->format_time_only(
				$start_date,
				$end_time,
				$time_format
			);
		}

		// Build HTML with the same format as display_event_dates().
		$html  = '<div class="ventocalendar-date-info" style="border-left-color: ' . esc_attr( $event_color ) . ';">';
		$html .= '<div class="ventocalendar-date-container">';

		if ( ! empty( $start_formatted ) ) {
			$html .= '<span class="ventocalendar-date-value">' . esc_html( $start_formatted ) . '</span>';
		}

		if ( ! empty( $start_formatted ) && ! empty( $end_formatted ) ) {
			$html .= '<span class="ventocalendar-date-separator"> - </span>';
		}

		if ( ! empty( $end_formatted ) ) {
			$html .= '<span class="ventocalendar-date-value">' . esc_html( $end_formatted ) . '</span>';
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
}
