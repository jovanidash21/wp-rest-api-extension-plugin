<?php

class WP_REST_API_Extension_i18n {

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wp-rest-api-extension',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
