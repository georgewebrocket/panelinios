<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


$agent = $_SESSION['user_fullname'];

$companyid = $_GET['id'];
$company = new COMPANIES($db1,$companyid);
//$onlineId = func::getCompanyPId($company);
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
$discount = func::vlookup("discount", "DISCOUNTS", "id=".$company->get_discount(), $db1);;
$discount = $discount==""? 0: $discount;
$finalprice = $price * (1-$discount/100); 
$finalprice = round($finalprice, 2);



$sql = "SELECT * FROM EMAIL_TEMPLATES ORDER BY id";
$rsTemplates = $db1->getRS($sql);

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
        $mail->setFrom('info@epagelmatias.gr', 'Epagelmatias.gr');
        $mail->addAddress($_POST['t_email'], $customer);
        $mail->AddCC('info@epagelmatias.gr', 'admin');
        $mail->Subject = $_POST['t_subject'];
        $imgsrc = "http://www.epagelmatias.gr/img/logo.png";
        $imgpath = "<img src=\" $imgsrc \" width=\"273\" height=\"46\"' />";
        $extramsg = "<br/><br/>CID:".$onlineId;
        $mail->msgHTML($_POST['t_message'].$imgpath.$extramsg);
        $mail->AltBody = $_POST['t_message'];
        if (!$mail->send()) {
                $err = "Mailer Error: " . $mail->ErrorInfo;
        } else {
                $msg = "Your message was sent successfully!";
        }
    }
    
}


$myStyle = <<<EOT
<style>
        
    #t_message {
        height: 400px;
    }
    
    body {
        background-color: #ddd !important;
    }
        
</style>
        
EOT;


include "_thePopupHeader.php";

?>

<div style="background-color: #ddd; margin-top: 30px;">
    <form action="sendEmail.php?send=1&id=<?php echo $companyid; ?>" method="POST" style="max-width: 1000px; margin: auto;">
        <h1 style="padding-left: 0px;">Αποστολή email</h1>
                
        <?php if ($err!="") { echo "<h2> $err </h2>";} ?>
        <?php if ($msg!="") { echo "<h2> $msg </h2>";} ?>
    
        <?php
                
        if (isset($_GET['send']) && $_GET['send']==1) {
            $sendTo = $_POST['t_email'];            
        }
        $t_email = new textbox("t_email", "ΠΡΟΣ:", $sendTo);
        $t_email->get_Textbox();
    
        $subject = "Υπόψη " .$customer;
        if (isset($_GET['send']) && $_GET['send']==1) {
            $subject = $_POST['t_subject'];            
        }
        $t_subject = new textbox("t_subject", "ΘΕΜΑ", $subject);
        $t_subject->get_Textbox();
        
        
        $t_emailtemplate = new comboBox("t_emailtemplate", $db1, 
                "SELECT * FROM EMAIL_TEMPLATES", "id", "description", 
                0, "Επιλογή template");
        $t_emailtemplate->get_comboBox();
        
        
        $msgText = "";
        
        if (isset($_GET['send']) && $_GET['send']==1) {
            $msgText = $_POST['t_message'];            
        }
    
        $t_message = new textbox("t_message", "ΚΕΙΜΕΝΟ ΠΡΟΣΦΟΡΑΣ", $msgText);
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
    
    <div style="max-width: 1000px; margin: auto; padding-top:40px">
        <a class="button" href="editcompany.php?id=<?php echo $companyid; ?>">Επιστροφή στην καρτέλα εταιρείας</a>
    </div>

</div>


<div style="position: fixed; top:20px; right:20px;font-size: 30px;">
    <a href="editcompany.php?id=<?php echo $companyid; ?>">
        <span class="fa fa-close"></span>
    </a>
</div>



<?php

/*$jsTemplates = "";
for ($i = 0; $i < count($rsTemplates); $i++) {
    $myTemplate = $rsTemplates[$i]['bodytext'];
    $myTemplate = preg_replace( "/\r|\n/", "", $myTemplate );
    $myTemplate = addslashes($myTemplate);
    $jsTemplates .= "emailTemplate[$i] = \" $myTemplate \"; \r\n \r\n";
    
}*/


$myScript = <<<EOT

<!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>-->
<script src="js/tinymce/tinymce.min.js"></script>
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
        $.post("_getEmailTemplate.php", 
            { 
                templateid: templateid,
                customerid: customerid
            }, 
            function(data) {
                console.log(data);
                $("#t_message_ifr").contents().find("#tinymce").html(data);
            }
        );
        
   
    });
        
</script>
   
        
EOT;

include "_thePopupFooter.php";