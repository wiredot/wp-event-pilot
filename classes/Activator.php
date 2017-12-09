<?php

namespace Wiredot\WPEP;

use Wiredot\WPEP\DB\DB_Registration;

class Activator {

	public static function activate() {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				self::run_install();
				restore_current_blog();
			}
		} else {
			self::run_install();
		}
	}

	public static function run_install() {
		$Db_Registration = new Db_Registration;
		$Db_Registration->create_table();
	}
}
