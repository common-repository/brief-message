<?php

defined( 'ABSPATH' ) || exit;


class BRIEF_MESSAGE_WIDGET extends WP_Widget {


	function __construct() {

		parent::__construct(
            'brief_msg', // Base ID
            esc_html__( 'Brief message', 'brief-message' ), // Name
            array( 'description' => esc_html__( 'Display a Brief message', 'brief-message' ), ) // Args
          );
	}

    /**
     * Set default settings of the widget
     */
    private function default_settings() {

    	$defaults = array(
    		'title'    => esc_html__( 'Message', 'brief-message' ),
    		'posts_per_page'    => 10,
    		'author_name'    => '',
    		'category'    => '',
    		'load_more'    => true,
    		'load_more_per_page'    => 10,
        'show_specific_widget'     => '',
      );

    	return $defaults;
    }

    public function widget( $args, $instance ) {


    	$settings = wp_parse_args( $instance, $this->default_settings() );

      if($settings['show_specific_widget'] !== ''){
        if( !in_array ( get_the_ID() , explode(',', $settings['show_specific_widget'] ) ) )
          return;
      }

      $q = array(
        'post_type' => 'brief_msg',
        'post_status' => 'publish',
        'posts_per_page' => $settings['posts_per_page'],
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

      $settings['found_posts'] = 0;

      echo $args['before_widget'];
      if ( ! empty($settings['title']) ) {
        echo $args['before_title']. $settings['title'] .  $args['after_title'];
      }

      echo '<div id="'.esc_attr($args['widget_id']).'_wrapper" class="bfm_wrapper">';
      echo '<div id="'.esc_attr($args['widget_id']).'_inner" class="bfm_inner">';

      if ( $loop->have_posts() ){

        $settings['found_posts'] = $loop->found_posts;


        while ( $loop->have_posts() ) : $loop->the_post();

         echo brief_message_show_content();

       endwhile; wp_reset_query();




     }else{
      ?>
      <div class="bfm_no_content">
       <?php esc_html_e( 'No content', 'brief-message' ); ?>
     </div>
     <?php
   }
   echo '</div>';

   
   if( $settings['load_more'] && $settings['found_posts'] > $settings['posts_per_page'] ){

    add_action( 'wp_footer', 'brief_message_widget_load_more_enqueue' );

    ?>
    <div class="bfm_load_more">
     <div class="bfm_load_more_button_wrap">
      <button id="<?php echo esc_attr($args['widget_id']); ?>_load_more_button" class="bfm_load_more_button" type="button" data-max_content="<?php echo esc_attr($settings['found_posts']); ?>" data-now_content="" data-author_name="<?php echo esc_attr($settings['author_name']); ?>" data-category="<?php echo esc_attr($settings['category']); ?>" data-load_more_per_page="<?php echo esc_attr($settings['load_more_per_page']); ?>" onclick="brief_message_load_more('<?php echo esc_attr($args['widget_id']); ?>');">
       <?php esc_html_e( 'Show more', 'brief-message' ); ?>
     </button>
     <div class="bfm_load_more_spin bfm_spin" style="display:none;">
       <?php brief_message_spin_icon(); ?>
     </div>
   </div>
 </div>
 <?php
}

echo '</div>';



if ( is_user_logged_in() ){

  $user = wp_get_current_user();

  if ( $user->user_nicename === $settings['author_name'] || $settings['author_name'] === '' ){

   add_action( 'wp_footer', 'brief_message_widget_add_post_enqueue' );

   ?>

   <form id="<?php echo esc_attr($args['widget_id']); ?>_form" class="bfm_form" name="new_post" method="post" action="<?php echo site_url(); ?>/">
    <span><?php esc_html_e( 'New post', 'brief-message' ); ?></span>
    <div class="bfm_textarea_wrap">
     <textarea id="<?php echo esc_attr($args['widget_id']); ?>_textarea" class="bfm_textarea" name="" rows="5"></textarea>
     <button id="<?php echo esc_attr($args['widget_id']); ?>_post_button" class="bfm_post_button" type="button" data-category="<?php echo esc_attr($settings['category']); ?>" data-author_name="<?php echo esc_attr($user->user_nicename); ?>" data-posts_per_page="<?php echo esc_attr($settings['posts_per_page']); ?>" onclick="brief_message_add_post('<?php echo esc_attr($args['widget_id']); ?>');"><?php esc_html_e( 'Post', 'brief-message' ); ?></button>
   </div>
   <div id="<?php echo esc_attr($args['widget_id']); ?>_post_spin" class="bfm_post_spin bfm_spin" style="display:none;">
     <?php brief_message_spin_icon(); ?>
   </div>
 </form>
<style>
.bfm_textarea{
  line-height: 1.6
}
</style>
 <?php
}
}

echo $args['after_widget'];


add_action( 'wp_footer', 'brief_message_widget_common_enqueue' );






}




public function form( $instance ) {

// Get Widget Settings.
 $settings = wp_parse_args( $instance, $this->default_settings() );

 ?>

 <p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'brief-message' ); ?>:</label>
  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $settings['title'] ); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php esc_html_e( 'Posts per page', 'brief-message' ); ?>:</label>
  <input class="" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $settings['posts_per_page'] ); ?>" />
</p>

<p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>">
   <?php esc_html_e( 'User', 'brief-message'); ?>:
 </label>
 <select id="<?php echo esc_attr( $this->get_field_id( 'author_name' )); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_name' )); ?>">
   <option <?php echo selected( $settings['author_name'], '' , false ); ?> value="" >
    -- <?php esc_html_e('Unspecified', 'brief-message'); ?> --
  </option>
  <?php
  $users = get_users(array(
    'orderby' => 'ID',
    'order' => 'ASC',
  ));
  foreach ($users as $user) { ?>
    <option <?php echo selected( $settings['author_name'], $user->user_nicename , false ); ?> value="<?php echo $user->user_nicename; ?>" >
     <?php echo $user->display_name; ?>
   </option>
 <?php } ?>
</select>
</p>

<p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
   <?php esc_html_e( 'Category', 'brief-message'); ?>:
 </label>
 <select id="<?php echo esc_attr( $this->get_field_id( 'category' )); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' )); ?>">
   <option <?php echo selected( $settings['category'], '' , false ); ?> value="" >
    -- <?php esc_html_e('Unspecified', 'brief-message'); ?> --
  </option>
  <?php
  $terms = get_terms([
    'taxonomy' => 'brief_msg_cat',
    'hide_empty' => false,
  ]);
  foreach ($terms as $term) { ?>
    <option <?php echo selected( $settings['category'], $term->term_id , false ); ?> value="<?php echo $term->term_id; ?>" >
     <?php echo $term->name; ?>
   </option>
 <?php } ?>
</select>
</p>

<p>
  <input id="<?php echo esc_attr( $this->get_field_id( 'load_more' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'load_more' ) ); ?>" type="checkbox"<?php checked( $settings['load_more']); ?> />
  <label for="<?php echo esc_attr( $this->get_field_id( 'load_more' ) ); ?>">
   <?php esc_html_e( 'Show more button', 'brief-message' ); ?>
 </label>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'load_more_per_page' ); ?>"><?php esc_html_e( 'Number of posts to display with Show more button', 'brief-message' ); ?>:</label>
  <input class="" id="<?php echo $this->get_field_id( 'load_more_per_page' ); ?>" name="<?php echo $this->get_field_name( 'load_more_per_page' ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $settings['load_more_per_page'] ); ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'show_specific_widget' ); ?>"><?php esc_html_e( 'Show widget only for specific posts', 'brief-message' ); ?>:</label>
  <input class="widefat" id="<?php echo $this->get_field_id( 'show_specific_widget' ); ?>" name="<?php echo $this->get_field_name( 'show_specific_widget' ); ?>" type="text" placeholder="<?php esc_html_e( 'Enter the post ID', 'brief-message' ).'&nbsp;'.esc_html_e( 'e.g.', 'brief-message' ); ?>&nbsp;1234" value="<?php echo esc_attr( $settings['show_specific_widget'] ); ?>" />
  <label for="<?php echo $this->get_field_id( 'show_specific_widget' ); ?>"><?php echo sprintf( esc_html__('Separate multiple %s with ,(comma).', 'brief-message') , esc_html__( 'ID', 'brief-message' ) ); ?>:</label>
</p>

<?php

}

public function update( $new_instance, $old_instance ) {
 $instance = $old_instance;
 $instance['title'] = sanitize_text_field( $new_instance['title'] );
 $instance['posts_per_page'] = (int) sanitize_text_field( $new_instance['posts_per_page'] );
 $instance['author_name'] = sanitize_text_field( $new_instance['author_name'] );
 $instance['category'] = $new_instance['category'] !== '' ? (int) sanitize_text_field( $new_instance['category'] ) : '';
 $instance[ 'load_more' ] = (bool)$new_instance[ 'load_more' ];
 $instance['load_more_per_page'] = (int) sanitize_text_field( $new_instance['load_more_per_page'] );
 $instance['show_specific_widget'] = sanitize_text_field( $new_instance['show_specific_widget'] );
 return $instance;
}




} // class BRIEF_MESSAGE_WIDGET


function brief_message_register_widgets() {
	register_widget( 'BRIEF_MESSAGE_WIDGET' );
}

add_action( 'widgets_init', 'brief_message_register_widgets' );

function brief_message_human_time ( $post_time ){

  $just_now = current_time('timestamp');


  
  $diff['days'] = ( strtotime( date("Y-m-d" , $just_now  ) ) - strtotime( date("Y-m-d", $post_time ) ) ) / 86400;

  $diff['year'] = (int) floor($diff['days'] / 365);

  if ($diff['year'] === 1) return __('a year ago','brief-message');
  
  if ($diff['year'] > 1) return  sprintf( __( '%s years ago' , 'brief-message' ), $diff['year'] );

  $diff['month'] = (int) floor($diff['days'] / 30);

  if ($diff['month'] === 1) return __('a month ago','brief-message');
  
  if ($diff['month'] > 1) return  sprintf( __( '%s months ago' , 'brief-message' ), $diff['month'] );

  $diff['week'] = (int) floor($diff['days'] / 7);
  
  if ($diff['week'] > 1) return sprintf( __('%s weeks ago','brief-message'), $diff['week'] );
  if ($diff['week'] === 1) return __('a week ago','brief-message');
  
  if ($diff['days'] > 1) return sprintf( __( '%s days ago' , 'brief-message' ), $diff['days'] );

  
  $diff['time'] = $just_now - $post_time ;

  $diff['hour'] = (int) floor( $diff['time'] / 3600 );

  if ($diff['hour'] === 1) return __('an hour ago','brief-message');

  
  if( $diff['hour'] <= 20 && $diff['hour'] >= 1) return sprintf( __( '%s hours ago' , 'brief-message' ), $diff['hour'] );
  
  if ($diff['hour'] >= 21 && $diff['days'] === 1) return sprintf( __('yesterday at %s','brief-message'), date("h:i a", $post_time ) );

  $diff['minute'] = (int) floor( $diff['time'] / 60 );

  if ($diff['minute'] === 1) return __('a minute ago','brief-message');
  
  if ($diff['minute'] > 1) return sprintf( __( '%s minutes ago' , 'brief-message' ), $diff['minute'] );

  return __('just now','brief-message');


}

function brief_message_show_content(){

	$author['nickname'] = get_the_author_meta('nickname');
	$author['ID'] = get_the_author_meta( 'ID' );

	$content = wpautop( get_the_content(), true );

	$time['post_utc'] = get_date_from_gmt( get_post_time('c', true) , 'c');
	$time['post'] = get_the_date() . ' ' . get_the_time();
	$time['human'] = brief_message_human_time( get_the_time('U') );

	return '<div class="bfm_container">
	<div class="bfm_head bfm_f_box">
	<div class="bfm_ava">
	<img src="'.esc_url( get_avatar_url( $author['ID'] , array('size'=>96 )) ).'" width="32" height="32" class="bfm_br50" alt="'.esc_attr($author['nickname']).'" />
	</div>
	<div class="bfm_name">
	<div>'.$author['nickname'].'</div>
	'.$time['human'].'
	</div>
	</div>
	<div class="bfm_content">
	'.$content.'
	</div>
	<div class="bfm_foot">
	<time datetime="'.$time['post_utc'].'" title="'.esc_html__( 'Posting time', 'brief-message' ).' '.$time['post'].'">
	'.get_the_date().'
	</time>
	</div>
	</div>';
}


function brief_message_widget_common_enqueue() {

	wp_enqueue_style( 'brief_message_widget', BRIEF_MESSAGE_URI . 'css/widget.min.css', array(), BRIEF_MESSAGE_VERSION );

	wp_enqueue_script( 'brief_message_common', BRIEF_MESSAGE_URI . 'js/common.min.js', array(), BRIEF_MESSAGE_VERSION,true );
	wp_localize_script( 'brief_message_common', 'brief_message_common', array(
		'pop_up_now_loading' => __( 'Now Loading...', 'brief-message' ),
		'pop_up_completed' => __( 'Completed', 'brief-message' ),
		'pop_up_error' => __( 'Error', 'brief-message' ),
		'pop_up_now_posting' => __( 'Now posting...', 'brief-message' ),
		'pop_up_is_blank' => __( 'Is blank', 'brief-message' ),
	) );

}

function brief_message_widget_load_more_enqueue() {

	wp_enqueue_script( 'brief_message_load_more', BRIEF_MESSAGE_URI . 'js/ajax_load_more.min.js', array('brief_message_common'), BRIEF_MESSAGE_VERSION,true );

	wp_localize_script( 'brief_message_load_more', 'brief_message_frontend_ajax', array(
		'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
		'nonce' => wp_create_nonce( 'brief_message_ajax_nonce' ),
	) );

}

function brief_message_widget_add_post_enqueue() {
	wp_enqueue_script( 'brief_message_add_post', BRIEF_MESSAGE_URI . 'js/ajax_post.min.js', array('brief_message_common'), BRIEF_MESSAGE_VERSION,true );

	wp_localize_script( 'brief_message_add_post', 'brief_message', array(
		'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
		'author' => get_current_user_id(),
		'nonce' => wp_create_nonce( 'brief_message_ajax_nonce' ),
	) );

	wp_enqueue_style( 'brief_message_add_post', BRIEF_MESSAGE_URI . 'css/ajax_post.min.css', array(), BRIEF_MESSAGE_VERSION );

}

function brief_message_spin_icon() {
	?>


	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 32 32" width="32" height="32">
		<path opacity="0.2" fill="#000" d="M16,0C7.2,0,0,7.2,0,16c0,8.8,7.2,16,16,16s16-7.2,16-16C32,7.2,24.8,0,16,0z M16,28.5 C9.1,28.5,3.5,22.9,3.5,16C3.5,9.1,9.1,3.5,16,3.5S28.5,9.1,28.5,16C28.5,22.9,22.9,28.5,16,28.5z"/>
		<path fill="#00bcd4" d="M22.2,5.2L24,2.1C21.6,0.8,18.9,0,16,0l0,0v3.5l0,0C18.3,3.5,20.4,4.1,22.2,5.2z">
			<animateTransform attributeType="xml" attributeName="transform" type="rotate" values="0 16,16;360 16,16" dur="0.9s" repeatCount="indefinite"/>
		</path>
	</svg>

	<?php

}
