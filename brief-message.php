<?php
/*
Plugin Name: Brief Message
Description: Add a widget to display a short sentence.It will be displayed in the form of Twitter.Like the theme "P2", logged-in users can post from the front end.
Version: 0.0.4
Author: ZIPANG
Author URI: https://back2nature.jp/
License: GNU General Public License v3 or later
Text Domain: brief-message
Domain Path: /languages/
*/

/*
    Brief Message
    Copyright (C) 2021 ZIPANG

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

    defined( 'ABSPATH' ) || exit;
    
    
    $data = get_file_data( __FILE__, array( 'Version' ) );

    define( 'BRIEF_MESSAGE_VERSION', $data[0] );
    define( 'BRIEF_MESSAGE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
    define( 'BRIEF_MESSAGE_URI', trailingslashit( esc_url( plugin_dir_url( __FILE__ ) ) ) );
    define( 'BRIEF_MESSAGE_PLUGIN_FILE', __FILE__ );

    function brief_message_file_load() {
        
        load_plugin_textdomain( 'brief-message', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );

        require_once BRIEF_MESSAGE_DIR . 'inc/register_post_type.php';

        if(is_user_logged_in())
            require_once BRIEF_MESSAGE_DIR . 'inc/ajax_post.php';

        require_once BRIEF_MESSAGE_DIR . 'inc/ajax_load_more.php';


        if(is_admin())
            require_once BRIEF_MESSAGE_DIR . 'inc/admin.php';

        require_once BRIEF_MESSAGE_DIR . 'inc/widget.php';

    }
    add_action( 'plugins_loaded', 'brief_message_file_load');
