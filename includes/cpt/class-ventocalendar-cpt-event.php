<?php
/**
 * The Event Custom Post Type for VentoCalendar.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/cpt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Event Custom Post Type class.
 *
 * Handles the registration and management of the Event custom post type,
 * including meta fields, meta boxes, custom columns, and validation logic
 * for event dates, times, and colors.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/includes/cpt
 * @since      1.0.0
 */
class VentoCalendar_CPT_Event {

	/**
	 * Meta field keys for event data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $meta_keys = array(
		'start_date' => '_start_date',
		'end_date'   => '_end_date',
		'start_time' => '_start_time',
		'end_time'   => '_end_time',
		'color'      => '_color',
	);

	/**
	 * Default color for events.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $default_color = '#2271b1';

	/**
	 * Init hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_meta_fields' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_ventocalendar_event_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_ventocalendar_event_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
		add_filter( 'manage_edit-ventocalendar_event_sortable_columns', array( $this, 'make_columns_sortable' ) );
		add_action( 'pre_get_posts', array( $this, 'custom_columns_orderby' ) );
	}

	/**
	 * Register the custom post type 'Event'.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Events', 'ventocalendar' ),
			'singular_name'      => __( 'Event', 'ventocalendar' ),
			'menu_name'          => __( 'Events', 'ventocalendar' ),
			'add_new'            => __( 'Add new', 'ventocalendar' ),
			'add_new_item'       => __( 'Add new event', 'ventocalendar' ),
			'edit_item'          => __( 'Edit event', 'ventocalendar' ),
			'new_item'           => __( 'New event', 'ventocalendar' ),
			'view_item'          => __( 'View event', 'ventocalendar' ),
			'search_items'       => __( 'Search events', 'ventocalendar' ),
			'not_found'          => __( 'No events found', 'ventocalendar' ),
			'not_found_in_trash' => __( 'No events found in trash', 'ventocalendar' ),
		);

		$args = array(
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'author', 'comments', 'revisions' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'event',
				'with_front' => false,
			),
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,

		);

		register_post_type( 'ventocalendar_event', $args );
	}

	/**
	 * Register meta fields for REST API access.
	 *
	 * @since 1.0.0
	 */
	public function register_meta_fields() {
		// Register start_date meta field (required).
		register_post_meta(
			'ventocalendar_event',
			$this->meta_keys['start_date'],
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Register end_date meta field (optional).
		register_post_meta(
			'ventocalendar_event',
			$this->meta_keys['end_date'],
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'default'       => '',
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Register start_time meta field (optional).
		register_post_meta(
			'ventocalendar_event',
			$this->meta_keys['start_time'],
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'default'       => '',
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Register end_time meta field (optional).
		register_post_meta(
			'ventocalendar_event',
			$this->meta_keys['end_time'],
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'default'       => '',
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		// Register color meta field.
		register_post_meta(
			'ventocalendar_event',
			$this->meta_keys['color'],
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Add Meta Boxes for Event details.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'ventocalendar_event_details',
			__( 'Event details', 'ventocalendar' ),
			array( $this, 'render_meta_box' ),
			'ventocalendar_event',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta Box content.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box( $post ) {
		// Add nonce for security.
		wp_nonce_field( 'ventocalendar_meta_box', 'ventocalendar_meta_box_nonce' );

		// Get current values.
		$start_date = get_post_meta( $post->ID, $this->meta_keys['start_date'], true );
		$end_date   = get_post_meta( $post->ID, $this->meta_keys['end_date'], true );
		$start_time = get_post_meta( $post->ID, $this->meta_keys['start_time'], true );
		$end_time   = get_post_meta( $post->ID, $this->meta_keys['end_time'], true );
		$color      = get_post_meta( $post->ID, $this->meta_keys['color'], true );

		// If no color, use the default.
		if ( empty( $color ) ) {
			$color = $this->default_color;
		}

		// Format time values for display (remove seconds if present).
		$start_time_formatted = $start_time ? substr( $start_time, 0, 5 ) : '';
		$end_time_formatted   = $end_time ? substr( $end_time, 0, 5 ) : '';

		// Determine if "All day" checkbox should be checked.
		// Checked only if: end_date has a value.
		$is_all_day = ! empty( $end_date );

		?>
		<div class="ventocalendar-meta-box-wrapper">
			<table class="form-table">
				<tbody>
					<!-- Start Date (Required) -->
					<tr>
						<th scope="row">
							<label for="ventocalendar-start-date">
								<?php esc_html_e( 'Start date', 'ventocalendar' ); ?>
								<span class="required" aria-hidden="true">*</span>
							</label>
						</th>
						<td>
							<input
								type="date"
								id="ventocalendar-start-date"
								name="ventocalendar_start_date"
								value="<?php echo esc_attr( $start_date ); ?>"
								class="regular-text"
								required
								aria-required="true"
								aria-describedby="ventocalendar-start-date-description"
							/>
							<p class="description" id="ventocalendar-start-date-description">
								<?php esc_html_e( 'Select the start date for the event (required).', 'ventocalendar' ); ?>
							</p>
							<div class="ventocalendar-error-message" style="color:red; display:none;" role="alert"></div>
						</td>
					</tr>

					<!-- Start Time (Hidden when All Day) -->
					<tr class="ventocalendar-time-field" style="<?php echo $is_all_day ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="ventocalendar-start-time">
								<?php esc_html_e( 'Start time', 'ventocalendar' ); ?>
							</label>
						</th>
						<td>
							<input
								type="time"
								id="ventocalendar-start-time"
								name="ventocalendar_start_time"
								value="<?php echo esc_attr( $start_time_formatted ); ?>"
								class="regular-text"
								step="1800"
								aria-describedby="ventocalendar-start-time-description"
							/>
							<p class="description" id="ventocalendar-start-time-description">
								<?php esc_html_e( 'Select the start time (optional).', 'ventocalendar' ); ?>
							</p>
							<div class="ventocalendar-error-message" style="color:red; display:none;" role="alert"></div>
						</td>
					</tr>

					<!-- End Time (Hidden when All Day) -->
					<tr class="ventocalendar-time-field" style="<?php echo $is_all_day ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="ventocalendar-end-time">
								<?php esc_html_e( 'End time', 'ventocalendar' ); ?>
							</label>
						</th>
						<td>
							<input
								type="time"
								id="ventocalendar-end-time"
								name="ventocalendar_end_time"
								value="<?php echo esc_attr( $end_time_formatted ); ?>"
								class="regular-text"
								step="1800"
								aria-describedby="ventocalendar-end-time-description"
							/>
							<p class="description" id="ventocalendar-end-time-description">
								<?php esc_html_e( 'Select the end time (optional).', 'ventocalendar' ); ?>
							</p>
							<div class="ventocalendar-error-message" style="color:red; display:none;" role="alert"></div>
						</td>
					</tr>

					<!-- End Date (Shown only when All Day) -->
					<tr class="ventocalendar-end-date-field" style="<?php echo ! $is_all_day ? 'display:none;' : ''; ?>">
						<th scope="row">
							<label for="ventocalendar-end-date">
								<?php esc_html_e( 'End date', 'ventocalendar' ); ?>
							</label>
						</th>
						<td>
							<input
								type="date"
								id="ventocalendar-end-date"
								name="ventocalendar_end_date"
								value="<?php echo esc_attr( $end_date ); ?>"
								class="regular-text"
								aria-describedby="ventocalendar-end-date-description"
							/>
							<p class="description" id="ventocalendar-end-date-description">
								<?php esc_html_e( 'Select the end date for multi-day events (optional). Leave empty for single-day events.', 'ventocalendar' ); ?>
							</p>
							<div class="ventocalendar-error-message" style="color:red; display:none;" role="alert"></div>
						</td>
					</tr>

					<!-- All Day Checkbox -->
					<tr>
						<th scope="row"></th>
						<td>
							<label for="ventocalendar-all-day">
								<input
									type="checkbox"
									id="ventocalendar-all-day"
									name="ventocalendar_all_day"
									value="1"
									<?php checked( $is_all_day, true ); ?>
								/>
								<?php esc_html_e( 'All day event', 'ventocalendar' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Check this for all-day events.', 'ventocalendar' ); ?>
							</p>
						</td>
					</tr>

					<!-- Event Color -->
					<tr>
						<th scope="row">
							<label for="ventocalendar-color">
								<?php esc_html_e( 'Event color', 'ventocalendar' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="ventocalendar-color"
								name="ventocalendar_color"
								value="<?php echo esc_attr( $color ); ?>"
								class="ventocalendar-color-picker"
								data-default-color="<?php echo esc_attr( $this->default_color ); ?>"
							/>
							<p class="description">
								<?php esc_html_e( 'Select a color for the event.', 'ventocalendar' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Save Meta Boxes data.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 */
	public function save_meta_boxes( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['ventocalendar_meta_box_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['ventocalendar_meta_box_nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'ventocalendar_meta_box' ) ) {
			return;
		}

		// Verify permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Verify it's not an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify correct post type.
		if ( 'ventocalendar_event' !== get_post_type( $post_id ) ) {
			return;
		}

		// Determine if "All day" is checked.
		$is_all_day = isset( $_POST['ventocalendar_all_day'] ) && '1' === $_POST['ventocalendar_all_day'];

		// Initialize validation errors array.
		$validation_errors = array();

		// Save start_date (REQUIRED).
		if ( isset( $_POST['ventocalendar_start_date'] ) && ! empty( $_POST['ventocalendar_start_date'] ) ) {
			$start_date = sanitize_text_field( wp_unslash( $_POST['ventocalendar_start_date'] ) );

			// Validate date format (Y-m-d).
			if ( $this->validate_date_format( $start_date, 'Y-m-d' ) ) {
				update_post_meta( $post_id, $this->meta_keys['start_date'], $start_date );
			} else {
				$validation_errors[] = __( 'Invalid start date format.', 'ventocalendar' );
			}
		} else {
			$validation_errors[] = __( 'Start date is required.', 'ventocalendar' );
		}

		// Save times and dates based on "All day" checkbox.
		if ( $is_all_day ) {
			// All day event: clear times and save end_date (optional).
			delete_post_meta( $post_id, $this->meta_keys['start_time'] );
			delete_post_meta( $post_id, $this->meta_keys['end_time'] );

			// Save end_date (optional for all-day).
			if ( isset( $_POST['ventocalendar_end_date'] ) && ! empty( $_POST['ventocalendar_end_date'] ) ) {
				$end_date = sanitize_text_field( wp_unslash( $_POST['ventocalendar_end_date'] ) );

				// Validate date format.
				if ( $this->validate_date_format( $end_date, 'Y-m-d' ) ) {
					update_post_meta( $post_id, $this->meta_keys['end_date'], $end_date );
				} else {
					$validation_errors[] = __( 'Invalid end date format.', 'ventocalendar' );
				}
			} else {
				// Clear end_date if not provided.
				delete_post_meta( $post_id, $this->meta_keys['end_date'] );
			}
		} else {
			// Not all-day: clear end_date and save times.
			delete_post_meta( $post_id, $this->meta_keys['end_date'] );

			// Save start_time (optional).
			if ( isset( $_POST['ventocalendar_start_time'] ) && ! empty( $_POST['ventocalendar_start_time'] ) ) {
				$start_time = sanitize_text_field( wp_unslash( $_POST['ventocalendar_start_time'] ) );

				// Convert H:i to H:i:s format.
				$start_time_full = $this->convert_time_to_full_format( $start_time );

				// Validate time format.
				if ( $this->validate_time_format( $start_time_full ) ) {
					update_post_meta( $post_id, $this->meta_keys['start_time'], $start_time_full );
				} else {
					$validation_errors[] = __( 'Invalid start time format.', 'ventocalendar' );
				}
			} else {
				// Clear start_time if not provided.
				delete_post_meta( $post_id, $this->meta_keys['start_time'] );
			}

			// Save end_time (optional).
			if ( isset( $_POST['ventocalendar_end_time'] ) && ! empty( $_POST['ventocalendar_end_time'] ) ) {
				$end_time = sanitize_text_field( wp_unslash( $_POST['ventocalendar_end_time'] ) );

				// Convert H:i to H:i:s format.
				$end_time_full = $this->convert_time_to_full_format( $end_time );

				// Validate time format.
				if ( $this->validate_time_format( $end_time_full ) ) {
					update_post_meta( $post_id, $this->meta_keys['end_time'], $end_time_full );
				} else {
					$validation_errors[] = __( 'Invalid end time format.', 'ventocalendar' );
				}
			} else {
				// Clear end_time if not provided.
				delete_post_meta( $post_id, $this->meta_keys['end_time'] );
			}
		}

		// Save color (unchanged).
		if ( isset( $_POST['ventocalendar_color'] ) ) {
			$color = sanitize_hex_color( wp_unslash( $_POST['ventocalendar_color'] ) );
			if ( empty( $color ) ) {
				$color = $this->default_color;
			}
			update_post_meta( $post_id, $this->meta_keys['color'], $color );
		}

		// Perform cross-field validation.
		$cross_validation_errors = $this->validate_event_dates_times( $post_id, $is_all_day );
		$validation_errors       = array_merge( $validation_errors, $cross_validation_errors );

		// Display validation errors if any.
		if ( ! empty( $validation_errors ) ) {
			add_action(
				'admin_notices',
				function () use ( $validation_errors ) {
					foreach ( $validation_errors as $error ) {
						?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo esc_html( $error ); ?></p>
					</div>
						<?php
					}
				}
			);
		}
	}

	/**
	 * Validate date format.
	 *
	 * @since 1.0.0
	 * @param string $date   Date string to validate.
	 * @param string $format Expected format (default: Y-m-d).
	 * @return bool True if valid, false otherwise.
	 */
	private function validate_date_format( $date, $format = 'Y-m-d' ) {
		if ( empty( $date ) ) {
			return false;
		}

		$d = DateTime::createFromFormat( $format, $date );
		return $d && $d->format( $format ) === $date;
	}

	/**
	 * Validate time format (H:i:s).
	 *
	 * @since 1.0.0
	 * @param string $time Time string to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private function validate_time_format( $time ) {
		if ( empty( $time ) ) {
			return false;
		}

		// Check format H:i:s.
		return (bool) preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $time );
	}

	/**
	 * Convert time from H:i to H:i:s format.
	 *
	 * @since 1.0.0
	 * @param string $time Time in H:i format.
	 * @return string Time in H:i:s format.
	 */
	private function convert_time_to_full_format( $time ) {
		if ( empty( $time ) ) {
			return '';
		}

		// If already in H:i:s format, return as is.
		if ( preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $time ) ) {
			return $time;
		}

		// If in H:i format, add :00 seconds.
		if ( preg_match( '/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $time ) ) {
			return $time . ':00';
		}

		return '';
	}

	/**
	 * Validate event dates and times cross-field logic.
	 *
	 * @since 1.0.0
	 * @param int  $post_id    Post ID.
	 * @param bool $is_all_day Whether event is all-day.
	 * @return array Array of error messages (empty if valid).
	 */
	private function validate_event_dates_times( $post_id, $is_all_day ) {
		$errors = array();

		$start_date = get_post_meta( $post_id, $this->meta_keys['start_date'], true );
		$start_time = get_post_meta( $post_id, $this->meta_keys['start_time'], true );
		$end_time   = get_post_meta( $post_id, $this->meta_keys['end_time'], true );
		$end_date   = get_post_meta( $post_id, $this->meta_keys['end_date'], true );

		// Validate times (if both present) - only if same day (no end_date or end_date = start_date).
		if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
			// Only validate time order if event is on same day.
			$is_same_day = empty( $end_date ) || $end_date === $start_date;

			if ( $is_same_day ) {
				// Combine date and time for comparison.
				$start_datetime_str = $start_date . ' ' . $start_time;
				$end_datetime_str   = $start_date . ' ' . $end_time;

				$start_timestamp = strtotime( $start_datetime_str );
				$end_timestamp   = strtotime( $end_datetime_str );

				if ( $end_timestamp < $start_timestamp ) {
					$errors[] = __( 'End time cannot be before the start time.', 'ventocalendar' );
				}
			}
		}

		// Validate end_date (if present and all-day is checked).
		if ( $is_all_day && ! empty( $end_date ) && ! empty( $start_date ) ) {
			$start_timestamp = strtotime( $start_date );
			$end_timestamp   = strtotime( $end_date );

			if ( $end_timestamp < $start_timestamp ) {
				$errors[] = __( 'End date must be on or after the start date.', 'ventocalendar' );
			}
		}

		return $errors;
	}

	/**
	 * Add custom columns to the Event post type list.
	 *
	 * @since 1.0.0
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_custom_columns( $columns ) {
		// Create a new array with columns in the desired order.
		$new_columns = array();

		// Add checkbox and title.
		$new_columns['cb']    = $columns['cb'];
		$new_columns['title'] = $columns['title'];

		// Add our custom columns.
		$new_columns['start_date_time'] = __( 'Start date', 'ventocalendar' );
		$new_columns['end_date_time']   = __( 'End date', 'ventocalendar' );

		// Add the rest of existing columns.
		unset( $columns['cb'], $columns['title'] );
		$new_columns = array_merge( $new_columns, $columns );

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @since 1.0.0
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'start_date_time':
				$start_date = get_post_meta( $post_id, $this->meta_keys['start_date'], true );

				if ( ! empty( $start_date ) ) {
					// Display only date (no time).
					$date_timestamp = strtotime( $start_date );
					if ( $date_timestamp ) {
						echo esc_html( date_i18n( get_option( 'date_format' ), $date_timestamp ) );
					} else {
						echo '—';
					}
				} else {
					echo '—';
				}
				break;

			case 'end_date_time':
				$start_date = get_post_meta( $post_id, $this->meta_keys['start_date'], true );
				$end_date   = get_post_meta( $post_id, $this->meta_keys['end_date'], true );

				// Show end_date if exists and different from start_date, otherwise show start_date.
				if ( ! empty( $end_date ) && $end_date !== $start_date ) {
					// Multi-day event: show end_date.
					$date_timestamp = strtotime( $end_date );
					if ( $date_timestamp ) {
						echo esc_html( date_i18n( get_option( 'date_format' ), $date_timestamp ) );
					} else {
						echo '—';
					}
				} elseif ( ! empty( $start_date ) ) {
					// Single-day event: show start_date.
					$date_timestamp = strtotime( $start_date );
					if ( $date_timestamp ) {
						echo esc_html( date_i18n( get_option( 'date_format' ), $date_timestamp ) );
					} else {
						echo '—';
					}
				} else {
					echo '—';
				}
				break;
		}
	}

	/**
	 * Make custom columns sortable.
	 *
	 * @since 1.0.0
	 * @param array $columns Sortable columns.
	 * @return array Modified sortable columns.
	 */
	public function make_columns_sortable( $columns ) {
		$columns['start_date_time'] = 'start_date_time';
		$columns['end_date_time']   = 'end_date_time';

		return $columns;
	}

	/**
	 * Handle custom column sorting.
	 *
	 * @since 1.0.0
	 * @param WP_Query $query The WordPress query object.
	 */
	public function custom_columns_orderby( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'start_date_time' === $orderby ) {
			// Sort by start_date.
			$query->set( 'meta_key', $this->meta_keys['start_date'] );
			$query->set( 'orderby', 'meta_value' );
		}

		if ( 'end_date_time' === $orderby ) {
			// Sort by end_date if present, otherwise by end_time.
			// For simplicity, we'll sort by start_date as fallback.
			$query->set( 'meta_key', $this->meta_keys['start_date'] );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
