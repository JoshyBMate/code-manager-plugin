<?php

class CM_Training_Course {
    public $id;
    public $name;
    public $image;
    public $duration;
    public $status;
    
    public function __construct($id,$name,$image,$duration,$status){
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->duration = $duration;
        $this->status = $status;
    }
}

function cm_get_training_courses(){
    global $wpdb;
    
    $training_course_array = [];
    
    $current_user = wp_get_current_user();
    $current_user_email = $current_user->user_email;
    $training_user_table = $wpdb->prefix ."cm_training_users";
    $course_code_table = $wpdb->prefix ."cm_course_codes";
    $product_table = $wpdb->prefix ."posts";
    
    $traing_user_id_string = $wpdb->prepare("SELECT id FROM $training_user_table WHERE email = %s", $current_user_email); 
    $traing_user_id_result = $wpdb->get_row($traing_user_id_string);
    $training_user_id = $traing_user_id_result->id;
    
    if(!$training_user_id){
        return array();
    }
    $training_course_string = $wpdb->prepare("SELECT wp_p.post_title, wp_p.id,cm_cc.code_status FROM $course_code_table cm_cc JOIN $product_table wp_p ON cm_cc.product_id = wp_p.id WHERE redeemed_by = %s",$training_user_id);
    $training_course_result = $wpdb->get_results($training_course_string,OBJECT);
    
    foreach($training_course_result as $result){
        $result->image = get_the_post_thumbnail_url($result->id);
		$result->url = get_permalink($result->id);
		$result->duration = get_field("course_duration",$result->id);
		
		$training_course_array[] = new CM_Training_Course($result->id,$result->post_title,$result->image,$result->duration,$result->code_status);
    }
    
    return $training_course_array;
    
}