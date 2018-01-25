<?php

namespace Wiredot\WPEP\Forms;

class Form_Factory {

	public function __construct() {
		if ( ! isset( $_GET['action'] ) || ! 'edit' == $_GET['action'] ) {
			return;
		}

		$post_type = get_post_type( $_GET['post'] );
		if ( 'wpep-form' == $post_type ) {
			$Form = new Form;
		}
	}
}
