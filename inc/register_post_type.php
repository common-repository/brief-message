<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'brief_message_create_post_type' );
function brief_message_create_post_type() {
    register_post_type( 'brief_msg', array( 
        'labels' => array(
            'name'                  => _x( 'Brief messages', 'Post Type General Name', 'brief-message' ),
            'singular_name'         => _x( 'Brief message', 'Post Type Singular Name', 'brief-message' ),
            'menu_name'             => __( 'Brief message', 'brief-message' ),
            'name_admin_bar'        => __( 'Brief message', 'brief-message' ),
        ),
        'public'        => true,  
        'has_archive'   => false, 
        'menu_position' => 25,     
        'show_in_rest'  => true,   
        'supports' => array(
            'editor','author','post-formats'
        ),
        'taxonomies' => array('brief_msg_cat')  
    ));

    
    register_taxonomy(
        'brief_msg_cat',   
        'brief_msg',   
        array(
          'label' => __( 'Category', 'brief-message' ),  
          'public' => true,  
          'hierarchical' => true,  
          'show_in_rest' => true,  
          'show_admin_column' => true,  
      )
    );

}

/*
register post type
http://wpdocs.m.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_post_type
*/

