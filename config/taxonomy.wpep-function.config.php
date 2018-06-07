<?php

$config['taxonomy']['wpep-function'] = array(
	'active' => true,
	'post_type' => 'wpep-registration',
	'args' => array(
		'label' => __( 'Function', 'wpep' ),
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => false,
		'show_tagcloud' => false,
		'show_admin_column' => true,
		'description' => __( 'Function', 'wpep' ),
		'hierarchical' => true,
		'rewrite' => array( 'slug' => 'wpep-function' ),
	),
);
