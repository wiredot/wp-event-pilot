<?php

namespace Wiredot\WPEP;

use Wiredot\WPEP\User_Fields;

class Event_Registration {

	public function __construct() {
		add_action( 'wp_ajax_wpep-event-registration', array( $this, 'event_registration' ) );
		add_action( 'wp_ajax_nopriv_wpep-event-registration', array( $this, 'event_registration' ) );

		if ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
			add_action( 'preamp_wpep_config', array( $this, 'add_user_fields' ) );
		}
	}

	public function event_registration() {
		$form_errors = array();

		$event_id = intval( $_POST['event_id'] );

		if ( empty( $event_id ) ) {
			$form_errors['event_id'] = __( 'Something went wrong, please try again.', 'wpep' );
		}

		if ( empty( $form_errors ) ) {

			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );

			$order_id = $this->get_order_id();

			$event = array(
				'post_type' => 'wpep-registration',
				'post_status' => 'publish',
				'post_name' => $order_id,
				'post_title' => $user->user_email,
				'post_author' => $user_id,
			);

			$registration_id = wp_insert_post( $event );

			update_post_meta( $registration_id, 'event_id', $event_id );

			$additional_fields = User_Fields::get_user_fields_list();

			foreach ( $additional_fields as $field ) {
				update_post_meta( $registration_id, $field['id'], get_user_meta( $user_id, $field['id'], true ) );
			}

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

	public function get_order_id() {
		return md5( uniqid( rand(), true ) );
	}

	public function add_user_fields( $config ) {
		return $config;
	}
}
