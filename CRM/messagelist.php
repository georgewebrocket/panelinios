<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("MESSAGES",$lang,$db1);

$err = 0;
$msg = "";

$userid = $_SESSION['user_id'];

$popupurl = $_GET['popupurl'];


if (isset($_GET['read']) && $_GET['read']==1) {
    $rmessage = new MESSAGES($db1, $_GET['msg']);
    $rmessage->set_read(1);
    if (!$rmessage->Savedata()) {
        $msg = $lg->l("error");
    }    
}

if (isset($_GET['del']) && $_GET['del']==1) {
    $dmessage = new MESSAGES($db1, $_GET['msg']);
    if (!$dmessage->Delete()) {
        $msg = $lg->l("error");
    }
}

//$sql = "SELECT * FROM MESSAGES WHERE read=0 AND (sender=? OR receiver=?)";
//$messages = $db1->getRS($sql, array($userid,$userid));

//$sql = "SELECT * FROM MESSAGES WHERE `receiver`<>43 AND ((`read`=0 AND `sender`=".$userid.") OR (`read`=0 AND `receiver`=".$userid.")) ORDER BY id DESC";

/*
if (isset($_GET['companyid'])) {
	$sql .= " AND companyid=".$_GET['companyid'];
}
*/

$sql = "SELECT * FROM MESSAGES WHERE `receiver`<>43 AND ((`read`=0 AND `sender`=?) OR (`read`=0 AND `receiver`=?)) ORDER BY id DESC";
$messages = $db1->getRS($sql, array($userid, $userid));


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<meta http-equiv="refresh" content="60" />
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
        
        
        
        <?php 
        
        echo $msg;
        
        for ($i=0;$i<count($messages);$i++) {
            $curmessage = new MESSAGES($db1,$messages[$i]['id'],$messages);
        ?>
        
        <div class="message">
            <div style="margin-bottom:5px;">
                <?php 
                if ($curmessage->get_receiver()==$userid) { 
                    $sendername = func::vlookup("fullname", "USERS", "id=".$curmessage->get_sender(), $db1)
                    ?>
                <strong><?php echo $sendername; ?> <br/>@ <?php echo $curmessage->get_mdatetime(); ?></strong><a class="button message-button" href="messagelist.php?read=1&msg=<?php echo $curmessage->get_id(); ?>&popupurl=<?php echo $popupurl; ?>">OK</a></div>
                <?php                 
                } 
                else { 
                    $receivername = func::vlookup("fullname", "USERS", "id=".$curmessage->get_receiver(), $db1)
                    ?>
                    <strong>TO: <?php echo $receivername; ?> <br/>@ <?php echo $curmessage->get_mdatetime();?></strong><a class="button message-button" href="messagelist.php?del=1&msg=<?php echo $curmessage->get_id(); ?>&popupurl=<?php echo $popupurl; ?>">DEL</a></div>
                <?php                
                } 
                ?>
            <?php echo $curmessage->get_message()." -- <a style=\"color:red\" onclick=\"parent.parent.openFancyFrame('".$popupurl.".php?id=".$curmessage->get_companyid()."',1000,800)\" href=\"#\">".$curmessage->get_companyid()."</a>"; ?>
        </div>
        
        <?php } ?>
        
        
        
        
        
    </body>
    
</html>


