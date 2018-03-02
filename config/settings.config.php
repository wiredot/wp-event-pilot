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
		'login_page' => array(
			'type' => 'post',
			'label' => __( 'Log-in page', 'wpep' ),
			'attributes' => array(
				'post_type' => 'page',
			),
		),
		'register_page' => array(
			'type' => 'post',
			'label' => __( 'Registration page', 'wpep' ),
			'attributes' => array(
				'post_type' => 'page',
			),
		),
		'account_page' => array(
			'type' => 'post',
			'label' => __( 'Account page', 'wpep' ),
			'attributes' => array(
				'post_type' => 'page',
			),
		),
	),
);

$config['settings']['section']['user_field_section'] = array(
	'tab' => 'user_field_tab',
	'active' => true,
	'name' => __( 'User Field Settings', 'wpep' ),
	'description' => __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.', 'wpep' ),
	'fields' => array(
		'fields' => array(
			'type' => 'group',
			'label' => __( 'Fields', 'wpep' ),
			'fields' => array(
				'name' => array(
					'type' => 'text',
					'label' => __( 'Title', 'wpep' ),
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
		),
	),
);
