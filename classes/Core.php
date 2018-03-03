<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Core as Preamp;
use Wiredot\WPEP\Registrations\Registration_List;
use Wiredot\WPEP\Forms\Form_Factory;
use Wiredot\WPEP\Admin\Admin;
use Wiredot\WPEP\Templates\Login;
use Wiredot\WPEP\Templates\Registration;
use Wiredot\WPEP\Templates\Account;
use Wiredot\WPEP\Templates\Event_List;
use Wiredot\WPEP\Templates\Single_Event;

class Core {

	private static $instance = null;

	private function __construct() {
		if ( ! session_id() ) {
			session_start();
		}

		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		Preamp::run( WPEP_URL, WPEP_DIR );

		if ( is_admin() ) {
			new Admin();
			new Support();
			new Addons();
			new Registration_List();
			new Welcome();
		}

		new User_Fields();
		new Form_Factory();
		new Login();
		new Registration();
		new Account();
		new Event_List();
		new Single_Event();
		new Event_Registration();
		new Additional_Fields();
		new Event();
	}

	public static function run() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Core ) ) {
			self::$instance = new Core;
		}
		return self::$instance;
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			WPEP_TEXT_DOMAIN,
			false,
			dirname( WPEP_BASENAME ) . '/languages/'
		);
	}
}
