<?php
defined( 'ABSPATH' ) || exit;

add_action('wp_ajax_brief_message_ajax_add_content', 'brief_message_ajax_add_content');


function brief_message_ajax_add_content() {

	check_ajax_referer( "brief_message_ajax_nonce" , 'nonce' );

	if ( !wp_verify_nonce( $_POST['nonce'], 'brief_message_ajax_nonce' ) ) {
		wp_die();
	}

	$settings['content'] = sanitize_textarea_field( urldecode( $_POST['content'] ) );
	$settings['author_name'] = sanitize_user( $_POST['author_name'] );

	$author = get_user_by( 'slug' , $settings['author_name'] );
	$settings['author'] = (int)$author->ID;

	$settings['category'] = '';

	if( $_POST['category'] !== '')
		$settings['category'] = (int) sanitize_key( $_POST['category'] );

	$post_value = array(
		'post_type' => 'brief_msg',
		'post_status' => 'publish',
		'post_author' => $settings['author'],
		'post_content' => $settings['content'],
		'tax_input'     => array('post_format' => 'post-format-status')
	);

	if( $settings['category'] !== '' ){
		$post_value['tax_input'] += array(
			'brief_msg_cat' => $settings['category'],
		);
	}

	$insert_id = wp_insert_post($post_value);

	$res['content'] = '';
	$res['message'] = 'NG';

	if( $insert_id === 0 ){
		
		echo json_encode( array(
			'message' => $res['message'],
			'content' => $res['content'],
		), JSON_UNESCAPED_UNICODE);
		die();
	}

	$q = array(
		'post_type' => 'brief_msg',
		'post_status' => 'publish',
		'posts_per_page' => 1,
	);

	if($settings['author_name'] !== ''){
		$q += array( 'author_name' => $settings['author_name'] );
	}

	if($settings['category'] !== ''){
		$q += array( 'tax_query' => array(
			array(
				'taxonomy' => 'brief_msg_cat',
				'field'    => 'term_id',
				'terms'    => $settings['category'],
			)
		));
	}

	$loop = new WP_Query( $q );


	if ( $loop->have_posts() ):
		while ( $loop->have_posts() ) : $loop->the_post();

			$res['content'] = brief_message_show_content();
			$res['message'] = 'OK';

		endwhile; wp_reset_query();

	endif;

	echo json_encode( array(
		'message' => $res['message'],
		'content' => $res['content'],
	), JSON_UNESCAPED_UNICODE);
	die();
}
