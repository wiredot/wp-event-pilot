<?php

namespace Wiredot\WPEP;

class Addons {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_addons_menu' ) );
	}

	public function add_addons_menu() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'Addons', 'wpep' ),
			__( 'Addons', 'wpep' ),
			'manage_options',
			'wpep-addons',
			array( $this, 'addons_page' )
		);
	}

	public function addons_page() {

	}
}
