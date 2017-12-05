<?php

$config['custom_post_type']['wpep'] = array(
	'active' => true,
	'labels' => array(
		'name'               => 'WP Event Pilot',
		'singular_name'      => 'WP Event Pilot',
		'menu_name'          => _x( 'WP Event Pilot', 'admin menu', 'wpep' ),
		'name_admin_bar'     => 'WP Event Pilot',
		'add_new'            => _x( 'Add New Event', 'event', 'wpep' ),
		'add_new_item'       => __( 'Add New Event', 'wpep' ),
		'new_item'           => __( 'New Event', 'wpep' ),
		'edit_item'          => __( 'Edit Event', 'wpep' ),
		'view_item'          => __( 'View Event', 'wpep' ),
		'all_items'          => __( 'All Events', 'wpep' ),
		'search_items'       => __( 'Search Event', 'wpep' ),
		'not_found'          => __( 'No Event found.', 'wpep' ),
		'not_found_in_trash' => __( 'No Event found in Trash.', 'wpep' ),
		'featured_image'     => __( 'Event Cover', 'wpep' ),
		'remove_featured_image'  => __( 'Remove Event Cover', 'wpep' ),
		'use_featured_image'     => __( 'Use as Event Cover', 'wpep' ),
		'set_featured_image'     => __( 'Set Event Cover', 'wpep' ),
	),
	'messages' => array(
		1  => __( 'Event updated.', 'wpep' ),
		2  => __( 'Event updated.', 'wpep' ),
		3  => __( 'Event deleted.', 'wpep' ),
		4  => __( 'Event updated.', 'wpep' ),
		5  => __( 'Event restored to revision from %revision%', 'wpep' ),
		6  => __( 'Event published.', 'wpep' ),
		7  => __( 'Event saved.', 'wpep' ),
		8  => __( 'Event submitted.', 'wpep' ),
		9  => __( 'Event scheduled for: <strong>%date%</strong>.', 'wpep' ),
		10 => __( 'Event draft updated.', 'wpep' ),
	),
	'description' => __( 'Description.', 'wpep' ),
	'public' => true,
	'hierarchical' => false,
	'exclude_from_search' => false,
	'publicly_queryable' => true,
	'show_ui' => true,
	'show_in_menu' => true,
	'show_in_nav_menus' => true,
	'show_in_admin_bar' => true,
	'menu_position' => null,
	'menu_icon' => 'dashicons-admin-post',
	'capability_type' => 'post',
	// 'capabilities' => array('edit_posts'),
	// 'map_meta_cap' => false,
	'supports' => array( 'title', 'editor', 'thumbnail' ), // 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'
	// 'register_meta_box_cb' => true,
	// 'taxonomies' => array(),
	// 'has_archive' => false,
	'rewrite' => array( 'slug' => 'events' ),
	'query_var' => true,
	'can_export' => true,
	'delete_with_user' => true,
	'custom_menu_icon' => 'assets/images/wpep.svg',
);

$config['meta_box']['post']['wpep-details'] = array(
	'active' => true,
	'name' => __( 'Details', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'event-type' => array(
			'type' => 'select',
			'label' => __( 'Event Type', 'wpep' ),
			'options' => array(
				1 => __( 'One day event', 'wpep' ),
				2 => __( 'Multi-day event', 'wpep' ),
			),
		),
		'start-date' => array(
			'type' => 'date',
			'label' => __( 'Start Date', 'wpep' ),
		),
		'start-time' => array(
			'type' => 'text',
			'label' => __( 'Start Time', 'wpep' ),
			'condition' => array(
				'event-type' => 1,
			),
		),
		'end-time' => array(
			'type' => 'text',
			'label' => __( 'End Time', 'wpep' ),
			'condition' => array(
				'event-type' => 1,
			),
		),
		'end-date' => array(
			'type' => 'date',
			'label' => __( 'End Date', 'wpep' ),
			'condition' => array(
				'event-type' => 2,
			),
		),
		'form' => array(
			'type' => 'post',
			'label' => __( 'Form', 'wpep' ),
			'arguments' => array(
				'post_type' => 'wpep-form',
			),
		),
		'options' => array(
			'type' => 'checkbox',
			'label' => __( 'Options', 'wpep' ),
			'options' => array(
				'location' => __( 'Location', 'wpep' ),
				'accommodation' => __( 'Accommodation', 'wpep' ),
				'additional' => __( 'Additional Options', 'wpep' ),
			),
		),
	),
);

$config['meta_box']['post']['wpep-location'] = array(
	'active' => true,
	'name' => __( 'Location', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'location',
	),
	'fields' => array(
		'location-name' => array(
			'type' => 'text',
			'label' => __( 'Name', 'wpep' ),
		),
		'location-address' => array(
			'type' => 'text',
			'label' => __( 'Address line 1', 'wpep' ),
			'size' => 'regular',
		),
		'location-addressb' => array(
			'type' => 'text',
			'label' => __( 'Address line 2', 'wpep' ),
			'size' => 'regular',
		),
		'location-city' => array(
			'type' => 'text',
			'label' => __( 'City', 'wpep' ),
		),
		'location-zip' => array(
			'type' => 'text',
			'label' => __( 'Postal Code / Zip', 'wpep' ),
		),
		'location-country' => array(
			'type' => 'text',
			'label' => __( 'Country', 'wpep' ),
		),
	),
);

$config['meta_box']['post']['wpep-accommodation'] = array(
	'active' => true,
	'name' => __( 'Accommodation', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'accommodation',
	),
	'fields' => array(
		'accommodation' => array(
			'type' => 'group',
			'label' => __( 'Accommodation', 'wpep' ),
			'fields' => array(
				'name' => array(
					'type' => 'text',
					'label' => __( 'Name', 'wpep' ),
					'size' => 'regular',
				),
				'max-people' => array(
					'type' => 'number',
					'label' => __( 'Max nr. of People', 'wpep' ),
					'size' => 'small',
				),
				'cost' => array(
					'type' => 'text',
					'label' => __( 'Cost (per person & night)', 'wpep' ),
				),
			),
		),
	),
);

$config['meta_box']['post']['wpep-additional'] = array(
	'active' => true,
	'name' => __( 'Additional Options', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'additional',
	),
	'fields' => array(
		'additional' => array(
			'type' => 'group',
			'label' => __( 'Options', 'wpep' ),
			'fields' => array(
				'name' => array(
					'type' => 'text',
					'label' => __( 'Name', 'wpep' ),
					'size' => 'regular',
				),
				'cost' => array(
					'type' => 'text',
					'label' => __( 'Cost', 'wpep' ),
				),
			),
		),
	),
);

$config['meta_box']['post']['wpep-tickets'] = array(
	'active' => true,
	'name' => __( 'Tickets', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'fields' => array(
		'tickets-price' => array(
			'type' => 'number',
			'label' => __( 'Price', 'wpep' ),
		),
	),
);

$config['admin_custom_columns']['gallery'] = array(
	'post_type' => 'wpep',
	'columns' => array(
		'featured_image',
		'title',
		'date',
	),
);
