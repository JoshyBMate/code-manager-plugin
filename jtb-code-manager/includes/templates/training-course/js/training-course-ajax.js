function gradeExam(answersArray) {
    const data = {
        id: courseID,
        answers: JSON.stringify(answersArray)
    };
    fetch('https://test.openform.online/staging/codeblue/wp-json/cm-exam/v1/grade-exam', {
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
        window.location.href = result;
    })
    .catch(error => {
        console.log('Error:', error);
    });
}