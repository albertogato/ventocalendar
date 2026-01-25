<?php
/**
 * Calendar Block functionality.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calendar Block class.
 *
 * Handles the registration and rendering of the VentoCalendar Gutenberg block.
 * Manages block attributes, enqueues necessary scripts and styles, and generates
 * the frontend calendar display with Vue.js integration.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/blocks
 * @since      1.0.0
 */
class VentoCalendar_Block_Calendar {

	/**
	 * Register the block.
	 *
	 * @since    1.0.0
	 */
	public function register_block() {
		$script_path = 'admin/js/blocks/calendar.js';
		$script_file = VENTOCALENDAR_CORE_PATH . $script_path;

		// Register the block editor script.
		wp_register_script(
			'ventocalendar-block-calendar',
			VENTOCALENDAR_CORE_URL . $script_path,
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data' ),
			file_exists( $script_file ) ? filemtime( $script_file ) : '1.0.0',
			true
		);

		wp_set_script_translations(
			'ventocalendar-block-calendar',
			'ventocalendar',
			VENTOCALENDAR_CORE_PATH . 'languages'
		);

		// Register the block.
		register_block_type(
			'ventocalendar/calendar',
			array(
				'editor_script'   => 'ventocalendar-block-calendar',
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'viewType'                => array(
						'type'    => 'string',
						'default' => 'calendar',
					),
					'firstDayOfWeek'          => array(
						'type'    => 'string',
						'default' => 'monday',
					),
					'initialMonth'            => array(
						'type'    => 'string',
						'default' => '',
					),
					'layout'                  => array(
						'type'    => 'string',
						'default' => 'basic',
					),
					'showStartDate'           => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showEndDate'             => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showStartTime'           => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showEndTime'             => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showAddToCalendarGoogle' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showAddToCalendarApple'  => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);
	}

	/**
	 * Render the block on the frontend.
	 *
	 * @since    1.0.0
	 * @param    array $attributes    Block attributes.
	 * @return   string   The rendered block HTML.
	 */
	public function render_block( $attributes ) {
		// Get attributes with defaults.
		$view_type                   = isset( $attributes['viewType'] ) ? $attributes['viewType'] : 'calendar';
		$first_day_of_week           = isset( $attributes['firstDayOfWeek'] ) ? $attributes['firstDayOfWeek'] : 'monday';
		$layout                      = isset( $attributes['layout'] ) ? $attributes['layout'] : 'basic';
		$show_start_date             = isset( $attributes['showStartDate'] ) ? $attributes['showStartDate'] : true;
		$show_end_date               = isset( $attributes['showEndDate'] ) ? $attributes['showEndDate'] : true;
		$show_start_time             = isset( $attributes['showStartTime'] ) ? $attributes['showStartTime'] : false;
		$show_end_time               = isset( $attributes['showEndTime'] ) ? $attributes['showEndTime'] : false;
		$show_add_to_calendar_google = isset( $attributes['showAddToCalendarGoogle'] ) ? $attributes['showAddToCalendarGoogle'] : false;
		$show_add_to_calendar_apple  = isset( $attributes['showAddToCalendarApple'] ) ? $attributes['showAddToCalendarApple'] : false;
		$date_format                 = get_option( 'date_format' );
		$time_format                 = get_option( 'time_format' );

		// Validate view_type.
		$view_type = in_array( $view_type, array( 'calendar', 'list' ), true ) ? $view_type : 'calendar';

		// Validate first_day_of_week.
		$first_day_of_week = in_array( $first_day_of_week, array( 'monday', 'sunday' ), true ) ? $first_day_of_week : 'monday';

		// Validate and sanitize initial_month (format: YYYY-MM).
		$initial_month = '';
		if ( isset( $attributes['initialMonth'] ) && ! empty( $attributes['initialMonth'] ) ) {
			$sanitized_month = sanitize_text_field( $attributes['initialMonth'] );
			// Validate format YYYY-MM.
			if ( preg_match( '/^\d{4}-\d{2}$/', $sanitized_month ) ) {
				$initial_month = $sanitized_month;
			}
		}

		// Validate layout.
		$layout = in_array( $layout, array( 'basic', 'compact', 'clean' ), true ) ? $layout : 'basic';

		// Sanitize date and time formats preserving backslashes for escaped characters.
		// We use wp_kses_post which allows backslashes, then strip all HTML tags.

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
		$calendar_id = 'ventocalendar-calendar-block-' . $calendar_instance;

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
