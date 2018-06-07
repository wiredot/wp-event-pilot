<?php

$config['js']['wpep-front'] = array(
	'footer' => true,
	'front' => true,
	'admin' => false,
	'dependencies' => array( 'jquery' ),
	'files' => array(
		'semantic-ui' => 'assets/bower/semantic-ui/dist/semantic.min.js',
		'wd-alerts' => 'assets/bower/wd-scripts/dist/jquery.wd-alerts.js',
		'wd-forms' => 'assets/bower/wd-scripts/dist/jquery.wd-forms.js',
		'wpep-front' => 'assets/js/wpep.js',
	),
	'dev_files' => array(),
);

$config['js']['wpep-admin'] = array(
	'footer' => true,
	'front' => false,
	'admin' => true,
	'dependencies' => array( 'jquery' ),
	'files' => array(
		'wd-alerts' => 'assets/bower/wd-scripts/dist/jquery.wd-alerts.js',
		'wd-forms' => 'assets/bower/wd-scripts/dist/jquery.wd-forms.js',
		'wpep-admin' => 'assets/js/wpep-admin.js',
	),
	'dev_files' => array(),
);
