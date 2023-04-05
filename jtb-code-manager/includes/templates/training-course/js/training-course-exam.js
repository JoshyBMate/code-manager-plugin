let currentIndex = 0;
let questionsList = [];
let totalQuestions ="";
let isSelected = 0;
const currentUrl = window.location.href;
// Create a new URL object
const url = new URL(currentUrl);
// Get the value of the parameter "id"
const courseID = url.searchParams.get("id");

function getNextQuestion() {
    const answerContainer = document.querySelector("div[data-element='cm-answer-container']");
    answerContainer.classList.add("loading");
    const currentQuestion = document.querySelectorAll("span[data-element='cm-current-question']");
    const data = {
        id: courseID,
        index: currentIndex
    };
    fetch('https://test.openform.online/staging/codeblue/wp-json/cm-exam/v1/get-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then((response) => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`Error submitting data: ${response.status} ${response.statusText}`);
        }
    })
    .then((result) => {
        let questionNumber = currentIndex + 1;
        const question = result.question;
        const questions = result.mixed_questions.sort(() => Math.random() - 0.5);
        const answer = result.answer;
        const questionContainer = document.querySelector("div[data-element='cm-question-container']");
        questionContainer.innerHTML = question;
        const answerBlocks = answerContainer.querySelectorAll("div[data-element='cm-answer-block']");
        if(currentIndex >= parseInt(totalQuestions)-1){
            const nextQuestionButton = document.querySelector("button[data-element='cm-next-question']");
            nextQuestionButton.innerHTML = 'Please click "Submit Answers"';
            nextQuestionButton.style.pointerEvents = "none";
            const submitButton = document.querySelector("a[data-element='cm-submitButton']");
            submitButton.classList.remove("buttonDisabled");
            submitButton.setAttribute("disabled",false);
        }
        currentQuestion[0].innerHTML = `${questionNumber}`;
        currentQuestion[1].innerHTML = `${questionNumber}`;
        for(let i = 0; i < questions.length; i++){
            answerBlocks[i].innerHTML = questions[i];
        }
        answerContainer.classList.remove("loading");
    })
    .catch(error => {
        console.log('Error:', error);
    });
}

window.addEventListener("load",()=>{
    totalQuestions = document.querySelector("span[data-element='cm-total-questions']").innerHTML.split(" ").pop();
    getNextQuestion();
    
    const nextQuestionButton = document.querySelector("button[data-element='cm-next-question']");
    const answerContainer = document.querySelector("div[data-element='cm-answer-container']");
    const answerBlocks = answerContainer.querySelectorAll("div[data-element='cm-answer-block']");
    const submitButton = document.querySelector("a[data-element='cm-submitButton']");
    const answersArray = [];
    submitButton.addEventListener("click",submitAnswers);
    for(let i = 0; i < answerBlocks.length; i++){
        answerBlocks[i].addEventListener("click",createSelectedState);
    }
    
    nextQuestionButton.addEventListener("click",()=>{
        if(!isSelected){
            alert("Please select an answer");
            return;
        }
        currentIndex += 1;
        getNextQuestion();
        const selectedBlock = answerContainer.querySelector(".selected");
        selectedBlock.classList.remove("selected");
        isSelected = 0;
        answersArray.push("placeholder");
    });
    
    function createSelectedState(){
        for(let i = 0; i < answerBlocks.length; i++){
            answerBlocks[i].classList.remove("selected");
        }
        this.classList.add("selected");
        isSelected = 1;
        answersArray.pop();
        answersArray.push(this.innerHTML);
    }
    
    function submitAnswers(event){
        event.preventDefault();
        if(!isSelected){
            alert("Please select an answer");
            return;
        }
        event.target.classList.add("loading");
        gradeExam(answersArray);
    }
    
});
