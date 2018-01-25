<?php

$config['custom_post_type']['wpep-participant'] = array(
	'active' => true,
	'labels' => array(
		'name'               => __( 'Participants', 'wpep' ),
		'singular_name'      => __( 'Participant', 'wpep' ),
		'menu_name'          => _x( 'Participants', 'admin menu', 'wpep' ),
		'name_admin_bar'     => __( 'Participants', 'wpep' ),
		'add_new'            => _x( 'Add New Participant', 'participant', 'wpep' ),
		'add_new_item'       => __( 'Add New Participant', 'wpep' ),
		'new_item'           => __( 'New Participant', 'wpep' ),
		'edit_item'          => __( 'Edit Participant', 'wpep' ),
		'view_item'          => __( 'View Participant', 'wpep' ),
		'all_items'          => __( 'Participants', 'wpep' ),
		'search_items'       => __( 'Search Participant', 'wpep' ),
		'not_found'          => __( 'No Participant found.', 'wpep' ),
		'not_found_in_trash' => __( 'No Participant found in Trash.', 'wpep' ),
		'featured_image'     => __( 'Participant Cover', 'wpep' ),
		'remove_featured_image'  => __( 'Remove Participant Cover', 'wpep' ),
		'use_featured_image'     => __( 'Use as Participant Cover', 'wpep' ),
		'set_featured_image'     => __( 'Set Participant Cover', 'wpep' ),
	),
	'messages' => array(
		1  => __( 'Participant updated.', 'wpep' ),
		2  => __( 'Participant updated.', 'wpep' ),
		3  => __( 'Participant deleted.', 'wpep' ),
		4  => __( 'Participant updated.', 'wpep' ),
		5  => __( 'Participant restored to revision from %revision%', 'wpep' ),
		6  => __( 'Participant published.', 'wpep' ),
		7  => __( 'Participant saved.', 'wpep' ),
		8  => __( 'Participant submitted.', 'wpep' ),
		9  => __( 'Participant scheduled for: <strong>%date%</strong>.', 'wpep' ),
		10 => __( 'Participant draft updated.', 'wpep' ),
	),
	'description' => __( 'Description.', 'wpep' ),
	'public' => true,
	'hierarchical' => false,
	'exclude_from_search' => true,
	'publicly_queryable' => false,
	'show_ui' => true,
	'show_in_menu' => 'edit.php?post_type=wpep',
	'show_in_nav_menus' => true,
	'show_in_admin_bar' => true,
	'menu_position' => null,
	'menu_icon' => 'dashicons-admin-post',
	'capability_type' => 'post',
	// 'capabilities' => array('edit_posts'),
	// 'map_meta_cap' => false,
	'supports' => array( null ), // 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	// 'register_meta_box_cb' => true,
	// 'taxonomies' => array(),
	// 'has_archive' => false,
	'rewrite' => array( 'slug' => 'participant' ),
	'query_var' => true,
	'can_export' => true,
	'delete_with_user' => true,
	'custom_menu_icon' => 'assets/images/wpep.svg',
);

$config['meta_box']['post']['wpep-participant-details'] = array(
	'active' => true,
	'name' => __( 'Personal Details', 'wpep' ),
	'post_type' => array( 'wpep-participant' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'first-name' => array(
			'type' => 'text',
			'label' => __( 'First Name', 'wpep' ),
		),
		'last-name' => array(
			'type' => 'text',
			'label' => __( 'Last Name', 'wpep' ),
			'size' => 'regular',
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'E-mail', 'wpep' ),
			'size' => 'regular',
		),
	),
);

$config['meta_box']['post']['wpep-participant-address'] = array(
	'active' => true,
	'name' => __( 'Address', 'wpep' ),
	'post_type' => array( 'wpep-participant' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'address' => array(
			'type' => 'text',
			'label' => __( 'Address', 'wpep' ),
			'size' => 'regular',
		),
		'addressb' => array(
			'type' => 'text',
			'label' => __( 'Address line 2', 'wpep' ),
			'size' => 'regular',
		),
		'city' => array(
			'type' => 'text',
			'label' => __( 'City', 'wpep' ),
		),
		'zip' => array(
			'type' => 'text',
			'label' => __( 'Postal Code / Zip', 'wpep' ),
		),
		'country' => array(
			'type' => 'text',
			'label' => __( 'Country', 'wpep' ),
		),
	),
);

$config['admin_custom_columns']['participant'] = array(
	'post_type' => 'wpep-participant',
	'columns' => array(
		'title',
		'date',
	),
);
