<?php

namespace Wiredot\WPEP\Templates;

use Wiredot\Preamp\Twig;
use Wiredot\WPEP\User_Fields;

class Single_Event {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'single_event' ) );
		add_action( 'edd_after_download_content', array( $this, 'edd_append_purchase_link' ) );
	}

	public function single_event( $content ) {
		global $post;

		if ( $post && 'wpep' == $post->post_type && is_singular( 'wpep' ) && is_main_query() && ! post_password_required() ) {

			$Twig = new Twig();

			if ( isset( $_GET['register'] ) ) {

				$content = $Twig->twig->render(
					'front/single_event_registration.twig', array(
						'additional_fields' => $this->get_user_fields(),
					)
				);
			} else {
				$content = $Twig->twig->render(
					'front/single_event.twig', array(
						'content' => $content,
					)
				);
			}
		}

		return $content;
	}

	public function get_user_fields() {
		$additional_fields = User_Fields::get_user_fields();

		$user_fields = '';

		foreach ( $additional_fields as $group ) {
			$user_fields .= $this->get_user_group( $group );
		}

		return $user_fields;
	}

	public function get_user_group( $group ) {
		$group_text = '';

		$Twig = new Twig();

		if ( is_user_logged_in() ) {
			$group_text .= $Twig->twig->render(
				'front/user_table.twig', array(
					'header' => $group['name'],
					'fields' => $group['fields'],
				)
			);
		} else {
			$group_text .= $Twig->twig->render(
				'forms/header.twig', array(
					'header' => $group['name'],
				)
			);

			$group_text .= $this->get_user_group_fields( $group['fields'] );
		}

		return $group_text;
	}

	public function get_user_group_fields( $fields ) {
		$fields_text = '';

		foreach ( $fields as $key => $field ) {
			$fields_text .= $this->get_user_group_field( $field );
		}

		return $fields_text;
	}

	public function get_user_group_field( $field ) {
		$Twig = new Twig();
		return $Twig->twig->render(
			'forms/' . $field['type'] . '.twig', array(
				'field' => $field,
			)
		);
	}

	public function edd_append_purchase_link( $l ) {
		echo 'asdxxxx';
	}
}
