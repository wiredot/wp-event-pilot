<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

class Login {

	public function __construct() {
		add_shortcode( 'wpep-login-form', array( $this, 'login_form' ) );

		add_action( 'wp_ajax_wpep-login', array( $this, 'log_in' ) );
		add_action( 'wp_ajax_nopriv_wpep-login', array( $this, 'log_in' ) );

		if ( isset( $_GET['wpep-logout'] ) ) {
			add_action( 'init', array( $this, 'log_out' ) );
		}
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
			$form_errors['email'] = __( 'The E-mail field is empty', 'wpep' );
		}

		// check if password is not empty
		if ( empty( $password ) ) {
			$form_errors['password'] = __( 'The password field is empty', 'wpep' );
		}

		// if there are no errors so far
		if ( empty( $form_errors ) ) {

			// get user with the provided email address
			$user = get_user_by( 'email', $email );

			// check if the user exists and if the password matches
			if ( ! $user || ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {

				// if the password is incorrect for the specified user
				$form_errors['user_login'] = ( 'Your E-Mail or Password is not correct' );
			}
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {

			// authenticate user
			$this->log_user_in( $email, $password );

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

	public function log_user_in( $email, $password ) {
		$user = wp_authenticate( $email, $password );
		wp_set_auth_cookie( $user->ID, true );
		wp_set_current_user( $user->ID, $user->user_login );
		do_action( 'wp_login', $user->user_login, $user );
	}

	public function log_out() {
		wp_logout();

		$login_page = get_option( 'wpep_settings_login_page' );

		$location = get_permalink( $login_page );

		wp_redirect( $location );
		exit;
	}
}
