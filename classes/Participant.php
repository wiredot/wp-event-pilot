<?php

namespace Wiredot\WPEP;

class Participant {

	public function __construct() {
		add_action( 'post_updated', array( $this, 'update_title' ), 10, 2 );
	}

	public function update_title( $post_id, $post ) {
		global $wpdb;

		if ( 'wpep-participant' == $post->post_type ) {
			$post_title = $_POST['first-name'] . ' ' . $_POST['last-name'];
			$wpdb->update(
				$wpdb->posts,
				array(
					'post_title' => $post_title,
				),
				array(
					'ID' => $post_id,
				),
				array(
					'%s',
				)
			);
		}
	}
}
