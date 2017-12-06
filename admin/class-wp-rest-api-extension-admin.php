<?php

class WP_REST_API_Extension_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register public styleheets
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-rest-api-extension-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register public scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-rest-api-extension-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Register the plugin's settings page to WordPress Dashboard
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page( 'WP REST API Extension Settings', 'WP REST API Extension', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
	}

	/**
	 * Add plugin's settings action link
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );
	}

	/**
	 * Render the plugin's settings page
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once( 'partials/wp-rest-api-extension-admin-display.php' );
	}

	/**
	 * Register the plugin's settings
	 *
	 * @since    1.0.0
	 */
	public function settings_update() {
		register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}

	/**
	 * Validate plugin's setting page form inputs
	 *
	 * @since    1.0.0
	 */
	public function validate($input) {
    $valid = array();

		$menus = get_registered_nav_menus();

		foreach ( $menus as $location => $description ) {
			$registered_nav_menu = 'registered-nav-menu-' . $location;

			$valid[$registered_nav_menu] = ( isset($input[$registered_nav_menu]) && !empty($input[$registered_nav_menu]) ) ? 1 : 0;
		}

		$args = array(
			'public' => true,
		);
		$output     = 'objects';
		$post_types = get_post_types( $args, $output );

		foreach ( $post_types as $post_type ) {
			if (
				($post_type->name != 'page') &&
				($post_type->name != 'attachment')
			) {
				$next_prev_post = 'next-prev-post-' . $post_type->name;

				$valid[$next_prev_post] = ( isset($input[$next_prev_post]) && !empty($input[$next_prev_post]) ) ? 1 : 0;
			} else {
				continue;
			}
		}

    return $valid;
	}
}
