<?php

class CM_Achievement{
    public $id;
    public $name;
    
    public function __construct($id,$name){
        $this->id=$id;
        $this->name=$name;
    }
}

function cm_get_achievements_data(){
    global $wpdb;
    
    $table_name = $wpdb->prefix ."posts";
    $table_name_achievements = $wpdb->prefix ."cm_user_achievements";
    $user_id = cm_get_user_id();
    
    $prepared_string = $wpdb->prepare("SELECT id,post_title,post_content FROM $table_name wp_p JOIN $table_name_achievements cm_ua ON wp_p.product_id = cm_ua.product_id WHERE cm_ua.user_id = %s",$user_id);
    $results = $wpdb->get_results($prepared_string,OBJECT);
    
    $achievement_list = [];
    
    foreach($results as $result){
        $achievment = new CM_Achievement($result->id,$result->post_title);
        $achievement_list[] = $achievment;
    }
    
    return $achievement_list;
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

?>