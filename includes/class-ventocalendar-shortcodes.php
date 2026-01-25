<?php
/**
 * Shortcodes functionality of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes class.
 *
 * Handles registration and functionality of all plugin shortcodes.
 * Provides shortcodes for displaying event dates, times, and the
 * interactive calendar component in posts and pages.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar_Shortcodes {

	/**
	 * Register all shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'ventocalendar-start-date', array( $this, 'start_date_shortcode' ) );
		add_shortcode( 'ventocalendar-end-date', array( $this, 'end_date_shortcode' ) );
		add_shortcode( 'ventocalendar-start-time', array( $this, 'start_time_shortcode' ) );
		add_shortcode( 'ventocalendar-end-time', array( $this, 'end_time_shortcode' ) );
		add_shortcode( 'ventocalendar-calendar', array( $this, 'calendar_shortcode' ) );
	}

	/**
	 * Shortcode to display the start date of an event (date only).
	 *
	 * @since    1.0.0
	 * @return   string   The formatted start date.
	 */
	public function start_date_shortcode() {
		return $this->get_event_date( '_start_date', get_option( 'date_format' ) );
	}

	/**
	 * Shortcode to display the end date of an event (date only).
	 *
	 * @since    1.0.0
	 * @return   string   The formatted end date.
	 */
	public function end_date_shortcode() {
		return $this->get_event_date( '_end_date', get_option( 'date_format' ) );
	}

	/**
	 * Shortcode to display the start time of an event (time only).
	 *
	 * @since    1.0.0
	 * @return   string   The formatted start time.
	 */
	public function start_time_shortcode() {
		return $this->get_event_time( '_start_date', '_start_time', get_option( 'time_format' ) );
	}

	/**
	 * Shortcode to display the end time of an event (time only).
	 *
	 * @since    1.0.0
	 * @return   string   The formatted end time.
	 */
	public function end_time_shortcode() {
		return $this->get_event_time( '_start_date', '_end_time', get_option( 'time_format' ) );
	}

	/**
	 * Get and format event date.
	 *
	 * @since    1.0.0
	 * @param    string $meta_key    The date meta key to retrieve.
	 * @param    string $format      The date format.
	 * @return   string    The formatted date or empty string.
	 */
	private function get_event_date( $meta_key, $format ) {
		global $post;

		// Verify we're in a post.
		if ( ! $post ) {
			return '';
		}

		// Verify it's an 'ventocalendar_event' post type.
		if ( 'ventocalendar_event' !== get_post_type( $post ) ) {
			return '';
		}

		// Get the meta value.
		$date = get_post_meta( $post->ID, $meta_key, true );

		// If no value, return empty.
		if ( empty( $date ) ) {
			return '';
		}

		// Convert to timestamp.
		$timestamp = strtotime( $date );

		// If timestamp is invalid, return empty.
		if ( false === $timestamp ) {
			return '';
		}

		// Format and return.
		return date_i18n( $format, $timestamp );
	}

	/**
	 * Get and format event time.
	 *
	 * @since    1.0.0
	 * @param    string $date_meta_key    The date meta key (for combining with time).
	 * @param    string $time_meta_key    The time meta key to retrieve.
	 * @param    string $format           The time format.
	 * @return   string    The formatted time or empty string.
	 */
	private function get_event_time( $date_meta_key, $time_meta_key, $format ) {
		global $post;

		// Verify we're in a post.
		if ( ! $post ) {
			return '';
		}

		// Verify it's an 'ventocalendar_event' post type.
		if ( 'ventocalendar_event' !== get_post_type( $post ) ) {
			return '';
		}

		// Get the time value.
		$time = get_post_meta( $post->ID, $time_meta_key, true );

		// If no time, return empty.
		if ( empty( $time ) ) {
			return '';
		}

		// Get the date for proper timestamp creation.
		$date = get_post_meta( $post->ID, $date_meta_key, true );
		if ( empty( $date ) ) {
			return '';
		}

		// Combine date and time.
		$datetime_str = $date . ' ' . $time;
		$timestamp    = strtotime( $datetime_str );

		// If timestamp is invalid, return empty.
		if ( false === $timestamp ) {
			return '';
		}

		// Format and return time only.
		return date_i18n( $format, $timestamp );
	}

	/**
	 * Shortcode to display the events calendar.
	 *
	 * @since    1.0.0
	 * @param    array $atts    Shortcode attributes.
	 * @return   string   The calendar HTML.
	 */
	public function calendar_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'view_type'                   => 'calendar',
				'first_day_of_week'           => 'monday',
				'initial_month'               => '',
				'layout'                      => 'basic',
				'show_start_date'             => 'true',
				'show_end_date'               => 'true',
				'show_start_time'             => 'false',
				'show_end_time'               => 'false',
				'show_add_to_calendar_google' => 'false',
				'show_add_to_calendar_apple'  => 'false',
			),
			$atts,
			'ventocalendar-calendar'
		);

		// Validate view_type.
		$view_type = in_array( $atts['view_type'], array( 'calendar', 'list' ), true )
			? $atts['view_type']
			: 'calendar';

		// Validate first_day_of_week.
		$first_day_of_week = in_array( $atts['first_day_of_week'], array( 'monday', 'sunday' ), true )
			? $atts['first_day_of_week']
			: 'monday';

		// Validate layout.
		$layout = in_array( $atts['layout'], array( 'basic', 'compact', 'clean' ), true )
			? $atts['layout']
			: 'basic';

		// Convert string booleans to actual booleans.
		$show_start_date             = filter_var( $atts['show_start_date'], FILTER_VALIDATE_BOOLEAN );
		$show_end_date               = filter_var( $atts['show_end_date'], FILTER_VALIDATE_BOOLEAN );
		$show_start_time             = filter_var( $atts['show_start_time'], FILTER_VALIDATE_BOOLEAN );
		$show_end_time               = filter_var( $atts['show_end_time'], FILTER_VALIDATE_BOOLEAN );
		$show_add_to_calendar_google = filter_var( $atts['show_add_to_calendar_google'], FILTER_VALIDATE_BOOLEAN );
		$show_add_to_calendar_apple  = filter_var( $atts['show_add_to_calendar_apple'], FILTER_VALIDATE_BOOLEAN );

		// Use WordPress date and time formats.
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		// Validate and sanitize initial_month (format: YYYY-MM).
		$initial_month = '';
		if ( ! empty( $atts['initial_month'] ) ) {
			$sanitized_month = sanitize_text_field( $atts['initial_month'] );
			// Validate format YYYY-MM.
			if ( preg_match( '/^\d{4}-\d{2}$/', $sanitized_month ) ) {
				$initial_month = $sanitized_month;
			}
		}

		// Enqueue Vue.js from local file.
		$vue_file = VENTOCALENDAR_CORE_PATH . 'public/js/vendor/vue.global.prod.js';
		wp_enqueue_script(
			'vue',
			VENTOCALENDAR_CORE_URL . 'public/js/vendor/vue.global.prod.js',
			array(),
			filemtime( $vue_file ),
			true
		);

		// Enqueue calendar component.
		$js_file = VENTOCALENDAR_CORE_PATH . 'public/js/ventocalendar-calendar.js';
		wp_enqueue_script(
			'ventocalendar-calendar',
			VENTOCALENDAR_CORE_URL . 'public/js/ventocalendar-calendar.js',
			array( 'vue', 'wp-i18n' ),
			filemtime( $js_file ),
			true
		);

		// Enqueue calendar initializer.
		$js_file = VENTOCALENDAR_CORE_PATH . 'public/js/ventocalendar-calendar-init.js';
		wp_enqueue_script(
			'ventocalendar-calendar-init',
			VENTOCALENDAR_CORE_URL . 'public/js/ventocalendar-calendar-init.js',
			array( 'ventocalendar-calendar' ),
			filemtime( $js_file ),
			true
		);

		// Enqueue calendar styles.
		$css_file = VENTOCALENDAR_CORE_PATH . 'public/css/ventocalendar-calendar.css';
		wp_enqueue_style(
			'ventocalendar-calendar',
			VENTOCALENDAR_CORE_URL . 'public/css/ventocalendar-calendar.css',
			array(),
			filemtime( $css_file )
		);

		// Localize script with REST API URL.
		wp_localize_script(
			'ventocalendar-calendar',
			'ventoCalendar',
			array(
				'restUrl' => rest_url(),
			)
		);

		// Set script translations.
		wp_set_script_translations( 'ventocalendar-calendar', 'ventocalendar', VENTOCALENDAR_CORE_PATH . 'languages' );

		// Generate unique ID for this calendar instance.
		static $calendar_instance = 0;
		++$calendar_instance;
		$calendar_id = 'ventocalendar-calendar-' . $calendar_instance;

		// Build the HTML.
		// Use JSON encoding to preserve backslashes in format strings.
		ob_start();
		?>
		<div
			id="<?php echo esc_attr( $calendar_id ); ?>"
			class="ventocalendar-calendar-wrapper"
			data-view-type="<?php echo esc_attr( $view_type ); ?>"
			data-first-day-of-week="<?php echo esc_attr( $first_day_of_week ); ?>"
			<?php if ( ! empty( $initial_month ) ) : ?>
			data-initial-month="<?php echo esc_attr( $initial_month ); ?>"
			<?php endif; ?>
			data-layout="<?php echo esc_attr( $layout ); ?>"
			data-show-start-date="<?php echo $show_start_date ? 'true' : 'false'; ?>"
			data-show-end-date="<?php echo $show_end_date ? 'true' : 'false'; ?>"
			data-show-start-time="<?php echo $show_start_time ? 'true' : 'false'; ?>"
			data-show-end-time="<?php echo $show_end_time ? 'true' : 'false'; ?>"
			data-show-add-to-calendar-google="<?php echo $show_add_to_calendar_google ? 'true' : 'false'; ?>"
			data-show-add-to-calendar-apple="<?php echo $show_add_to_calendar_apple ? 'true' : 'false'; ?>"
			data-date-format="<?php echo esc_attr( $date_format ); ?>"
			data-time-format="<?php echo esc_attr( $time_format ); ?>"
		></div>
		<?php
		return ob_get_clean();
	}
}
