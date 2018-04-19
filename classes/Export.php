<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;
use Wiredot\WPEP\Additional_Fields;
use Wiredot\WPEP\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Export {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'export_admin_menu' ) );

		add_action( 'wp_ajax_wpep-excel-export', array( $this, 'excel_export' ) );
		add_action( 'wp_ajax_nopriv_wpep-excel-export', array( $this, 'excel_export' ) );
	}

	public function export_admin_menu() {
		add_submenu_page( 'edit.php?post_type=wpep', 'export', __( 'Excel Export ', 'wpep' ), 'read', 'cpf', array( $this, 'export_page' ) );
	}

	public function export_page() {
		$events = Event::get_events();

		$Twig = new Twig();
		echo $Twig->twig->render(
			'backend/export.twig', array(
				'events' => $events,
			)
		);
	}

	public function excel_export() {
		global $wpdb;

		$event_id = $_POST['event_id'];

		$columns = array(
			'email' => 'Email',
			'registration_date' => 'Date',
			'paid' => 'Paid',
			'paid_date' => 'Paid Date',
		);

		$user_fields_sql = '';

		$user_fields = User_Fields::get_user_fields_list();
		
		foreach ( $user_fields as $user_field ) {
			$columns[ $user_field['id'] ] = $user_field['label'];
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
					$columns[ $additional_field['id'] ] = $additional_field['label'];
					$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $additional_field['id'] . "' AND post_id = ID) " . $additional_field['id'] . ',';
					break;
				case 'checkbox':
					if ( ! is_array( $additional_field['options'] ) ) {
						$columns[ $additional_field['id'] ] = $additional_field['label'];
						$user_fields_sql .= '(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = '" . $additional_field['id'] . "' AND post_id = ID) " . $additional_field['id'] . ',';
					} else {
						foreach ( $additional_field['options'] as $option ) {
							$columns[ $additional_field['id'] . '_' . $option['id'] ] = $option['label'];
						}
					}
					break;
			}
		}
		// print_r($additional_fields);
		// exit;

		$registrations = $wpdb->get_results(
			'
			SELECT ID, 
				(SELECT meta_value FROM ' . $wpdb->postmeta . " WHERE meta_key = 'email' AND post_id = ID) email,
				(SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'paid' AND post_id = ID) paid,
				(SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'paid_date' AND post_id = ID) paid_date,
				" . $user_fields_sql . '
				post_date registration_date 
			FROM ' . $wpdb->posts . "
			WHERE post_type = 'wpep-registration'
				AND (SELECT meta_value FROM " . $wpdb->postmeta . " WHERE meta_key = 'event_id' AND post_id = ID) = '" . $event_id . "' 
				AND post_status = 'publish'
			ORDER BY post_date DESC
			", ARRAY_A
		);

		foreach ( $registrations as $key => $reg ) {
			foreach ( $additional_fields as $additional_field ) {
				switch ( $additional_field['type'] ) {
					case 'checkbox':
						if ( is_array( $additional_field['options'] ) ) {
							$values = get_post_meta( $reg['ID'], $additional_field['id'], true );
							foreach ( $values as $value ) {
								$registrations[$key][$additional_field['id'] . '_' . $value] = 1;
							}
						}
						break;
				}
			}
		}

		$post = get_post( $event_id );
		$excel_name = $post->post_name . '_' . date( 'Y-m-d' );

		$this->create_excel( $registrations, $columns, $excel_name );

		print_r( $registrations );
		exit;
	}

	public function create_excel( $data, $columns, $filename = 'spreadsheet' ) {
		$spreadsheet = new Spreadsheet();

		$spreadsheet->setActiveSheetIndex( 0 );

		$header = $data[0];

		for ( $i = 'A'; $i < 'ZZ'; $i++ ) {
			$alphabet[] = $i;
		}

		$column_index = array();

		$i = 0;

		foreach ( $columns as $column_key => $name ) {

			$column_index[ $column_key ] = $alphabet[ $i ];
			$spreadsheet->getActiveSheet()->setCellValue( $alphabet[ $i ] . '1', $name );
			$i++;
		}

		foreach ( $data as $rownr => $row ) {
			foreach ( $row as $column => $cell ) {
				if ( isset( $column_index[ $column ] ) ) {
					$spreadsheet->getActiveSheet()->setCellValue( $column_index[ $column ] . ($rownr + 2), $cell );
				}
			}
		}

		// Redirect output to a client’s web browser (Xlsx)
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="' . $filename . '.xlsx"' );
		header( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header( 'Cache-Control: max-age=1' );
		// If you're serving to IE over SSL, then the following may be needed
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header( 'Pragma: public' ); // HTTP/1.0
		$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
		$writer->save( 'php://output' );
		exit;
	}
}
