<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("MESSAGES",$lang,$db1);

$id = $_GET['id'];
$message = new MESSAGES($db1,$id);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="form-container">
                
        <h1><?php echo $l->l("form_message"); ?></h1> 
            
        <form >
            <?php 
            //title
            $txtTitle = new textbox("txtTitle", $l->l('form_title'),$message->get_title());
            $txtTitle->get_Textbox();
            //message
            $txtMessage = new textbox("txtMessage", $l->l('form_message'),$message->get_message());
            $txtMessage->set_multiline();
            $txtMessage->get_Textbox();
            //sender
            $txtSender = new textbox("txtSender", $l->l('form_sender'), func::vlookup("fullname", "USERS", "id=".$message->get_sender(), $db1)); 
            $txtSender->get_Textbox();
            //receiver
            $txtReceiver = new textbox("txtReceiver", $l->l('form_receiver'),func::vlookup("fullname", "USERS", "id=".$message->get_receiver(), $db1));
            $txtReceiver->get_Textbox();
            //Mdatetime
            $txtMdatetime = new textbox("txtMdatetime", $l->l('form_mdatetime'),$message->get_mdatetime());
            $txtMdatetime->set_format("DATE");
            $txtMdatetime->set_locale($locale);
            $txtMdatetime->get_Textbox();
            //read
            $chkRead = new checkbox("chkRead", $l->l('form_read'), $message->get_read());
            $chkRead->get_Checkbox();
            
            ?> 
            <div style="clear: both;"></div>
        </form>
    </div>    
</body>
</html>