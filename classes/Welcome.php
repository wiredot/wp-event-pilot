<?php

namespace Wiredot\WPEP;

use Wiredot\Preamp\Twig;

class Welcome {

	public function __construct() {
		// add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_row_meta' ), 10, 2 );
	}

	public function add_admin_menus() {
		add_submenu_page(
			'edit.php?post_type=wpep',
			__( 'Getting Started', 'wpep' ),
			__( 'Getting Started', 'wpep' ),
			'read',
			'getting_started',
			array( $this, 'welcome_page' )
		);
	}

	public function welcome_page() {
		if ( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		} else {
			$tab = 'getting-started';
		}

		$Twig = new Twig();
		echo $Twig->twig->render(
			'welcome.twig', array(
				'tab' => $tab,
			)
		);
	}

	public function add_row_meta( $links, $file ) {
		// run for this plugin
		if ( WPEP_BASENAME == $file ) {
			// settings link
			$links[] = "<a href='edit.php?post_type=wpep&page=getting_started'>" . __( 'Getting Started', 'wpep' ) . '</a>';
			$links[] = "<a href='edit.php?post_type=wpep&page=wpep-support'>" . __( 'Support', 'wpep' ) . '</a>';
		}
		return $links;
	}
}
