document.addEventListener("DOMContentLoaded", registerEventListeners);
 function registerEventListeners() {
    var registrationForm = document.getElementById("custom-registration-form");
    var loginForm = document.getElementById("custom-login-form");

    if (registrationForm) {
        registrationForm.addEventListener("submit", registerUser);
    }

    if (loginForm) {
        loginForm.addEventListener("submit", loginUser);
    }
}

function registerUser(event){
     event.preventDefault();
     console.log(event.target.action);
    let formData = new FormData(event.target);
    fetch('/staging/codeblue/wp-admin/admin-post.php', {
        method: 'POST',
        body: formData
    })
    .then(function (response) {
        if (response.ok) {
            window.location.href = 'https://test.openform.online/staging/codeblue/my-account/';
        } else {
            const errorContainer = document.querySelector("div[data-element='cm-form-error']");
            errorContainer.innerHTML = data.error;
        }
    })
    .catch(function (error) {
        alert(error.message);
    });
}


//const loginButton = document.querySelector('#login-button');

function loginUser(event){
    event.preventDefault();
    let formData = new FormData(event.target);
    fetch('/staging/codeblue/wp-admin/admin-post.php', {
        method: 'POST',
        body: formData
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (data) {
        if (data.success) {
            window.location.href = 'https://test.openform.online/staging/codeblue/my-account/';
        } else {
            // Display the error message to the user
            const errorContainer = document.querySelector("div[data-element='cm-form-error']");
            errorContainer.innerHTML = data.error;
        }
    })
    .catch(function (error) {
        alert("An error occurred while processing your request.");
    });
}