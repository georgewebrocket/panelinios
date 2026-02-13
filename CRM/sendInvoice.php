<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

require 'phpmailer/PHPMailerAutoload.php';

class Mailer extends PHPMailer {
    /**
     * Save email to a folder (via IMAP)
     *
     * This function will open an IMAP stream using the email
     * credentials previously specified, and will save the email
     * to a specified folder. Parameter is the folder name (ie, Sent)
     * if nothing was specified it will be saved in the inbox.
     *
     * @author David Tkachuk <http://davidrockin.com/>
     */
    public function copyToFolder($folderPath = null) {
        $message = $this->MIMEHeader . $this->MIMEBody;
        $path = "INBOX" . (isset($folderPath) && !is_null($folderPath) ? ".".$folderPath : ""); // Location to save the email
        //$imapStream = imap_open("{" . $this->Host . ":993}" . $path , $this->Username, $this->Password);
        $imapStream = imap_open("{mail.epagelmatias.gr:993/imap/ssl/novalidate-cert}" . $path , $this->Username, $this->Password);

        //imap_append($imapStream, "{" . $this->Host . ":993}" . $path, $message);
        imap_append($imapStream, "{mail.epagelmatias.gr:993/imap/ssl/novalidate-cert}" . $path, $message);
        
        imap_close($imapStream);
    }
}

$id = $_GET['id'];
$invoice = new INVOICES($db1, $id);
$companyid = $invoice->get_company();
$company = new COMPANIES($db1, $companyid);
$sendTo = $company->get_email();
if ($company->get_contactperson()!="") {
    $customer = $company->get_contactperson();
}
else {
    $customer = $company->get_companyname();
}



//$price = func::vlookup("price", "PACKAGES", "id=".$company->get_package(), $db1);
//$discount = func::vlookup("discount", "DISCOUNTS", "id=".$company->get_discount(), $db1);;
//$finalprice = $price * (1-$discount/100); 
//$finalprice = round($finalprice, 2);

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
	//require 'phpmailer/PHPMailerAutoload.php';
        //Create a new PHPMailer instance
        //$mail = new PHPMailer();
        $mail = new Mailer(true);
        $mail->IsSMTP();
        $mail->Host = "srv1.epagelmatias.gr"; //srv1.epagelmatias.gr
        ////$mail->SMTPDebug = 2; ///
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "srv1.epagelmatias.gr";
        //$mail->Host = "mail.epagelmatias.gr";
        $mail->Port = 465; //465 //25                // set the SMTP port
        $mail->Username = "logistirio@epagelmatias.gr"; // SMTP account username
        $mail->Password = "dtranq12!";        // SMTP account password
        
        $mail->CharSet = 'UTF-8';
        //Set who the message is to be sent from
        $mail->setFrom('logistirio@epagelmatias.gr', 'Epagelmatias.gr');
        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to

        //$mail->addAddress('info@abhayayoga.gr', 'Abhaya Yoga');
        $mail->addAddress($_POST['t_email'], $customer);
        //$mail->AddCC('logistirio@epagelmatias.gr', 'admin');
        //Set the subject line
        $mail->Subject = $_POST['t_subject'];
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
        //Replace the plain text body with one created manually
        $imgsrc = "http://www.epagelmatias.gr/img/logo.png";
        $imgpath = "<img src=\" $imgsrc \" width=\"273\" height=\"46\"' />";
        $extramsg = "<br/><br/><span style=\"font-size:10px\">Αυτό το ηλεκτρονικό μήνυμα σας αποστέλλεται απευθείας από 
                    την ιστοσελίδα epagelmatias.gr.<br/><br/>
                    &copy; epagelmatias.gr 2014. Όλα τα δικαιώματα διατηρούνται.</span>".
					"<br/><br/>CUSTOMER:".$companyid." / USER:".$_SESSION['user_fullname'];
        
        $mail->msgHTML($_POST['t_message'].$imgpath.$extramsg);
        $mail->AltBody = $_POST['t_message'];
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.gif');

        //send the message, check for errors
        if (!$mail->send()) {
                $err = "Mailer Error: " . $mail->ErrorInfo;
        } else {
            //$mail->copyToFolder(); 
            $mail->copyToFolder("Sent"); // Will save into Sent folder    
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
    <body>
        
        
        <form action="sendInvoice.php?send=1&id=<?php echo $id; ?>" method="POST">
            
            <h1>Αποστολή τιμολογίου</h1>
            
            <?php if ($err!="") { echo "<h2> $err </h2>";} ?>
            <?php if ($msg!="") { echo "<h2> $msg </h2>";} ?>
            
            <?php
            
            if (isset($_GET['send']) && $_GET['send']==1) {
                $sendTo = $_POST['t_email'];            
            }
            $t_email = new textbox("t_email", "ΠΡΟΣ:", $sendTo);
            $t_email->get_Textbox();
            
            $invoicenr = $invoice->get_icode();
            
            $subject = "Epagelmatias.gr :: Τιμολόγιο #$invoicenr [$companyid]";
            if (isset($_GET['send']) && $_GET['send']==1) {
                $subject = $_POST['t_subject'];            
            }
            $t_subject = new textbox("t_subject", "ΘΕΜΑ", $subject);
            $t_subject->get_Textbox();
            
            $invoicelink = "http://www.epagelmatias.gr/crm/tcpdf/examples/invoicepdf.php?id=".
                    $invoice->get_id()."&accesstoken=".$invoice->get_accesstoken();
            
            
            $offerText = "Αγαπητέ $customer <br/><br/>
                    Πατήστε στο κόκκινο κουμπί για να κατεβάστε το τιμολόγιο παροχής υπηρεσιών για την καταχώρηση σας στο epagelmatias.gr.<br/> 
                    Παρακαλώ εκτυπώστε το και προωθήστε το στο λογιστήριο σας .
                    <br/><br/><br/><br/>

                    <a href=\"$invoicelink\" style=\"background-color:red; color:white; padding:10px; border-radius:20px; font-size:20px\">ΤΙΜΟΛΟΓΙΟ #$invoicenr</a>
                    
<br/><br/><br/><br/>
                    www.epagelmatias.gr <br/><br/>

                    Σας ευχαριστούμε, <br/>
                    epagelmatias.gr <br/><br/>

                    τηλεφωνικό κεντρο : 211-1091200 ( 20 γραμμες ) <br/><br/>

                    mail : info@epagelmatias.gr <br/><br/><br/><br/>";
            
            if (isset($_GET['send']) && $_GET['send']==1) {
                $offerText = $_POST['t_message'];            
            }
            
            $t_message = new textbox("t_message", "ΚΕΙΜΕΝΟ ΠΡΟΣΦΟΡΑΣ", $offerText);
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
        
        <div style="padding-left:15px">
            <!--<a class="button" href="editcompany.php?id=<?php echo $companyid; ?>">Επιστροφή στην καρτέλα εταιρείας</a>-->
        </div>
        
        
    </body>
</html>