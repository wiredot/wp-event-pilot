<?php

$config['custom_post_type']['wpep-form'] = array(
	'active' => true,
	'labels' => array(
		'name'               => __( 'Forms', 'wpep' ),
		'singular_name'      => __( 'Form', 'wpep' ),
		'menu_name'          => _x( 'Forms', 'admin menu', 'wpep' ),
		'name_admin_bar'     => __( 'Forms', 'wpep' ),
		'add_new'            => _x( 'Add New Form', 'form', 'wpep' ),
		'add_new_item'       => __( 'Add New Form', 'wpep' ),
		'new_item'           => __( 'New Form', 'wpep' ),
		'edit_item'          => __( 'Edit Form', 'wpep' ),
		'view_item'          => __( 'View Form', 'wpep' ),
		'all_items'          => __( 'Forms', 'wpep' ),
		'search_items'       => __( 'Search Form', 'wpep' ),
		'not_found'          => __( 'No Form found.', 'wpep' ),
		'not_found_in_trash' => __( 'No Form found in Trash.', 'wpep' ),
		'featured_image'     => __( 'Form Cover', 'wpep' ),
		'remove_featured_image'  => __( 'Remove Form Cover', 'wpep' ),
		'use_featured_image'     => __( 'Use as Form Cover', 'wpep' ),
		'set_featured_image'     => __( 'Set Form Cover', 'wpep' ),
	),
	'messages' => array(
		1  => __( 'Form updated.', 'wpep' ),
		2  => __( 'Form updated.', 'wpep' ),
		3  => __( 'Form deleted.', 'wpep' ),
		4  => __( 'Form updated.', 'wpep' ),
		5  => __( 'Form restored to revision from %revision%', 'wpep' ),
		6  => __( 'Form published.', 'wpep' ),
		7  => __( 'Form saved.', 'wpep' ),
		8  => __( 'Form submitted.', 'wpep' ),
		9  => __( 'Form scheduled for: <strong>%date%</strong>.', 'wpep' ),
		10 => __( 'Form draft updated.', 'wpep' ),
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
	'rewrite' => array( 'slug' => 'form' ),
	'query_var' => true,
	'can_export' => true,
	'delete_with_user' => true,
	'custom_menu_icon' => 'assets/images/wpep.svg',
);

$config['meta_box']['post']['wpep-form-fields'] = array(
	'active' => true,
	'name' => __( 'Form Fields', 'wpep' ),
	'post_type' => array( 'wpep-form' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'fields' => array(
			'type' => 'group',
			'label' => __( 'Fields', 'wpep' ),
			'fields' => array(
				'name' => array(
					'type' => 'text',
					'label' => __( 'Name', 'wpep' ),
				),
				'fields' => array(
					'type' => 'group',
					'label' => __( 'Fields', 'wpep' ),
					'fields' => array(
						'type' => array(
							'type' => 'select',
							'label' => __( 'Type', 'wpep' ),
							'options' => array(
								'text' => __( 'Text', 'wpep' ),
								'email' => __( 'E-Mail', 'wpep' ),
								'date' => __( 'Date', 'wpep' ),
								'textarea' => __( 'Textarea', 'wpep' ),
								'checkbox' => __( 'Checkboxes', 'wpep' ),
							),
						),
						'id' => array(
							'type' => 'text',
							'label' => __( 'ID', 'wpep' ),
							'condition' => array(
								'type' => array( 'text', 'email', 'date', 'textarea', 'checkbox' ),
							),
						),
						'label' => array(
							'type' => 'text',
							'label' => __( 'Label', 'wpep' ),
							'condition' => array(
								'type' => array( 'text', 'email', 'date', 'textarea', 'checkbox' ),
							),
						),
						'options' => array(
							'type' => 'group',
							'label' => __( 'Options', 'wpep' ),
							'condition' => array(
								'type' => 'checkbox',
							),
							'fields' => array(
								'id' => array(
									'type' => 'text',
									'label' => __( 'ID', 'wpep' ),
								),
							),
						),
					),
				),
			),
		),
	),
);
