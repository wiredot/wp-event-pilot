<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;

class Filters {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'filters_admin_menu' ) );

		add_action( 'wp_ajax_wpep-new-filter-save', array( $this, 'new_filter_save' ) );
		add_action( 'wp_ajax_nopriv_wpep-new-filter-save', array( $this, 'new_filter_save' ) );

		add_action( 'wp_ajax_wpep-filter-act', array( $this, 'filter_actions' ) );
		add_action( 'wp_ajax_nopriv_wpep-filter-act', array( $this, 'filter_actions' ) );
	}

	public function filters_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wpep', 'filters', __( 'Filters', 'wpep' ), 'read', 'filters', array( $this, 'filters_page' ) );
	}

	public function filters_page() {
		$events = Event::get_events();

		if ( ! isset( $_GET['paid'] ) ) {
			$mode = 'all';
		} else if ( $_GET['paid'] ) {
			$mode = 'paid';
		} else {
			$mode = 'notpaid';
		}

		$ufs = array();

		$filter_id = 0;

		if ( isset( $_GET['filter_id'] ) ) {
			$filter_id = $_GET['filter_id'];
		}

		if ( isset( $_POST['ufs'] ) ) {
			$ufs = $_POST['ufs'];
			$_SESSION['ufs'] = $ufs;
		} else if ( $filter_id ) {
			$ufs = get_post_meta( $filter_id, 'ufs', true );
		} else if ( isset( $_SESSION['ufs'] ) ) {
			$ufs = $_SESSION['ufs'];
		}

		if ( isset( $ufs['event_id'] ) ) {
			$event_id = $ufs['event_id'];
		} else {
			$event_id = 0;
		}

		$sql_search = $this->get_user_field_search( $event_id, $ufs );
		$sql_search .= $this->get_additional_field_search( $event_id, $ufs );

		$Twig = new Twig();
		echo $Twig->twig->render(
			'backend/filters.twig', array(
				'events' => $events,
				'event_id' => $_SESSION['filters_event_id'],
				'additional_fields_columns' => $this->get_additional_fields_columns( $event_id ),
				'additional_fields' => Additional_Fields::get_additional_fields( $event_id ),
				'user_fields' => User_Fields::get_user_fields_list(),
				'registrations' => $this->get_registrations( $event_id, $mode, $sql_search ),
				'count_all' => $this->count_registrations( $event_id, 'all', $sql_search ),
				'count_paid' => $this->count_registrations( $event_id, 'paid', $sql_search ),
				'count_notpaid' => $this->count_registrations( $event_id, 'notpaid', $sql_search ),
				'mode' => $mode,
				'ufs' => $ufs,
				'filter_id' => $filter_id,
				'filters' => $this->get_filters(),
			)
		);
	}

	public function get_registrations( $event_id, $mode = 'all', $sql_search = '' ) {
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
				" . $sql . $sql_search . '
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
		if ( ! $event_id ) {
			return;
		}

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

	public function get_user_field_search( $event_id, $ufs ) {
		global $wpdb;
		$sql_search = '';

		$user_fields = User_Fields::get_user_fields_list();

		foreach ( $user_fields as $user_field ) {
			if ( isset( $ufs[ $user_field['id'] ] ) && $ufs[ $user_field['id'] ] ) {
				$sql_search .= $this->get_field_search( $user_field, $ufs[ $user_field['id'] ], $event_id );
			}
		}

		return $sql_search;
	}

	public function get_additional_field_search( $event_id, $ufs ) {
		global $wpdb;

		if ( ! $event_id ) {
			return;
		}

		$sql_search = '';

		$additional_fields = Additional_Fields::get_additional_fields( $event_id );

		foreach ( $additional_fields as $additional_field ) {
			if ( isset( $ufs[ $additional_field['id'] ] ) && $ufs[ $additional_field['id'] ] ) {
				$sql_search .= $this->get_field_search( $additional_field, $ufs[ $additional_field['id'] ], $event_id );
			}
		}

		return $sql_search;
	}

	public function get_field_search( $field, $search, $event_id ) {
		global $wpdb;

		switch ( $field['type'] ) {
			case 'text':
			case 'email':
				return ' AND (SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND post_id = ID) LIKE '" . $search . "'";
			break;
			case 'textarea':
				return ' AND (SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND post_id = ID) LIKE '%" . $search . "%'";
			break;
			case 'date':
				if ( $search['start'] && $search['end'] ) {
					return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value >= '" . $search['start'] . "' AND meta_value <= '" . $search['end'] . "' AND post_id = ID) > 0";
				} else if ( $search['start'] && ! $search['end'] ) {
					return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value >= '" . $search['start'] . "' AND post_id = ID) > 0";
				} else if ( ! $search['start'] && $search['end'] ) {
					return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value <= '" . $search['end'] . "' AND post_id = ID) > 0";
				}
			break;
			case 'checkbox':
				if ( is_array( $field['options'] ) ) {
					return $this->get_registration_checkbox( $event_id, $field, $search );
				} else {
					if ( 1 == $search ) {
						return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value = '1' AND post_id = ID) < 1";
					} else if ( 2 == $search ) {
						return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value = '1' AND post_id = ID) > 0";
					}
				}
			break;
			case 'radio':
				return ' AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND meta_value = '" . $search . "' AND post_id = ID) > 0";
			break;
			case 'table':
				return $this->get_registrations_table( $event_id, $field, $search );
				break;
		}

		return;
	}

	public function get_registrations_table( $event_id, $field, $search ) {
		global $wpdb;

		$registrations = $wpdb->get_results(
			'
			SELECT ID,
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND post_id = ID) field
			FROM " . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				and (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' and post_id = ID) = '" . $event_id . "'
				and post_status = 'publish'
				" . '
			', ARRAY_A
		);

		if ( ! $registrations ) {
			return;
		}

		$good = array();

		foreach ( $registrations as $key => $reg ) {
			$ok = 1;

			$field = maybe_unserialize( $reg['field'] );

			foreach ( $search as $row => $cols ) {
				foreach ( $cols as $col => $val ) {
					if ( 1 == $val ) {
						if ( isset( $field[ $row ][ $col ] ) && 1 == $field[ $row ][ $col ] ) {
							$ok = 0;
						}
					} else if ( 2 == $val ) {
						if ( ! isset( $field[ $row ][ $col ] ) || ! 1 == $field[ $row ][ $col ] ) {
							$ok = 0;
						}
					}
				}
			}

			if ( $ok ) {
				$good[] = $reg['ID'];
			}
		}

		$sql = ' AND ID IN ( 0';

		if ( count( $good ) ) {
			foreach ( $good as $id ) {
				$sql .= ', ' . $id;
			}
		}

		$sql .= ')';

		return $sql;
	}

	public function get_registration_checkbox( $event_id, $field, $search ) {
		global $wpdb;

		$registrations = $wpdb->get_results(
			'
			SELECT ID,
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $field['id'] . "' AND post_id = ID) field
			FROM " . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				and (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' and post_id = ID) = '" . $event_id . "'
				and post_status = 'publish'
				" . '
			', ARRAY_A
		);

		if ( ! $registrations ) {
			return;
		}

		$good = array();

		foreach ( $registrations as $key => $reg ) {
			$ok = 1;

			$checkbox = maybe_unserialize( $reg['field'] );
			foreach ( $search as $s => $val ) {
				if ( 1 == $val ) {
					if ( is_array( $checkbox ) && in_array( $s, $checkbox ) ) {
						$ok = 0;
					}
				} else if ( 2 == $val ) {
					if ( ! is_array( $checkbox ) || ! in_array( $s, $checkbox ) ) {
						$ok = 0;
					}
				}
			}

			if ( $ok ) {
				$good[] = $reg['ID'];
			}
		}

		$sql = ' AND ID IN ( 0';

		if ( count( $good ) ) {
			foreach ( $good as $id ) {
				$sql .= ', ' . $id;
			}
		}

		$sql .= ')';

		return $sql;
	}

	public function count_registrations( $event_id, $mode = 'all', $sql_search = '' ) {
		global $wpdb;

		$sql = '';

		if ( 'paid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' and meta_value = 1 and post_id = ID
		) > 0";
		} else if ( 'notpaid' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'paid' and meta_value = 1 and post_id = ID) < 1";
		}

		return $wpdb->get_var(
			'
			SELECT count(*)
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				and (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' and post_id = ID) = '" . $event_id . "'
				and post_status = 'publish'
			" . $sql . $sql_search
		);
	}

	public function new_filter_save() {
		$post = array(
			'post_title' => $_POST['filter_name'],
			'post_type' => 'wpep-filter',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
		);

		$post_id = wp_insert_post( $post );

		$response = array();

		if ( $post_id ) {
			add_post_meta( $post_id, 'ufs', $_POST['ufs'] );

			$response = array(
				'success' => 1,
				'redirect' => 'edit.php?post_type=wpep&page=filters&filter_id=' . $post_id,
			);
		}

		$response_json = json_encode( $response );
		header( 'Content-Type: application/json' );
		echo $response_json;
		exit;
	}

	public function get_filters() {
		global $wpdb;

		$filters = $wpdb->get_results(
			'
			SELECT ID, post_title as title
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-filter'
				AND post_status = 'publish'
			ORDER BY post_title
			", ARRAY_A
		);

		return $filters;
	}

	public function filter_actions() {
		$action = $_POST['a'];

		$response = array();

		if ( 'delete' == $action ) {
			wp_delete_post( $_POST['filter_id'] );

			$response = array(
				'success' => 1,
				'redirect' => 'edit.php?post_type=wpep&page=filters&filter_id=0',
			);

			unset( $_SESSION['ufs'] );
		} else if ( 'save' == $action ) {
			update_post_meta( $_POST['filter_id'], 'ufs', $_POST['ufs'] );

			$response = array(
				'success' => 1,
				'redirect' => 'edit.php?post_type=wpep&page=filters&filter_id=' . $_POST['filter_id'],
			);
		}

		$response_json = json_encode( $response );
		header( 'Content-Type: application/json' );
		echo $response_json;
		exit;
	}
}
