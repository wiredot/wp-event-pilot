<?php

namespace Wiredot\WPEP;

use Wiredot\WPEP\User_Fields;

class Event_Registration {

	public function __construct() {
		add_action( 'wp_ajax_wpep-event-confirmation', array( $this, 'event_confirmation' ) );
		add_action( 'wp_ajax_nopriv_wpep-event-confirmation', array( $this, 'event_confirmation' ) );

		add_action( 'wp_ajax_wpep-event-registration', array( $this, 'event_registration' ) );
		add_action( 'wp_ajax_nopriv_wpep-event-registration', array( $this, 'event_registration' ) );
	}

	public function event_registration() {
		$form_errors = array();

		$email = sanitize_text_field( trim( $_POST['email'] ) );

		// check if email is not empty and valid
		if ( empty( $email ) ) {
			$form_errors['email'] = __( 'The E-mail field is empty', 'wpep' );
		}

		$event_id = intval( $_POST['event_id'] );

		if ( empty( $event_id ) ) {
			$form_errors['event_id'] = __( 'Something went wrong, please try again.', 'wpep' );
		}

		$additional_fields = User_Fields::get_user_fields_list();

		foreach ( $additional_fields as $field ) {
			switch ( $field['type'] ) {
				case 'text':
				case 'date':
				default:
					if ( empty( $_POST[ $field['id'] ] ) ) {
						$form_errors[ $field['id'] ] = $field['label'] . __( ' field is empty', 'wpep' );
					}
					break;
			}
		}

		if ( empty( $form_errors ) ) {
			$_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ] = $email;

			foreach ( $additional_fields as $field ) {
				$_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] = $_POST[ $field['id'] ];
			}

			// response alert
			$response = array(
				'redirect' => array(
					'url' => get_permalink( $event_id ) . '?confirm=1',
					'delay' => 0,
				),
			);
		} else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => __( 'Something went wrong:', 'wpep' ),
					'list' => $form_errors,
					'container' => '#wpep-event-registration form',
					'animationIn' => 'dissolve',
				),
			);
		}

		// encode and return response
		$response_json = json_encode( $response );
		header( 'Content-Type: application/json' );
		echo $response_json;
		exit;
	}

	public function event_confirmation() {
		$form_errors = array();

		$event_id = intval( $_POST['event_id'] );

		if ( empty( $event_id ) ) {
			$form_errors['event_id'] = __( 'Something went wrong, please try again.', 'wpep' );
		}

		if ( empty( $form_errors ) ) {
			$user_id = get_current_user_id();

			if ( ! $user_id ) {
				$user_id = $this->create_user( $event_id );
			}

			$order_id = $this->get_order_id();

			$event = array(
				'post_type' => 'wpep-registration',
				'post_status' => 'publish',
				'post_name' => $order_id,
				'post_title' => $_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ],
				'post_author' => $user_id,
			);

			$registration_id = wp_insert_post( $event );

			update_post_meta( $registration_id, 'event_id', $event_id );

			$additional_fields = User_Fields::get_user_fields_list();

			foreach ( $additional_fields as $field ) {
				update_post_meta( $registration_id, $field['id'], $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] );
				unset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] );
			}

			unset( $_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ] );

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => __( 'Success', 'wpep' ),
					'message' => __( 'You have been registered for the event', 'wpep' ),
					'container' => '#wpep-event-registration form',
					'animationIn' => 'dissolve',
				),
				'redirect' => array(
					'url' => get_permalink( $registration_id ),
					'delay' => 2000,
				),
			);
		} else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => __( 'Something went wrong:', 'wpep' ),
					'list' => $form_errors,
					'container' => '#wpep-event-registration form',
					'animationIn' => 'dissolve',
				),
			);
		}

		// encode and return response
		$response_json = json_encode( $response );
		header( 'Content-Type: application/json' );
		echo $response_json;
		exit;
	}

	public function create_user( $event_id ) {
		$email = $_SESSION[ 'wpep_event_registration_' . $event_id . '_email' ];

		$user = get_user_by_email( $email );
		if ( $user ) {
			return $user->ID;
		}

		$participant = array(
			'user_login' => $email,
			'user_email' => $email,
			'role' => 'subscriber',
		);

		$participant_id = wp_insert_user( $participant );

		$additional_fields = User_Fields::get_user_fields_list();

		foreach ( $additional_fields as $field ) {
			update_user_meta( $participant_id, $field['id'], $_SESSION[ 'wpep_event_registration_' . $event_id . '_' . $field['id'] ] );
		}

		return $participant_id;
	}

	public function get_order_id() {
		return md5( uniqid( rand(), true ) );
	}
}
