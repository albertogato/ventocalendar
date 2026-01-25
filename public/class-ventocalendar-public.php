<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public-facing functionality class.
 *
 * Defines all functionality for the public-facing side of the plugin.
 * Handles enqueueing of public stylesheets and scripts, and manages
 * the automatic display of event date/time information in event posts
 * based on plugin settings.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/public
 * @since      1.0.0
 */
class VentoCalendar_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$css_file = VENTOCALENDAR_CORE_PATH . 'public/css/ventocalendar-public.css';
		wp_enqueue_style( $this->plugin_name, VENTOCALENDAR_CORE_URL . 'public/css/ventocalendar-public.css', array(), filemtime( $css_file ), 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$js_file = VENTOCALENDAR_CORE_PATH . 'public/js/ventocalendar-public.js';
		wp_enqueue_script( $this->plugin_name, VENTOCALENDAR_CORE_URL . 'public/js/ventocalendar-public.js', array( 'jquery' ), filemtime( $js_file ), false );
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
	 * @return   string    The formatted date/time string.
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
	 * @return   string    The formatted time string.
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
	 * Add event dates to the content of event posts.
	 *
	 * @since    1.0.0
	 * @param    string $content    The post content.
	 * @return   string    The modified post content.
	 */
	public function display_event_dates( $content ) {
		// Check if the option is enabled.
		$options = get_option( $this->plugin_name, array() );
		if ( ! isset( $options['show_event_info_automatically'] ) || ! $options['show_event_info_automatically'] ) {
			return $content;
		}

		// Only apply to individual posts of type 'ventocalendar_event'.
		if ( ! is_singular( 'ventocalendar_event' ) ) {
			return $content;
		}

		global $post;

		// Get the dates, times, and color of the event.
		$start_date  = get_post_meta( $post->ID, '_start_date', true );
		$end_date    = get_post_meta( $post->ID, '_end_date', true );
		$start_time  = get_post_meta( $post->ID, '_start_time', true );
		$end_time    = get_post_meta( $post->ID, '_end_time', true );
		$event_color = get_post_meta( $post->ID, '_color', true );

		// If there is no start date, return the content without modification.
		if ( empty( $start_date ) ) {
			return $content;
		}

		// If there is no color, use the default color.
		if ( empty( $event_color ) ) {
			$event_color = '#2271b1';
		}

		// Use WordPress formats.
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		// Get settings for showing times.
		$show_start_time = isset( $options['show_start_time'] ) && $options['show_start_time'];
		$show_end_time   = isset( $options['show_end_time'] ) && $options['show_end_time'];

		// Format the start date (with or without time).
		$start_formatted = $this->format_date_time_display(
			$start_date,
			$start_time,
			$date_format,
			$time_format,
			$show_start_time
		);

		// Format the end date/time.
		$end_formatted = '';

		if ( ! empty( $end_date ) && $end_date !== $start_date ) {
			// Multi-day event: show end_date with end_time if it exists.
			$end_formatted = $this->format_date_time_display(
				$end_date,
				$end_time,
				$date_format,
				$time_format,
				$show_end_time
			);
		} elseif ( ! empty( $end_time ) && $show_end_time ) {
			// One day event: only show the time.
			$end_formatted = $this->format_time_only(
				$start_date,
				$end_time,
				$time_format
			);
		}

		// Build the HTML for the dates with the event color.
		$dates_html  = '<div class="ventocalendar-date-info" style="border-left-color: ' . esc_attr( $event_color ) . ';">';
		$dates_html .= '<div class="ventocalendar-date-container">';

		if ( ! empty( $start_formatted ) ) {
			$dates_html .= '<span class="ventocalendar-date-value">' . esc_html( $start_formatted ) . '</span>';
		}

		if ( ! empty( $start_formatted ) && ! empty( $end_formatted ) ) {
			$dates_html .= '<span class="ventocalendar-date-separator"> - </span>';
		}

		if ( ! empty( $end_formatted ) ) {
			$dates_html .= '<span class="ventocalendar-date-value">' . esc_html( $end_formatted ) . '</span>';
		}

		$dates_html .= '</div>';
		$dates_html .= '</div>';

		// Add the dates to the beginning of the content.
		return $dates_html . $content;
	}
}
