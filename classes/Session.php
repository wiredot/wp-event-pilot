<?php

namespace Wiredot\WPEP;

class Session {

	public function __construct() {
		if ( isset( $_GET['wpep-logout'] ) ) {
			add_action( 'init', array( $this, 'log_out' ) );
		}
	}

	public static function is_logged_in() {
		if ( isset( $_SESSION['wpep-participant'] ) && $_SESSION['wpep-participant'] ) {
			return true;
		}

		return false;
	}

	public static function log_in() {
		$_SESSION['wpep-participant'] = true;
	}

	public static function log_out() {
		$_SESSION['wpep-participant'] = false;
		unset( $_SESSION['wpep-participant'] );

		$login_page = get_option( 'wpep_settings_login_page' );
		$location = get_permalink( $login_page );
		wp_redirect( $location );
		exit;
	}

	public static function check_login( $email, $password ) {
		return null;		
	}
}
