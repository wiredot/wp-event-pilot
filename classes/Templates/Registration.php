<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;

class Registration {

	public function __construct() {
		add_shortcode( 'wpep-registration-form', array( $this, 'registration_form' ) );

		add_action( 'wp_ajax_wpep-register', array( $this, 'register' ) );
		add_action( 'wp_ajax_nopriv_wpep-register', array( $this, 'register' ) );
	}

	public function registration_form() {
		$Twig = new Twig();

		echo $Twig->twig->render(
			'front/registration.twig', array(
				'additional_fields' => $this->get_user_fields(),
			)
		);
	}

	public function get_user_fields() {
		$additional_fields = User_Fields::get_user_fields();

		$user_fields = '';

		foreach ( $additional_fields as $group ) {
			$user_fields .= $this->get_user_group( $group );
		}

		return $user_fields;
	}

	public function get_user_group( $group ) {
		$group_text = '';

		$Twig = new Twig();
		$group_text .= $Twig->twig->render(
			'forms/header.twig', array(
				'header' => $group['name'],
			)
		);

		$group_text .= $this->get_user_group_fields( $group['fields'] );

		return $group_text;
	}

	public function get_user_group_fields( $fields ) {
		$fields_text = '';

		foreach ( $fields as $key => $field ) {
			$fields_text .= $this->get_user_group_field( $field );
		}

		return $fields_text;
	}

	public function get_user_group_field( $field ) {
		$Twig = new Twig();
		return $Twig->twig->render(
			'forms/' . $field['type'] . '.twig', array(
				'field' => $field,
			)
		);
	}

	public function register() {
		$form_errors = array();

		// those have to be protected against bad entries - SQL Injection etc...
		$email = sanitize_text_field( trim( $_POST['email'] ) );
		$password = sanitize_text_field( trim( $_POST['password'] ) );

		// check if email is not empty and valid
		if ( empty( $email ) ) {
			$form_errors['email'] = __( 'The E-mail field is empty', 'wpep' );
		}

		// check if password is not empty
		if ( empty( $password ) ) {
			$form_errors['password'] = __( 'The password field is empty', 'wpep' );
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

			$participant = array(
				'user_login' => $email,
				'user_email' => $email,
				'user_pass' => $password,
				'role' => 'subscriber',
			);

			$participant_id = wp_insert_user( $participant );

			// Validate inserted user
			if ( is_wp_error( $user_id ) ) {
				$form_errors['password'] = __( 'There was a problem, please try again.', 'wpep' );
			}
		}

		if ( empty( $form_errors ) ) {

			foreach ( $additional_fields as $field ) {
				update_user_meta( $participant_id, $field['id'], $_POST[ $field['id'] ] );
			}

			$this->log_user_in( $email, $password );

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => __( 'Success', 'wpep' ),
					'message' => __( 'You have been registered', 'wpep' ),
					'container' => '#wpep-register form',
					'animationIn' => 'dissolve',
				),
			);

			$account_page = get_option( 'wpep_settings_account_page' );

			if ( $account_page ) {
				$response['redirect'] = array(
					'url' => get_permalink( $account_page ),
					'delay' => 2000,
				);
			}
		} else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => __( 'Something went wrong:', 'wpep' ),
					'list' => $form_errors,
					'container' => '#wpep-register form',
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

	public function log_user_in( $email, $password ) {
		$user = wp_authenticate( $email, $password );
		wp_set_auth_cookie( $user->ID, true );
		wp_set_current_user( $user->ID, $user->user_login );
		do_action( 'wp_login', $user->user_login, $user );
	}
}
