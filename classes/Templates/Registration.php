<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

class Registration {

	public function __construct() {
		add_shortcode( 'wpep-registration-form', array( $this, 'registration_form' ) );
	}

	public function registration_form() {
		$Twig = new Twig();

		echo $Twig->twig->render(
			'front/registration.twig', array()
		);
	}
}

