<?php

namespace Wiredot\WPEP;

use Wiredot\WPEP\Templates\Single_Event;


class Shortcode {

	public function __construct() {
		add_shortcode( 'wpep-registration', array( $this, 'show_shortcode' ) );
	}

	public function show_shortcode( $atts ) {
		if ( is_array( $atts ) && array_key_exists( 'id', $atts ) ) {
			$Single_Event = new Single_Event;
			return $Single_Event->show_event_registration( $atts['id'] );
		}
	}
}
