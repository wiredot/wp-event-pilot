<?php

namespace Wiredot\WPEP;

class Support {

	public function __construct() {
		// add_action( 'admin_menu', array( $this, 'add_support_menu' ) );
	}

	public function add_support_menu() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'Support', 'wpep' ),
			__( 'Support', 'wpep' ),
			'manage_options',
			'wpep-support',
			array( $this, 'support_page' )
		);
	}

	public function support_page() {

	}
}
