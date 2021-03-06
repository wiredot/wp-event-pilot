<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;

class Single_Event {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'single_event' ) );
	}

	public function single_event( $content ) {
		global $post;

		if ( $post && 'wpep' == $post->post_type && is_singular( 'wpep' ) && is_main_query() && ! post_password_required() ) {
			$Twig = new Twig();

			$event_id = $post->ID;

			$email = '';

			if ( isset( $_GET['register'] ) ) {
				$content = $this->show_event_registration_form( $event_id );
			} else if ( isset( $_GET['confirm'] ) ) {
				$content = $this->show_event_registration_confirmation( $event_id );
			} else {
				$content = $Twig->twig->render(
					'front/single_event.twig', array(
						'content' => $content,
					)
				);
			}
		}

		return $content;
	}

	public function show_event_registration( $event_id ) {
		if ( isset( $_GET['confirm'] ) ) {
			$content = $this->show_event_registration_confirmation( $event_id );
		} else {
			$content = $this->show_event_registration_form( $event_id );
		}

		return $content;
	}

	public function show_event_registration_form( $event_id ) {
		if ( get_current_user_id() ) {
			$user = get_userdata( get_current_user_id() );
			$email = $user->user_email;
		} else {
			$email = '';
		}

		$Twig = new Twig();

		$content = $Twig->twig->render(
			'front/single_event_registration.twig', array(
				'user_fields' => $this->get_user_fields( $event_id, 'registration' ),
				'additional_fields' => Additional_Fields::get_additional_fields_form( $event_id ),
				'email' => $email,
				'event_id' => $event_id,
			)
		);

		return $content;
	}

	public function show_event_registration_confirmation( $event_id ) {
		$Twig = new Twig();

		$email = '';

		if ( isset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ] ) ) {
			$email = $_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ];
		}

		$additional_fields = Additional_Fields::get_additional_fields( $event_id );

		$values = array();

		foreach ( $additional_fields as $field ) {
			if ( $field['id'] ) {
				if ( isset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] ) ) {
					$values[ $field['id'] ] = $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ];
				}
			}
		}

		$content = $Twig->twig->render(
			'front/single_event_confirmation.twig', array(
				'user_fields' => $this->get_user_fields( $event_id, 'confirmation' ),
				'additional_fields' => Additional_Fields::get_additional_fields_table( $event_id, $values ),
				'email' => $email,
				'event_id' => $event_id,
			)
		);

		return $content;
	}

	public function get_user_fields( $event_id = null, $mode = 'registration' ) {
		$user_fields = User_Fields::get_user_fields();

		$user_fields_text = '';

		foreach ( $user_fields as $group ) {
			$user_fields_text .= $this->get_user_group( $group, $event_id, $mode );
		}

		return $user_fields_text;
	}

	public function get_user_group( $group, $event_id = null, $mode = 'registration' ) {
		$group_text = '';

		$Twig = new Twig();

		if ( 'confirmation' == $mode ) {
			$values = array();

			foreach ( $group['fields'] as $key => $field ) {
				if ( isset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] ) ) {
					$values[ $field['id'] ] = $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ];
				}
			}

			$group_text .= $Twig->twig->render(
				'front/user_table.twig', array(
					'header' => $group['name'],
					'fields' => $group['fields'],
					'values' => $values,
				)
			);
		} else {
			$group_text .= $Twig->twig->render(
				'forms/header.twig', array(
					'header' => $group['name'],
				)
			);

			$group_text .= $this->get_user_group_fields( $group['fields'], $event_id );
		}

		return $group_text;
	}

	public function get_user_group_fields( $fields, $event_id = null ) {
		$fields_text = '';

		foreach ( $fields as $key => $field ) {
			$fields_text .= $this->get_user_group_field( $field, $event_id );
		}

		return $fields_text;
	}

	public function get_user_group_field( $field, $event_id = null ) {
		$Twig = new Twig();

		$value = null;

		if ( get_current_user_id() ) {
			$value = get_user_meta( get_current_user_id(), $field['id'], true );
		} else if ( isset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] ) ) {
			$value = $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ];
		}

		return $Twig->twig->render(
			'forms/' . $field['type'] . '.twig', array(
				'field' => $field,
				'value' => $value,
			)
		);
	}
}
