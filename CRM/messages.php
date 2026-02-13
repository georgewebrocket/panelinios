<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

function stripAngleTags($string) {
    // Replace <something> or <something with just something
    return preg_replace('/<\s*\/?\s*([^>]+?)\s*>?/', '$1', $string);
}

$receiver = 0;
if (isset($_GET['receiver'])) {
    $receiver = $_GET['receiver'];
}
if ($receiver=="") { $receiver=0;}

$showlist = true;
if (isset($_GET['showlist']) && $_GET['showlist']==0) {
	$showlist = false;
}
$companyid = "";
if (isset($_GET['companyid'])) {
	$companyid = $_GET['companyid'];
}

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("MESSAGES",$lang,$db1);

$err = 0;
$msg = "";

$userid = $_SESSION['user_id'];

if (isset($_GET['send']) && $_GET['send']==1) {
    if ($_POST['cReceiver']==0) {
        $err=1;
        $msg = $lg->l("error");
    }
    if (trim($_POST['txtMessage'])=="") {
        $err=1;
        $msg = $lg->l("error");
    }
    if ($err==0) {
        $message = new MESSAGES($db1,0);
        $message->set_sender($userid);
        $message->set_receiver($_POST['cReceiver']);
        $message->set_message(stripAngleTags($_POST['txtMessage']));
		$message->set_companyid($_POST['t_companyid']);
        $message->set_title("Message");
        $message->set_read(0);
        
        $message->set_senddatetime("..");
        $message->set_readdatetime("..");
        
        if ($message->Savedata()) {
            //$msg = $l->l("message-sent-ok");
        }
        else {
            //$msg = $lg->l("error");
        }
    }
    
}

$popupurl="";
if (isset($_GET['popupurl'])) {
    $popupurl = $_GET['popupurl'];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />    

<style>
    
    form.message {
        margin-left: 0px;
        
    }
    
    div.message {
        border-bottom: 1px dashed rgb(200,200,200);        
        padding: 10px 0px;
        font-size: 0.9em;
    }
    div.message h3 {
        display:inline; 
        margin-right: 20px;
        
    }
    a.message-button {
        padding: 3px 5px; float:right; margin-right: 1em;
    }
    
</style>

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        


</head>
    
    <body>
        
        
        
        <form class="message" action="messages.php?send=1&companyid=<?php echo $companyid; ?>&showlist=<?php echo $showlist; ?>&popupurl=<?php echo $popupurl; ?>" method="POST">
            <textarea name="txtMessage" rows="3" cols="20" placeholder="Send message"></textarea>
            
            <?php
            //oi aploi xristes epikoinwnoun mono me super-users kai admin
            if ($_SESSION['user_profile']>1) {
                $sql = "SELECT id, fullname FROM USERS WHERE active=1 AND id<>".$userid;
            } 
            else {
                $sql = "SELECT id, fullname FROM USERS WHERE /*userprofile>1*/ active=1 AND id<>".$userid;
            }
            $cReceiver = new comboBox("cReceiver", $db1, 
                    $sql,
                    "id","fullname",$receiver);
            echo $cReceiver->comboBox_simple();
			
			echo "<br/>";
			$t_companyid = new textbox("t_companyid","CompanyId",$companyid,"Company Id");
            echo $t_companyid->textboxSimple();
			echo "<br/>";
			
            ?>
            
            <input type="submit" value="<?php echo $l->l("ok"); ?>" />
            
            
        </form>
        
        <?php echo $msg; ?>
        
		<?php if ($showlist) { ?>
        <iframe src="messagelist.php?popupurl=<?php echo $popupurl; ?>" width="100%" height="250" frameborder="0"></iframe>
        <?php } ?>
        
        
        
        
    </body>
    
</html>

