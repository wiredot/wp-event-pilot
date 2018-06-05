<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;

class Id {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'id_admin_menu' ) );
		add_action( 'wp_ajax_wpep-id-export', array( $this, 'id_card_export' ) );
		add_action( 'wp_ajax_nopriv_wpep-id-export', array( $this, 'id_card_export' ) );
	}

	public function id_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wpep', 'id', __( 'Id cards ', 'wpep' ), 'read', 'id_cards', array( $this, 'id_page' ) );
	}

	public function id_page() {
		$events = Event::get_events();

		if ( isset( $_POST['event_id'] ) ) {
			$_SESSION['id_event_id'] = $_POST['event_id'];
		}

		if ( ! isset( $_SESSION['id_event_id'] ) ) {
			$_SESSION['id_event_id'] = 0;
		}

		if ( ! isset( $_GET['printed'] ) ) {
			$mode = 'all';
		} else if ( $_GET['printed'] ) {
			$mode = 'printed';
		} else {
			$mode = 'notprinted';
		}

		$Twig = new Twig();
		echo $Twig->twig->render(
			'backend/id_cards.twig', array(
				'events' => $events,
				'event_id' => $_SESSION['id_event_id'],
				'user_fields' => User_Fields::get_user_fields_list(),
				'registrations' => $this->get_registrations( $_SESSION['id_event_id'], $mode ),
				'count_all' => $this->count_registrations( $_SESSION['id_event_id'] ),
				'count_printed' => $this->count_registrations( $_SESSION['id_event_id'], 'printed' ),
				'count_notprinted' => $this->count_registrations( $_SESSION['id_event_id'], 'notprinted' ),
				'mode' => $mode,
			)
		);
	}

	public function get_registrations( $event_id, $mode = 'all' ) {
		global $wpdb;

		if ( ! $event_id ) {
			return array();
		}

		$sql = '';

		if ( 'printed' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'printed' AND meta_value = 1 AND post_id = ID) > 0";
		} else if ( 'notprinted' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'printed' AND meta_value = 1 AND post_id = ID) < 1";
		}

		$user_fields_sql = '';

		$user_fields = User_Fields::get_user_fields_list();

		foreach ( $user_fields as $user_field ) {
			$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $user_field['id'] . "' AND post_id = ID) " . $user_field['id'] . ',';
		}

		$registrations = $wpdb->get_results(
			'
			SELECT ID, post_title as title,
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = 'email' AND post_id = ID) email,
				(SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'printed' AND post_id = ID) printed,
				" . $user_fields_sql . '
				post_date registration_date 
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "' 
				AND post_status = 'publish'
				" . $sql . '
			ORDER BY post_date DESC
			', ARRAY_A
		);

		return $registrations;
	}

	public function id_card_export() {
		$Twig = new Twig();

		$posts = $_POST['post'];

		$pdf = $Twig->twig->render(
			'backend/id_cards_pdf.twig', array(
				'posts' => $posts,
			)
		);

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$this->update_unique_id( $post );
				$this->mark_as_printed( $post );
			}
		}

		// echo $pdf;
		$mpdf = new \Mpdf\Mpdf(
			[
				'mode' => 'utf-8',
				'format' => 'A4',
				'orientation' => 'L',
				'margin_left' => 0,
				'margin_right' => 0,
				'margin_top' => 0,
				'margin_bottom' => 0,
			]
		);
		$mpdf->WriteHTML( $pdf );
		$mpdf->Output();
		exit;
	}

	public function count_registrations( $event_id, $mode = 'all' ) {
		global $wpdb;

		$sql = '';

		if ( 'printed' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'printed' AND meta_value = 1 AND post_id = ID) > 0";
		} else if ( 'notprinted' == $mode ) {
			$sql = 'AND (SELECT count(*) FROM ' . $wpdb->postmeta . " WHERE meta_key = 'printed' AND meta_value = 1 AND post_id = ID) < 1";
		}

		return $wpdb->get_var(
			'
			SELECT count(*)
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "' 
				AND post_status = 'publish'
			" . $sql
		);
	}

	public function mark_as_printed( $registration_id ) {
		update_post_meta( $registration_id, 'printed', 1 );
	}

	public function update_unique_id( $registration_id ) {
		$uid = get_post_meta( $registration_id, 'id_card_uid', true );

		if ( ! $uid ) {
			update_post_meta( $registration_id, 'id_card_uid', $this->generate_unique_id() );
		}
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

		$count = $wpdb->get_var(
			$wpdb->prepare(
				'
			SELECT count(*)
			FROM ' . $wpdb->postmeta . '
			WHERE meta_key = %s
				AND meta_value = %d
		', 'id_card_uid', $id
			)
		);

		if ( $count ) {
			$id = $this->generate_unique_id();
		}

		return $id;
	}
}
