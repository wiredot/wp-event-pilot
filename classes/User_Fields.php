<?php

namespace Wiredot\WPEP;

class User_Fields {

	public $user_fields = array();

	public function __construct() {
		$this->user_fields = $this->load_user_fields();

		// update_option( 'wpep-participant-user-fields', $this->user_fields );

		add_action( 'preamp_wpep_config', array( $this, 'add_user_fields' ) );
	}

	public function load_user_fields() {
		return get_option( 'wpep-participant-user-fields' );
	}

	public function add_user_fields( $config ) {
		if ( is_array( $this->user_fields ) ) {
			foreach ( $this->user_fields as $group => $fields ) {
				$config['meta_box']['post'][ $group ] = $fields;
			}
		}

		return $config;
	}
}
