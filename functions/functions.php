<?php 

use Wiredot\WPEP\Session;

function wpep_is_logged_in() {
	return Session::is_logged_in();
}