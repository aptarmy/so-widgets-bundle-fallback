<?php

/**
 * Action to handle searching
 */
function siteorigin_widget_search_posts_action(){
	if ( empty( $_REQUEST['_widgets_nonce'] ) || !wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) return;

	header('content-type: application/json');

	// Get all public post types, besides attachments
	$post_types = (array) get_post_types( array(
		'public'   => true
	) );
	unset($post_types['attachment']);

	$post_types = apply_filters( 'siteorigin_widgets_search_posts_post_types', $post_types );

	global $wpdb;
	if( !empty($_GET['query']) ) {
		$query = "AND post_title LIKE '%" . esc_sql( $_GET['query'] ) . "%'";
	}
	else {
		$query = '';
	}

	$post_types = "'" . implode("', '", array_map( 'esc_sql', $post_types ) ) . "'";

	$results = $wpdb->get_results( "
		SELECT ID, post_title, post_type
		FROM {$wpdb->posts}
		WHERE
			post_type IN ( {$post_types} ) AND post_status = 'publish' {$query}
		ORDER BY post_modified DESC
		LIMIT 20
	", ARRAY_A );

	echo json_encode( apply_filters( 'siteorigin_widgets_search_posts_results', $results ) );
	exit();
}
add_action('wp_ajax_so_widgets_search_posts', 'siteorigin_widget_search_posts_action');

function siteorigin_widget_remote_image_search(){
	if( empty( $_GET[ '_sononce' ] ) || ! wp_verify_nonce( $_GET[ '_sononce' ], 'so-image' ) ) {
		exit();
	}

	if( empty( $_GET['q'] ) ) {
		exit();
	}

	// Send the query to stock search server
	$url = add_query_arg( array(
		'q' => $_GET[ 'q' ],
		'page' => !empty( $_GET[ 'page' ] ) ? intval( $_GET[ 'page' ] ) : 1,
	), 'http://stock.siteorigin.com/wp-admin/admin-ajax.php?action=image_search' );

	$result = wp_remote_get( $url, array(
		'timeout' => 20,
	) );

	if( ! is_wp_error( $result ) ) {
		$result = json_decode( $result['body'], true );
		if( !empty( $result['items'] ) ) {
			foreach( $result['items'] as & $r ) {
				if( !empty( $r['full_url'] ) ) {
					$r['import_signature'] = md5( $r['full_url'] . '::' . NONCE_SALT );
				}
			}
		}
	}
	else {
		$result = array(
			'error' => true,
			'message' => $result->get_error_message()
		);
	}

	header( 'content-type:application/json' );
	echo json_encode( $result );
	exit();
}
add_action('wp_ajax_so_widgets_image_search', 'siteorigin_widget_remote_image_search');

function siteorigin_widget_image_import(){
	if( empty( $_GET[ '_sononce' ] ) || ! wp_verify_nonce( $_GET[ '_sononce' ], 'so-image' ) ) {
		$result = array(
			'error' => true,
			'message' => __( 'Nonce error', 'seed' ),
		);
	}
	else if(
		empty( $_GET['import_signature'] ) ||
		empty( $_GET['full_url'] ) ||
		md5( $_GET['full_url'] . '::' . NONCE_SALT ) !== $_GET['import_signature']
	) {
		$result = array(
			'error' => true,
			'message' => __( 'Signature error', 'seed' ),
		);
	}
	else {
		// Fetch the image
		$src = media_sideload_image( $_GET['full_url'], $_GET['post_id'], null, 'src' );
		if( is_wp_error( $src ) ) {
			$result = array(
				'error' => true,
				'message' => $src->get_error_code(),
			);
		}
		else {
			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $src ) );
			if( !empty( $attachment ) ) {
				$thumb_src = wp_get_attachment_image_src( $attachment[0], 'thumbnail' );
				$result = array(
					'error' => false,
					'attachment_id' => $attachment[0],
					'thumb' => $thumb_src[0]
				);
			}
			else {
				$result = array(
					'error' => true,
					'message' => __( 'Attachment error', 'seed' ),
				);
			}
		}
	}

	// Return the result
	header( 'content-type:application/json' );
	echo json_encode( $result );
	exit();
}
add_action('wp_ajax_so_widgets_image_import', 'siteorigin_widget_image_import');
