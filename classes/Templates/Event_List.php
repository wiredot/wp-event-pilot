<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

class Event_List {

	public function __construct() {
		add_shortcode( 'wpep-event-list', array( $this, 'event_list' ) );
	}

	public function event_list() {
		$Twig = new Twig();
		echo $Twig->twig->render(
			'front/event_list.twig', array()
		);
	}

	public function get_events() {
		
	}
}
