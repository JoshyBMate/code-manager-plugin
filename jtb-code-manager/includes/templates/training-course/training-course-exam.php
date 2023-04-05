<?php
    require_once( plugin_dir_path( __FILE__ ) . 'training-course-queries.php');
    $total_questions = cm_count_course_questions($_GET['id']);
    $training_course = cm_get_course_data();
    $product_time = get_field('course_duration',$training_course->id);
    $course_name = $training_course->name;
?>


<div class="cm-training-container">
    <div class="cm-training-left">
        <h1><?php echo $course_name ?></h1>
        <p id="examStatus">Started</p>
        <a data-element="cm-submitButton" href="../training-course-exam/<?php echo "?id=".$training_course->id ?>" class="cm-submitButton buttonDisabled">Submit Answers</a>
        <a href="../training-course-dashboard/" class="cm-exitButton">Exit Test</a>
    </div>
    
    <div class="cm-training-right">
        <div class="cm-breadcrumb">My Account > Training Material > <?php echo $course_name ?> </div>
        <div class="questionNumber">Question <span data-element="cm-current-question"></span></div>
        <div class="cm-question" data-element="cm-question-container"></div>
        
        <div class="cm-answer-container" data-element="cm-answer-container">
            <div class="cm-answer-block" data-element="cm-answer-block"></div>
            <div class="cm-answer-block" data-element="cm-answer-block"></div>
            <div class="cm-answer-block" data-element="cm-answer-block"></div>
            <div class="cm-answer-block last" data-element="cm-answer-block"></div>
        </div>
        
        <div class="cm-page-controls">
            <div class="cm-controlsLeft">
                <span class="cm-current-question" data-element="cm-current-question"></span><span data-element='cm-total-questions'><?php echo " / ". $total_questions ?></span>
            </div>
            
            <div class="cm-controlsRight">
                <button data-element="cm-next-question">Next Question</button>
            </div>
            
        </div>
        
    </div>
</div>





