<?php

$config['custom_post_type']['wpep-registration'] = array(
	'active' => true,
	'labels' => array(
		'name'               => __( 'Registrations', 'wpep' ),
		'singular_name'      => __( 'Registration', 'wpep' ),
		'menu_name'          => _x( 'Registrations', 'admin menu', 'wpep' ),
		'name_admin_bar'     => __( 'Registrations', 'wpep' ),
		'add_new'            => _x( 'Add New Registration', 'event', 'wpep' ),
		'add_new_item'       => __( 'Add New Registration', 'wpep' ),
		'new_item'           => __( 'New Registration', 'wpep' ),
		'edit_item'          => __( 'Edit Registration', 'wpep' ),
		'view_item'          => __( 'View Registration', 'wpep' ),
		'all_items'          => __( 'Registrations', 'wpep' ),
		'search_items'       => __( 'Search Registration', 'wpep' ),
		'not_found'          => __( 'No Registration found.', 'wpep' ),
		'not_found_in_trash' => __( 'No Registration found in Trash.', 'wpep' ),
		'featured_image'     => __( 'Registration Cover', 'wpep' ),
		'remove_featured_image'  => __( 'Remove Registration Cover', 'wpep' ),
		'use_featured_image'     => __( 'Use as Registration Cover', 'wpep' ),
		'set_featured_image'     => __( 'Set Registration Cover', 'wpep' ),
	),
	'messages' => array(
		1  => __( 'Registration updated.', 'wpep' ),
		2  => __( 'Registration updated.', 'wpep' ),
		3  => __( 'Registration deleted.', 'wpep' ),
		4  => __( 'Registration updated.', 'wpep' ),
		5  => __( 'Registration restored to revision from %revision%', 'wpep' ),
		6  => __( 'Registration published.', 'wpep' ),
		7  => __( 'Registration saved.', 'wpep' ),
		8  => __( 'Registration submitted.', 'wpep' ),
		9  => __( 'Registration scheduled for: <strong>%date%</strong>.', 'wpep' ),
		10 => __( 'Registration draft updated.', 'wpep' ),
	),
	'description' => __( 'Description.', 'wpep' ),
	'public' => true,
	'hierarchical' => false,
	'exclude_from_search' => false,
	'publicly_queryable' => true,
	'show_ui' => true,
	'show_in_menu' => 'edit.php?post_type=wpep',
	'show_in_nav_menus' => true,
	'show_in_admin_bar' => true,
	'menu_position' => null,
	'menu_icon' => 'dashicons-admin-post',
	'capability_type' => 'post',
	// 'capabilities' => array('edit_posts'),
	// 'map_meta_cap' => false,
	'supports' => array( 'title', 'author' ), // 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	// 'register_meta_box_cb' => true,
	// 'taxonomies' => array(),
	// 'has_archive' => false,
	'rewrite' => array( 'slug' => 'registration' ),
	'query_var' => true,
	'can_export' => true,
	'delete_with_user' => true,
	// 'custom_menu_icon' => 'assets/images/wpep.svg',
);

$config['meta_box']['post']['wpep-registration-details'] = array(
	'active' => true,
	'name' => __( 'Details', 'wpep' ),
	'post_type' => array( 'wpep-registration' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'paid' => array(
			'type' => 'checkbox',
			'label' => __( 'Paid', 'wpep' ),
		),
		'paid_date' => array(
			'type' => 'date',
			'label' => __( 'Paid Date', 'wpep' ),
		),
		'event_id' => array(
			'type' => 'post',
			'arguments' => array(
				'post_type' => 'wpep',
				'post_status' => 'publish',
			),
			'label' => __( 'Event', 'wpep' ),
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'E-Mail', 'wpep' ),
		),
	),
);

$config['admin_custom_columns']['wpep-registration'] = array(
	'post_type' => 'wpep-registration',
	'columns' => array(
		'title',
		'event_id',
		'paid',
	),
);
