<?php

class CM_Training_Material{
    public $id;
    public $name;
    public $description;
    public $length;
    public $videoUrl;
    
    public function __construct($id,$name,$description,$length,$videoUrl){
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->length = $length;
        $this->videoUrl = $videoUrl;
    }
}

class CM_Exam{
    public $question;
    public $mixed_questions;
    public $answer;
    
    public function __construct($question,$mixed_questions,$answer){
        $this->question = $question;
        $this->mixed_questions = $mixed_questions;
        $this->answer = $answer;
    }
}

function cm_get_course_data(){
    global $wpdb;
    
    $table_name = $wpdb->prefix ."posts";
    $course_id = $_GET["id"];
    
    $prepared_string = $wpdb->prepare("SELECT id,post_title,post_content FROM $table_name WHERE id = %s",$course_id);
    $results = $wpdb->get_row($prepared_string,OBJECT);
    
    $user_validation = cm_verify_user_code_access($course_id);
    
    if($user_validation !== "valid"){
        return $user_validation;
    }
    
    $training_course = new CM_Training_Material($course_id,$results->post_title,$results->post_content,get_field('course_duration',$results->id),get_field('training_course_video',$results->id));
    
    return $training_course;
}

function cm_verify_user_code_access($course_id,$get_id = false){
    global $wpdb;
    
    $current_user_email = wp_get_current_user()->user_email;
    
    $training_users_table = $wpdb->prefix ."cm_training_users";
    $course_codes_table = $wpdb->prefix. "cm_course_codes";
    
    $training_users_string = $wpdb->prepare("SELECT id,name FROM $training_users_table WHERE email = %s",$current_user_email);
    $training_users_result = $wpdb->get_row($training_users_string,OBJECT);
    $training_user_id = $training_users_result->id;
    $training_user_name = $training_users_result->name;
    
    $course_codes_string = $wpdb->prepare("SELECT id FROM $course_codes_table WHERE product_id = %s",$course_id);
    $course_codes_result = $wpdb->get_row($course_codes_string,OBJECT);
    $course_code_id = $course_codes_result->id;
    
    if(!$course_code_id){
        return "You do not have access to this training course";
    }
    if($get_id){
        return $training_user_name;
    }
    return "valid";
}

function cm_get_course_exam_data(WP_REST_Request $request){
    $course_id = sanitize_text_field($request->get_param("id"));
    $current_index = sanitize_text_field($request->get_param("index"));
    $user_validation = cm_verify_user_code_access($course_id);
   
    if($user_validation !== "valid"){
        wp_send_json_error(array('message' => $user_validation));
    }
    
    // Get the saved questions and answers
    $course_questions_json = get_post_meta($course_id, 'cm_course_questions', true);
    $course_questions = json_decode($course_questions_json, true); // Set the second parameter to true to get an associative array
    $questions_list = [];
    // Check if there are any saved questions and answers
    if (!empty($course_questions) && is_array($course_questions)) {
        // Loop through each question and its answers
        for ($x = 0; $x <= $current_index; $x++) {
            $all_questions = [];
            $question = $course_questions[$x]['question'];
            $correct_answer = $course_questions[$x]['correct_answer'];
            $all_questions[] = $course_questions[$x]['incorrect_answer_1'];
            $all_questions[] = $course_questions[$x]['incorrect_answer_2'];
            $all_questions[] = $course_questions[$x]['incorrect_answer_3'];
            $all_questions[] = $course_questions[$x]['correct_answer'];
            
            $exam = new CM_Exam($question,$all_questions,$correct_answer);
            $questions_list[] = $exam;
        }
        if ($current_index >= count($questions_list)) {
            // Reset the index to 0 and shuffle the question list
            $current_index = 0;
            shuffle($questions_list);
        }
        // Return the question at the current index
        return $questions_list[$current_index];
    }
}

function cm_count_course_questions($course_id) {
    $course_questions_json = get_post_meta($course_id, 'cm_course_questions', true);
    $course_questions = json_decode($course_questions_json, true);
    $question_count = count($course_questions);
    return $question_count;
}

function cm_grade_exam(WP_REST_Request $request){
    global $wpdb;
    $table_name = $wpdb->prefix . "cm_user_achievements";
    
    $course_id = sanitize_text_field($request->get_param("id"));
    $answers_json = sanitize_text_field($request->get_param("answers"));
    $answers = json_decode($answers_json);
    $user_validation = cm_verify_user_code_access($course_id);
    
    if($user_validation === "You do not have access to this training course"){
        return $user_validation;
    }
    
    $course_questions_json = get_post_meta($course_id, 'cm_course_questions', true);
    $course_questions = json_decode($course_questions_json, true); // Set the second parameter to true to get an associative array
    
     $correct_answers = [];
    
    if (!empty($course_questions) && is_array($course_questions)) {
        // Loop through each question and its answers
        foreach ($course_questions as $question_data) {
            $correct_answer = $question_data['correct_answer'];
            
            $correct_answers[] = $correct_answer;
        }
    }
    
    $incorrect_answers = array_diff($answers,$correct_answers);
    if(empty($incorrect_answers)){
        $status = 1;
        // $wpdb->insert(
        //     $table_name,
        //     array(
        //         'user_id' => cm_get_user_id(),
        //         'product_id' => $course_id,
        //         'data_achieved' => current_time( 'mysql' )
        //     )
        // );
    }else{
         $status = 0;        
    }
    setcookie('user_name', $user_validation, time() + 3600, '/');
    $encrypted_status = encrypt_status($status, 'openform');
    $encoded_status = urlencode($encrypted_status);
    $result_url = home_url("my-account/training-course-exam-results/?status={$encoded_status}&id={$course_id}");
    
    return $result_url;
}

function encrypt_status($status, $key) {
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($status, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

function decrypt_status($ciphertext, $key) {
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $status = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    
    // Confirm that the HMAC matches
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
    if (hash_equals($hmac, $calcmac)) {
        return $status;
    } else {
        return null;
    }
}

function cm_get_user_name(){
    global $wpdb;
    
    $current_user_email = wp_get_current_user()->user_email;
    
    $training_users_table = $wpdb->prefix ."cm_training_users";
    $course_codes_table = $wpdb->prefix. "cm_course_codes";
    
    $training_users_string = $wpdb->prepare("SELECT name FROM $training_users_table WHERE email = %s",$current_user_email);
    $training_users_result = $wpdb->get_row($training_users_string,OBJECT);
    $training_user_name = $training_users_result->name;
    
    return $training_user_name;
}

function cm_get_user_id(){
    global $wpdb;
    
    $current_user_email = wp_get_current_user()->user_email;
    
    $training_users_table = $wpdb->prefix ."cm_training_users";
    $course_codes_table = $wpdb->prefix. "cm_course_codes";
    
    $training_users_string = $wpdb->prepare("SELECT id FROM $training_users_table WHERE email = %s",$current_user_email);
    $training_users_result = $wpdb->get_row($training_users_string,OBJECT);
    $training_user_id = $training_users_result->id;
    
    return $training_user_id;
}

function cm_set_user_cookie() {
    // Get the current user
    $user_name = cm_get_user_name();
    if ($user_name) {
        // Set a cookie with the user's name
        setcookie('user_name', $user_name, time() + 3600, '/');
    }
}
add_action('init', 'cm_set_user_cookie');

function cm_set_user_cookie_id() {
    // Get the current user
    $user_id = cm_get_user_id();
    if ($user_id) {
        // Set a cookie with the user's name
        setcookie('user_id', $user_id, time() + 3600, '/');
    }
}
add_action('init', 'cm_set_user_cookie_id');































