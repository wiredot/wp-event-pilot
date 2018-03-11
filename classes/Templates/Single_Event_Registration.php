<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;

class Single_Event_Registration {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'single_event_registration' ) );
	}

	public function single_event_registration( $content ) {
		global $post;

		if ( $post && 'wpep-registration' == $post->post_type && is_singular( 'wpep-registration' ) && is_main_query() && ! post_password_required() ) {

			$registration_id = $post->ID;
			$event_id = get_post_meta( $registration_id, 'event_id', true );

			$Twig = new Twig();

			$additional_fields = Additional_Fields::get_additional_fields( $event_id );

			$values = array();

			foreach ( $additional_fields as $field ) {
				if ( $field['id'] ) {
					$values[ $field['id'] ] = get_post_meta( $registration_id, $field['id'], true );
				}
			}

			$content = $Twig->twig->render(
				'front/single_registration.twig', array(
					'user_fields' => $this->get_user_fields( $registration_id ),
					'additional_fields' => Additional_Fields::get_additional_fields_table( $event_id, $values ),
					'email' => get_post_meta( $event_id, 'email', true ),
					'event_id' => $event_id,
				)
			);
		}
		
		return $content;
	}

	public function get_user_fields( $registration_id = null ) {
		$user_fields = User_Fields::get_user_fields();

		$user_fields_text = '';

		foreach ( $user_fields as $group ) {
			$user_fields_text .= $this->get_user_group( $group, $registration_id );
		}

		return $user_fields_text;
	}

	public function get_user_group( $group, $registration_id = null ) {
		$group_text = '';

		$Twig = new Twig();

		$values = array();

		foreach ( $group['fields'] as $key => $field ) {
			$values[ $field['id'] ] = get_post_meta( $registration_id, $field['id'], true );
		}

		$group_text .= $Twig->twig->render(
			'front/user_table.twig', array(
				'header' => $group['name'],
				'fields' => $group['fields'],
				'values' => $values,
			)
		);

		return $group_text;
	}
}
