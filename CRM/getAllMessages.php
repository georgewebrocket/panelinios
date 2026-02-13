<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

//session_start(); 
$userid = $_SESSION['user_id'];
$personid = isset($_REQUEST['personid'])? $_REQUEST['personid']: 0;
$unreadonly = isset($_REQUEST['unreadonly'])? $_REQUEST['unreadonly']: 0;
$messageid = isset($_REQUEST['messageid'])? $_REQUEST['messageid']: 0;
$fromdate = isset($_REQUEST['fromdate'])? $_REQUEST['fromdate']: 0;
$todate = isset($_REQUEST['todate'])? $_REQUEST['todate']: 0;
$allconversations = isset($_REQUEST['allconversations'])? $_REQUEST['allconversations']: 0;
if ($allconversations==1) {
    $userid = $personid;
}

if ($messageid>0) {
    //$sql = "SELECT * FROM MESSAGES WHERE id= ?";
    //$rsMessages = $db1->getRS($sql, array($messageid));
}
else {
    if ($personid==0) {
        $sql = "SELECT * FROM CONVERSATIONS WHERE (sender=? OR receiver=?) ";
    }
    else {
        $sql = "SELECT * FROM CONVERSATIONS WHERE (sender=? OR receiver=?) AND  (sender=? OR receiver=?) ";
    }

    if ($unreadonly==1) {
        $sql .= " AND (`isread`=0) AND (`receiver`<>43) ";
    }
    
    if ($fromdate>0) {
        $sql .= " AND (lastdatetime>='$fromdate')";
    }
    if ($todate>0) {
        $sql .= " AND (lastdatetime<='$todate')";
    }
    
    $sql .= " ORDER BY lastdatetime DESC ";
    //echo "<!--$sql-->";
    if ($personid==0) {
        $rsConv = $db1->getRS($sql, array($userid,$userid));
    }
    else {
        $rsConv = $db1->getRS($sql, array($userid,$userid,$personid,$personid));
    }
}

$sql = "SELECT * FROM USERS ORDER BY fullname";
$rsUsers = $db1->getRS($sql);

$sql = "SELECT * FROM MESSAGES WHERE sender=? OR receiver=?";
$rsAllMessages = $db1->getRS($sql, array($userid, $userid));

if ($rsConv) {
    for ($i = 0; $i < count($rsConv); $i++) {
        $conversation = $rsConv[$i]['id']; 
        $conv_sender = $rsConv[$i]['sender'];
        $conv_receiver = $rsConv[$i]['receiver'];
        
        $messageto = $conv_sender == $userid? $conv_receiver: $conv_sender;
        
        
        //$sql = "SELECT * FROM MESSAGES WHERE conversation=?";
        //$rsMessages = $db1->getRS($sql, array($rsConv[$i]['id']));
        $rsMessages = arrayfunctions::filter_by_value($rsAllMessages, "conversation", $conversation);
        
        if ($rsMessages) {
            
            $convUserFullname = func::vlookupRS("fullname", $rsUsers, $messageto);
            echo "<div class=\"allmessages-conversation-title\">$convUserFullname</div>";
            
            echo "<div class=\"allmessages-conversation\">";  
        
        
            for ($k = 0; $k < count($rsMessages); $k++) {
                $companyid = $rsMessages[$k]['companyid'];
                $message = $rsMessages[$k]['message'];
                $mdatetime = $rsMessages[$k]['mdatetime'];
                $mYear = substr($mdatetime, 0, 4);
                $mMonth = substr($mdatetime, 5, 2);
                $mDay = substr($mdatetime, 8, 2);
                $mHour = substr($mdatetime, 11, 2);
                $mMin = substr($mdatetime, 14, 2);
                $mdatetime = $mDay."-".$mMonth."-".$mYear." ".$mHour.":".$mMin; 

                $senderid = $rsMessages[$k]['sender'];
                $sender = func::vlookupRS("fullname", $rsUsers, $senderid);
                $receiverid = $rsMessages[$k]['receiver'];
                $receiver = func::vlookupRS("fullname", $rsUsers, $receiverid);
                $mymessageid = $rsMessages[$k]['id'];
                
                $userPhoto = func::vlookupRS("photo", $rsUsers, $senderid);
                
                $msgstatusclass = $rsMessages[$k]['read']==0? "newmessage": "readmessage";
                
                echo "<div class=\"$msgstatusclass\" style=\"border-bottom: 1px dashed rgb(200,200,200);\"><div class=\"col-2 \" style=\"padding:10px\"><div class=\"message-user-photo\" style=\"background-image:url($userPhoto)\"></div></div>";
                
                echo "<div class=\"col-10 allmessages-message \">";
                //echo "<div class=\"allmessages-message-users\">$sender -> $receiver </div>";
                
                echo "<div class=\"allmessages-message-message\">$message";
                if ($companyid>0) {
                    echo " - <a target=\"_blank\" href=\"editcompany.php?id=$companyid\" class=\"color-red\">$companyid</a>";
                }
                echo "</div>";
                
                echo "<div class=\"allmessages-message-datetime\">($mdatetime)</div>";
                //echo "<div class=\"allmessages-message-controls\">";
                if ($rsMessages[$k]['sender'] == $userid) {
                    if ($rsMessages[$k]['read']==0) {
                        echo "<div class=\"allmessages-message-controls\">";
                        //echo "<span data-id=\"$mymessageid\" class=\"allmessages-message-delete button-little\">Delete</span>";
                        echo "</div>";
                    }
                }
                else {
                    //echo "&nbsp;<span class=\"allmessages-message-ok button-little\">OK</span>";            
                }

                //echo "</div>";            
                echo "</div><div class=\"clear\"></div></div>";
                
            }
            echo "<div class=\"clear\"></div>";

            echo "<div class=\"allmessages-conversation-controls align-right\" style=\"padding:10px\"><span class=\"allmessages-message-ok button-little\" data-conversation=\"$conversation\">OK</span>&nbsp;<span class=\"allmessages-message-reply button-little\">Reply</span></div>";
            echo "<div class=\"allmessages-sendmessage invisible\">";
            echo "<textarea data-receiver=\"$messageto\" data-companyid=\"$companyid\" data-conversation=\"$conversation\" class=\"allmessages-sendmessage-textarea\"></textarea>";
            echo "<div class=\"align-right\">";
            echo "<span class=\"allmessages-sendmessage-ok button-little\">OK</span>&nbsp;";
            echo "<span class=\"allmessages-sendmessage-cancel button-little\">Cancel</span>";
            echo "</div>";
            echo "</div>";
            
            echo "</div>";
        }
        
    }
    
    
}

?>