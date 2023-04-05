<?php
    require_once( plugin_dir_path( __FILE__ ) . 'training-course-queries.php');
    $training_course = cm_get_course_data();
    $product_time = get_field('course_duration',$training_course->id);
    $course_name = $training_course->name;
?>

<div class="cm-training-container">
    <div class="cm-training-left">
        <h1><?php echo $course_name ?></h1>
        <p><?php echo $product_time ?></p>
        <a href="../training-course-exam/<?php echo "?id=".$training_course->id ?>" class="cm-beginButton">Begin Test</a>
        <a href="../training-course-dashboard/" class="cm-exitButton">Exit Test</a>
    </div>

    <div class="cm-training-right">

    <div class="cm-breadcrumb">My Account > Training Material > <?php echo $course_name ?> </div>
    <div class="cm-video">
     <video controls preload="metadata">
         <source src="<?php echo $training_course->videoUrl ?>" type="video/mp4">
         <source src="<?php echo $training_course->videoUrl ?>" type="video/ogg">
     </video>
    </div>
    <div class="cm-desc">
        <h2 style="color: #FF0F82; font-size: 18px; padding-bottom: 0;">Watch the video and answer the questions.</h2>
        <h2>Description</h2>
        <p><?php echo $training_course->description ?></p>
    </div>
    </div>

</div>