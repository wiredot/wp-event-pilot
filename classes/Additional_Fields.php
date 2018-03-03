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
	}

	public function add_additional_fields( $config ) {
		$event_id = get_post_meta( $_GET['post'], 'event_id', true );

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
			'fields' => $fields,
		);

		return $meta_box;
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

		$fields_form .= $Twig->twig->render(
			'forms/header.twig', array(
				'header' => $additional_fields_label,
			)
		);

		foreach ( $additional_fields as $key => $field ) {
			$value = '';

			$fields_form .= $Twig->twig->render(
				'forms/' . $field['type'] . '.twig', array(
					'field' => $field,
					'value' => $value,
				)
			);
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
				'header' => $additional_fields_label,
				'fields' => $additional_fields,
				'values' => $values,
			)
		);

		return $fields_table;
	}
}
