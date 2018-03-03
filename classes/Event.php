<?php

namespace Wiredot\WPEP;

class Event {

	public $event_fields = array();

	public function __construct() {
		$this->event_fields = $this->load_event_fields();

		add_action( 'preamp_wpep_config', array( $this, 'add_event_fields' ) );
	}

	public function load_event_fields() {
		$fields = array();

		$wpep_event_fields = self::get_event_fields();

		foreach ( $wpep_event_fields as $fkey => $field ) {

			if ( isset( $field['options'] ) ) {
				$field['options'] = $this->fix_options( $field['options'] );
			}
			$fields[ $field['id'] ] = $field;
		}

		return $fields;
	}

	public function add_event_fields( $config ) {
		if ( is_array( $this->event_fields ) ) {
			$new_fields = array();
			foreach ( $config['meta_box']['post']['wpep-details']['fields'] as $key => $field ) {
				if ( 'event_fields' == $key ) {
					$new_fields = array_merge( $new_fields, $this->event_fields );
				} else {
					$new_fields[ $key ] = $field;
				}
			}
			$config['meta_box']['post']['wpep-details']['fields'] = $new_fields;
		}

		return $config;
	}

	public static function get_event_fields() {
		return get_option( 'wpep_settings_event_fields' );
	}

	public function fix_options( $options ) {
		$fixed_options = array();
		foreach ( $options as $option ) {
			$fixed_options[ $option['id'] ] = $option['label'];
		}

		return $fixed_options;
	}
}
