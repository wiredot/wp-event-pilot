<?php

namespace Wiredot\WPEP\Registrations;

use Wiredot\Preamp\Twig;

class Registration_List {

	public function __construct() {
		// add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	public function add_submenu_page() {
		$page_hook = add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'WP Event Pilot Registrations', 'wpep' ),
			__( 'Registrations', 'wpep' ),
			'manage_options',
			'wpep_registrations',
			array( $this, 'registrations_page' )
		);

		add_action( 'load-' . $page_hook, array( $this, 'load_user_list_table_screen_options' ) );
	}

	public function load_user_list_table_screen_options() {
		$arguments = array(
			'label'     => __( 'Users Per Page', 'wpep' ),
			'default'   => 5,
			'option'    => 'users_per_page',
		);
		add_screen_option( 'per_page', $arguments );

		$List_Table = new Registration_List_Table();

		/*
		 * Instantiate the User List Table. Creating an instance here will allow the core WP_List_Table class to automatically
         * load the table columns in the screen options panel
		 */
		// $this->user_list_table = new User_List_Table( $this->plugin_text_domain );
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
