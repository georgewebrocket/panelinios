<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sender = $_SESSION['user_id'];
$receiver = $_REQUEST['receiver'];
$messageText = $_REQUEST['messageText'];
$companyid = $_REQUEST['companyid'];
$conversation = $_REQUEST['conversation'];

if ($conversation>0) {
    $sql = "UPDATE MESSAGES SET `read` = 1 WHERE conversation=?";
    $res = $db1->execSQL($sql, array($conversation));
}

$message = new MESSAGES($db1, 0);
$message->set_sender($sender);
$message->set_receiver($receiver);
$message->set_message($messageText);
$message->set_companyid($companyid);
$message->set_conversation($conversation);
$message->set_title("Message");
$message->set_read(0);
$message->set_senddatetime("..");
$message->set_readdatetime("..");
$message->Savedata();
echo $message->get_id();        
/*if ($message->Savedata()) {
    echo $message->get_id();
}
else {
    echo "ERROR";
}*/