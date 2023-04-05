<?php


function cm_create_user_process(){
    return "Hello";
}


function cm_create_wordpress_user($username, $password, $email) {
    global $wpdb;
    
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        // Error handling code
        return false;
    }

    // Set the user role to 'subscriber'
    $user = new WP_User($user_id);
    $user->set_role('subscriber');

    return true;
}


