<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_brief_message_ajax_load_more', 'brief_message_ajax_load_more');
add_action( 'wp_ajax_nopriv_brief_message_ajax_load_more', 'brief_message_ajax_load_more' );

function brief_message_ajax_load_more() {

    check_ajax_referer( "brief_message_ajax_nonce" , 'nonce' );

    if ( !wp_verify_nonce( $_POST['nonce'], 'brief_message_ajax_nonce' ) ) {
        wp_die();
    }


    $settings['category'] = sanitize_key( $_POST['category'] );
    $settings['author_name'] = sanitize_user( $_POST['author_name'] );
    $settings['content_count'] = (int) sanitize_key( $_POST['content_count'] );
    $settings['load_more_per_page'] = (int) sanitize_key( $_POST['load_more_per_page'] );


    $res['content'] = '';
    $res['message'] = 'NG';
    $res['last'] = false;

    $q = array(
        'post_type' => 'brief_msg',
        'post_status' => 'publish',
        'posts_per_page' => $settings['load_more_per_page'],
        'offset' => $settings['content_count'],
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


    if ( $loop->have_posts() ){

        $res['message'] = 'OK';
        while ( $loop->have_posts() ) {

            $loop->the_post();

            $res['content'] .= brief_message_show_content();


        }

        wp_reset_query();

        
        if( $loop->post_count + $settings['content_count'] >= $loop->found_posts ){
            $res['last'] = true;
        }
    }


    echo json_encode( array(
        'message' => $res['message'],
        'content' => $res['content'],
        'last' => $res['last'],
    ), JSON_UNESCAPED_UNICODE);
    die();
}

