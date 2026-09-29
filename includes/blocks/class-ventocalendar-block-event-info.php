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
	 * Plugin name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $plugin_name;

	/**
	 * Initialize block class.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name Plugin name.
	 */
	public function __construct( $plugin_name = 'ventocalendar' ) {
		$this->plugin_name = $plugin_name;
	}

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
					'showLocation'  => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showAddress'   => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showMap'       => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
			)
		);
	}

	/**
	 * Build the location text HTML for event info block.
	 *
	 * @since    1.0.0
	 * @param    int  $post_id       Event post ID.
	 * @param    bool $show_location Whether location should be shown.
	 * @param    bool $show_address  Whether address should be shown.
	 * @return   string
	 */
	private function get_event_location_details_html( $post_id, $show_location, $show_address ) {
		if ( ! $show_location && ! $show_address ) {
			return '';
		}

		$location = get_post_meta( $post_id, '_location', true );
		$address  = get_post_meta( $post_id, '_address', true );

		$location = trim( (string) $location );
		$address  = trim( (string) $address );

		$location_html      = '<div class="ventocalendar-event-location-details">';
		$has_location_lines = false;

		if ( $show_location && '' !== $location ) {
			$location_html .= '<p class="ventocalendar-event-location-line">' . esc_html( $location ) . '</p>';
			$has_location_lines = true;
		}

		if ( $show_address && '' !== $address ) {
			$location_html .= '<p class="ventocalendar-event-location-line">' . esc_html( $address ) . '</p>';
			$has_location_lines = true;
		}

		$location_html .= '</div>';

		if ( ! $has_location_lines ) {
			return '';
		}

		return $location_html;
	}

	/**
	 * Build the map HTML for event info block.
	 *
	 * @since    1.0.0
	 * @param    int  $post_id  Event post ID.
	 * @param    bool $show_map Whether map should be shown.
	 * @return   string
	 */
	private function get_event_map_html( $post_id, $show_map ) {
		if ( ! $show_map ) {
			return '';
		}

		$event_show_map = get_post_meta( $post_id, '_show_map', true );

		if ( '1' !== (string) $event_show_map ) {
			return '';
		}

		$latitude  = get_post_meta( $post_id, '_location_latitude', true );
		$longitude = get_post_meta( $post_id, '_location_longitude', true );

		if ( ! $this->is_valid_coordinate( $latitude, -90, 90 ) || ! $this->is_valid_coordinate( $longitude, -180, 180 ) ) {
			return '';
		}

		$latitude  = round( (float) $latitude, 7 );
		$longitude = round( (float) $longitude, 7 );

		$map_html  = '<div class="ventocalendar-event-map-wrapper">';
		$map_html .= '<div class="ventocalendar-event-map-view" data-latitude="' . esc_attr( $latitude ) . '" data-longitude="' . esc_attr( $longitude ) . '" aria-label="' . esc_attr__( 'Event location map', 'ventocalendar' ) . '"></div>';
		$map_html .= '</div>';

		return $map_html;
	}

	/**
	 * Validate coordinate value.
	 *
	 * @since    1.0.0
	 * @param    mixed $value Coordinate value.
	 * @param    float $min   Minimum accepted value.
	 * @param    float $max   Maximum accepted value.
	 * @return   bool
	 */
	private function is_valid_coordinate( $value, $min, $max ) {
		if ( '' === (string) $value || ! is_numeric( $value ) ) {
			return false;
		}

		$value = (float) $value;

		return $value >= $min && $value <= $max;
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
	 * Build event taxonomies HTML for Pro event information.
	 *
	 * @since    1.0.0
	 * @param    int   $post_id Event post ID.
	 * @param    array $options Plugin options.
	 * @return   string
	 */
	private function get_event_taxonomies_html( $post_id, $options ) {
		if ( 'ventocalendar-pro' !== $this->plugin_name ) {
			return '';
		}

		$show_categories = isset( $options['show_categories'] ) && $options['show_categories'];
		$show_tags       = isset( $options['show_tags'] ) && $options['show_tags'];

		if ( ! $show_categories && ! $show_tags ) {
			return '';
		}

		$taxonomies_html = '';

		if ( $show_categories ) {
			$categories = get_the_terms( $post_id, 'ventocalendar_event_category' );
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$category_names   = wp_list_pluck( $categories, 'name' );
				$taxonomies_html .= '<div class="ventocalendar-event-taxonomy ventocalendar-event-categories">';
				$taxonomies_html .= '<span class="ventocalendar-event-taxonomy-values">' . esc_html( implode( ', ', $category_names ) ) . '</span>';
				$taxonomies_html .= '</div>';
			}
		}

		if ( $show_tags ) {
			$tags = get_the_terms( $post_id, 'ventocalendar_event_tag' );
			if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
				$tag_names        = wp_list_pluck( $tags, 'name' );
				$taxonomies_html .= '<div class="ventocalendar-event-taxonomy ventocalendar-event-tags">';
				$taxonomies_html .= '<span class="ventocalendar-event-taxonomy-values">' . esc_html( implode( ', ', $tag_names ) ) . '</span>';
				$taxonomies_html .= '</div>';
			}
		}

		if ( '' === $taxonomies_html ) {
			return '';
		}

		return '<div class="ventocalendar-event-taxonomies">' . $taxonomies_html . '</div>';
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
		$show_location   = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true;
		$show_address    = isset( $attributes['showAddress'] ) ? $attributes['showAddress'] : true;
		$show_map        = isset( $attributes['showMap'] ) ? $attributes['showMap'] : true;
		$options         = get_option( $this->plugin_name, array() );
		$location_html   = $this->get_event_location_details_html( $post->ID, $show_location, $show_address );
		$map_html        = $this->get_event_map_html( $post->ID, $show_map );

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
		$html .= $this->get_event_taxonomies_html( $post->ID, $options );
		if ( '' !== $location_html ) {
			$html .= $location_html;
		}
		if ( '' !== $map_html ) {
			$html .= $map_html;
		}
		$html .= '</div>';

		return $html;
	}
}
