<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

ini_set("max_execution_time",360);

require_once('../php/config.php');
require_once('../php/db2.php');
//require_once('../php/controls.php');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

include "imap/webmail.php";


$sql = "SELECT * FROM EMAIL_ACCOUNTS WHERE active=1 ORDER BY id";
$rsAccounts = $db1->getRS($sql);

if ($rsAccounts) {
  for ($i=0;$i<count($rsAccounts);$i++) {
    $host = $rsAccounts[$i]['mailhost'];
    $imap_port = $rsAccounts[$i]['imap_port'];
    $incoming_secure_protocol = $rsAccounts[$i]['incoming_secure'];
    $hostname = "{" . "$host:$imap_port/imap/$incoming_secure_protocol" . "}INBOX";
    //$hostname = "{" . "$host:$imap_port/imap" . "}INBOX";
    
    
    $username = $rsAccounts[$i]['email'];
    echo "<h2>Account $username</h2>";
    echo "<h3>{$hostname}</h3>";
    $password = $rsAccounts[$i]['password'];
    $accountId = $rsAccounts[$i]['id'];
    
    $ar = explode("@", $username);
    $accountName = $ar[0];

     
   
    $imapResource = imap_open($hostname, $username, $password);
    $inbox = $imapResource;
    $search = 'SINCE "' . date("j F Y", strtotime("-1 days")) . '"';
    $emails = imap_sort($imapResource, SORTDATE, 1, SE_UID, $search);
    
    echo count($emails) . " messages<br/>";

    
    
    /*
    for ($k=0;$k<count($emails);$k++) {
      $uid = $emails[$k];
      $sql = "SELECT * FROM EMAILS WHERE email_account=? AND email_id=?";
      $rsEmail = $db1->getRS($sql, array($accountId, $uid));
      
      if (!$rsEmail) {
        $overview = imap_fetch_overview($inbox, $uid, FT_UID); //FT_UID - 0
        $overview = $overview[0];
        $hText = imap_fetchbody($inbox, $uid, '0', FT_UID);
        $header = imap_rfc822_parse_headers($hText);
        
        
        
        $fromAddress = $header->from[0]->mailbox . "@" . $header->from[0]->host;
        $toAddress = isset($header->to[0])? $header->to[0]->mailbox . "@" . $header->to[0]->host: "";
        $ccAddress = isset($header->cc[0])? $header->cc[0]->mailbox . "@" . $header->cc[0]->host: "";
        $bccAddress = isset($header->bcc[0])? $header->bcc[0]->mailbox . "@" . $header->bcc[0]->host: "";
        $replyAddress = isset($header->reply_to[0])? $header->reply_to[0]->mailbox . "@" . $header->reply_to[0]->host: "";
        
        $subject = mb_decode_mimeheader($header->subject);
        $msgDate = $header->date;
        
        $myDate = strtotime($msgDate);
        $msgDate = date("YmdHis", $myDate);
        
        $webmail = new Webmail();
        $message = $webmail->getEmailBody($uid, $inbox);
        
        $email_message = new Email_message(array("connection" => $inbox, "message_no" => $uid));
        $mailbody = $email_message->get_mail_body($accountName);
        
        $body = $mailbody!=""? $mailbody: $message;
        
        
        $attachments = $webmail->extract_attachments($inbox, $uid, FT_UID);
        $attach_hrefs = '';
        if(count($attachments)!=0){
          $target_dir = "emails/$accountName/".$uid;
          if (!is_dir($target_dir)) {
            mkdir($target_dir);
          }
          
          $index = 0;
          $fileindex = 1;
          foreach($attachments as $at) {
      
              if($at['is_attachment']==1){
                  $filename = $at['name']!=""? $at['name']: $at['filename'];
                  $filename = mb_decode_mimeheader($filename);
                  $filename = str_replace(" ", "_", $filename);
                  file_put_contents($target_dir.'/'. $filename, $at['attachment']);
                  $attach_hrefs .= 'https://crm.panelinios.gr/mailclient/'.$target_dir.'/'. $filename;
              }
              elseif (isset($at['attachment'])) {
                $ext = "";
                
                $extPDF = strtolower(substr($at['attachment'],1,3));
                $ext = $extPDF== "pdf"? "pdf": $ext;
                
                if ($ext=="") {
                    $extRTF = strtolower(substr($at['attachment'],2,3));
                    $ext = $extRTF== "rtf"? "rtf": $ext;
                }
                
                if ($ext=="") {
                    $docx = strpos($at['attachment'], "word/document.xml");
                    $ext = $docx? "docx": $ext;
                }
                
                if ($ext=="") {
                    $doc = strpos($at['attachment'], "Word.Document");
                    $ext = $doc? "doc": $ext;
                }
                
                if ($ext=="") {
                    $xls = strpos($at['attachment'], "Office 2007 XLSX Document");
                    $ext = $xls? "xls": $ext;
                }
                
                if ($ext!="") {
                    $filename = "file-$fileindex" . ".$ext";
                    //$filename = str_replace(" ", "_", $filename);
                    file_put_contents($target_dir.'/'. mb_decode_mimeheader($filename), $at['attachment']);
                    $attach_hrefs .= 'https://crm.panelinios.gr/mailclient/'.$target_dir.'/'. $filename;
                    $fileindex++;
                }
              } 
              
              if ($index < count($at)-1) {
                $attach_hrefs .= "|";
              }
              
              $index++;
              
          }
        
        }
        
        
        $email_type = 1; // incoming
        $sql = "SELECT id FROM COMPANIES WHERE active=1 AND email LIKE '%$fromAddress%'";
        if ($fromAddress == $ccAddress && $fromAddress == $username) {
          $email_type = 2; // outgoing
          $sql = "SELECT id FROM COMPANIES WHERE active=1 AND email LIKE '%$toAddress%'";
        }
        $rsCompany = $db1->getRS($sql);
        $companyId = $rsCompany? $rsCompany[0]['id']: 0;
        
        
        $email = new EMAILS($db1, 0);
        
        $email->set_email_id($uid);
        $email->set_email_account($accountId);
        $email->set_from_address($fromAddress);
        $email->set_to_address($toAddress);
        $email->set_cc_address($ccAddress);
        $email->set_bcc_address($bccAddress);
        $email->set_replyto_address($replyAddress);
        $email->set_subject($subject);
        $email->set_email_date($msgDate);
        $email->set_body($body);
        //$email->set_body("");
        $email->set_attachments($attach_hrefs);
        $email->set_company($companyId);
        $email->set_email_type($email_type);
        $email->set_mark(0);
        $email->set_spam(0);
        $email->set_trash(0);
        $email->set_isread(0);
        
        $email->Savedata();
        
        if ($email->get_id()>0) {
          echo $email->get_id() . " $fromAddress<br/>";
        }
        else {
          echo "Error $uid - $fromAddress<br/>";
        }
        
        
      }
      
    
    }
      */
      
  }
  
}