<?php
/**
 * Plugin Name: WP Event Pilot
 * Plugin URI:  https://wiredot.com/wp-event-pilot/
 * Description: Event Management
 * Author: WireDot Labs
 * Version: 1.0.0
 * Text Domain: wp-event-pilot
 * Domain Path: /languages
 * Author URI: https://wiredot.com/labs/
 * License: GPLv2 or later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// load composer libraries
require __DIR__ . '/vendor/autoload.php';
