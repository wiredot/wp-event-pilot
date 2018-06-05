<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;

class Filters {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'filters_admin_menu' ) );
	}

	public function filters_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wpep', 'filters', __( 'Filters', 'wpep' ), 'read', 'filters', array( $this, 'filters_page' ) );
	}

	public function filters_page() {
		$events = Event::get_events();

		if ( isset( $_POST['event_id'] ) ) {
			$_SESSION['filters_event_id'] = $_POST['event_id'];
		}

		if ( ! isset( $_SESSION['filters_event_id'] ) ) {
			$_SESSION['filters_event_id'] = 0;
		}

		if ( ! isset( $_GET['paid'] ) ) {
			$mode = 'all';
		} else if ( $_GET['paid'] ) {
			$mode = 'paid';
		} else {
			$mode = 'notpaid';
		}

		$ufs = array();

		if ( isset( $_POST['ufs'] ) ) {
			$ufs = $_POST['ufs'];
			$_SESSION['ufs'] = $ufs;
		} else if ( isset( $_SESSION['ufs'] ) ) {
			$ufs = $_SESSION['ufs'];
		}

		$event_id = $_SESSION['filters_event_id'];

		$Twig = new Twig();
		echo $Twig->twig->render(
			'backend/filters.twig', array(
				'events' => $events,
				'event_id' => $_SESSION['filters_event_id'],
				'additional_fields_columns' => $this->get_additional_fields_columns( $event_id ),
				'additional_fields' => Additional_Fields::get_additional_fields( $event_id ),
				'user_fields' => User_Fields::get_user_fields_list(),
				'registrations' => $this->get_registrations( $event_id, $mode, $ufs ),
				'count_all' => $this->count_registrations( $event_id, 'all', $ufs ),
				'count_paid' => $this->count_registrations( $event_id, 'paid', $ufs ),
				'count_notpaid' => $this->count_registrations( $event_id, 'notpaid', $ufs ),
				'mode' => $mode,
				'ufs' => $ufs,
			)
		);
	}

	public function get_registrations( $event_id, $mode = 'all', $ufs = array() ) {
		global $wpdb;

		if ( ! $event_id ) {
			return array();
		}

		$sql = '';

		if ( 'paid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' AND meta_value = 1 AND post_id = ID) > 0";
		} else if ( 'notpaid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' AND meta_value = 1 AND post_id = ID) < 1";
		}

		$sql_search = $this->get_user_field_search( $ufs );

		$user_fields_sql = '';

		$user_fields = User_Fields::get_user_fields_list();

		$additional_fields = Additional_Fields::get_additional_fields( $event_id );

		foreach ( $user_fields as $user_field ) {
			$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $user_field['id'] . "' AND post_id = ID) " . $user_field['id'] . ',';
		}

		$additional_fields = Additional_Fields::get_additional_fields( $event_id );

		foreach ( $additional_fields as $additional_field ) {
			switch ( $additional_field['type'] ) {
				case 'text':
				case 'datetime':
				case 'date':
				case 'time':
				case 'email':
				case 'textarea':
				case 'radio':
				case 'select':
					$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $additional_field['id'] . "' AND post_id = ID) '" . $additional_field['id'] . "', ";
					break;
				case 'checkbox':
					if ( ! is_array( $additional_field['options'] ) ) {
						$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $additional_field['id'] . "' AND post_id = ID) '" . $additional_field['id'] . "', ";
					}
					break;
			}
		}

		$registrations = $wpdb->get_results(
			'
			SELECT ID, post_title as title,
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = 'email' AND post_id = ID) email,
				(SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'paid' AND post_id = ID) paid,
				" . $user_fields_sql . '
				post_date registration_date 
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "' 
				AND post_status = 'publish'
				" . $sql . $sql_search .'
			ORDER BY post_date DESC
			', ARRAY_A
		);

		foreach ( $registrations as $key => $reg ) {
			foreach ( $additional_fields as $additional_field ) {
				switch ( $additional_field['type'] ) {
					case 'checkbox':
						if ( is_array( $additional_field['options'] ) ) {
							$values = get_post_meta( $reg['ID'], $additional_field['id'], true );
							if ( is_array( $values ) ) {
								foreach ( $values as $value ) {
									$registrations[ $key ][ $additional_field['id'] . '_' . $value ] = 1;
								}
							}
						}
						break;
					case 'table':
						$values = get_post_meta( $reg['ID'], $additional_field['id'], true );
						if ( is_array( $values ) ) {
							foreach ( $values as $row => $cols ) {
								foreach ( $cols as $col => $value ) {
									$registrations[ $key ][ $additional_field['id'] . '_' . $row . '_' . $col ] = $value;
								}
							}
						}
						break;
				}
			}
		}

		return $registrations;
	}

	public function get_additional_fields_columns( $event_id ) {
		$additional_fields = Additional_Fields::get_additional_fields( $event_id );

		$columns = array();

		foreach ( $additional_fields as $additional_field ) {
			switch ( $additional_field['type'] ) {
				case 'text':
				case 'datetime':
				case 'date':
				case 'time':
				case 'email':
				case 'textarea':
				case 'radio':
				case 'select':
					$columns[ $additional_field['id'] ] = $additional_field['label'];
					break;
				case 'checkbox':
					if ( ! is_array( $additional_field['options'] ) ) {
						$columns[ $additional_field['id'] ] = $additional_field['label'];
					} else {
						foreach ( $additional_field['options'] as $option ) {
							$columns[ $additional_field['id'] . '_' . $option['id'] ] = $option['label'];
						}
					}
					break;
				case 'table':
					foreach ( $additional_field['rows'] as $row ) {
						foreach ( $additional_field['cols'] as $col ) {
							$columns[ $additional_field['id'] . '_' . $row['id'] . '_' . $col['id'] ] = $additional_field['id'] . ' ' . $row['id'] . ' ' . $col['id'];
						}
					}
					break;
			}
		}

		return $columns;
	}

	public function get_user_field_search( $ufs ) {
		global $wpdb;
		$sql_search = '';

		$user_fields = User_Fields::get_user_fields_list();

		foreach ( $user_fields as $user_field ) {
			if ( isset( $ufs[ $user_field['id'] ] ) && $ufs[ $user_field['id'] ] ) {
				$sql_search .= ' AND (SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $user_field['id'] . "' AND post_id = ID) LIKE '%" . $ufs[ $user_field['id'] ] . "%'";
			}
		}

		return $sql_search;
	}

	public function count_registrations( $event_id, $mode = 'all', $ufs = array() ) {
		global $wpdb;

		$sql = '';

		if ( 'paid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' AND meta_value = 1 AND post_id = ID) > 0";
		} else if ( 'notpaid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' AND meta_value = 1 AND post_id = ID) < 1";
		}

		$sql_search = $this->get_user_field_search( $ufs );

		return $wpdb->get_var(
			'
			SELECT count(*)
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "' 
				AND post_status = 'publish'
			" . $sql . $sql_search
		);
	}
}
