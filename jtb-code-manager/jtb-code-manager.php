<?php
/*
 * Plugin Name:       Code Manager
 * Description:       Allows for unique code creation for digital courses.
 * Version:           1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Joshua Bell
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       CodeBlueCPR
 * Domain Path:       /languages
 */
 
require_once( plugin_dir_path( __FILE__ ) . 'includes/custom-roles.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/db-setup.php');
require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php');
require_once( plugin_dir_path( __FILE__ ) . 'custom_hooks.php');

function code_manager_activate(){
    create_hr_manager_role();
    create_custom_tables();
}
register_activation_hook( __FILE__, 'code_manager_activate' );

function cm_load_theme_fonts(){
    if ( ! is_child_theme() ) {
        wp_enqueue_style( 'parent-theme-styles', get_template_directory_uri() . '/style.css' );
    }
    if ( is_child_theme() ) {
        wp_enqueue_style( 'child-theme-styles', get_stylesheet_directory_uri() . '/style.css', array( 'parent-theme-styles' ) );
        wp_enqueue_style( 'training-course-dashboard', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course-dashboard/css/training-course-dashboard.css', array(), '1.0.0' );
    }
    if(is_account_page()){
        wp_enqueue_style( 'account', plugin_dir_url( __FILE__ ) . 'public/css/account.css', array(), '1.0.0' );
         if(!is_user_logged_in()){
            wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
            wp_enqueue_script( 'registration', plugin_dir_url( __FILE__ ) . 'includes/templates/registration/js/registration.js', array(), '1.0.0' );
            wp_localize_script('registration', 'ajax_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'ajax_nonce' => $ajax_nonce,
                'wp_login_url' => site_url( '/wp-login.php' ),
            ));
         }
    }
}
add_action("wp_enqueue_scripts", "cm_load_theme_fonts");

function training_registration_enqueue_scripts(){
    if(!is_user_logged_in()){
        wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
    }
}
add_action( 'woocommerce_account_endpoint', 'training_registration_enqueue_scripts' );

function training_course_enqueue_scripts(){
    wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
 //   wp_enqueue_script( 'training-course-exam', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/js/training-course.js', array(), '1.0.0' );
}
add_action( 'woocommerce_account_training-course_endpoint', 'training_course_enqueue_scripts' );

function training_course_exam_enqueue_scripts(){
    wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
    wp_enqueue_script( 'training-course-ajax', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/js/training-course-ajax.js', array(), '1.0.0' );
    wp_enqueue_script( 'training-course-exam', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/js/training-course-exam.js', array(), '1.0.0' );
    wp_localize_script('training-course-exam', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => $ajax_nonce
        ));
    wp_localize_script('training-course-ajax', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => $ajax_nonce
        ));
    wp_enqueue_script( 'exam-results', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/js/exam-results.js', array(), '1.0.0' );
    wp_localize_script('exam-results', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => $ajax_nonce
        ));
}
add_action( 'woocommerce_account_training-course-exam_endpoint', 'training_course_exam_enqueue_scripts' );

function training_course_certificate_enqueue_scripts(){
    wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
}
add_action( 'woocommerce_account_training-course-congratulations_endpoint', 'training_course_certificate_enqueue_scripts' );

function training_course_exam_results_enqueue_scripts(){
    wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
    wp_enqueue_script( 'exam-results', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/js/exam-results.js', array(), '1.0.0' );
}
add_action( 'woocommerce_account_training-course-exam-results_endpoint', 'training_course_exam_results_enqueue_scripts' );

function registration_enqueue_scripts(){
    wp_enqueue_style( 'training-course', plugin_dir_url( __FILE__ ) . 'includes/templates/training-course/css/training-course.css', array(), '1.0.0' );
}
add_action( 'woocommerce_account_registration_endpoint', 'registration_enqueue_scripts' );

function code_manager_enqueue_scripts(){
    if ( current_user_can( 'hr_manager' ) || current_user_can( 'administrator' )) {
        wp_enqueue_script( 'hr-manager-dashboard-ajax', plugin_dir_url( __FILE__ ) . 'includes/templates/hr-manager-dashboard/js/hr-manager-dashboard-ajax.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script('hr-manager-dashboard-ajax', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => $ajax_nonce,
            'user_id' => get_current_user_id(),
        ));
        wp_enqueue_style( 'hr-manager-dashboard', plugin_dir_url( __FILE__ ) . 'includes/templates/hr-manager-dashboard/css/hr-manager-dashboard.css', array(), '1.0.0' );
        wp_enqueue_script( 'hr-manager-dashboard', plugin_dir_url( __FILE__ ) . 'includes/templates/hr-manager-dashboard/js/hr-manager-dashboard.js', array( 'jquery' ), '1.0.0', true );
    }
}
add_action( 'woocommerce_account_hr-manager-dashboard_endpoint', 'code_manager_enqueue_scripts' );

function cm_enqueue_admin_assets() {
    wp_enqueue_script('cm-admin-js', plugin_dir_url(__FILE__) . 'admin/js/admin.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('cm-admin-css', plugin_dir_url(__FILE__) . 'admin/css/admin.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'cm_enqueue_admin_assets');

function myplugin_override_woocommerce_templates($template, $template_name, $template_path) {
    $plugin_path = plugin_dir_path(__FILE__) . 'woocommerce/';
    $reg_path = plugin_dir_path(__FILE__) . 'includes/templates/registration/';
    
    // Check if the desired templates are being called
    if ('myaccount/my-account.php' === $template_name || 'myaccount/dashboard.php' === $template_name) {
        $custom_template = $plugin_path . $template_name;

        // Check if the custom template exists in the plugin folder
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    
    if ('myaccount/navigation.php' === $template_name) {
        $custom_template = $plugin_path . $template_name;
        // Check if the custom template exists in the plugin folder
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    
    if ('myaccount/form-login.php' === $template_name) {
        $custom_template = $reg_path . 'form-login.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}

add_filter('woocommerce_locate_template', 'myplugin_override_woocommerce_templates', 10, 3);

// Add the custom meta box for questions and answers
function cm_add_course_questions_meta_box() {
    add_meta_box('course-questions', 'Course Questions', 'cm_course_questions_meta_box_callback', 'product');
}
add_action('add_meta_boxes', 'cm_add_course_questions_meta_box');

// Display the custom meta box
function cm_course_questions_meta_box_callback($post) {
    $questions = get_post_meta($post->ID, 'cm_course_questions', true);
    $question_data = json_decode($questions, true) ?: [];
    ?>
    <div id="course-questions-container">
        <?php foreach ($question_data as $index => $question) : ?>
            <div class="course-question" data-index="<?php echo $index; ?>">
                <label>Question:</label>
                <input type="text" name="cm_course_questions[<?php echo $index; ?>][question]" value="<?php echo esc_attr($question['question']); ?>" />
                <label>Correct Answer:</label>
                <input type="text" name="cm_course_questions[<?php echo $index; ?>][correct_answer]" value="<?php echo esc_attr($question['correct_answer']); ?>" />
                <label>Incorrect Answer 1:</label>
                <input type="text" name="cm_course_questions[<?php echo $index; ?>][incorrect_answer_1]" value="<?php echo esc_attr($question['incorrect_answer_1']); ?>" />
                <label>Incorrect Answer 2:</label>
                <input type="text" name="cm_course_questions[<?php echo $index; ?>][incorrect_answer_2]" value="<?php echo esc_attr($question['incorrect_answer_2']); ?>" />
                <label>Incorrect Answer 3:</label>
                <input type="text" name="cm_course_questions[<?php echo $index; ?>][incorrect_answer_3]" value="<?php echo esc_attr($question['incorrect_answer_3']); ?>" />
                <button type="button" class="button remove-question">Remove Question</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button" id="add-course-question">Add Question</button>
    <?php
}

// Save the custom meta box data
function cm_save_course_questions_meta_box_data($post_id) {
    if (isset($_POST['cm_course_questions'])) {
        $course_questions = json_encode($_POST['cm_course_questions']);
        update_post_meta($post_id, 'cm_course_questions', $course_questions);
    }
}
add_action('save_post', 'cm_save_course_questions_meta_box_data');

