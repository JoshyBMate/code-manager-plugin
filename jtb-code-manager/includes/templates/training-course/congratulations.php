<?php
    
?>

<div class="cm-training-container">
    <div class="cm-training-left">
        <h1>CONGRATULATIONS ONCE AGAIN!</h1>
        <button class="cm-beginButton" onclick="printPdf()">DOWNLOAD</button>
        <a href="https://test.openform.online/staging/codeblue/my-account/"><button class="cm-beginButton">Return to account dashboard</button></a>
    </div>

    <div class="cm-training-right">
        <iframe class="cm-iframe" id="myIframe" src="https://test.openform.online/staging/codeblue/wp-content/plugins/jtb-code-manager/includes/templates/training-course/exam-certificate.php"></iframe>
    </div>

</div>

<script>
    function printPdf() {
      var iframe = document.getElementById('myIframe');
      var contentWindow = iframe.contentWindow;
      console.log(contentWindow)
      var message = {
        type: 'print',
        content: contentWindow.document.documentElement.outerHTML
      };
      contentWindow.postMessage(message, '*');
    }

    window.addEventListener('message', function(event) {
      if (event.data.type === 'print-pdf') {
        var options = {
          printable: event.data.content,
          type: 'html',
          header: '<h1>My Document</h1>',
          footer: null
        };
        printJS(options);
      }
    });
  </script>

