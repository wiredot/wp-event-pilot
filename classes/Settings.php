<?php

namespace Wiredot\WPEP;

class Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_menu' ) );
	}

	public function add_settings_menu() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'Settings', 'wpep' ),
			__( 'Settings', 'wpep' ),
			'manage_options',
			'wpep-settings',
			array( $this, 'settings_page' )
		);
	}

	public function settings_page() {

	}
}
