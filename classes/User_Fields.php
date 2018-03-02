<?php

namespace Wiredot\WPEP;

class User_Fields {

	public $user_fields = array();

	public function __construct() {
		$this->user_fields = $this->load_user_fields();

		add_action( 'preamp_wpep_config', array( $this, 'add_user_fields' ) );
	}

	public function load_user_fields() {
		$meta_boxes = array();

		$wpep_settings_fields = self::get_user_fields();

		foreach ( $wpep_settings_fields as $key => $mb ) {
			$meta_box = array(
				'active' => true,
				'name' => $mb['name'],
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(),
			);

			foreach ( $mb['fields'] as $fkey => $field ) {

				if ( isset( $field['options'] ) ) {
					$field['options'] = $this->fix_options( $field['options'] );
				}
				$meta_box['fields'][ $field['id'] ] = $field;
			}

			$meta_boxes[ $key ] = $meta_box;
		}

		return $meta_boxes;
	}

	public function add_user_fields( $config ) {
		if ( is_array( $this->user_fields ) ) {
			foreach ( $this->user_fields as $group => $fields ) {
				$config['meta_box']['user'][ $group ] = $fields;
				$fields['post_type'] = 'wpep-registration';
				$config['meta_box']['post'][ $group ] = $fields;
			}
		}

		return $config;
	}

	public function fix_options( $options ) {
		$fixed_options = array();
		foreach ( $options as $option ) {
			$fixed_options[ $option['id'] ] = $option['label'];
		}

		return $fixed_options;
	}

	public static function get_user_fields() {
		return get_option( 'wpep_settings_fields' );
	}

	public static function get_user_fields_list() {
		$fields = self::get_user_fields();

		$field_list = array();

		foreach ( $fields as $group ) {
			$field_list = array_merge( $field_list, $group['fields'] );
		}

		return $field_list;
	}
}
