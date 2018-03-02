<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;

class Additional_Fields {

	public function __construct() {
		// add_action( 'admin_menu', array( $this, 'add_addons_menu' ) );
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
}
