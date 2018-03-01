<?php

$config['css']['wpep-admin'] = array(
	'front' => false,
	'admin' => true,
	'media' => 'all',
	'dependencies' => array(),
	'version' => null,
	'files' => array(
		'wpep-admin' => 'assets/css/wpep.css',
	),
	'dev_files' => array(),
);

$config['css']['wpep-front'] = array(
	'front' => true,
	'admin' => false,
	'media' => 'all',
	'dependencies' => array(),
	'version' => null,
	'files' => array(
		'semantic-ui' => 'assets/bower/semantic-ui/dist/semantic.min.css',
	),
	'dev_files' => array(),
);
