<?php

namespace Wiredot\WPEP;

class Groups {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'groups_admin_menu' ) );
	}

	public function groups_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'Functions', 'wpep' ),
			__( 'Functions', 'wpep' ),
			'manage_options',
			'edit-tags.php?taxonomy=wpep-function&post_type=wpep-registration'
		);
	}

}
