<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$company = new COMPANIES($db1,$id);
$sendTo = $company->get_email();
if ($company->get_contactperson()!="") {
    $customer = $company->get_contactperson();
}
else {
    $customer = $company->get_companyname();
}

$username = $company->get_username();
$password = $company->get_password();

$err = ""; $msg="";
if (isset($_GET['send']) && $_GET['send']==1) {
    if (trim($_POST['t_email'])=='' 
            || !strpos($_POST['t_email'],"@")
            || strlen(trim($_POST['t_email']))<4) {
            $err .= 'Invalid Email.<br/>';	
    }
    if (trim($_POST['t_subject'])=='') {
        $err .= 'Invalid Subject.<br/>';	
    }
    if (trim($_POST['t_message'])=='') {
        $err .= 'Invalid Text.<br/>';	
    }
    
    if ($err == '') {
	require 'phpmailer/PHPMailerAutoload.php';
        //Create a new PHPMailer instance
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom('info@epagelmatias.gr', 'Epagelmatias.gr');
        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to

        //$mail->addAddress('info@abhayayoga.gr', 'Abhaya Yoga');
        $mail->addAddress($_POST['t_email'], $customer);
        $mail->AddCC('info@epagelmatias.gr', 'admin');
        //Set the subject line
        $mail->Subject = $_POST['t_subject'];
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
        //Replace the plain text body with one created manually
        $imgsrc = "http://www.epagelmatias.gr/img/logo.png";
        $imgpath = "<img src=\" $imgsrc \" width=\"267\" height=\"90\"' />";
        $extramsg = "<br/><br/><span style=\"font-size:10px\">Αυτό το ηλεκτρονικό μήνυμα σας αποστέλλεται απευθείας από 
                    την ιστοσελίδα epagelmatias.gr.<br/><br/>
                    &copy; epagelmatias.gr 2014. Όλα τα δικαιώματα διατηρούνται.</span>".
					"<br/><br/>CUSTOMER:".$id." / USER:".$_SESSION['user_fullname'];
        
        $mail->msgHTML($_POST['t_message'].$imgpath.$extramsg);
        $mail->AltBody = $_POST['t_message'];
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.gif');

        //send the message, check for errors
        if (!$mail->send()) {
                $err = "Mailer Error: " . $mail->ErrorInfo;
        } else {
                $msg = "Your message was sent successfully!";
        }
    }
}

?>


<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>PANELINIOS- CRM</title>
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />
        
        <script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector:'#t_message',
                menubar : false,
                relative_urls: false,
                convert_urls: false}); 

        </script>
        
        <style>
            
            #t_message {
                height: 400px;
            }
            
        </style>
    
    </head>
    <body style="background-color: #ddd; padding-top:40px">
        
        
        <form action="sendoffer.php?send=1&id=<?php echo $id; ?>" method="POST" style="max-width:1000px; margin: auto; padding: 20px;">
            
            <h1 style="margin-bottom: 20px;">Αποστολή κωδικών</h1>
            
            <h3 style="margin-bottom: 20px;"><?php echo "Εταιρεία: ".$company->get_companyname(); ?></h3>
            
            <?php if ($err!="") { echo "<h2> $err </h2>";} ?>
            <?php if ($msg!="") { echo "<h2> $msg </h2>";} ?>
            
            <?php
            
            if (isset($_GET['send']) && $_GET['send']==1) {
                $sendTo = $_POST['t_email'];            
            }
            $t_email = new textbox("t_email", "ΠΡΟΣ:", $sendTo);
            $t_email->get_Textbox();
            
            $subject = "Αποστολή κωδικών epagelmatias.gr";
            if (isset($_GET['send']) && $_GET['send']==1) {
                $subject = $_POST['t_subject'];            
            }
            $t_subject = new textbox("t_subject", "ΘΕΜΑ", $subject);
            $t_subject->get_Textbox();
            
            
            $offerText = "Αγαπητέ $customer <br/><br/>
 
                    Μετά από την επικοινωνία με το τηλεφωνικό μας κέντρο, σας 
                    στέλνουμε τους κωδικούς πρόσβασης για την καταχώρησή σας 
                    στον online κατάλογο epagelmatias.gr. <br/>
                    <br/><br/>
                    <strong>
                    Username: $username <br/>
                    Password: $password  </strong><br/><br/>

                    www.epagelmatias.gr <br/><br/>

                    Σας ευχαριστούμε, <br/>
                    epagelmatias.gr <br/><br/>

                    τηλεφωνικό κεντρο : 211-1091200 ( 20 γραμμες ) <br/><br/>

                    mail : info@epagelmatias.gr <br/><br/><br/><br/>";
            
            if (isset($_GET['send']) && $_GET['send']==1) {
                $offerText = $_POST['t_message'];            
            }
            
            $t_message = new textbox("t_message", "ΚΕΙΜΕΝΟ", $offerText);
            $t_message->set_multiline();
            $t_message->get_Textbox();
            
            ?>
            
            <div style="clear: both; height: 1em"></div>
            
            <?php
            
            $btnOK = new button("btnOK", "ΑΠΟΣΤΟΛΗ");
            $btnOK->get_button();
               
            
            
            ?>
            
            
            <div style="clear: both"></div>
        
        </form>
        
        <div style="max-width:1000px; margin: auto; padding-top: 40px;">
            <a class="button" href="editcompany.php?id=<?php echo $id; ?>">Επιστροφή στην καρτέλα εταιρείας</a>
        </div>
        
        
        <div style="position: fixed; top:20px; right:20px;font-size: 30px;">
            <a href="editcompany.php?id=<?php echo $id; ?>">
                <span class="fa fa-close"></span>
            </a>
        </div>
        
        
    </body>
</html>