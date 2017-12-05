<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Core as Preamp;

class Core {

	private static $instance = null;

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		$Preamp = Preamp::run( WPEP_URL );

		if ( is_admin() ) {
			new Settings();
			new Support();
			new Addons();
			new Welcome();
		}
	}

	public static function run() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Core ) ) {
			self::$instance = new Core;
		}
		return self::$instance;
	}

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wpep',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
