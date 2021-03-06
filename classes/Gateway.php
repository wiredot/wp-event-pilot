<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;

class Gateway {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'filters_admin_menu' ) );

		add_action( 'wp_ajax_wpep-gateway-add-pdf', array( $this, 'gateway_pdf' ) );
		add_action( 'wp_ajax_nopriv_wpep-gateway-add-pdf', array( $this, 'gateway_pdf' ) );
	}

	public function filters_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wpep', 'gateway', __( 'Gateway', 'wpep' ), 'read', 'gateway', array( $this, 'filters_page' ) );
	}

	public function filters_page() {
		global $wpdb;

		$events = Event::get_events();

		if ( isset( $_POST['event_id'] ) ) {
			$event_id = $_POST['event_id'];
			$_SESSION['gateway_event_id'] = $_POST['event_id'];
		} else if ( isset( $_SESSION['gateway_event_id'] ) ) {
			$event_id = $_SESSION['gateway_event_id'];
		} else {
			$event_id = 0;
		}

		if ( isset( $_GET['remove'] ) ) {
			$meta_key = 'used_' . $event_id . '_' . $_GET['field_id'] . '_' . $_GET['row'] . '_' . $_GET['col'];
			delete_post_meta( $_GET['remove'], $meta_key );
		}

		$Twig = new Twig();
		echo $Twig->twig->render(
			'backend/gateway.twig', array(
				'events' => $events,
				'event_id' => $event_id,
				'tables' => $this->get_tables( $event_id ),
				'used_codes' => $this->get_used_codes( $event_id ),
				'used_codes_totals' => $this->get_used_codes_totals( $event_id ),
				'get' => $_GET,
			)
		);
	}

	public function get_tables( $event_id ) {
		$tables = array();
		$additional_fields = Additional_Fields::get_additional_fields( $event_id );
		if ( ! is_array( $additional_fields ) ) {
			return $tables;
		}

		foreach ( $additional_fields as $field ) {
			if ( 'table' == $field['type'] ) {
				$tables[] = $field;
			}
		}
		foreach ( $tables as $table ) {
			foreach ( $table['cols'] as $col ) {
				foreach ( $table['rows'] as $row ) {
					$code = $this->get_unique_code( $event_id, $table['id'], $row['id'], $col['id'] );
				}
			}
		}

		return $tables;
	}

	public function get_unique_code( $event_id, $id, $row, $col ) {
		$meta_key = $this->get_meta_key( $id, $row, $col );
		$code = get_post_meta( $event_id, $meta_key, true );

		if ( ! $code || '0' == substr( $code, 0, 1 ) ) {
			$code = $this->create_unique_code( $event_id, $meta_key );
		}

		return $code;
	}

	public function create_unique_code( $event_id, $meta_key ) {
		$code = $this->generate_unique_id();
		update_post_meta( $event_id, $meta_key, $code );
	}

	public function generate_unique_id() {
		global $wpdb;

		$id = '';

		for ( $i = 0; $i < 12; $i++ ) {
			$id .= rand( 0, 9 );
		}

		$weightflag = true;
		$sum = 0;

		for ( $i = strlen( $id ) - 1; $i >= 0; $i-- ) {
			$sum += ( int )$id[$i] * ( $weightflag?3:1 );
			$weightflag = ! $weightflag;
		}
		$id .= (10 - ( $sum % 10 ) ) % 10;

		if ( '0' == substr( $id, 0, 1 ) ) {
			$id = $this->generate_unique_id();
		}

		$count = $wpdb->get_var(
			$wpdb->prepare( 'SELECT count(*) FROM ' . $wpdb->postmeta . ' WHERE meta_value = %d', $id )
		);

		if ( $count ) {
			$id = $this->generate_unique_id();
		}

		return $id;
	}

	public function get_meta_key( $field_id, $row, $col ) {
		return $field_id . '_%%_' . $row . '_%%_' . $col;
	}

	public function get_used_codes( $event_id ) {
		if ( ! isset( $_GET['code'] ) ) {
			return array();
		}

		global $wpdb;

		$meta_key = 'used_' . $event_id . '_' . $_GET['field_id'] . '_' . $_GET['row'] . '_' . $_GET['col'];

		$codes = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT *
				FROM $wpdb->postmeta
				WHERE meta_key = %s
			", $meta_key
			), ARRAY_A
		);

		return $codes;
	}

	public function get_used_codes_totals( $event_id ) {
		if ( ! isset( $_GET['code'] ) ) {
			return 0;
		}

		$field_id = $_GET['field_id'];
		$f_row = $_GET['row'];
		$f_col = $_GET['col'];

		global $wpdb;

		$registrations = $wpdb->get_results(
			'
			SELECT ID,
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field_id . "' AND post_id = ID) field
			FROM " . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND post_status = 'publish'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "'
				" . '
			', ARRAY_A
		);

		if ( ! $registrations ) {
			return 0;
		}

		$ok = 0;

		foreach ( $registrations as $key => $reg ) {
			$field = maybe_unserialize( $reg['field'] );

			if ( isset( $field[ $f_row ][ $f_col ] ) ) {
				$ok++;
			}
		}


		return $ok;
	}

	public function gateway_pdf() {
		if ( isset( $_POST['event_id'] ) ) {
			$event_id = $_POST['event_id'];
		}

		$Twig = new Twig();
		$pdf = $Twig->twig->render(
			'backend/gateway_pdf.twig', array(
				'event_id' => $event_id,
				'tables' => $this->get_tables( $event_id ),
			)
		);

		$mpdf = new \Mpdf\Mpdf(
			[
				'mode' => 'utf-8',
				'format' => 'A4',
				'orientation' => 'L',
			]
		);
		$mpdf->WriteHTML( $pdf );
		$mpdf->Output();

		exit;
	}

	public function get_used_meta_key( $event_id, $field, $row, $col ) {
		return 'used_' . $event_id . '_' . $field . '_' . $row . '_' . $col;
	}
}
