function verifyEmployeeExistance(employeeCode,employeeName,employeeEmail,inviteButton,selectedRow,selectedRowParent){
    const formData = new FormData();
    formData.append('code',employeeCode);
    formData.append('name',employeeName);
    formData.append('email',employeeEmail);
    formData.append("user_id", ajax_object.user_id);
    formData.append("product_id", selectedRowParent);
    
    fetch('https://test.openform.online/staging/codeblue/wp-json/cm-training/v1/verify-employee', {
        method: "POST",
        body: formData
        
    })
    .then((response) => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`Error submitting data: ${response.status} ${response.statusText}`);
        }
    })
    .then((result) => {
        if(result.isAssigned){
            const employeeNameInput = selectedRow.querySelector("input[name='fullname']");
    	    const employeeEmailInput = selectedRow.querySelector("input[name='email']");
    	    employeeNameInput.removeAttribute("readonly");
    	    employeeEmailInput.removeAttribute("readonly");
    	    inviteButton.removeAttribute("disabled");
            alert(result.message);
            return;
        }
        const accordionHeader = document.querySelector(`[data-product-id='${result.productID}']`).previousElementSibling; 
        const seatsUsed = accordionHeader.querySelector(`span[data-element='cm-seats-used']`);
        seatsUsed.innerHTML = result.seatsUsed;
        console.log(result);
    })
    .catch((error) => {
        console.error(error);
    });
}


function verifyEmployeeExistenceBulk(users) {
    const formData = new FormData();

    for (let i = 0; i < users.length; i++) {
        const user = users[i];
        formData.append(`codes[${i}]`, user.code);
        formData.append(`emails[${i}]`, user.email);
        formData.append(`user_ids[${i}]`, ajax_object.user_id);
    }

    fetch('https://test.openform.online/staging/codeblue/wp-json/cm-training/v1/verify-employee-batch', {
        method: 'POST',
        body: formData
    })
    .then((response) => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`Error submitting data: ${response.status} ${response.statusText}`);
        }
    })
    .then((result) => {
        const seatsUsed = document.querySelector("span[data-element='cm-seats-used']");
        seatsUsed.innerHTML = result.seatsUsed;

        const assignedCodes = result.assignedCodes;
        const failedCodes = result.failedCodes;

        assignedCodes.forEach((code) => {
            const selectedRow = document.querySelector(`tr[data-code="${code.code}"]`);
            const inviteButton = selectedRow.querySelector('.invite-button');
            const employeeNameInput = selectedRow.querySelector("input[name='fullname']");
            const employeeEmailInput = selectedRow.querySelector("input[name='email']");

            employeeNameInput.removeAttribute('readonly');
            employeeEmailInput.removeAttribute('readonly');
            inviteButton.removeAttribute('disabled');

            alert(code.message);
        });

        failedCodes.forEach((code) => {
            console.error(`Failed to verify user with code ${code.code}: ${code.message}`);
        });
    })
    .catch((error) => {
        console.error(error);
    });
}