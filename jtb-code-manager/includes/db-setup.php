<?php

function create_custom_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Create course_codes table
    //code_status: 
    //0 = no action taken
    //1 = sent but not redeemed
    //2 = redeemed by user
    $course_codes_table_name = $wpdb->prefix . 'cm_course_codes';
    $course_codes_sql = "CREATE TABLE $course_codes_table_name (
        id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code varchar(128) NOT NULL UNIQUE,
        product_id bigint(20) UNSIGNED NOT NULL,
        purchased_by bigint(20) UNSIGNED,
        code_status TINYINT NOT NULL DEFAULT 0,
        redeemed_by bigint(20) UNSIGNED,
        date_created datetime DEFAULT current_timestamp(),
        date_redeemed datetime
    ) $charset_collate;";

    // Create mini_users table
    $mini_users_table_name = $wpdb->prefix . 'cm_training_users';
    $mini_users_sql = "CREATE TABLE $mini_users_table_name (
        id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email varchar(128) NOT NULL UNIQUE,
        name varchar(128),
        hr_manager_id bigint(20) UNSIGNED
    ) $charset_collate;";

    // Create user_code_assignments table
    $user_code_assignments_table_name = $wpdb->prefix . 'cm_user_code_assignments';
    $user_code_assignments_sql = "CREATE TABLE $user_code_assignments_table_name (
        id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        training_user_id bigint(20) UNSIGNED,
        code_id bigint(20) UNSIGNED
    ) $charset_collate;";
    
    // Create user_achievements table
    $user_achievements_table_name = $wpdb->prefix . 'cm_user_achievements';
    $user_achievements_sql = "CREATE TABLE $user_achievements_table_name (
        id bigint(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id bigint(20) UNSIGNED,
        product_id bigint(20) UNSIGNED,
        date_achieved datetime DEFAULT current_timestamp()
    ) $charset_collate;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($course_codes_sql);
    dbDelta($mini_users_sql);
    dbDelta($user_code_assignments_sql);
    dbDelta($user_achievements_sql);
}

?>