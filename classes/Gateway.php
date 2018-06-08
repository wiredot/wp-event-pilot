<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;

class Gateway {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'filters_admin_menu' ) );
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
					if ( ! $code ) {
						$this->create_unique_code( $event_id, $table['id'], $row['id'], $col['id'] );
					}
				}
			}
		}

		return $tables;
	}

	public function get_unique_code( $event_id, $id, $row, $col ) {
		$meta_key = $this->get_meta_key( $id, $row, $col );
		return get_post_meta( $event_id, $meta_key, true );
	}

	public function create_unique_code( $event_id, $id, $row, $col ) {
		$code = uniqid( md5( rand() ), true );
		$meta_key = $this->get_meta_key( $id, $row, $col );
		update_post_meta( $event_id, $meta_key, $code );
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
}
