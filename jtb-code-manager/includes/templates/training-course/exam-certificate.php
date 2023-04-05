<?php
if (isset($_COOKIE['user_name'])) {
    $stored_value = urldecode($_COOKIE['user_name']);
}
?>
<style>
    
    body, html {
        margin:0px;
        padding:0px;
        background-color:#2400BA;
        background-size:contain;
        color:#ffffff !important;
    }
    
    .cm-training-right iframe {
    width:100%;
    height:550px;
    margin-top: 51px;
    margin-bottom:50px;
    background-color: #2400BA;
}

@font-face {
  font-family: Handwritten;
  src: url(https://test.openform.online/staging/codeblue/wp-content/uploads/2023/04/Marla.otf);
}

.cm-certificate .cm-certLogo {
    width: 220px;
    height: 66px;
    display: block;
    margin: 100px auto 30px auto;
}

.cm-cert-text1 {
    color: #ffffff;
    width: 100%;
    text-align: center;
    font-family: 'UniSans';
    font-size:34px;
}

.cm-cert-text2 {
    color: #ffffff;
    width: 100%;
    text-align: center;
    font-family: 'UniSans';
    font-size:22px;
}

.cm-cert-name {
    color: #ffffff;
    width: 100%;
    text-align: center;
    font-family: Handwritten;
    font-size: 110px;
    line-height: 100px;
    padding-top: 24px;
}

.cm-certBadge {
    text-align: center;
    margin-top: 30px;
}

.cm-certBadge img {
    width: 90px;
    margin: auto;
    text-align: center;
}

.cm-backgroundImage {
    width: 100%;
    position: absolute;
    top: 0;
    z-index:10;
}

.cm-backgroundImage img {
    width: 100%;
}


.cm-certificate {
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 99999;
}

@media print { 
 
    .cm-cert-text1, .cm-cert-text2, .cm-cert-name {
        filter:brightness(1000%);
        
    }
 
}

@-moz-document url-prefix() {
    
    @media print { 
 
        .cm-cert-text1, .cm-cert-text2, .cm-cert-name {
            filter:invert(100%);
        
        }
 
    }
}

    
</style>
<script>
    window.addEventListener('message', function(event) {
      if (event.data.type === 'print') {
        var content = event.data.content;
        // Print the content as a PDF and send a reply message
        window.print();
        var message = {
          type: 'print-pdf',
          content: content
        };
        event.source.postMessage(message, '*');
      }
    });
  </script>

<div class="cm-certificate">
    <div class="cm-certLogo"><img src="https://test.openform.online/staging/codeblue/wp-content/uploads/2023/01/logo_light.svg" /></div>
    <div class="cm-cert-text1" style="color:#ffffff;">CERTIFICATE OF COMPLETION</div>
    <div class="cm-cert-text2">THIS CERTIFICATE IS PROUDLY PRESENTED TO</div>
    <div class="cm-cert-name"><?php echo $stored_value ?></div>
    <div class="cm-cert-text2">WHO HAS SUCCESSFULLY COMPLETED TRAINING</div>
    <div class="cm-certBadge"><img src="https://test.openform.online/staging/codeblue/wp-content/uploads/2023/03/badge1.png" /></div>
</div>
<div class="cm-backgroundImage">
    <img src="https://test.openform.online/staging/codeblue/wp-content/uploads/2023/03/cert-bg-scaled.jpg"/>
</div>



