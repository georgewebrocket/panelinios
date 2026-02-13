<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/config.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');

$id = $_GET['email'];
$email = new EMAILS($db1, $id);

$account = $_GET['account'];

$company = $email->get_company();
$theCompany = new COMPANIES($db1, $company);
$onlineId = $theCompany->get_catalogueid();


echo "<div style=\"font-size:16px; font-weight:bold; line-height:20px; background-color:#ddd; padding:20px \">";

echo "SUBJECT: " . $email->get_subject() . "<br/>";
echo "DATE: " . func::str14toDateTime($email->get_email_date()) . "<br/>";
echo "<hr style=\"border:none; border-bottom:1px dotted #ccc\"></hr>";

echo "FROM: " . $email->get_from_address() . "<br/>";
echo "TO: " . $email->get_to_address() . "<br/>";
if ($email->get_cc_address()!="") {
    echo "CC: " . $email->get_cc_address() . "<br/>";
}
if ($email->get_from_address()!=$email->get_replyto_address()) {
    echo "REPLY: " . $email->get_replyto_address() . "<br/>";
}

echo "<hr style=\"border:none; border-bottom:1px dotted #ccc\"></hr>";

if ($company>0) {
    echo "CUSTOMER ID: <span id=\"customerlink\"><a target=\"_blank\" href=\"$appHost/editcompany.php?id=$company\">$onlineId (CRM-$company)</a></span> &nbsp;";
}

$ac_customer = new autocomplete("ac_customer2", "COMPANIES2", 0, $db1);
$ac_customer->getAutocompleteSimple();

echo "<input title=\"Enter new ID or ONLINE-ID\" type=\"button\" class=\"button\" id=\"change-customerid\" value=\"Change Customer ID\" /> <br/>";



echo "</div>";

//echo "<hr></hr>";

echo "<div style=\"min-height:200px; padding:20px 0px \">";

$iso88597 = strpos($email->get_body(), "charset=iso-8859-7");
//echo "iso88597=" . $iso88597;
if ($iso88597) {
    echo mb_convert_encoding($email->get_body(), "UTF-8", "iso-8859-7");
}
else {
    echo mb_convert_encoding($email->get_body(), "UTF-8");
}



echo "</div>";
echo "<hr></hr>";
echo "<div style=\"padding:20px \">";

if ($email->get_attachments()!="") {
    echo "<h2 style=\"font-size:20px\">ATTACHMENTS</h2>";
    
    $uid = $email->get_email_id();
    $attachments = explode("|", $email->get_attachments());
    $dir    = "emails/info/$uid/";
    echo "<ul>";
    for ($i=0;$i<count($attachments);$i++) {
        $link = trim($attachments[$i]);
        if ($link!="") {
          $myAr = explode("/", $link);
          $filename = $myAr[count($myAr) - 1];
          echo "<li class=\"attachment\"><a target=\"_blank\" href=\"$link\">" . $filename . "</a></li>";
        }
        
    }
    echo "</ul>";
}
else {
    echo "<p>No attachments</p>";
}

echo "</div>";

$trashClass= $email->get_trash()==0? "trash-email": "untrash-email";
$trashLabel = $email->get_trash()==0? "Delete": "Undelete";

$spamClass= $email->get_spam()==0? "spam-email": "unspam-email";
$spamLabel = $email->get_spam()==0? "Spam": "Not spam";

echo <<<EOT

<div style="position:fixed; bottom:20px; right:20px; text-align: right;">
  <a href="sendEmail.php?account=$account&customer=$company" class="button fancybox">New</a> &nbsp;    
  <a href="sendEmail.php?account=$account&customer=$company&email=$id&action=reply" class="button fancybox">Reply</a> &nbsp;
  <a href="sendEmail.php?account=$account&customer=$company&email=$id&action=replyall" class="button fancybox">Reply All</a> &nbsp;
  <a href="sendEmail.php?account=$account&customer=$company&email=$id&action=forward" class="button fancybox">Forward</a> &nbsp;
  <!--<a class="button">Archive</a> &nbsp;
  <a class="button mark-email" data-id="$id">Mark</a> &nbsp;-->
  <a class="button $trashClass" data-id="$id">$trashLabel</a> &nbsp;
  <a class="button spam-email" data-id="$id">Spam</a>
  
</div>


<script>

$(function() {
    
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 450 });	
    
    
    $('#change-customerid').click(function() {
        
        var email = $id;
        var customer = $('#h_ac_customer2').val();
        
        $.post("_changeEmailCustomer.php",  
            {
            email: email, 
            customer:customer
            },  
            function(data){
                if (data=='OK') {
                    var href = 'message.php?email=$id';
                    
                    $.post(href,  {},  function(data){ 
                      $("#mail-container").html(data);
                    });
                }
            });
        
    });
    
    $('.trash-email').click(function(e) {
        e.preventDefault();
        var email = $(this).data('id');
        $.post("updateEmail.php",  
            {email: email, trash:1},  
            function(data){ 
                console.log(data); 
                alert("Email was sent to trash"); 
            });
    });
    
    $('.untrash-email').click(function(e) {
        e.preventDefault();
        var email = $(this).data('id');
        $.post("updateEmail.php",  
            {email: email, trash:0},  
            function(data){ 
                console.log(data); 
                alert("Email was removed from trash"); 
            });
    });
    
    $('.spam-email').click(function(e) {
        e.preventDefault();
        var email = $(this).data('id');
        $.post("updateEmail.php",  
            {email: email, spam:1},  
            function(data){ 
                console.log(data); 
                alert("Email was sent to spam"); 
            });
    });
    
    $('.unspam-email').click(function(e) {
        e.preventDefault();
        var email = $(this).data('id');
        $.post("updateEmail.php",  
            {email: email, spam:0},  
            function(data){ 
                console.log(data); 
                alert("Email was removed from spam"); 
            });
    });
    
    
});

</script>


EOT;

