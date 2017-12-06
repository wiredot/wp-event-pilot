<?php

$config['settings']['page']['wpep_settings'] = array(
	'active' => true,
	'parent_slug' => 'edit.php?post_type=wpep',
	'page_title' => __( 'WP Event Pilot Settings', 'wpep' ),
	'menu_title' => __( 'Settings', 'wpep' ),
	'capability' => 'manage_options',
	'options_prefix' => 'wpep_settings_',
);

$config['settings']['tab']['general_tab'] = array(
	'page' => 'wpep_settings',
	'active' => true,
	'name' => __( 'General', 'wpep' ),
);

$config['settings']['tab']['user_field_tab'] = array(
	'page' => 'wpep_settings',
	'active' => true,
	'name' => __( 'User Fields', 'wpep' ),
);

$config['settings']['section']['general_section'] = array(
	'tab' => 'general_tab',
	'active' => true,
	'name' => __( 'General Settings', 'wpep' ),
	'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.', 'wpep' ),
	'fields' => array(
		'event-type' => array(
			'type' => 'select',
			'label' => __( 'Event Type', 'wpep' ),
			'options' => array(
				1 => __( 'One day event', 'wpep' ),
				2 => __( 'Multi-day event', 'wpep' ),
			),
		),
		'event-box' => array(
			'type' => 'checkbox',
			'label' => __( 'Event Type', 'wpep' ),
			'options' => array(
				1 => __( 'One day event', 'wpep' ),
				2 => __( 'Multi-day event', 'wpep' ),
			),
			'autoload' => true,
		),
		'third' => array(
			'type' => 'text',
			'label' => __( 'Text Type', 'wpep' ),
			'condition' => array(
				'event-type' => 2,
			),
		),
	),
);
