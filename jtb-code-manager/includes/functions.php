<?php

require_once( plugin_dir_path( __FILE__ ) . 'templates/hr-manager-dashboard/hr-manager-dashboard-queries.php');
require_once( plugin_dir_path( __FILE__ ) . 'templates/training-course/training-course-queries.php');

function generate_unique_code($quantity) {
    global $wpdb;
    $table_name = $wpdb->prefix . "cm_course_codes";
    $codes = array();
    
    for($i = 0; $i < $quantity; $i++){
        do{
            $code = strtoupper(wp_generate_password( 10, false, false ));
            $query_string = $wpdb->prepare("SELECT id FROM $table_name WHERE code = %s",$code);
            $query = $wpdb->get_row($query_string,OBJECT);
        } while($query->id > 0);
        $codes[] = $code;
    }
    return $codes;
}

function generate_unique_course_codes( $order_id ) {
    $order = wc_get_order( $order_id );
    $user_id = $order->get_user_id();
    $user_roles = get_userdata($user_id)->roles;
    $user_email = get_userdata($user_id)->user_email;
    $user_name = get_userdata($user_id)->user_login;
    global $wpdb;
    
    $training_user_table_name = $wpdb->prefix ."cm_training_users";
    
    $prepared_string = $wpdb->prepare("SELECT id FROM $training_user_table_name WHERE email = %s",$user_email);
    $training_user_results = $wpdb->get_row($prepared_string,OBJECT);
    $training_user_id = $training_user_results->id;
    
    $table_name = $wpdb->prefix . 'cm_course_codes';
    
    $meta_key = "_code_created";
	$meta_value = get_post_meta($order_id, $meta_key, true);
	if ($meta_value !== "") {
		return;
	}
	update_post_meta($order_id, $meta_key, true);
	
	if(!$training_user_id){
        $wpdb->insert(
            $training_user_table_name,
            array(
                'name' => $user_name,
                'email' => $user_email,
                'hr_manager_id' => 0
            )
        );
        $training_user_id = $wpdb->insert_id;
    }
	
    foreach ( $order->get_items() as $item ) {
        $product_data = $item->get_data();
        $product_id = $product_data['product_id'];
        $product_quantity = $product_data['quantity'];
        $product = wc_get_product( $product_id );
        $terms = wp_get_post_terms( $product_id, 'product_cat' );
        $category = $terms[0]->name;

        if ( 'Online Training' === $category ) {
            if ( in_array( 'hr_manager', $user_roles ) || in_array( 'administrator', $user_roles )) {
                $codes = generate_unique_code($product_quantity);
                foreach($codes as $code){
                    $wpdb->insert( $table_name, 
                    array(
                        'code' => $code,
                        'product_id' => $product_id,
                        'purchased_by' => $user_id,
                    ));
                }
            }else{
                $codes = generate_unique_code($product_quantity);
                foreach($codes as $code){
                $wpdb->insert( $table_name, 
                array(
                    'code' => $code,
                    'product_id' => $product_id,
                    'purchased_by' => $user_id,
                    'redeemed_by' => $training_user_id,
                    'date_redeemed' => current_time( 'mysql' ),
                    'code_status' => 1
                ));
                }
            }
        }
    }
}
add_action( 'woocommerce_thankyou', 'generate_unique_course_codes' );

function cm_register_rest_routes() {
  register_rest_route('cm-training/v1', '/verify-employee', array(
    'methods' => 'POST',
    'callback' => 'cm_verify_employee_existence'
  ));
  register_rest_route('cm-exam/v1', '/get-data', array(
    'methods' => 'POST',
    'callback' => 'cm_get_course_exam_data'
  ));
    register_rest_route('cm-exam/v1', '/grade-exam', array(
      'methods' => 'POST',
      'callback' => 'cm_grade_exam',
      'args' => array(),
      'permission_callback' => '__return_true', // Allows public access to the endpoint
    ));
    register_rest_route('cm-training/v1', '/verify-employee-batch', array(
      'methods' => 'POST',
      'callback' => 'cm_verify_employee_existence_bulk',
      'args' => array(),
      'permission_callback' => '__return_true', // Allows public access to the endpoint
    ));
}

add_action('rest_api_init', 'cm_register_rest_routes');

add_action('wp_ajax_cm_verify_employee_existence', 'cm_verify_employee_existence' );
add_action('wp_ajax_nopriv_cm_verify_employee_existence', 'cm_verify_employee_existence' );
add_action('wp_ajax_cm_verify_employee_existence_bulk', 'cm_verify_employee_existence_bulk' );
add_action('wp_ajax_nopriv_cm_verify_employee_existence_bulk', 'cm_verify_employee_existence_bulk' );
add_action('wp_ajax_cm_get_course_exam_data', 'cm_get_course_exam_data');
add_action('wp_ajax_nopriv_cm_get_course_exam_data', 'cm_get_course_exam_data');
?>