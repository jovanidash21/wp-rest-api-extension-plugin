<?php
/**
 * Plugin Name: WP REST API Extension
 * Plugin URI: https://jovaniwarguez.wordpress.com
 * Description: A plugin that extends WordPress REST API with new routes
 * Version: 1.0.0
 * Author: Jovani Warguez
 * Author URI: https://jovaniwarguez.wordpress.com
 * Text Domain: wp-rest-api-extension
 */

if ( ! defined( 'WPINC' ) ) {
  die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );

function activate_wp_rest_api_extension() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-rest-api-extension-activator.php';
  WP_REST_API_Extension_Activator::activate();
}

function deactivate_wp_rest_api_extension() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-rest-api-extension-deactivator.php';
  WP_REST_API_Extension_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_rest_api_extension' );
register_deactivation_hook( __FILE__, 'deactivate_wp_rest_api_extension' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wp-rest-api-extension.php';

function run_wp_rest_api_extension() {

  $plugin = new WP_REST_API_Extension();
  $plugin->run();

}
run_wp_rest_api_extension();
