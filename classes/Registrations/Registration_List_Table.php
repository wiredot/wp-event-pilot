<?php

namespace Wiredot\WPEP\Registrations;


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;

class Registration_List_Table extends WP_List_Table {

	public $per_page = 30;

	public $count = 0;

	public $total = 0;

	public $args = array();

	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct(
			array(
				'singular' => __( 'Participant', 'wpep' ),
				'plural'   => __( 'Participants', 'wpep' ),
				'ajax'     => false,
			)
		);
	}

	public function get_views() {
		$current          = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count      = '&nbsp;<span class="count">(22)</span>';
		$views = array(
			'all'        => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All','easy-digital-downloads' ) . $total_count ),
			'publish'    => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'publish', 'paged' => FALSE ) ), $current === 'publish' ? ' class="current"' : '', __('Completed','easy-digital-downloads' ) . $total_count ),
		);

		return $views;
	}

	protected function get_primary_column_name() {
		return 'name';
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}

		return $value;
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s">',
			'payment',
			$item['ID']
		);
	}

	public function column_name( $item ) {
		$edit_url    = admin_url( 'edit.php?post_type=wpep&page=wpep_registrations&view=edit&id=' . $item['ID'] );
		$delete_url    = admin_url( 'edit.php?post_type=wpep&page=wpep_registrations&view=delete&id=' . $item['ID'] );
		$name = $item['first_name'] . ' ' . $item['last_name'];

		// '<a href="'.$edit_url.'">' . $item['first_name'] . ' ' . $item['last_name'] . '</a>';
		$actions     = array(
			'view'   => '<a href="' . esc_url( $edit_url ) . '">' . __( 'Edit', 'wpep' ) . '</a>',
			'delete' => '<a href="' . esc_url( $delete_url ) . '">' . __( 'Delete', 'wpep' ) . '</a>',
		);

		return '<a href="' . esc_url( $edit_url ) . '">' . $name . '</a>' . $this->row_actions( $actions );
	}

	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox">',
			'name'          => __( 'Name', 'wpep' ),
			'email'         => __( 'Email', 'wpep' ),
			'event_id' => __( 'Event', 'wpep' ),
			'date_created'  => __( 'Date Created', 'wpep' ),
		);

		return apply_filters( 'edd_report_customer_columns', $columns );
	}

	public function get_sortable_columns() {
		return array(
			'date_created'  => array( 'date_created', true ),
			'name'          => array( 'first_name', true ),
			'email' => array( 'email', false ),
		);
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'wpep' ),
		);

		return $actions;
	}

	public function process_bulk_action() {
		$ids    = isset( $_GET['payment'] ) ? $_GET['payment'] : false;
		$action = $this->current_action();

		print_r($action);
	}

	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->get_registrations();

		$this->total = count( $this->items );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $this->total / $this->per_page ),
			)
		);
	}

	public function get_registrations() {
		global $wpdb;

		$order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'id';

		if ( 'name' == $orderby ) {
			$orderby = 'first_name ' . $order . ', last_name';
		}

		$query = '
			SELECT *
			FROM ' . $wpdb->prefix . 'wpep_registrations
		';

		$query .= 'ORDER BY ' . $orderby . ' ' . $order;

		$registrations = $wpdb->get_results(
			$query, ARRAY_A
		);

		return $registrations;
	}

	public function no_items() {
		_e( 'No users avaliable.', 'wpep' );
	}

}
