<?php

function cm_training_course_left_bar() {
    ob_start();
    include(plugin_dir_path(__FILE__) . 'includes/templates/training-course/training-course-left-content.php');
    $output = ob_get_clean();
    echo $output;
}
add_action('cm_training_course_left_bar', 'cm_training_course_left_bar');
