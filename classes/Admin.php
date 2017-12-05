<?php

namespace Wiredot\WPEP;

class Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'remove_add_event_link' ) );
	}

	public function remove_add_event_link() {
		remove_submenu_page( 'edit.php?post_type=wpep', 'post-new.php?post_type=wpep' );
	}
}
