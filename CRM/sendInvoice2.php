<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

require 'phpmailer/PHPMailerAutoload.php';

require_once('php/mailgun.php');

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
$invoice = new INVOICEHEADERS($db1, $id);
$companyid = $invoice->get_company();
$company = new COMPANIES($db1, $companyid);
$onlineId = $company->get_catalogueid() * 2 + 7128;
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

    if ($err=='') {
        $mail = new mailgun();
        $mail->from('logistirio@panelinios.gr')->to($_POST['t_email'])->cc('logistirio@panelinios.gr');
        $mail->subject($_POST['t_subject']);
        
        $imgsrc = "http://www.panelinios.gr/images/logo.png";
        $imgpath = "<img src=\" $imgsrc \" width=\"190\" height=\"70\"' />";
        $extramsg = "<br/><br/><span style=\"font-size:10px\">Αυτό το ηλεκτρονικό μήνυμα σας αποστέλλεται απευθείας από 
        την ιστοσελίδα panelinios.gr.<br/><br/>
        &copy; panelinios.gr 2017. Όλα τα δικαιώματα διατηρούνται.</span>".
        "<br/><br/>CUSTOMER:".$onlineId." / USER:".$_SESSION['user_fullname'];        
        $extramsg = "<br/><br/>CID:".$onlineId;
        $mail->body($_POST['t_message'].$imgpath.$extramsg);
        $ret = $mail->Send();
        $msg = $ret;


        if ($msg=="OK") {
            $sentEmail = new EMAILS($db1, 0);
                
            $sentEmail->set_email_id(0);
            $sentEmail->set_email_account(3);
            $sentEmail->set_from_address('logistirio@panelinios.gr');
            $sentEmail->set_to_address($_POST['t_email']);
            $sentEmail->set_cc_address('');
            $sentEmail->set_bcc_address('');
            $sentEmail->set_replyto_address('logistirio@panelinios.gr');
            $sentEmail->set_subject($_POST['t_subject']);
            $sentEmail->set_email_date(date('YmdHis'));
            $sentEmail->set_body($_POST['t_message'].$imgpath.$extramsg);
            $attach_hrefs = '';
            $sentEmail->set_attachments($attach_hrefs);
            $sentEmail->set_company($companyid);
            $sentEmail->set_email_type(2);
            $sentEmail->set_mark(0);
            $sentEmail->set_spam(0);
            $sentEmail->set_trash(0);
            $sentEmail->set_isread(0);
            
            $sentEmail->Savedata();
        }
        
    }
    
    
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>PANELINIOS - CRM</title>
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />
        
        <!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>-->
        <!-- <script src="https://cdn.tiny.cloud/1/fiq8lm63smdu8mc2fhs375nntdcf27e6r8gdeb3c5zqzllnl/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
        <script src="js/tinymce/tinymce.min.js"></script>
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
        
        
        <form action="sendInvoice2.php?send=1&id=<?php echo $id; ?>" method="POST">
            
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
            
            $subject = "Τιμολόγιο #$invoicenr, υπόψην $customer ";
            if (isset($_GET['send']) && $_GET['send']==1) {
                $subject = $_POST['t_subject'];            
            }
            $t_subject = new textbox("t_subject", "ΘΕΜΑ", $subject);
            $t_subject->get_Textbox();
            
            $invoicelink = "https://crm.panelinios.gr/tcpdf/examples/invoicepdf2.php?id=".
                    $invoice->get_id()."&accesstoken=".$invoice->get_accesstoken();
            
            
                    $offerText = "Αγαπητέ συνεργάτη 
                    
                    <br/><br/>
                    
                    Παρακαλούμε όπως επιλέξετε το παρακάτω κουμπί με σκοπό να κατεβάσετε το τιμολόγιο παροχής υπηρεσιών για την καταχώρηση σας στον Πανελλήνιο επαγγελματικό οδηγό panelinios.gr. 
                    
                    <br/><br/><br/><br/>

                    <a href=\"$invoicelink\" style=\"background-color:#000; color:#fff; padding:10px; font-size:20px\">ΤΙΜΟΛΟΓΙΟ No $invoicenr</a>
                    <br/><br/><br/>
                    
                    Είμαστε στην διάθεση σας για οποιαδήποτε διευκρίνιση
                    <br/><br/><br/>
                    
                    Με εκτίμηση,
                    
                    <br/><br/><br/>
                    Λογιστήριο panelinios.gr <br/><br/>

                    Τηλ. 211 100 10 60
                    <br/><br/>

                    <br/><br/><br/><br/>";
            
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
        
        <div style="padding-left:15px">
            <!--<a class="button" href="editcompany.php?id=<?php echo $companyid; ?>">Επιστροφή στην καρτέλα εταιρείας</a>-->
        </div>
        
        
    </body>
</html>