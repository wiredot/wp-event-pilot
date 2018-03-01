<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

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

		// those have to be protected against bad entries - SQL Injection etc...
		$email = sanitize_text_field( trim( $_POST['email'] ) );
		$password = sanitize_text_field( trim( $_POST['password'] ) );

		// check if email is not empty and valid
		if ( empty( $email ) ) {
			$form_errors['email'] = __cp( 'The username field is empty', 'user' );
		}

		// check if password is not empty
		if ( empty( $password ) ) {
			$form_errors['password'] = __cp( 'The password field is empty' );
		}

		$remember = false;

		// remember for 2 weeks checkbox
		if ( ! empty( $_POST['remember'] ) ) {
			$remember = true;
		}

		// if there are no errors so far
		if ( empty( $form_errors ) ) {

			// get user with the provided email address
			$user = get_user_by( 'email', $email );

			// check if the user exists and if the password matches
			if ( ! $user || ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {

				// if the password is incorrect for the specified user
				$form_errors['user_login'] = ( 'Your password is incorrect' );
			}
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {

			// authenticate user
			$this->log_user_in( $email, $password );

			global $CP_Membership;

			$user_status = $CP_Membership->get_user_status();
			if ( $user_status == 1 ) {
				$page_id = 151;
			} else {
				$page_id = 21;
			}

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => ( 'Success' ),
					'message' => ( 'You are logged in' ),
					'container' => '#wpep-login form',
					'animationIn' => 'dissolve',
					'hideAfter' => 1000,
				),
				'redirect' => array(
					'url' => get_permalink( $page_id ),
					'delay' => 1000,
				),
			);
		} // if there are errors
		else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => ( 'Ups!' ),
					'message' => ( 'Something went wrong:' ),
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
