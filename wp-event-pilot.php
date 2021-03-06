<?php
/**
 * Plugin Name: WP Event Pilot
 * Plugin URI:  https://wiredot.com/wp-event-pilot/
 * Description: Event Management
 * Author: WireDot Labs
 * Version: 1.0.0
 * Text Domain: wpep
 * Domain Path: /languages
 * Author URI: https://wiredot.com/labs/
 * License: GPLv2 or later
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPEP', 'wp-event-pilot' );
define( 'WPEP_NS', 'wpep' );
define( 'WPEP_VERSION', '1.0.0' );
define( 'WPEP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPEP_URL', plugin_dir_url( __FILE__ ) );
define( 'WPEP_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPEP_TEXT_DOMAIN', 'wpep' );

// load composer libraries
require __DIR__ . '/vendor/autoload.php';

use Wiredot\WPEP\Core;
use Wiredot\WPEP\Activator;
use Wiredot\WPEP\Deactivator;

function wpep() {
	return Core::run();
}

wpep();

register_activation_hook( __FILE__, 'wpep_activate' );
register_deactivation_hook( __FILE__, 'wpep_deactivate' );

function wpep_activate() {
	Activator::activate();
}

function wpep_deactivate() {
	Deactivator::deactivate();
}
