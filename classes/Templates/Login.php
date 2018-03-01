<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;

class Login {

	public function __construct() {
		add_shortcode( 'wpep-login-form', array( $this, 'login_form' ) );
	}

	public function login_form() {
		$Twig = new Twig();
		echo $Twig->twig->render(
			'front/login.twig', array()
		);
	}
}
