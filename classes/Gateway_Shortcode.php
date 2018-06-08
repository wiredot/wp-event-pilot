<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;

class Gateway_Shortcode {

	public function __construct() {
		add_shortcode( 'wpep-gateway', array( $this, 'gateway_shortcode' ) );

		add_action( 'wp_ajax_wpep-gateway-add', array( $this, 'add_code' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-add', array( $this, 'add_code' ) );

		add_action( 'wp_ajax_wpep-gateway-remove', array( $this, 'remove_code' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-remove', array( $this, 'remove_code' ) );
	}

	public function gateway_shortcode() {
		$Twig = new Twig();

		if ( ! isset( $_SESSION['wpep-gateway'] ) ) {
			$_SESSION['wpep-gateway'] = array();
		}

		echo $Twig->twig->render(
			'backend/gateway_shortcode.twig', array(
				'codes' => $_SESSION['wpep-gateway'],
			)
		);
	}

	public function add_code() {
		global $wpdb;

		$form_errors = array();

		$code = sanitize_text_field( trim( $_POST['code'] ) );

		// check if email is not empty and valid
		if ( empty( $code ) ) {
			$form_errors = __( 'Enter Unique Code', 'wpep' );
		}

		$code_meta = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT *
				FROM $wpdb->postmeta
				WHERE meta_value = %s
				", $code
			),
			ARRAY_A
		);

		if ( ! $code_meta ) {
			$form_errors = __( 'Unique Code not valid', 'wpep' );
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {
			$code_d = explode( '_%%_', $code_meta['meta_key'] );

			$code_details = array(
				'event_id' => $code_meta['post_id'],
				'field' => $code_d[0],
				'row' => $code_d[1],
				'col' => $code_d[2],
			);

			$code_details['title'] = $this->get_table_field_title( $code_details );

			$_SESSION['wpep-gateway'][ $code ] = $code_details;

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => ( 'Success' ),
					'message' => ( 'Code Added' ),
					'container' => '#wpep-gateway form',
					'animationIn' => 'dissolve',
				),
				'redirect' => array(
					'url' => get_permalink( intval( $_POST['page'] ) ),
					'delay' => 2000,
				),
			);
		} // if there are errors
		else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => $form_errors,
					'container' => '#wpep-gateway form',
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

	public function remove_code() {
		$code = sanitize_text_field( $_GET['code'] );
		unset( $_SESSION['wpep-gateway'][ $code ] );
		wp_redirect( get_permalink( intval( $_GET['page'] ) ) );
		exit;
	}

	public function get_table_field_title( $code_details ) {
		$fields = get_post_meta( $code_details['event_id'], 'additional_fields', true );

		if ( ! is_array( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( $field['id'] == $code_details['field'] ) {
				// print_r( $field );

				foreach ( $field['rows'] as $row ) {
					foreach ( $field['cols'] as $col ) {
						if ( $row['id'] == $code_details['row'] && $col['id'] == $code_details['col'] ) {
							return $row['label'] . ' - ' . $col['label'];
						}
					}
				}
			}
		}

		// print_r( $fields );
	}
}
