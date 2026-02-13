<?php

//panelinios

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
require_once('php/mailgun.php');

$send_red = $_POST['sendred']=='1' ? true : false;
$send_acs = $_POST['sendacs']=='1' ? true : false;
$courier_email_text = trim($_POST['sendtext']);
$companyid = $_POST['companyid'];
$voucherid = $_POST['voucher'];
$userid = $_POST['user'];

$imgsrc = "https://www.panelinios.gr/assets/images/logo.png";
$img = "<img src=\"{$imgsrc}\" width=\"273\"  />";

$to = [];
if ($send_red) {
    $to[] = "info@redcourier.gr";
    // $to[] = "george@webrocket.gr";
}
if ($send_acs) {
    $to[] = "bbk@acscourier.gr";
    // $to[] = "george.apollo@gmail.com";
}

if (count($to)>0) {
    $mail = new mailgun();
    $email_from = "info@panelinios.gr";
    $sendTo = $to;
    $mail->from($email_from)->to($sendTo);
    $subject = "Οδηγίες Παράδοσης Voucher - Panelinios.gr #{$companyid}";
    $mail->subject($subject);

    $msgText = str_replace("\n", "<br/>", $courier_email_text) ;
    $msgText .= "<br/><br/>{$img}<br/><strong>PANELINIOS.GR</strong><br/>";
    $mail->body($msgText);
    $ret = $mail->Send();

    // var_dump($ret);
    // echo "...";

    if ($ret == 'OK') {
        $sentEmail = new EMAILS($db1, 0);
            
        $sentEmail->set_email_id(0);
        $sentEmail->set_email_account(1); //info
        $sentEmail->set_from_address($email_from);
        $sendTo = implode(", ", $sendTo);
        $sentEmail->set_to_address($sendTo);
        $sentEmail->set_cc_address('');
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

        $action = new ACTIONS($db1,0);
        $action->set_company($companyid); //email sent
        $action->set_user($userid);
        $action->set_status1(0);
        $action->set_status2(14);
        $action->set_voucherid($voucherid);
        $action->set_comment("Αποστολή email στον courier: " . ($send_red ? "RED " : "") . ($send_acs ? "ACS" : ""));
        
        $action->Savedata();

        echo "OK";
    }
    else {
        echo "Error sending email: ".$ret;
    }
}