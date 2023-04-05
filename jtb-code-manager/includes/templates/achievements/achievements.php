<?php
require_once( plugin_dir_path( __FILE__ ) . 'training-course-dashboard-queries.php');
$training_user = cm_get_training_courses();
?>

<h1>Achievements</h1>

<div class="cm-training-content-container" id="trainingContainer2">
    
    <?php foreach ($training_user as $course) : ?>
    <!--Achievement Card-->
    <div class="cm-training-card">
        <div class="cm-image-card">
            <img src="https://test.openform.online/staging/codeblue/wp-content/uploads/2023/03/trophy.svg" />
        </div>
        <h4><?php echo $course->name ?></h4>
    </div>
    <!--END-Achievement Card-END-->
    <?php endforeach;?>
    
</div>