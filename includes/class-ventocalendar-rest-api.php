<?php
/**
 * REST API functionality of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API class.
 *
 * Handles all REST API endpoints for the VentoCalendar plugin.
 * Provides public endpoints for fetching event data to be consumed
 * by the frontend calendar component.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes
 * @since      1.0.0
 */
class VentoCalendar_REST_API {

	/**
	 * Register REST API routes.
	 *
	 * @since    1.0.0
	 */
	public function register_routes() {
		// -------------------------------------------------------------------
		// Public endpoint: list of events
		// -------------------------------------------------------------------
		// This endpoint is intentionally public; anyone can fetch events.
		// Using '__return_true' as permission_callback is correct for public data.
		register_rest_route(
			'ventocalendar/v1',
			'/events',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_events' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'start' => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						},
					),
					'end'   => array(
						'required'          => false,
						'validate_callback' => function ( $param ) {
							return is_string( $param );
						},
					),
				),
			)
		);
	}

	/**
	 * Get events for the calendar.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request    The request object.
	 * @return   WP_REST_Response   The response object.
	 */
	public function get_events( $request ) {
		$start_date = $request->get_param( 'start' );
		$end_date   = $request->get_param( 'end' );

		$args = array(
			'post_type'      => 'ventocalendar_event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'meta_key'       => '_start_date',
			'order'          => 'ASC',
		);

		// Add date range filter if provided.
		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				// Events with start_date in range (includes events without end_date).
				array(
					'key'     => '_start_date',
					'value'   => array( $start_date, $end_date ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				),
				// Events with end_date in range.
				array(
					'key'     => '_end_date',
					'value'   => array( $start_date, $end_date ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				),
				// Events that span the entire range (start before, end after).
				array(
					'relation' => 'AND',
					array(
						'key'     => '_start_date',
						'value'   => $start_date,
						'compare' => '<=',
						'type'    => 'DATE',
					),
					array(
						'key'     => '_end_date',
						'value'   => $end_date,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			);
		}

		$query  = new WP_Query( $args );
		$events = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				$start_date = get_post_meta( $post_id, '_start_date', true );
				$end_date   = get_post_meta( $post_id, '_end_date', true );
				$start_time = get_post_meta( $post_id, '_start_time', true );
				$end_time   = get_post_meta( $post_id, '_end_time', true );
				$color      = get_post_meta( $post_id, '_color', true );

				// Keep end_date as empty/null if not set (for point vs bar rendering).
				// Only set to null explicitly if it's an empty string.
				if ( empty( $end_date ) ) {
					$end_date = null;
				}

				// Default color if not set.
				if ( empty( $color ) ) {
					$color = '#2271b1';
				}

				$events[] = array(
					'id'         => $post_id,
					'title'      => get_the_title(),
					'start_date' => $start_date,
					'end_date'   => $end_date,
					'start_time' => $start_time ? $start_time : null,
					'end_time'   => $end_time ? $end_time : null,
					'color'      => $color,
					'permalink'  => get_permalink(),
				);
			}
			wp_reset_postdata();
		}

		return new WP_REST_Response( $events, 200 );
	}
}
