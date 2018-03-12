<?php

namespace Wiredot\WPEP;

use Wiredot\WPEP\Event;
use Wiredot\WPEP\User_Fields;
use Wiredot\Preamp\Twig;

class Backend {

	public function __construct() {
		add_action( 'restrict_manage_posts', array( $this, 'event_filter' ) );
		add_action( 'parse_query', array( $this, 'event_filter_query' ) );

		add_action( 'preamp_wpep_config', array( $this, 'add_additional_admin_columns' ) );
	}

	public function event_filter() {
		$events = Event::get_events();

		$values = array();

		$event_filters = $this->get_event_filters();

		$Twig = new Twig();
		echo $Twig->twig->render(
			'forms/filter.twig', array(
				'options' => $events,
				'values' => $event_filters,
				'id' => 'event_id',
			)
		);
	}

	public function event_filter_query( $query ) {
		global $pagenow;

		$type = 'post';
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}

		$event_filters = $this->get_event_filters();

		if ( 'wpep-registration' == $type && is_admin() && 'edit.php' == $pagenow && $event_filters ) {
			$query->query_vars['meta_key'] = 'event_id';
			$query->query_vars['meta_value'] = $event_filters;
			$query->query_vars['compare'] = 'IN';

		}
	}

	public function get_event_filters() {
		if ( isset( $_GET['event_id'] ) ) {
			$events = $_GET['event_id'];
			if ( is_array( $events ) ) {
				foreach ( $events as $key => $event ) {
					if ( '-1' == $event ) {
						unset( $events[ $key ] );
					}
				}
			}

			$_SESSION['wpep_event_filters'] = $events;
			return $events;
		} else if ( isset( $_SESSION['wpep_event_filters'] ) ) {
			return $_SESSION['wpep_event_filters'];
		}

		return null;
	}

	public function add_additional_admin_columns( $config ) {
		$user_fields = User_Fields::get_user_fields_list();

		foreach ( $user_fields as $field ) {
			$config['admin_custom_columns']['wpep-registration']['columns'][] = $field['id'];
		}
		$config['admin_custom_columns']['wpep-registration']['columns'][] = 'date';
		return $config;
	}
}

