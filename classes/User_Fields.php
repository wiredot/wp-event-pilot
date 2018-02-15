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
				'post_type' => array( 'wpep-participant' ),
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(),
			);

			foreach ( $mb['fields'] as $fkey => $field ) {
				$meta_box['fields'][ $field['id'] ] = $field;
			}

			$meta_boxes[ $key ] = $meta_box;
		}

		return $meta_boxes;
	}

	public function add_user_fields( $config ) {
		if ( is_array( $this->user_fields ) ) {
			foreach ( $this->user_fields as $group => $fields ) {
				$config['meta_box']['post'][ $group ] = $fields;
			}
		}

		return $config;
	}

	public static function get_main_fields() {
		$wpep_settings_fields = self::get_user_fields();

		$main_fields = array();

		foreach ( $wpep_settings_fields as $key => $mb ) {

			foreach ( $mb['fields'] as $fkey => $field ) {
				if ( isset( $field['main'] ) ) {
					$main_fields[] = $field['id'];
				}
			}
		}

		return $main_fields;
	}

	public static function get_user_fields() {
		return get_option( 'wpep_settings_fields' );
	}
}
