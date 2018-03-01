<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

class Account {

	public function __construct() {
		add_shortcode( 'wpep-my-account', array( $this, 'account_page' ) );

		add_action( 'wp_ajax_wpep-account-save', array( $this, 'save_account' ) );
		add_action( 'wp_ajax_nopriv_wpep-account-save', array( $this, 'save_account' ) );
	}

	public function account_page() {
		$Twig = new Twig();
		echo $Twig->twig->render(
			'front/account.twig', array()
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

		// check if first_name is not empty and valid
		if ( empty( $first_name ) ) {
			$form_errors['first_name'] = __( 'The First Name field is empty', 'wpep' );
		}

		// check if last_name is not empty and valid
		if ( empty( $last_name ) ) {
			$form_errors['last_name'] = __( 'The Last Name field is empty', 'wpep' );
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {

			$user_id = get_current_user_id();

			update_user_meta( $user_id, 'first_name', $first_name );
			update_user_meta( $user_id, 'last_name', $last_name );

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
