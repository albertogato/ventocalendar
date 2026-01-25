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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
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
			'ventocalendar',
			'',
			'dashicons-calendar-alt',
			6
		);

		// Add Events submenu (list all events).
		add_submenu_page(
			'ventocalendar',
			__( 'Events', 'ventocalendar' ),
			__( 'Events', 'ventocalendar' ),
			'edit_posts',
			'edit.php?post_type=ventocalendar_event'
		);

		// Add New Event submenu.
		add_submenu_page(
			'ventocalendar',
			__( 'Add new event', 'ventocalendar' ),
			__( 'Add new event', 'ventocalendar' ),
			'edit_posts',
			'post-new.php?post_type=ventocalendar_event'
		);

		// Add Settings submenu.
		add_submenu_page(
			'ventocalendar',
			__( 'Settings', 'ventocalendar' ),
			__( 'Settings', 'ventocalendar' ),
			'manage_options',
			'ventocalendar-settings',
			array( $this, 'display_plugin_settings_page' )
		);

		// Add Usage / Help submenu.
		add_submenu_page(
			'ventocalendar',
			__( 'Usage / Help', 'ventocalendar' ),
			__( 'Usage / Help', 'ventocalendar' ),
			'manage_options',
			'ventocalendar-help',
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
		remove_submenu_page( 'ventocalendar', 'ventocalendar' );
	}

	/**
	 * Register the settings for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		// Register setting.
		register_setting(
			$this->plugin_name,
			$this->plugin_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'validate_settings' ),
				'default'           => array(
					'show_event_info_automatically' => 0,
					'show_start_time'               => 0,
					'show_end_time'                 => 0,
				),
			),
		);

		// Add settings section.
		add_settings_section(
			$this->plugin_name . '_general',
			__( 'General settings', 'ventocalendar' ),
			array( $this, 'render_settings_section' ),
			'ventocalendar-settings'
		);

		// Add settings field.
		add_settings_field(
			'show_event_info_automatically',
			__( 'Show event info automatically', 'ventocalendar' ),
			array( $this, 'render_show_event_info_field' ),
			'ventocalendar-settings',
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_event_info_automatically' )
		);

		// Add show start time field.
		add_settings_field(
			'show_start_time',
			__( 'Show start time', 'ventocalendar' ),
			array( $this, 'render_show_start_time_field' ),
			'ventocalendar-settings',
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_start_time' )
		);

		// Add show end time field.
		add_settings_field(
			'show_end_time',
			__( 'Show end time', 'ventocalendar' ),
			array( $this, 'render_show_end_time_field' ),
			'ventocalendar-settings',
			$this->plugin_name . '_general',
			array( 'label_for' => 'show_end_time' )
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
		);

		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		// Normalize values.
		$valid = $defaults;

		foreach ( $defaults as $key => $default ) {
			if ( isset( $input[ $key ] ) && '1' === (string) $input[ $key ] ) {
				$valid[ $key ] = 1;
			}
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
			$js_file = VENTOCALENDAR_CORE_PATH . 'admin/js/ventocalendar-meta-box.js';
			wp_enqueue_script(
				$this->plugin_name . '-meta-box',
				VENTOCALENDAR_CORE_URL . 'admin/js/ventocalendar-meta-box.js',
				array( 'jquery', 'wp-color-picker', 'wp-data', 'wp-edit-post', 'wp-notices', 'wp-i18n' ),
				filemtime( $js_file ),
				false
			);

			// Set script translations.
			wp_set_script_translations(
				$this->plugin_name . '-meta-box',
				'ventocalendar',
				VENTOCALENDAR_CORE_PATH . 'languages'
			);
		}
	}
}
