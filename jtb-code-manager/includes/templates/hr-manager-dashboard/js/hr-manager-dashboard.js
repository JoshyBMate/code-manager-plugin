window.addEventListener("load",()=>{
    const headers = document.querySelectorAll(".cm-accordion-header");
    const invite_buttons = document.querySelectorAll("[data-element='cm-invite-button']");
    const bulk_email_button = document.querySelector("[data-element='cm-bulk-email-button']");
    const email_menu_close_button = document.querySelector("[data-element='cm-email-menu-close-button']");
    const email_menu = document.querySelector("[data-element='cm-email-menu']");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const send_button = document.querySelector("[data-element='cm-send-bulk-email-button']");
    const all_codes = document.querySelectorAll("div[data-element='cm-accordion-body-row']");
    const filter_items = document.querySelectorAll("div[data-element='cm-filter-item']");
    
    bulk_email_button.addEventListener("click",toggle_email_menu);
    email_menu_close_button.addEventListener("click",toggle_email_menu);
    
    
    function toggle_email_menu(){
    	email_menu.classList.toggle("open");
    }
    
    function validateInputs(name,email) {
        // Check if the inputs are valid
        if (name.length > 0 && email.length > 0 && isValidEmail(email)) {
            // Inputs are valid - do something
           console.log('Inputs are valid!');
           return true;
        } else {
            // Inputs are invalid - display an error message
            alert('Please fill out both inputs with valid information.');
            return false;
         }
    }
    
    function isValidEmail(email) {
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         return emailRegex.test(email);
    }
    
    function filterDashboard(event){
        filter_items[0].classList.remove("selected");
        filter_items[1].classList.remove("selected");
        filter_items[2].classList.remove("selected");
        event.target.classList.add("selected");
        if(event.target.getAttribute("data-unused")){
            for(let i = 0; i < all_codes.length; i++){
                all_codes[i].style.display = "flex";
                if(all_codes[i].getAttribute("data-code-used")){
                    all_codes[i].style.display = "none";
                }
            }
        }
        
        if(event.target.getAttribute("data-used")){
            for(let i = 0; i < all_codes.length; i++){
                all_codes[i].style.display = "flex";
                if(all_codes[i].getAttribute("data-code-id")){
                    all_codes[i].style.display = "none";
                }
            }
         }
         
        if(event.target.getAttribute("data-all")){
            for(let i = 0; i < all_codes.length; i++){
                all_codes[i].style.display = "flex";
            }
        }
    }
    
    headers.forEach(header => {
    	header.addEventListener("click", function() {
    		this.classList.toggle("active");
    		const accordionContainer = this.parentElement;
    		accordionContainer.classList.toggle("active");
    		const body = this.nextElementSibling;
    		if (body.style.maxHeight) {
    			body.style.maxHeight = null;
    		} else {
    			body.style.maxHeight = body.scrollHeight + "px";
    		} 
    	});
    });

    invite_buttons.forEach((button,index) =>{
    	button.addEventListener("click",function(event){
    	    const codeID = this.getAttribute("data-code-id");
    	    const selectedRow = document.querySelector(`[data-element='cm-accordion-body-row'][data-code-id='${codeID}']`);
    	    const selectedRowParent = selectedRow.parentElement.getAttribute("data-product-id");
    	    const employeeNameInput = selectedRow.querySelector("input[name='fullname']");
    	    const employeeEmailInput = selectedRow.querySelector("input[name='email']");
    	    const employeeCodeInput = selectedRow.querySelector("input[name='code']");
    	    const employeeName = employeeNameInput.value;
    	    const employeeEmail = employeeEmailInput.value.toLowerCase();
    	    const employeeCode = employeeCodeInput.value;
    	    if(validateInputs(employeeName,employeeEmail)){
    	        employeeNameInput.setAttribute("readonly",true);
    	        employeeEmailInput.setAttribute("readonly",true);
    	        event.target.setAttribute("disabled",true);
    	        verifyEmployeeExistance(employeeCode,employeeName,employeeEmail,event.target,selectedRow,selectedRowParent);
    	    }
    	});
    });
    
    send_button.addEventListener('click',()=>{
        const bulkEmailList = document.querySelector('[data-element="cm-bulk-email-list"]').value.trim();
        const bulkCodeList = document.querySelectorAll('input[data-element="cm-read-only-input"]');
        const selectedProductId = document.querySelector('[data-element="product-dropdown"]').value;
        
        if (!bulkEmailList || !selectedProductId) {
            alert('Please enter email addresses and select a product');
            return;
        }
  
        const emailList = bulkEmailList.split(',').map(code => code.trim());
        const codeArray = [];
        
        bulkCodeList.forEach(code => {
            codeArray.push(code.value);
        });
        
        if(emailList.length > codeArray.length){
            alert("You have entered more emails than you have code, you have: " + codeArray.length + " remaining");
            return;
        }
        const formData = new FormData();
        formData.append('emails', JSON.stringify(emailList));
        formData.append('codes', JSON.stringify(codeArray));
        formData.append('product_id', selectedProductId);
        formData.append("user_id", ajax_object.user_id);
        
        fetch('https://test.openform.online/staging/codeblue/wp-json/cm-training/v1/verify-employee-batch', {
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
            const seatsUsed = document.querySelector("span[data-element='cm-seats-used']");
            seatsUsed.innerHTML = result.seatsUsed;
            console.log(result);
        })
        .catch((error) => {
            console.error(error);
        });
    });
    
    for(let i = 0; i < filter_items.length; i++ ){
        filter_items[i].addEventListener("click",filterDashboard);
    }
});
