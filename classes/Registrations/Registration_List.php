<?php

namespace Wiredot\WPEP\Registrations;

use Wiredot\Preamp\Twig;

class Registration_List {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	public function add_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'WP Event Pilot Registrations', 'wpep' ),
			__( 'Registrations', 'wpep' ),
			'manage_options',
			'wpep_registrations',
			array( $this, 'registrations_page' )
		);
	}

	public function registrations_page() {
		$List_Table = new Registration_List_Table();
		$List_Table->prepare_items();
		// $List_Table->display();

		$Twig = new Twig;
		echo $Twig->twig->render(
			'registrations/list.twig',
			array(
				'registrations' => $this->get_registrations(),
				'List_Table' => $List_Table,
			)
		);
	}

	public function get_registrations() {
		global $wpdb;

		$registrations = $wpdb->get_results(
			'
			SELECT *
			FROM ' . $wpdb->prefix . 'wpep_registrations
		', ARRAY_A
		);

		return $registrations;
	}
}
