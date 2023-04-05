<?php
require_once( plugin_dir_path( __FILE__ ) . 'training-course-dashboard-queries.php');
$training_user = cm_get_training_courses();
?>

<h1>Training</h1>

<div class="cm-training-content-container" id="trainingContainer2">
	<?php if(empty($training_user)) : ?>
    <div style="padding-bottom: 10px;"><p>No Courses to display</p></div>
    <?php endif; ?>
    <?php foreach ($training_user as $course) : ?>
    <!--Training Card-->
    <div class="cm-training-card">
        <div class="cm-image-card">
            <img src="<?php echo $course->image ?>" />
        </div>
        <h4><?php echo $course->name ?></h4>
        <div class="cm-training-duration"><?php echo $course->duration ?></div>
        <?php if($course->course_status === "2") : ?>
            <div class="cm-training-status" id="status" data-element="status-text">Completed</div>
        <?php endif; ?>
        <a href="<?php echo "../training-course/?id=".$course->id?>" class="cm-training-button">Start Training</a>
    </div>
    <!--END-Training Card-END-->
    <?php endforeach;?>
    
</div>