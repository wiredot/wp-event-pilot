<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;

class Additional_Fields {

	public function __construct() {
		if ( isset( $_GET['action'] ) && isset( $_GET['post'] ) && 'edit' == $_GET['action'] ) {
			$post_id = $_GET['post'];
			$post = get_post( $post_id );

			if ( 'wpep-registration' == $post->post_type ) {
				add_action( 'preamp_wpep_config', array( $this, 'add_additional_fields' ) );
			}
		}

		if ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && 'wpep-registration' == $_POST['post_type'] ) {
			add_action( 'preamp_wpep_config', array( $this, 'add_additional_fields' ) );
		}
	}

	public function add_additional_fields( $config ) {
		if ( isset( $_GET['post'] ) ) {
			$post = $_GET['post'];
		} else if ( isset( $_POST['post_ID'] ) ) {
			$post = $_POST['post_ID'];
		}
		$event_id = get_post_meta( $post, 'event_id', true );

		$additional_fields = self::get_additional_fields( $event_id );
		$label = self::get_additional_fields_label( $event_id );
		$config['meta_box']['post']['additional_fields'] = $this->get_meta_box( $label, $additional_fields );

		return $config;
	}

	public function get_meta_box( $label, $fields ) {
		$meta_box = array(
			'active' => true,
			'name' => $label,
			'context' => 'normal',
			'priority' => 'high',
			'post_type' => 'wpep-registration',
			'fields' => array(),
		);

		foreach ( $fields as $fkey => $field ) {

			if ( isset( $field['options'] ) ) {
				$field['options'] = $this->fix_options( $field['options'] );
			}
			$meta_box['fields'][ $field['id'] ] = $field;
		}

		return $meta_box;
	}

	public function fix_options( $options ) {
		$fixed_options = array();
		foreach ( $options as $option ) {
			$fixed_options[ $option['id'] ] = $option['label'];
		}

		return $fixed_options;
	}

	public static function get_additional_fields( $event_id ) {
		$fields = get_post_meta( $event_id, 'additional_fields', true );

		return $fields;
	}

	public static function get_additional_fields_label( $event_id ) {
		$label = get_post_meta( $event_id, 'additional_fields_label', true );

		return $label;
	}

	public static function get_additional_fields_form( $event_id ) {
		$additional_fields_label = self::get_additional_fields_label( $event_id );
		$additional_fields = self::get_additional_fields( $event_id );

		$Twig = new Twig();

		$fields_form = '';

		foreach ( $additional_fields as $key => $field ) {
			$value = '';

			if ( 'header' == $field['type'] ) {
				$fields_form .= $Twig->twig->render(
					'forms/header.twig', array(
						'header' => $field['label'],
					)
				);
			} else {
				$fields_form .= $Twig->twig->render(
					'forms/' . $field['type'] . '.twig', array(
						'field' => $field,
						'value' => $value,
					)
				);
			}
		}

		return $fields_form;
	}

	public static function get_additional_fields_table( $event_id ) {
		$additional_fields_label = self::get_additional_fields_label( $event_id );
		$additional_fields = self::get_additional_fields( $event_id );

		$Twig = new Twig();

		$fields_table = '';
		$values = array();

		foreach ( $additional_fields as $key => $field ) {
			if ( isset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] ) ) {
				$values[ $field['id'] ] = $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ];
			}
		}

		$fields_table .= $Twig->twig->render(
			'front/user_table.twig', array(
				'fields' => $additional_fields,
				'values' => $values,
			)
		);

		return $fields_table;
	}
}
