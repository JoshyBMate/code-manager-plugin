<?php

class CM_HRManager {
    public $id;
    public $purchased_products;
    public $employees;
    
    public function __construct($id){
        $this->id = $id;
        $this->populate_data();
    }
    
    private function populate_data(){
        $this->purchased_products = cm_get_manager_codes();
        $this->employees = cm_get_manager_employees();
    }
}

class CM_Employee{
    public $name;
    public $email;
    public $product_id;
    
    public function __construct($name,$email,$product_id){
        $this->name = $name;
        $this->email = $email;
        $this->product_id = $product_id;
    }
}

class CM_Product {
    public $id;
    public $name;
    public $codes;
    
    function __construct($id,$name,$codes){
        $this->id = $id;
        $this->name = $name;
        $this->codes = $codes;
    }
}

function cm_get_manager_codes(){
    global $wpdb;
    
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix ."cm_course_codes";
    $join_table_1 = $wpdb->prefix ."posts";
    $join_table_2 = $wpdb->prefix ."cm_training_users";
    
    $prepared_string = $wpdb->prepare("SELECT 
    cm_cc.id,
    cm_cc.code,
    cm_cc.product_id as product_id,
    wp_p.post_title as product_name
    FROM $table_name cm_cc 
    JOIN $join_table_1 wp_p ON cm_cc.product_id = wp_p.ID 
    WHERE purchased_by = %s AND code_status = 0",$user_id); 
   
    $results = $wpdb->get_results($prepared_string);

    $products_array = [];
    
    $product_objects = [];
    
    foreach ($results as $result) {
        $code_id = $result->id;
        $product_id = $result->product_id;
    
        if (!isset($products_array[$product_id])) {
            $products_array[$product_id] = [];
        }
    
        $products_array[$product_id][$code_id] = [
            'code' => $result->code,
        ];
    }
    
    foreach($products_array as $product_id => $product){
        $product_name = wc_get_product($product_id)->get_name();
        $training_course = new CM_Product($product_id,$product_name,$product);
        $product_objects[] = $training_course;
    }
    
    
    return $product_objects;
}

function cm_get_manager_employees(){
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    //$user_id = get_current_user_id();
    $table_name = $wpdb->prefix ."cm_training_users";
    $product_table = $wpdb->prefix ."cm_user_code_assignments";
    $code_table  = $wpdb->prefix ."cm_course_codes";
    $employee_array = [];
    $prepared_string = $wpdb->prepare("
    SELECT 
        name, 
        email, 
        product_id 
    FROM $table_name cm_tu 
    JOIN $product_table cm_ca ON cm_tu.ID = cm_ca.training_user_id 
    JOIN $code_table cm_cc ON cm_cc.purchased_by = $user_id AND cm_ca.code_id = cm_cc.ID AND redeemed_by IS NOT NULL
    WHERE cm_tu.hr_manager_id = %s", $user_id);
    
    $results = $wpdb->get_results($prepared_string);
    
    foreach($results as $result){
        $name = $result->name;
        $email = $result->email;
        $product_id = $result->product_id;
        
        $employee = new CM_Employee($name,$email,$product_id);
        
        $employee_array[] = $employee;
    }
    $unique_employees = array();
    $unique_emails = array();

    foreach ($employee_array as $employee) {
        if (!in_array($employee->email, $unique_emails)) {
            $unique_employees[] = $employee;
            $unique_emails[] = $employee->email;
        }
    }

    return $unique_employees;
}

function cm_create_manager_object(){
    $user_id = get_current_user_id();
    $hr_manager = new CM_HRManager($user_id);
    return $hr_manager;
}

function cm_verify_employee_existence(WP_REST_Request $request){
    global $wpdb;
    
    $name = sanitize_text_field($request->get_param('name'));
    $email = sanitize_text_field($request->get_param('email'));
    $code = sanitize_text_field($request->get_param('code'));
    $product_id = sanitize_text_field($request->get_param('product_id'));
    $hr_manager_id = sanitize_text_field($request->get_param('user_id'));
    
    $training_user_table_name = $wpdb->prefix ."cm_training_users";
    
    $prepared_string = $wpdb->prepare("SELECT id FROM $training_user_table_name WHERE email = %s",$email);
    $training_user_results = $wpdb->get_row($prepared_string,OBJECT);
    $user_id = $training_user_results->id;
    
    $course_code_table = $wpdb->prefix ."cm_course_codes";
    $prepared_string = $wpdb->prepare("SELECT id FROM $course_code_table WHERE redeemed_by = %s",$user_id);
    $course_code_result = $wpdb->get_row($prepared_string,OBJECT);
    
    if($course_code_result){
        return new WP_REST_Response(array(
            'status' => 'success',
            'message' => 'This user has already been assigned a code for this course',
            'isAssigned' => true,
        ), 200);
    }
    
    if(!$user_id){
        $wpdb->insert(
            $training_user_table_name,
            array(
                'name' => $name,
                'email' => $email,
                'hr_manager_id' => $hr_manager_id
            )
        );
        $user_id = $wpdb->insert_id;
    }
    
    if(cm_add_code_to_user($user_id,$code) === 1){
        $employee_count = count(cm_get_manager_employees());
        if(!sendinblue_email_single($name,$email,$code)){
            error_log("Error sending email");
        }
        return new WP_REST_Response(array(
            'status' => 'success',
            'message' => 'Data submitted successfully',
            'name' => $name,
            'email' => $email,
            'seatsUsed' => $employee_count,
            'code' => $code,
            'productID' => $product_id
        ), 200);
    }
    return new WP_REST_Response(array(
        'status' => 'error',
        'message' => 'Error during submission',
    ), 500);
}

function cm_add_code_to_user($id,$code){
    global $wpdb;
    
    if(!$id){
        return 0;
    }
    
    $course_code_table_name = $wpdb->prefix . "cm_course_codes";
    $code_assignments_table_name = $wpdb->prefix ."cm_user_code_assignments";
    
    $prepared_string = $wpdb->prepare("SELECT id FROM $course_code_table_name WHERE code = %s",$code);
    $results = $wpdb->get_row($prepared_string,OBJECT);
    
    if(!$results->id){
        return "Please refrain from using Dev Tools to edit the code value. Do this again and you will be banned.";
    }
    
    $wpdb->insert(
        $code_assignments_table_name,
        array(
            'code_id' => $results->id,
            'training_user_id' => $id
        )
    );
    
    $update = $wpdb->update(
        $course_code_table_name,
        array(
            'code_status' => 1,
            'redeemed_by' => $id,
            'date_redeemed' => current_time( 'mysql' )
        ),
        array(
            'id' => $results->id
        )
    );
    
    if(!$update){
        return 0;
    }
    
    return 1;
}

function cm_verify_employee_existence_bulk(WP_REST_Request $request){
    global $wpdb;
    
    $recipientData = array();
    $emails = json_decode($request->get_params()['emails']);
    $hr_manager_id = $request->get_params()['user_id'];
    $product_id = $request->get_params()['product_id'];
    $codes = json_decode($request->get_params()['codes']);
    foreach ($emails as $index => $email) {
        $name = explode('@', $email)[0];
        $code = $codes[$index];
        
        $training_user_table_name = $wpdb->prefix ."cm_training_users";
        
        $prepared_string = $wpdb->prepare("SELECT id FROM $training_user_table_name WHERE email = %s",$email);
        $training_user_results = $wpdb->get_row($prepared_string,OBJECT);
        $user_id = $training_user_results->id;
        if(!$user_id){
            
            $course_code_table = $wpdb->prefix ."cm_course_codes";
            $prepared_string = $wpdb->prepare("SELECT id FROM $course_code_table WHERE redeemed_by = %s",$user_id);
            $course_code_result = $wpdb->get_row($prepared_string,OBJECT);
        
        if($course_code_result){
            return new WP_REST_Response(array(
                'status' => 'success',
                'message' => 'This user has already been assigned a code for this course',
                'isAssigned' => true,
            ), 200);
        }
        
        
            $wpdb->insert(
                $training_user_table_name,
                array(
                    'name' => $name,
                    'email' => $email,
                    'hr_manager_id' => $hr_manager_id
                )
            );
            $user_id = $wpdb->insert_id;
        }
        
        if(cm_add_code_to_user($user_id,$code) === 1){
            $employee_count = count(cm_get_manager_employees());
            array_push($recipientData, array(
                'email' => $email,
                'params' => array(
                    'code' => $code,
                    'url' => 'https://example.com/registration?' . urlencode($name) . '&' . $email,
                    'name' => $name
                )
            ));
        }
    }

    if(!sendinblue_email_bulk($recipientData)){
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'Error during submission',
        ), 500);
    }
    
    return new WP_REST_Response(array(
        'status' => 'success',
        'message' => 'Data submitted successfully',
        'seatsUsed' => count(cm_get_manager_employees()),
    ), 200);
}




























