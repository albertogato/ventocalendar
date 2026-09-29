<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    VentoCalendar
 * @subpackage VentoCalendar/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the admin functionality for VentoCalendar plugin.
 *
 * Registers admin menus, settings pages, and handles all backend
 * administrative tasks for the plugin.
 *
 * @package VentoCalendar
 * @since   1.0.0
 */
class VentoCalendar_Admin {

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
	 * Admin menu slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $menu_slug;

	/**
	 * Settings page slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $settings_page_slug;

	/**
	 * Help page slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $help_page_slug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->menu_slug   = $this->plugin_name;

		if ( 'ventocalendar-pro' === $this->plugin_name ) {
			$this->settings_page_slug = 'ventocalendar-pro-settings';
			$this->help_page_slug     = 'ventocalendar-pro-help';
		} else {
			$this->settings_page_slug = 'ventocalendar-settings';
			$this->help_page_slug     = 'ventocalendar-help';
		}
	}

	/**
	 * Register the administration menu for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		// Add top-level menu.
		add_menu_page(
			__( 'VentoCalendar', 'ventocalendar' ),
			__( 'VentoCalendar', 'ventocalendar' ),
			'edit_posts',
			$this->menu_slug,
			'',
			'dashicons-calendar-alt',
			6
		);

		// Add Events submenu (list all events).
		add_submenu_page(
			$this->menu_slug,
			__( 'Events', 'ventocalendar' ),
			__( 'Events', 'ventocalendar' ),
			'edit_posts',
			'edit.php?post_type=ventocalendar_event'
		);

		// Add New Event submenu.
		add_submenu_page(
			$this->menu_slug,
			__( 'Add new event', 'ventocalendar' ),
			__( 'Add new event', 'ventocalendar' ),
			'edit_posts',
			'post-new.php?post_type=ventocalendar_event'
		);

		// Add Settings submenu.
		add_submenu_page(
			$this->menu_slug,
			__( 'Settings', 'ventocalendar' ),
			__( 'Settings', 'ventocalendar' ),
			'manage_options',
			$this->settings_page_slug,
			array( $this, 'display_plugin_settings_page' )
		);

		// Add Usage / Help submenu.
		add_submenu_page(
			$this->menu_slug,
			__( 'Usage / Help', 'ventocalendar' ),
			__( 'Usage / Help', 'ventocalendar' ),
			'manage_options',
			$this->help_page_slug,
			array( $this, 'display_plugin_help_page' )
		);
	}

	/**
	 * Clean up admin menu by removing duplicate submenu item.
	 *
	 * @since    1.0.0
	 */
	public function cleanup_admin_menu() {
		// Remove the auto-generated first submenu with same slug as parent.
		remove_submenu_page( $this->menu_slug, $this->menu_slug );
	}

	/**
	 * Register the settings for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		$is_pro = 'ventocalendar-pro' === $this->plugin_name;

		$default_options = array(
			'show_event_info_automatically' => 0,
			'show_start_time'               => 0,
			'show_end_time'                 => 0,
		);

		if ( $is_pro ) {
			$default_options['show_categories'] = 0;
			$default_options['show_tags']       = 0;
		}

		// Register setting.
		register_setting(
			$this->plugin_name,
			$this->plugin_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'validate_settings' ),
				'default'           => $default_options,
			),
		);

		// Add settings section.
		add_settings_section(
			$this->plugin_name . '_general',
			__( 'General settings', 'ventocalendar' ),
			array( $this, 'render_settings_section' ),
			$this->settings_page_slug
		);

		// Add settings field.
		add_settings_field(
			'show_event_info_automatically',
			__( 'Show event info automatically', 'ventocalendar' ),
			array( $this, 'render_show_event_info_field' ),
			$this->settings_page_slug,
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_event_info_automatically' )
		);

		// Add show start time field.
		add_settings_field(
			'show_start_time',
			__( 'Show start time', 'ventocalendar' ),
			array( $this, 'render_show_start_time_field' ),
			$this->settings_page_slug,
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_start_time' )
		);

		// Add show end time field.
		add_settings_field(
			'show_end_time',
			__( 'Show end time', 'ventocalendar' ),
			array( $this, 'render_show_end_time_field' ),
			$this->settings_page_slug,
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_end_time' )
		);

		if ( $is_pro ) {
			add_settings_field(
				'show_categories',
				__( 'Show categories', 'ventocalendar' ),
				array( $this, 'render_show_categories_field' ),
				$this->settings_page_slug,
				$this->plugin_name . '_general',
				array( 'label_for' => 'show_categories' )
			);

			add_settings_field(
				'show_tags',
				__( 'Show tags', 'ventocalendar' ),
				array( $this, 'render_show_tags_field' ),
				$this->settings_page_slug,
				$this->plugin_name . '_general',
				array( 'label_for' => 'show_tags' )
			);
		}

		// Add settings section for Custom CSS.
		add_settings_section(
			$this->plugin_name . '_custom_css',
			__( 'Custom CSS', 'ventocalendar' ),
			array( $this, 'render_custom_css_section' ),
			$this->settings_page_slug
		);

		// Add custom CSS field.
		add_settings_field(
			'custom_css',
			__( 'Custom CSS', 'ventocalendar' ),
			array( $this, 'render_custom_css_field' ),
			$this->settings_page_slug,
			$this->plugin_name . '_custom_css',
			array( 'label_for' => 'custom_css' )
		);
	}

	/**
	 * Render the settings section.
	 *
	 * @since    1.0.0
	 */
	public function render_settings_section() {
		echo '<p>' . esc_html__( 'Configure the VentoCalendar plugin settings.', 'ventocalendar' ) . '</p>';
	}

	/**
	 * Render the checkbox field for show event info automatically.
	 *
	 * @since    1.0.0
	 */
	public function render_show_event_info_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['show_event_info_automatically'] ) ? $options['show_event_info_automatically'] : 0;
		?>
		<input type="checkbox"
				id="show_event_info_automatically"
				name="<?php echo esc_attr( $this->plugin_name ); ?>[show_event_info_automatically]"
				value="1"
				<?php checked( 1, $value ); ?> />
		<label for="show_event_info_automatically">
			<?php esc_html_e( 'Automatically display event information on single event pages', 'ventocalendar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the show start time checkbox field.
	 *
	 * @since    1.0.0
	 */
	public function render_show_start_time_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['show_start_time'] ) ? $options['show_start_time'] : 0;
		?>
		<input type="checkbox"
				id="show_start_time"
				name="<?php echo esc_attr( $this->plugin_name ); ?>[show_start_time]"
				value="1"
				<?php checked( 1, $value ); ?> />
		<label for="show_start_time">
			<?php esc_html_e( 'Include the start time in the event information', 'ventocalendar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the show end time checkbox field.
	 *
	 * @since    1.0.0
	 */
	public function render_show_end_time_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['show_end_time'] ) ? $options['show_end_time'] : 0;
		?>
		<input type="checkbox"
				id="show_end_time"
				name="<?php echo esc_attr( $this->plugin_name ); ?>[show_end_time]"
				value="1"
				<?php checked( 1, $value ); ?> />
		<label for="show_end_time">
			<?php esc_html_e( 'Include the end time in the event information', 'ventocalendar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the show categories checkbox field (Pro only).
	 *
	 * @since    1.0.0
	 */
	public function render_show_categories_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['show_categories'] ) ? $options['show_categories'] : 0;
		?>
		<input type="checkbox"
				id="show_categories"
				name="<?php echo esc_attr( $this->plugin_name ); ?>[show_categories]"
				value="1"
				<?php checked( 1, $value ); ?> />
		<label for="show_categories">
			<?php esc_html_e( 'Include categories in the event information', 'ventocalendar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the show tags checkbox field (Pro only).
	 *
	 * @since    1.0.0
	 */
	public function render_show_tags_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['show_tags'] ) ? $options['show_tags'] : 0;
		?>
		<input type="checkbox"
				id="show_tags"
				name="<?php echo esc_attr( $this->plugin_name ); ?>[show_tags]"
				value="1"
				<?php checked( 1, $value ); ?> />
		<label for="show_tags">
			<?php esc_html_e( 'Include tags in the event information', 'ventocalendar' ); ?>
		</label>
		<?php
	}

	/**
	 * Render the custom CSS section description.
	 *
	 * @since    1.0.0
	 */
	public function render_custom_css_section() {
		echo '<p>' . esc_html__( 'Add custom CSS to style the calendar and event information.', 'ventocalendar' ) . '</p>';
	}

	/**
	 * Render the custom CSS textarea field.
	 *
	 * @since    1.0.0
	 */
	public function render_custom_css_field() {
		$options = get_option( $this->plugin_name, array() );
		$value   = isset( $options['custom_css'] ) ? $options['custom_css'] : '';
		?>
		<textarea
			id="custom_css"
			name="<?php echo esc_attr( $this->plugin_name ); ?>[custom_css]"
			rows="10"
			cols="50"
			class="large-text code"
			placeholder="<?php esc_attr_e( '/* Add your custom CSS here */', 'ventocalendar' ); ?>"
		><?php echo esc_textarea( $value ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'A list of available CSS variables can be found in VentoCalendar > Usage / Help > Custom CSS.', 'ventocalendar' ); ?>
		</p>
		<?php
	}

	/**
	 * Validate settings before saving.
	 *
	 * @since    1.0.0
	 * @param    array $input    The array of settings to validate.
	 * @return   array    The validated settings.
	 */
	public function validate_settings( $input ) {
		$defaults = array(
			'show_event_info_automatically' => 0,
			'show_start_time'               => 0,
			'show_end_time'                 => 0,
			'custom_css'                    => '',
		);

		if ( 'ventocalendar-pro' === $this->plugin_name ) {
			$defaults['show_categories'] = 0;
			$defaults['show_tags']       = 0;
		}

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		// Normalize values.
		$valid = $defaults;

		$checkbox_fields = array( 'show_event_info_automatically', 'show_start_time', 'show_end_time' );

		if ( 'ventocalendar-pro' === $this->plugin_name ) {
			$checkbox_fields[] = 'show_categories';
			$checkbox_fields[] = 'show_tags';
		}

		foreach ( $checkbox_fields as $key ) {
			if ( isset( $input[ $key ] ) && '1' === (string) $input[ $key ] ) {
				$valid[ $key ] = 1;
			}
		}

		// Sanitize custom CSS (strip PHP tags, allow CSS content).
		if ( isset( $input['custom_css'] ) ) {
			$valid['custom_css'] = wp_strip_all_tags( $input['custom_css'] );
		}

		return $valid;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_settings_page() {
		include_once 'partials/ventocalendar-admin-settings-display.php';
	}

	/**
	 * Render the help page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_help_page() {
		include_once 'partials/ventocalendar-admin-help-display.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$css_file = VENTOCALENDAR_CORE_PATH . 'admin/css/ventocalendar-admin.css';
		wp_enqueue_style( $this->plugin_name, VENTOCALENDAR_CORE_URL . 'admin/css/ventocalendar-admin.css', array(), filemtime( $css_file ), 'all' );

		wp_enqueue_style( 'wp-color-picker' );

		$screen = get_current_screen();
		if ( $screen && 'ventocalendar_event' === $screen->post_type ) {
			$maplibre_css_file = VENTOCALENDAR_CORE_PATH . 'public/css/maplibre-gl.css';
			wp_enqueue_style( 'ventocalendar-maplibre-gl', VENTOCALENDAR_CORE_URL . 'public/css/maplibre-gl.css', array(), filemtime( $maplibre_css_file ), 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$js_file = VENTOCALENDAR_CORE_PATH . 'admin/js/ventocalendar-admin.js';
		wp_enqueue_script( $this->plugin_name, VENTOCALENDAR_CORE_URL . 'admin/js/ventocalendar-admin.js', array( 'jquery', 'wp-color-picker', 'wp-data', 'wp-edit-post', 'wp-notices', 'wp-plugins', 'wp-compose' ), filemtime( $js_file ), false );

		// Enqueue meta box script only on event edit screen.
		$screen = get_current_screen();
		if ( $screen && 'ventocalendar_event' === $screen->post_type ) {
			$maplibre_js_file = VENTOCALENDAR_CORE_PATH . 'public/js/maplibre-gl.js';
			wp_enqueue_script(
				'ventocalendar-maplibre-gl',
				VENTOCALENDAR_CORE_URL . 'public/js/maplibre-gl.js',
				array(),
				filemtime( $maplibre_js_file ),
				false
			);

			$js_file = VENTOCALENDAR_CORE_PATH . 'admin/js/ventocalendar-meta-box.js';
			wp_enqueue_script(
				$this->plugin_name . '-meta-box',
				VENTOCALENDAR_CORE_URL . 'admin/js/ventocalendar-meta-box.js',
				array( 'jquery', 'wp-color-picker', 'wp-data', 'wp-edit-post', 'wp-notices', 'wp-i18n', 'ventocalendar-maplibre-gl' ),
				filemtime( $js_file ),
				false
			);

			wp_localize_script(
				$this->plugin_name . '-meta-box',
				'ventocalendarMapConfig',
				array(
					'styleUrl'     => 'https://tiles.openfreemap.org/styles/liberty',
					'lastViewKey'  => 'ventocalendar_last_map_view',
					'defaultLat'   => 40.4168,
					'defaultLng'   => -3.7038,
					'defaultZoom'  => 4,
					'initialZoom'  => 12,
					'coordsPrefix' => __( 'Coordinates:', 'ventocalendar' ),
					'geoError'     => __( 'Unable to access your location. You can still set it manually on the map.', 'ventocalendar' ),
				)
			);

			// Set script translations.
			wp_set_script_translations(
				$this->plugin_name . '-meta-box',
				'ventocalendar',
				VENTOCALENDAR_CORE_PATH . 'languages'
			);
		}
	}

	/**
	 * Get settings page slug.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_settings_page_slug() {
		return $this->settings_page_slug;
	}

	/**
	 * Get help page slug.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	public function get_help_page_slug() {
		return $this->help_page_slug;
	}
}
