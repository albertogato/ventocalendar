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

		if ( $this->should_enqueue_event_map_assets() ) {
			$maplibre_css_file = VENTOCALENDAR_CORE_PATH . 'public/css/maplibre-gl.css';
			wp_enqueue_style( 'ventocalendar-maplibre-gl', VENTOCALENDAR_CORE_URL . 'public/css/maplibre-gl.css', array(), filemtime( $maplibre_css_file ), 'all' );
		}

		// Append custom CSS after the plugin stylesheet.
		$this->output_custom_css();
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$js_file      = VENTOCALENDAR_CORE_PATH . 'public/js/ventocalendar-public.js';
		$dependencies = array( 'jquery' );

		if ( $this->should_enqueue_event_map_assets() ) {
			$maplibre_js_file = VENTOCALENDAR_CORE_PATH . 'public/js/maplibre-gl.js';
			wp_enqueue_script( 'ventocalendar-maplibre-gl', VENTOCALENDAR_CORE_URL . 'public/js/maplibre-gl.js', array(), filemtime( $maplibre_js_file ), false );
			$dependencies[] = 'ventocalendar-maplibre-gl';
		}

		wp_enqueue_script( $this->plugin_name, VENTOCALENDAR_CORE_URL . 'public/js/ventocalendar-public.js', $dependencies, filemtime( $js_file ), false );

		if ( $this->should_enqueue_event_map_assets() ) {
			wp_localize_script(
				$this->plugin_name,
				'ventocalendarPublicMapConfig',
				array(
					'styleUrl' => 'https://tiles.openfreemap.org/styles/liberty',
				)
			);
		}
	}

	/**
	 * Check if map assets should be enqueued for current request.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	private function should_enqueue_event_map_assets() {
		if ( ! is_singular( 'ventocalendar_event' ) ) {
			return false;
		}

		$post_id = get_queried_object_id();

		if ( ! $post_id ) {
			return false;
		}

		$latitude  = get_post_meta( $post_id, '_location_latitude', true );
		$longitude = get_post_meta( $post_id, '_location_longitude', true );
		$has_valid_coordinates = $this->is_valid_coordinate( $latitude, -90, 90 ) && $this->is_valid_coordinate( $longitude, -180, 180 );

		if ( ! $has_valid_coordinates ) {
			return false;
		}

		$show_map = get_post_meta( $post_id, '_show_map', true );

		if ( '1' === (string) $show_map ) {
			return true;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'ventocalendar-map' );
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
	 * Add event dates to the content of event posts.
	 *
	 * @since    1.0.0
	 * @param    string $content    The post content.
	 * @return   string    The modified post content.
	 */
	public function display_event_dates( $content ) {
		if ( ! is_singular( 'ventocalendar_event' ) ) {
			return $content;
		}

		global $post;

		$map_html      = $this->get_event_map_html( $post->ID );
		$location_html = $this->get_event_location_details_html( $post->ID );

		// Check if the option is enabled.
		$options = get_option( $this->plugin_name, array() );
		if ( ! $this->is_option_enabled( $options, 'show_event_info_automatically' ) ) {
			return $content;
		}

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
		$show_start_time = $this->is_option_enabled( $options, 'show_start_time' );
		$show_end_time   = $this->is_option_enabled( $options, 'show_end_time' );

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
		$dates_html .= $this->get_event_taxonomies_html( $post->ID, $options );
		if ( '' !== $location_html ) {
			$dates_html .= $location_html;
		}
		if ( '' !== $map_html ) {
			$dates_html .= $map_html;
		}

		$dates_html .= '</div>';

		// Add the dates to the beginning of the content.
		return $dates_html . $content;
	}

	/**
	 * Build the location text HTML for event single page.
	 *
	 * @since 1.0.0
	 * @param int $post_id Event post ID.
	 * @return string
	 */
	private function get_event_location_details_html( $post_id ) {
		$location = get_post_meta( $post_id, '_location', true );
		$address  = get_post_meta( $post_id, '_address', true );

		if ( '' === trim( (string) $location ) && '' === trim( (string) $address ) ) {
			return '';
		}

		$location_html = '<div class="ventocalendar-event-location-details">';

		if ( '' !== trim( (string) $location ) ) {
			$location_html .= '<p class="ventocalendar-event-location-line">' . esc_html( $location ) . '</p>';
		}

		if ( '' !== trim( (string) $address ) ) {
			$location_html .= '<p class="ventocalendar-event-location-line">' . esc_html( $address ) . '</p>';
		}

		$location_html .= '</div>';

		return $location_html;
	}

	/**
	 * Build the map HTML for event single page.
	 *
	 * @since 1.0.0
	 * @param int $post_id Event post ID.
	 * @return string
	 */
	private function get_event_map_html( $post_id ) {
		$show_map = get_post_meta( $post_id, '_show_map', true );

		if ( '1' !== (string) $show_map ) {
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
	 * @since 1.0.0
	 * @param mixed $value Coordinate value.
	 * @param float $min Minimum accepted value.
	 * @param float $max Maximum accepted value.
	 * @return bool
	 */
	private function is_valid_coordinate( $value, $min, $max ) {
		if ( '' === (string) $value || ! is_numeric( $value ) ) {
			return false;
		}

		$value = (float) $value;

		return $value >= $min && $value <= $max;
	}

	/**
	 * Check whether a settings option is enabled.
	 *
	 * @since 1.0.0
	 * @param array  $options Plugin options.
	 * @param string $key     Option key.
	 * @return bool
	 */
	private function is_option_enabled( $options, $key ) {
		if ( ! isset( $options[ $key ] ) ) {
			return false;
		}

		return filter_var( $options[ $key ], FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Output custom CSS.
	 *
	 * @since    1.0.0
	 */
	public function output_custom_css() {
		$options    = get_option( $this->plugin_name, array() );
		$custom_css = isset( $options['custom_css'] ) ? trim( $options['custom_css'] ) : '';

		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( $this->plugin_name, wp_strip_all_tags( $custom_css ) );
		}
	}
}
