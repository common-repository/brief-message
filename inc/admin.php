<?php
defined( 'ABSPATH' ) || exit;


function brief_message_add_columns( $columns ) {

    $new_columns = array();
    foreach ( $columns as $key => $val ) {
        if ( 'title' === $key ) {
            $new_columns['brief_msg_content'] = __( 'Content', 'brief-message' );
        }else{
            $new_columns[ $key ] = $val;
        }

    }

    return $new_columns;


}
add_filter( 'manage_brief_msg_posts_columns' , 'brief_message_add_columns' );

function brief_message_add_custom_column($column_name, $post_id) {

    if ($column_name === 'brief_msg_content') {

        global $post;
        get_inline_data($post);
        the_content();

    }



}
add_action( 'manage_posts_custom_column', 'brief_message_add_custom_column', 10, 2 );


/*
function brief_message_edit_script($hook) {

    if ( 'edit.php' === $hook && isset($_GET["post_type"]) && 'brief_msg' === $_GET["post_type"] ) {
        wp_enqueue_script( 'brief_message_edit', BRIEF_MESSAGE_URI . 'js/edit.min.js', array(), BRIEF_MESSAGE_VERSION,true );
    }

}
*/
//add_action('admin_enqueue_scripts', 'brief_message_edit_script');



add_action( 'restrict_manage_posts', 'brief_message_add_term_dropdown', 10, 2 );
function brief_message_add_term_dropdown( $post_type ) {
    if ( 'brief_msg' == $post_type ) {

        wp_dropdown_categories( array(
            'show_option_all'  => __( 'Category', 'brief-message' ),
            'selected'         => get_query_var( 'brief_msg_cat' ), 
            'name'             => 'brief_msg_cat',
            'taxonomy'         => 'brief_msg_cat',
            'value_field'      => 'slug',
            'hide_if_empty'    => true,
            'hide_empty'       => true,
            'orderby'          => 'name',

        ));
    }
}