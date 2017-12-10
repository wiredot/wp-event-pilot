<?php

namespace Wiredot\WPEP\DB;

class DB_Registration extends Db {

	private $table_name;
	private $primary_key;
	private $version;

	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . 'wpep_registrations';
		$this->primary_key = 'ID';
		$this->version     = '1.0';
	}

	public function get_columns() {
		return array(
			'ID' => '%d',
			'participant_id' => '%d',
			'event_id' => '%d',
			'first_name' => '%s',
			'last_name' => '%s',
			'email' => '%s',
			'date_created' => '%s',
		);
	}

	public function get_column_defaults() {
		return array(
			'participant_id' => 0,
			'event_id' => 0,
			'first_name' => '',
			'last_name' => '',
			'email' => '',
			'date_created' => date( 'Y-m-d H:i:s' ),
		);
	}

	public function create_table() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = 'CREATE TABLE ' . $this->table_name . ' (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			participant_id bigint(20) NOT NULL,
			event_id bigint(20) NOT NULL,
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			email varchar(255) NOT NULL,
			date_created datetime NOT NULL,
			PRIMARY KEY  (' . $this->primary_key . ')
		) CHARACTER SET utf8 COLLATE utf8_general_ci;';

		$s = dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
