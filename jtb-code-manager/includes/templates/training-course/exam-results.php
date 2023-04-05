<?php
    require_once( plugin_dir_path( __FILE__ ) . 'training-course-queries.php');
if (isset($_GET['status'])) {
    $encoded_status = $_GET['status'];
    $encrypted_status = rawurldecode($encoded_status);
    $status = decrypt_status($encrypted_status, 'openform');
    $training_course = cm_get_course_data();
    $product_time = get_field('course_duration',$training_course->id);
    $course_name = $training_course->name;
    
    if ($status !== null) {
        echo "Status: " . $status . "<br>";
    } else {
        echo "Do not try to edit the status in the URL";
        exit;
    }
} else {
    echo "Status is not set.<br>";
}
$course_id = $_GET['id'];
?>


<?php if(!$status) : ?>


<div class="cm-training-container">
    <div class="cm-training-left">
        <h1><?php echo $course_name ?></h1>
        <p><?php echo $product_time ?></p>
        <a href="../training-course-dashboard/" class="cm-exitButton">Exit Test</a>
    </div>
    <div class="cm-training-right">
        <div class="cm-breadcrumb">My Account > Training Material > <?php echo $course_name ?> </div>
        <h1 class="cm-result-title">Based off your answer selections you haven't met the required pass rate.</h1>
        <h2 class="cm-result-subtitle">Not to worry these things happen, let's just try again.</h2>
        <p class="cm-result-direction">Please click the link below to review the course material once more
and start the examination process.</p>
        <a class="cm-result-button" href=" ../training-course/?id=<?php echo $course_id?>">Training Material</a>
    </div>
</div>
<div class="cm-fail-graphic"></div>


<?php endif; ?>

<?php if($status) : ?>

<div class="cm-training-container">
    <div class="cm-training-left">
        <h1><?php echo $course_name ?></h1>
        <p><?php echo $product_time ?></p>
        <a href="../training-course-dashboard/" class="cm-exitButton">Exit Test</a>
    </div>
    <div class="cm-training-right">
        <div class="cm-breadcrumb">My Account > Training Material > <?php echo $course_name ?> </div>
        
        <h1 class="cm-result-title">BASED OFF OF YOUR ANSWERS SUBMITTED YOU HAVE PASSED.</h1>
        <h2 class="cm-result-subtitle">Congratulations on Becoming a Lifesaver!</h2>

        <p class="cm-result-direction">Please click the link below to get your certificate, for this amazing achievement.</p>
        
        <a class="cm-result-button" href="../training-course-congratulations/?id=<?php echo $course_id ?>">Get Certificate</a>
        
        </div>
</div>
<div class="cm-success-graphic"></div>

<?php endif; ?>


