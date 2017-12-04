<?php

$config['custom_post_type']['wpep-attribute'] = array(
	'active' => true,
	'labels' => array(
		'name'               => __( 'Attributes', 'wpep' ),
		'singular_name'      => __( 'Attribute', 'wpep' ),
		'menu_name'          => _x( 'Attributes', 'admin menu', 'wpep' ),
		'name_admin_bar'     => __( 'Attributes', 'wpep' ),
		'add_new'            => _x( 'Add New Attribute', 'attribute', 'wpep' ),
		'add_new_item'       => __( 'Add New Attribute', 'wpep' ),
		'new_item'           => __( 'New Attribute', 'wpep' ),
		'edit_item'          => __( 'Edit Attribute', 'wpep' ),
		'view_item'          => __( 'View Attribute', 'wpep' ),
		'all_items'          => __( 'Attributes', 'wpep' ),
		'search_items'       => __( 'Search Attribute', 'wpep' ),
		'not_found'          => __( 'No Attribute found.', 'wpep' ),
		'not_found_in_trash' => __( 'No Attribute found in Trash.', 'wpep' ),
		'featured_image'     => __( 'Attribute Cover', 'wpep' ),
		'remove_featured_image'  => __( 'Remove Attribute Cover', 'wpep' ),
		'use_featured_image'     => __( 'Use as Attribute Cover', 'wpep' ),
		'set_featured_image'     => __( 'Set Attribute Cover', 'wpep' ),
	),
	'messages' => array(
		1  => __( 'Attribute updated.', 'wpep' ),
		2  => __( 'Attribute updated.', 'wpep' ),
		3  => __( 'Attribute deleted.', 'wpep' ),
		4  => __( 'Attribute updated.', 'wpep' ),
		5  => __( 'Attribute restored to revision from %revision%', 'wpep' ),
		6  => __( 'Attribute published.', 'wpep' ),
		7  => __( 'Attribute saved.', 'wpep' ),
		8  => __( 'Attribute submitted.', 'wpep' ),
		9  => __( 'Attribute scheduled for: <strong>%date%</strong>.', 'wpep' ),
		10 => __( 'Attribute draft updated.', 'wpep' ),
	),
	'description' => __( 'Description.', 'wpep' ),
	'public' => true,
	'hierarchical' => false,
	'exclude_from_search' => true,
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
	'supports' => array( 'title' ), // 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	// 'register_meta_box_cb' => true,
	// 'taxonomies' => array(),
	// 'has_archive' => false,
	'rewrite' => array( 'slug' => 'Attribute' ),
	'query_var' => true,
	'can_export' => true,
	'delete_with_user' => true,
	'custom_menu_icon' => 'assets/images/wpep.svg',
);

$config['meta_box']['post']['wpep-attribute'] = array(
	'active' => true,
	'name' => __( 'Attribute Details', 'wpep' ),
	'post_type' => array( 'wpep-attribute' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'attribute-type' => array(
			'type' => 'select',
			'label' => __( 'Type', 'wpep' ),
			'options' => array(
				1 => __( 'Text', 'wpep' ),
				2 => __( 'Select', 'wpep' ),
				3 => __( 'Checkbox', 'wpep' ),
			),
		),
		'attribute-value' => array(
			'type' => 'text',
			'label' => __( 'Value', 'wpep' ),
			'condition' => array(
				'attribute-type' => 2,
			),
		),
	),
);

$config['admin_custom_columns']['wpep-attribute'] = array(
	'post_type' => 'wpep-attribute',
	'columns' => array(
		'title',
		'attribute-type',
		'date',
	),
);
