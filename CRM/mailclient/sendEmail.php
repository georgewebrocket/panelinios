<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');
require_once('../php/mailgun.php');


$agent = $_SESSION['user_fullname'];

$accountId = $_GET['account'];
if ($accountId>0) {
    $account = new EMAIL_ACCOUNTS($db1, $accountId);
    $accountEmail = $account->get_email();
}
else {
   $accountId = 1;
   $accountEmail = "info@panelinios.gr"; 
}

$onlineId = 0;
$keywords = "";
$sendTo = "";
$cc = "";
$customer = "";
$price = 0;
$discount = 0;
$finalprice = 0;

$subject = "";
$quote = "";
$msgText = "";
$cc = "";
               
    
$companyid = isset($_GET['customer'])? $_GET['customer']: 0;
if ($companyid>0) {
    $company = new COMPANIES($db1,$companyid);
    $onlineId = $company->get_catalogueid();
    $keywords = $company->get_vn_keywords();
    $keywords = "<ul><li>" . str_replace(",", "</li><li>", $keywords) . "</li></ul>";
    $sendTo = $company->get_email();
    if ($company->get_contactperson()!="") {
        $customer = $company->get_contactperson();
    }
    else {
        $customer = $company->get_companyname();
    }
    $price = func::vlookup("price", "PACKAGES", "id=".$company->get_package(), $db1);
    $price = $price!=""? $price: 0;
    $discount = func::vlookup("discount", "DISCOUNTS", "id=".$company->get_discount(), $db1);
    $discount = $discount!=""? $discount: 0;
    $finalprice = $price * (1-$discount/100); 
    $finalprice = round($finalprice, 2);
    
    $subject = "Υπόψη " .$customer;
    //$msgText .= "<br/><br/>CID:".$onlineId;
    
}

$msgText0 = $msgText;


$email = isset($_GET['email'])? $_GET['email']: 0;
if ($email>0) {
    $email = new EMAILS($db1, $email);
    
    $quote =  "<blockquote style=\"margin: 0px 0px 0px 0.8ex; border-left: 1px solid #cccccc; padding-left: 1ex;\">" .
        "SUBJECT " . $email->get_subject() . "<br/>" .
        "FROM " . $email->get_from_address() . "<br/>" .
        "TO " . $email->get_from_address() . "<br/>" .
        "DATE: " . func::str14toDateTime($email->get_email_date()) . "<br/>" .
        "<br/>" .
        $email->get_body() . "</blockquote>";
}


$action = isset($_GET['action'])? $_GET['action']: "" ;

if ($action=="reply") {
    $sendTo = $email->get_from_address();
    $subject = "RE: " . $email->get_subject();
    $msgText .= $quote;
    
}

if ($action=="replyall") {
    $sendTo = $email->get_from_address();
    $cc = $email->get_cc_address();
    $subject = "RE: " . $email->get_subject();
    $msgText .= $quote;
}

if ($action=="forward") {
    $sendTo = "";
    $subject = "FW: " . $email->get_subject();
    $msgText .= $quote;
    if ($email->get_attachments()!="") {
        $msgText .= "<hr></hr>";
        $att = explode('|', $email->get_attachments());
        for ($i=0;$i<count($att);$i++) {
            if ($att[$i]!="") {
                $att[$i] = "<a target=\"_blank\" href=\"$att[$i]\">$att[$i]</a>";
            }
        }
        $attachments = implode("<br/>", $att);
        $msgText .= $attachments;
    }
    
    
}



$sql = "SELECT * FROM EMAIL_TEMPLATES ORDER BY id";
$rsTemplates = $db1->getRS($sql);

$err = ""; $msg="";
if (isset($_GET['send']) && $_GET['send']==1) {
    
    $subject = $_POST['t_subject']; 
    $sendTo = $_POST['t_email'];
    $cc = $_POST['t_cc'];
    $body = $_POST['t_message'];
    $msgText = $_POST['t_message'];            
    
    
    
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
    
    $cc = $_POST['t_cc']; //xxx
    
    if ($err == '') {
	        
        
        $mail = new mailgun();
        $email_from = $accountEmail;
        // $mail->from($email_from)->to($sendTo);
        $mail->from('sales@panelinios.gr')->to($sendTo)->cc('sales@panelinios.gr');
        $cc = trim($cc);
        //echo "CC=$cc.";
        if ($cc!="") {
            // $mail->cc($cc); /////////////
        }
        $mail->subject($subject);
        $mail->body($msgText);
        $ret = $mail->Send();
        
        if ($ret=="OK") {
            $msg = "<span style=\"color:#0a0\">" . "Η αποστολή του email έγινε κανονικά" . "</span>";
            
            $sentEmail = new EMAILS($db1, 0);
            
            $sentEmail->set_email_id(0);
            $sentEmail->set_email_account($accountId);
            $sentEmail->set_from_address($email_from);
            $sentEmail->set_to_address($sendTo);
            $sentEmail->set_cc_address($cc);
            $sentEmail->set_bcc_address('');
            $sentEmail->set_replyto_address($email_from);
            $sentEmail->set_subject($subject);
            $sentEmail->set_email_date(date('YmdHis'));
            $sentEmail->set_body($msgText);
            $attach_hrefs = '';
            $sentEmail->set_attachments($attach_hrefs);
            $sentEmail->set_company($companyid);
            $sentEmail->set_email_type(2);
            $sentEmail->set_mark(0);
            $sentEmail->set_spam(0);
            $sentEmail->set_trash(0);
            $sentEmail->set_isread(0);
            
            $sentEmail->Savedata();
            
            if ($sentEmail->get_id()>0) {
              //echo $email->get_id() . " $fromAddress<br/>";
            }
            else {
              //echo "Error $uid - $fromAddress<br/>";
              echo "<!--ERROR EMAILS TABLE SENT-->";
            }
            
        }
        else {
            $msg = $ret;
        }
        
    }
    
}


$myStyle = <<<EOT
<style>
        
    #t_message {
        height: 400px;
    }
    
    body {
        background-color: #fff !important;
    }
        
</style>
        
EOT;


include "popupheader.php";

?>

<div style="background-color: #fff; padding-top: 10px;">
    <form action="sendEmail.php?send=1&customer=<?php echo $companyid; ?>&account=<?php echo $accountId ?>" method="POST" style="max-width: 1000px; margin: auto;">
        <h1 style="padding-left: 0px; margin-bottom: 20px;">Αποστολή email</h1>
                
        <?php if ($err!="") { echo "<h2> $err </h2>";} ?>
        <?php if ($msg!="") { echo "<h2> $msg </h2>";} ?>
    
        <?php
                
        if (isset($_GET['send']) && $_GET['send']==1) {
            $sendTo = $_POST['t_email'];            
        }
        $t_email = new textbox("t_email", "ΠΡΟΣ:", $sendTo);
        $t_email->get_Textbox();
        
        $t_cc = new textbox("t_cc", "CC:", $cc);
        $t_cc->get_Textbox();
    
        
        $t_subject = new textbox("t_subject", "ΘΕΜΑ", $subject);
        $t_subject->get_Textbox();
        
        
        $t_emailtemplate = new comboBox("t_emailtemplate", $db1, 
                "SELECT * FROM EMAIL_TEMPLATES", "id", "description", 
                0, "Επιλογή template");
        $t_emailtemplate->get_comboBox();
        
        
        
    
        $t_message = new textbox("t_message", "ΚΕΙΜΕΝΟ", $msgText);
        $t_message->set_multiline();
        echo "<br/>ΚΕΙΜΕΝΟ<br/><br/>";
        echo $t_message->textboxSimple();
    
        ?>
    
        <div style="clear: both; height: 30px"></div>
    
        <?php
    
        $btnOK = new button("btnOK", "ΑΠΟΣΤΟΛΗ");
        echo $btnOK->get_button_simple();
    
    
    
        ?>
    
    
        <div style="clear: both; height: 50px"></div>
    
    </form>
    
    

</div>


<!--<div style="position: fixed; top:20px; right:20px;font-size: 30px;">
    <a href="editcompany.php?id=<?php echo $companyid; ?>">
        <span class="fa fa-close"></span>
    </a>
</div>-->



<?php

/*$jsTemplates = "";
for ($i = 0; $i < count($rsTemplates); $i++) {
    $myTemplate = $rsTemplates[$i]['bodytext'];
    $myTemplate = preg_replace( "/\r|\n/", "", $myTemplate );
    $myTemplate = addslashes($myTemplate);
    $jsTemplates .= "emailTemplate[$i] = \" $myTemplate \"; \r\n \r\n";
    
}*/


$myScript = <<<EOT


<script src="../js/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector:'#t_message',
        plugins: "link",
        entity_encoding : "raw",
        relative_urls: false,
        convert_urls: false
    }); 

</script>
    
        
<script>
        
    $("#t_emailtemplate").change(function() {
        var templateid = $(this).val();
        var customerid = $companyid;
        console.log(templateid);
        $.post("https://crm.panelinios.gr/_getEmailTemplate.php", 
            { 
                templateid: templateid,
                customerid: customerid
            }, 
            function(data) {
                console.log(data);
                $("#t_message_ifr").contents().find("#tinymce").html(data + '$msgText0');
            }
        );
        
   
    });
        
</script>
   
        
EOT;

include "popupfooter.php";