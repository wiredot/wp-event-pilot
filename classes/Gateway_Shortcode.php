<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\Gateway;

class Gateway_Shortcode {

	public function __construct() {
		add_shortcode( 'wpep-gateway', array( $this, 'gateway_shortcode' ) );

		add_action( 'wp_ajax_wpep-gateway-add', array( $this, 'add_code' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-add', array( $this, 'add_code' ) );

		add_action( 'wp_ajax_wpep-gateway-remove', array( $this, 'remove_code' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-remove', array( $this, 'remove_code' ) );

		add_action( 'wp_ajax_wpep-gateway-check', array( $this, 'check_code' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-check', array( $this, 'check_code' ) );
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

	public function check_code() {
		global $wpdb;

		$form_errors = array();

		$code = sanitize_text_field( trim( $_POST['code'] ) );

		// check if email is not empty and valid
		if ( empty( $code ) ) {
			$form_errors = __( 'Enter Barcode', 'wpep' );
		}

		if ( ! isset( $_SESSION['wpep-gateway'] ) || ! count( $_SESSION['wpep-gateway'] ) ) {
			$form_errors = __( 'No Unique Code for event added', 'wpep' );
		} else {
			$codes = $_SESSION['wpep-gateway'];
		}

		if ( empty( $form_errors ) ) {
			$reg_id = $this->get_user_registration_id( $code );

			if ( ! $reg_id ) {
				$form_errors = __( 'Invalid Barcode', 'wpep' );
			} else {
				$validation_error = $this->check_validity( $reg_id, $codes );
				if ( $validation_error ) {
					$form_errors = $validation_error;
				} else {
					$this->mark_used( $reg_id, $codes );
				}
			}
		}

		// only log the user in if there are no errors
		if ( empty( $form_errors ) ) {

			// response alert
			$response = array(
				'alert' => array(
					'type' => 'success',
					'title' => ( 'Success' ),
					'message' => ( 'All ok!' ),
					'container' => '.wpep-barcode',
					'animationIn' => 'dissolve',
				),
				'reset' => 1,
			);
		} // if there are errors
		else {
			$response = array(
				'alert' => array(
					'type' => 'error',
					'title' => __( 'Ups!', 'wpep' ),
					'message' => $form_errors,
					'container' => '.wpep-barcode',
					'animationIn' => 'dissolve',
				),
				'reset' => 1,
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

	public function get_user_registration_id( $barcode ) {
		global $wpdb;

		$registration = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT post_id
				FROM $wpdb->postmeta
				WHERE meta_value = %s
					AND meta_key = 'id_card_uid'
				", $barcode
			)
		);

		return $registration;
	}

	public function check_validity( $reg_id, $codes = array() ) {
		$event_id = get_post_meta( $reg_id, 'event_id', true );
		foreach ( $codes as $code ) {
			if ( $event_id == $code['event_id'] ) {
				$field = get_post_meta( $reg_id, $code['field'], true );
				if ( isset( $field[ $code['row'] ][ $code['col'] ] ) ) {
					if ( $this->check_if_used( $reg_id, $event_id, $code['field'], $code['row'], $code['col'] ) ) {
						return __( 'User has already entered', 'wpep' );
					} else {
						return null;
					}
				}
			}
		}

		return __( 'User has no permissions to enter', 'wpep' );
	}

	public function check_if_used( $reg_id, $event_id, $field, $row, $col ) {
		$Gateway = new Gateway;
		$meta_key = $Gateway->get_used_meta_key( $event_id, $field, $row, $col );
		$used = get_post_meta( $reg_id, $meta_key, true );
		if ( $used ) {
			return true;
		}

		return false;
	}

	public function mark_used( $reg_id, $codes = array() ) {
		$event_id = get_post_meta( $reg_id, 'event_id', true );
		foreach ( $codes as $code ) {
			if ( $event_id == $code['event_id'] ) {
				$field = get_post_meta( $reg_id, $code['field'], true );
				if ( isset( $field[ $code['row'] ][ $code['col'] ] ) ) {
					$Gateway = new Gateway;
					$meta_key = $Gateway->get_used_meta_key( $event_id, $code['field'], $code['row'], $code['col'] );
					update_post_meta( $reg_id, $meta_key, date( 'Y-m-d H:i:s' ) );
				}
			}
		}
	}
}
