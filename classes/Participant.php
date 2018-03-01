<?php

namespace Wiredot\WPEP;

class Participant {

	public function __construct() {
		add_action( 'post_updated', array( $this, 'update_title' ), 10, 2 );

		if ( isset( $_GET['wpep-logout'] ) ) {
			add_action( 'init', array( $this, 'log_out' ) );
		}
	}

	public function update_title( $post_id, $post ) {
		global $wpdb;

		if ( 'wpep-participant' == $post->post_type ) {
			$post_title = '';

			$main_fields = User_Fields::get_main_fields();

			foreach ( $main_fields as $field ) {
				if ( isset( $_POST[ $field ] ) ) {
					$post_title .= $_POST[ $field ] . ' ';
				}
			}

			$wpdb->update(
				$wpdb->posts,
				array(
					'post_title' => $post_title,
				),
				array(
					'ID' => $post_id,
				),
				array(
					'%s',
				)
			);
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
}
