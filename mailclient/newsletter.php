<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');
require_once('../php/mailgun.php');

$offset = $_REQUEST['offset'];


$sql = "SELECT DISTINCT COMPANIES.id, COMPANIES.email, COMPANIES.companyname 
FROM `COMPANIES_STATUS` 
INNER JOIN COMPANIES ON COMPANIES_STATUS.companyid = COMPANIES.id  
WHERE COMPANIES_STATUS.`status`IN (8,9) 
AND COMPANIES.email <> ''
AND `csdatetime`>'20210101000000' ORDER BY `companyid` LIMIT 100 OFFSET $offset";

$rs = $db1->getRS($sql);

/**/
/*echo "<table>";
for ($i=0; $i<count($rs); $i++) {
    echo "<tr><td>" . $rs[$i]['id'] ."</td><td>". $rs[$i]['companyname'] ."</td><td>".  $rs[$i]['email'] . "</td></tr>";
}
echo "</table>";
exit();*/
/** */

/*$rs = [
    [
        'id'=>6400,
        'email'=>'george.apollo@gmail.com'
    ],
    [
        'id'=>735128,
        'email'=>'sarris_n@pc4u.gr'
    ]
    ];*/

$templateid = 25;
$emailTemplate = new EMAIL_TEMPLATES($db1, $templateid);
$emailBody = $emailTemplate->get_bodytext();


if ($rs) {
    
    for ($i=0; $i<count($rs); $i++) {
        $companyid = $rs[$i]['id'];
        $emailBody = str_replace("[EMAIL]", $rs[$i]['email'], $emailBody);
        
        $mail = new mailgun();
        $email_from = "info@epagelmatias.gr";
        $sendTo = $rs[$i]['email'];
        $mail->from($email_from)->to($sendTo);
        $cc = "";
        if ($cc!="") {
            $mail->cc($cc);
        }
        $subject = "Μοναδική παροχή για τους πελάτες του epagelmatias.gr";
        $mail->subject($subject);
        $mail->body($emailBody);
        $ret = $mail->Send();
        if ($ret=="OK") {
            echo $rs[$i]['id'] ." - ". $rs[$i]['email'] . " OK <br/>";
            
            $sentEmail = new EMAILS($db1, 0);
            
            $sentEmail->set_email_id(0);
            $sentEmail->set_email_account(1);
            $sentEmail->set_from_address($email_from);
            $sentEmail->set_to_address($sendTo);
            $sentEmail->set_cc_address($cc);
            $sentEmail->set_bcc_address('');
            $sentEmail->set_replyto_address($email_from);
            $sentEmail->set_subject($subject);
            $sentEmail->set_email_date(date('YmdHis'));
            $sentEmail->set_body($emailBody);
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
        else {
            echo $rs[$i]['email'] . " ERROR <br/>";
        }
        
    }

    $offset += count($rs);

    echo <<<EOT

    <script>
    setTimeout(function(){ 
        /*window.location.href = "https://www.epagelmatias.gr/crm4/mailclient/newsletter.php?offset=$offset";*/
        window.open("https://www.epagelmatias.gr/crm4/mailclient/newsletter.php?offset=$offset");
    }, 3000);
    
    </script>

EOT;
    
    
}