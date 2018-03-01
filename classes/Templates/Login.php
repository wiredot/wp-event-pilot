<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\Session;

class Login {

	public function __construct() {
		add_shortcode( 'wpep-login-form', array( $this, 'login_form' ) );

		add_action( 'wp_ajax_wpep-login', array( $this, 'log_in' ) );
		add_action( 'wp_ajax_nopriv_wpep-login', array( $this, 'log_in' ) );
	}

	public function login_form() {
		$Twig = new Twig();
		echo $Twig->twig->render(
			'front/login.twig', array()
		);
	}

	public function log_in() {
		$form_errors = array();
		$remember = true;

		// those have to be protected against bad entries - SQL Injection etc...
		$email = sanitize_text_field( trim( $_POST['email'] ) );
		$password = sanitize_text_field( trim( $_POST['password'] ) );

		// check if email is not empty and valid
		if ( empty( $email ) ) {
			$form_errors['email'] = __cp( 'The E-mail field is empty', 'wpep' );
		}

		// check if password is not empty
		if ( empty( $password ) ) {
			$form_errors['password'] = __cp( 'The password field is empty', 'wpep' );
		}

		// if there are no errors so far
		if ( empty( $form_errors ) ) {

			$user_id = Session::check_login( $email, $password );

			// check if the user exists and if the password matches
			if ( ! $user_id ) {

				// if the password is incorrect for the specified user
				$form_errors['user_login'] = ( 'Your E-Mail or Password is not correct' );
			}
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {

			// authenticate user
			Session::log_in( $email, $password );

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => ( 'Success' ),
					'message' => ( 'You are logged in' ),
					'container' => '#wpep-login form',
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
		} // if there are errors
		else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => __( 'Something went wrong:', 'wpep' ),
					'list' => $form_errors,
					'container' => '#wpep-login form',
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
