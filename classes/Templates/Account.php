<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;

class Account {

	public function __construct() {
		add_shortcode( 'wpep-my-account', array( $this, 'account_page' ) );

		add_action( 'wp_ajax_wpep-account-save', array( $this, 'save_account' ) );
		add_action( 'wp_ajax_nopriv_wpep-account-save', array( $this, 'save_account' ) );
	}

	public function account_page() {
		$Twig = new Twig();
		echo $Twig->twig->render(
			'front/account.twig', array(
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
				'value' => get_user_meta( get_current_user_id(), $field['id'], true ),
			)
		);
	}

	public function save_account() {
		$form_errors = array();
		$remember = true;

		// those have to be protected against bad entries - SQL Injection etc...
		$first_name = sanitize_text_field( trim( $_POST['first_name'] ) );
		$last_name = sanitize_text_field( trim( $_POST['last_name'] ) );
		$email = sanitize_text_field( trim( $_POST['email'] ) );

		// check if email is not empty and valid
		if ( empty( $email ) ) {
			$form_errors['email'] = __( 'The E-mail field is empty', 'wpep' );
		}

		$additional_fields = User_Fields::get_user_fields_list();

		foreach ( $additional_fields as $field ) {
			switch ( $field['type'] ) {
				case 'text':
				case 'date':
				default:
					if ( $field['required'] && empty( $_POST[ $field['id'] ] ) ) {
						$form_errors[ $field['id'] ] = $field['label'] . __( ' field is empty', 'wpep' );
					}
					break;
			}
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {
			$participant_id = get_current_user_id();

			foreach ( $additional_fields as $field ) {
				update_user_meta( $participant_id, $field['id'], $_POST[ $field['id'] ] );
			}

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => ( 'Success' ),
					'message' => ( 'Your Account is saved' ),
					'container' => '#wpep-account form',
					'animationIn' => 'dissolve',
					'hideAfter' => 3000,
				),
			);

		} // if there are errors
		else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => __( 'Something went wrong:', 'wpep' ),
					'list' => $form_errors,
					'container' => '#wpep-account form',
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
}
