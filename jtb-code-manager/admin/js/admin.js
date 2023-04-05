(function ($) {
    $(document).ready(function () {
        console.log("hello");
        let courseQuestionsContainer = $('#course-questions-container');
        let addCourseQuestionBtn = $('#add-course-question');

        addCourseQuestionBtn.on('click', function () {
            let newIndex = $('.course-question').length;
            let newQuestion = $('<div class="course-question" data-index="' + newIndex + '">' +
                '<label>Question:</label>' +
                '<input type="text" name="cm_course_questions[' + newIndex + '][question]" />' +
                '<label>Correct Answer:</label>' +
                '<input type="text" name="cm_course_questions[' + newIndex + '][correct_answer]" />' +
                '<label>Incorrect Answer 1:</label>' +
                '<input type="text" name="cm_course_questions[' + newIndex + '][incorrect_answer_1]" />' +
                '<label>Incorrect Answer 2:</label>' +
                '<input type="text" name="cm_course_questions[' + newIndex + '][incorrect_answer_2]" />' +
                '<label>Incorrect Answer 3:</label>' +
                '<input type="text" name="cm_course_questions[' + newIndex + '][incorrect_answer_3]" />' +
                '<button type="button" class="button remove-question">Remove Question</button>' +
                '</div>');

            courseQuestionsContainer.append(newQuestion);
        });

        courseQuestionsContainer.on('click', '.remove-question', function () {
            $(this).closest('.course-question').remove();
        });
    });
})(jQuery);
