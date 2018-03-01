<?php

namespace Wiredot\WPEP\Forms;

use Wiredot\WPEP\User_Fields;

class Form {

	public function __construct() {
		add_action( 'preamp_wpep_config', array( $this, 'add_field_types' ) );
	}

	public function add_field_types( $config ) {
		$new_fields = array();

		$wpep_settings_fields = User_Fields::get_user_fields();

		foreach ( $wpep_settings_fields as $key => $mb ) {

			foreach ( $mb['fields'] as $fkey => $field ) {
				$new_fields[ 'standard_field_' . $field['id'] ] = $mb['name'] . ' - ' . $field['label'];
			}
		}

		$config['meta_box']['post']['wpep-form-fields']['fields']['fields']['fields']['fields']['fields']['type']['options'] = array_merge( $new_fields, $config['meta_box']['post']['wpep-form-fields']['fields']['fields']['fields']['fields']['fields']['type']['options'] );

		return $config;
	}
}
