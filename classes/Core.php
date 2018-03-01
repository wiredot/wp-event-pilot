<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Core as Preamp;
use Wiredot\WPEP\Registrations\Registration_List;
use Wiredot\WPEP\Forms\Form_Factory;
use Wiredot\WPEP\Admin\Admin;
use Wiredot\WPEP\Templates\Login;
use Wiredot\WPEP\Templates\Registration;
use Wiredot\WPEP\Templates\Account;

class Core {

	private static $instance = null;

	private function __construct() {
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
