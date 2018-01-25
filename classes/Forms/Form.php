<?php

namespace Wiredot\WPEP\Forms;

class Form {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
	}

	public function add_meta_box() {
		add_meta_box(
			'asdasd',
			'wefsdvsv',
			array( $this, 'add_meta_box_content' ),
			'wpep-form'
		);
	}

	public function add_meta_box_content() {
		echo 'asdasd';
	}
}
