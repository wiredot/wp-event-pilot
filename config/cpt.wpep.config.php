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
		'all_items'          => __( 'Events', 'wpep' ),
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
		'event_type' => array(
			'type' => 'select',
			'label' => __( 'Event Type', 'wpep' ),
			'options' => array(
				1 => __( 'One day event', 'wpep' ),
				2 => __( 'Multi-day event', 'wpep' ),
			),
		),
		'full_day' => array(
			'type' => 'select',
			'label' => __( 'All Day Event', 'wpep' ),
			'options' => array(
				0 => __( 'no', 'wpep' ),
				1 => __( 'yes', 'wpep' ),
			),
		),
		'start_date' => array(
			'type' => 'date',
			'label' => __( 'Start Date', 'wpep' ),
		),
		'start_time' => array(
			'type' => 'text',
			'label' => __( 'Start Time', 'wpep' ),
			'condition' => array(
				'full_day' => 0,
			),
		),
		'end_date' => array(
			'type' => 'date',
			'label' => __( 'End Date', 'wpep' ),
			'condition' => array(
				'event_type' => 2,
			),
		),
		'end_time' => array(
			'type' => 'text',
			'label' => __( 'End Time', 'wpep' ),
			'condition' => array(
				'full_day' => 0,
			),
		),
		'website' => array(
			'type' => 'url',
			'label' => __( 'Website', 'wpep' ),
		),
		'options' => array(
			'type' => 'checkbox',
			'label' => __( 'Options', 'wpep' ),
			'options' => array(
				'organizer' => __( 'Organizer', 'wpep' ),
				'location' => __( 'Location', 'wpep' ),
				'registration' => __( 'Event Registration', 'wpep' ),
				'tickets' => __( 'Tickets', 'wpep' ),
				'accommodation' => __( 'Accommodation', 'wpep' ),
				'additional' => __( 'Additional Options', 'wpep' ),
				'additional_fields' => __( 'Additional Form Fields', 'wpep' ),
			),
		),
	),
);

$config['meta_box']['post']['wpep-organizer'] = array(
	'active' => true,
	'name' => __( 'Organizer', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'organizer',
	),
	'fields' => array(
		'organizer_name' => array(
			'type' => 'text',
			'label' => __( 'Organizer Name', 'wpep' ),
		),
		'organizer_telephone' => array(
			'type' => 'text',
			'label' => __( 'Telephone', 'wpep' ),
		),
		'organizer_email' => array(
			'type' => 'email',
			'label' => __( 'E-mail', 'wpep' ),
		),
		'organizer_website' => array(
			'type' => 'url',
			'label' => __( 'Website', 'wpep' ),
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
			'label' => __( 'Location Name', 'wpep' ),
		),
		'location-address' => array(
			'type' => 'text',
			'label' => __( 'Address', 'wpep' ),
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
		'google_map' => array(
			'type' => 'checkbox',
			'label' => __( 'Show Google Map', 'wpep' ),
		),
	),
);

$config['meta_box']['post']['wpep-registration'] = array(
	'active' => true,
	'name' => __( 'Event Registration', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'registration',
	),
	'fields' => array(
		'registration-start' => array(
			'type' => 'date',
			'label' => __( 'Registration Start Date', 'wpep' ),
		),
		'registration-end' => array(
			'type' => 'date',
			'label' => __( 'Registration End Date', 'wpep' ),
		),
	),
);

$config['meta_box']['post']['wpep-tickets'] = array(
	'active' => true,
	'name' => __( 'Tickets', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'tickets',
	),
	'fields' => array(
		'tickets-price' => array(
			'type' => 'number',
			'label' => __( 'Price', 'wpep' ),
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

$config['meta_box']['post']['wpep-additional_fields'] = array(
	'active' => true,
	'name' => __( 'Additional Form Fields', 'wpep' ),
	'post_type' => array( 'wpep' ),
	'context' => 'normal', // normal | advanced | side
	'priority' => 'high', // high | core | default | low
	'condition' => array(
		'options' => 'additional_fields',
	),
	'fields' => array(
		'additional_fields_label' => array(
			'type' => 'text',
			'label' => __( 'Label', 'wpep' ),
		),
		'additional_fields' => array(
			'type' => 'group',
			'label' => __( 'Additional Fields', 'wpep' ),
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
						'radio' => __( 'Radio', 'wpep' ),
						'select' => __( 'Select Box', 'wpep' ),
					),
				),
				'id' => array(
					'type' => 'text',
					'label' => __( 'ID', 'wpep' ),
				),
				'label' => array(
					'type' => 'text',
					'label' => __( 'Label', 'wpep' ),
				),
				'required' => array(
					'type' => 'checkbox',
					'label' => __( 'Required Field', 'wpep' ),
				),
				'options' => array(
					'type' => 'group',
					'label' => __( 'Options', 'wpep' ),
					'condition' => array(
						'type' => array( 'checkbox', 'select', 'radio' ),
					),
					'fields' => array(
						'id' => array(
							'type' => 'text',
							'label' => __( 'ID', 'wpep' ),
						),
						'label' => array(
							'type' => 'text',
							'label' => __( 'Label', 'wpep' ),
						),
					),
				),
			),
		),
	),
);

$config['admin_custom_columns']['gallery'] = array(
	'post_type' => 'wpep',
	'columns' => array(
		'featured_image',
		'title',
		'start_date',
		'end_date',
		'date',
	),
);
