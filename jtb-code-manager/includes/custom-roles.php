<?php

// Custom user registration handler
function custom_user_registration_handler() {
    if (
        !isset($_POST['custom_user_registration_nonce'])
        || !wp_verify_nonce($_POST['custom_user_registration_nonce'], 'custom_user_registration')
    ) {
        wp_die('Nonce verification failed');
    }
    
    $user_login = sanitize_user($_POST['user_login']);
    $user_email = sanitize_email($_POST['user_email']);
    $user_password = $_POST['user_password'];
    $user_role = sanitize_text_field($_POST['user_role']);
    
    $user_id = wp_create_user($user_login, $user_password, $user_email);
    
    if (is_wp_error($user_id)) {
       echo json_encode(array('error' => 'A user with that email already exists.'));
    }
    
    // Assign the user role
    $user = new WP_User($user_id);
    if ($user_role === 'hr_manager') {
        $user->set_role('hr_manager');
    } else {
        $user->set_role('customer');
    }
    
    // Log the user in
    wp_set_auth_cookie($user_id, true);
    
    // Redirect the user to the WooCommerce account page
    wp_redirect(wc_get_page_permalink('myaccount'));
    exit;
}
add_action('admin_post_nopriv_custom_user_registration', 'custom_user_registration_handler');

function custom_user_login_handler() {
    if (
        !isset($_POST['custom_user_login_nonce'])
        || !wp_verify_nonce($_POST['custom_user_login_nonce'], 'custom_user_login')
    ) {
        wp_die('Nonce verification failed');
    }

    $user_login = $_POST['username'];
    $user_password = $_POST['password'];

    // Authenticate the user
    $user = wp_authenticate($user_login, $user_password);

    // Check if the authentication was successful
    if (!is_wp_error($user)) {
        // Set the authentication cookie
        wp_set_auth_cookie($user->ID, false);

        // Redirect the user to the desired page
        echo json_encode(array('success' => true));
        
    } else {
        // Handle the authentication error
        echo json_encode(array('error' => 'Email or Username are incorrect'));
    }
}
add_action('admin_post_nopriv_custom_user_login', 'custom_user_login_handler');


// Register the custom 'HR Manager' user role
function custom_register_hr_manager_role() {
    add_role(
        'hr_manager',
        'HR Manager',
        array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => false,
        )
    );
}
add_action('init', 'custom_register_hr_manager_role');

function add_hr_manager_dashboard_endpoint() {
    add_rewrite_endpoint( 'hr-manager-dashboard', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_hr_manager_dashboard_endpoint' );

function add_hr_manager_dashboard_menu_item( $items ) {
    if ( current_user_can( 'hr_manager' ) || current_user_can( 'administrator' ) ) {
        $items['hr-manager-dashboard'] = __( 'Organisation Dashboard', 'course-codes-manager' );
    }
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'add_hr_manager_dashboard_menu_item' );

function add_training_course_dashboard_endpoint() {
    add_rewrite_endpoint( 'training-course-dashboard', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_training_course_dashboard_endpoint' );

function add_training_course_endpoint() {
    add_rewrite_endpoint( 'training-course', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_training_course_endpoint' );

function add_training_course_exam_endpoint() {
    add_rewrite_endpoint( 'training-course-exam', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_training_course_exam_endpoint' );

function add_training_course_exam_results_endpoint() {
    add_rewrite_endpoint( 'training-course-exam-results', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_training_course_exam_results_endpoint' );

function add_training_course_congratulations_endpoint() {
    add_rewrite_endpoint( 'training-course-congratulations', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_training_course_congratulations_endpoint' );

function add_registration_endpoint() {
    add_rewrite_endpoint( 'registration', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'add_registration_endpoint' );

function add_training_course_dashboard_menu_item( $items ) {
    $items['training-course-dashboard'] = __( 'Training & Courses', 'course-codes-manager' );
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'add_training_course_dashboard_menu_item' );

// function add_achievements_endpoint() {
//     add_rewrite_endpoint( 'achievements', EP_ROOT | EP_PAGES );
// }
// add_action( 'init', 'add_achievements_endpoint' );

// function add_achievements_menu_item( $items ) {
//     $items['achievements'] = __( 'Achievements', 'course-codes-manager' );
//     return $items;
// }
// add_filter( 'woocommerce_account_menu_items', 'add_achievements_menu_item' );

function display_hr_manager_dashboard_content() {
    if ( current_user_can( 'hr_manager' ) || current_user_can( 'administrator' ) ) {
        display_course_codes_dashboard();
    }
}
add_action( 'woocommerce_account_hr-manager-dashboard_endpoint', 'display_hr_manager_dashboard_content' );

function display_course_codes_dashboard() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/hr-manager-dashboard/hr-manager-dashboard.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_training_course_dashboard_content() {
    display_training_course_dashboard();
}
add_action( 'woocommerce_account_training-course-dashboard_endpoint', 'display_training_course_dashboard_content' );

function display_training_course_dashboard() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/training-course-dashboard/training-course-dashboard.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_training_course_content() {
    display_training_course();
}
add_action( 'woocommerce_account_training-course_endpoint', 'display_training_course_content' );

function display_training_course() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/training-course/training-course.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_training_course_exam_content() {
    display_training_course_exam();
}
add_action( 'woocommerce_account_training-course-exam_endpoint', 'display_training_course_exam_content' );

function display_training_course_exam() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/training-course/training-course-exam.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_training_course_exam_results_content() {
    display_training_course_exam_results();
}
add_action( 'woocommerce_account_training-course-exam-results_endpoint', 'display_training_course_exam_results_content' );

function display_training_course_exam_results() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/training-course/exam-results.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_training_course_certificate_content() {
    display_training_course_certificate();
}
add_action( 'woocommerce_account_training-course-congratulations_endpoint', 'display_training_course_certificate_content' );

function display_training_course_certificate() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/training-course/congratulations.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

function display_registration_content() {
    display_registration();
}
add_action( 'woocommerce_account_registration_endpoint', 'display_registration_content' );

function display_registration() {
    $template_path = plugin_dir_path( __FILE__ ) . 'templates/registration/form-login.php';
    if ( file_exists( $template_path ) ) {
        include $template_path;
    } else {
        echo  $template_path;
    }
}

// function display_achievements_content() {
//     display_registration();
// }
// add_action( 'woocommerce_account_achievements_endpoint', 'display_achievements_content' );

// function display_achievements() {
//     $template_path = plugin_dir_path( __FILE__ ) . 'templates/achievements/achievements.php';
//     if ( file_exists( $template_path ) ) {
//         include $template_path;
//     } else {
//         echo  $template_path;
//     }
// }

?>