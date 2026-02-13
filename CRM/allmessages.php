<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT * FROM USERS WHERE active=1 ORDER BY fullname";
$rsUsers = $db1->getRS($sql);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript" src="js/code.js"></script>

<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>

<script>

$(function() {
    
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 800 });
    
    $("#chk_allconversations").change(function() {
        if ($("#chk_allconversations").is(":checked")) {
            $("#all-users").hide();
        }
        else {
            $("#all-users").show();
        }
    });
        
    $('.allmessages-user').click(function() {
        
        $('.allmessages-user').each(function() {
            $(this).css('background-color', '#fff');
        });
        $(this).css('background-color', 'rgb(200,220,250)');
        
        $("#message-dock-wait").show();
        $('#allmessages').html("");
        
        var personid = $(this).data('id');
        var fromdate = $("#timeframe").val();
        var allconversations = 0;
        if ($("#chk_allconversations").length) {
            allconversations = $("#chk_allconversations").is(":checked")? 1: 0;
        }
        console.log(allconversations);
        $.post('getAllMessages.php', 
            {personid: personid, 
                unreadonly: 0, 
                fromdate: fromdate,
                allconversations: allconversations
            },
            function(data) {                
                $('#allmessages').html(data);
                bindMessageEvents();
                $("#message-dock-wait").hide();
            }
        );
    });
});


</script>

<style>
    
.allmessages-user {
    padding: 10px;
    border-bottom: 1px solid rgb(200,200,200);
    cursor: pointer;
}

.allmessages-users-container, #allmessages-container {
    height: 94vh;
    overflow: auto;
}
  
.allmessages-user:hover {
    background-color: rgb(200,220,250);
}
    
.controlpanel {
    height: 5vh;
    border-bottom: 1px solid rgb(200,200,200);
} 

#timeframe {
    width:200px;
}
    
</style>

</head>

<body style="padding:10px">
    
    <div class="controlpanel" style="">
        <div class="padding-10">
            <span class="text-20">Μηνύματα</span> &nbsp;
            <select id="timeframe">
                <option value="<?php echo date("YmdHis", strtotime('-1 week')) ?>" selected>Τελ. εβδομάδα</option>
                <option value="<?php echo date("YmdHis", strtotime('-1 month')) ?>">Τελ. μήνας</option>
                <option value="<?php echo date("YmdHis", strtotime('-3 month')) ?>">Τελ. 3-μηνο</option>
                <option value="<?php echo date("YmdHis", strtotime('-6 month')) ?>">Τελ. 6-μηνο</option>
                <option value="<?php echo date("YmdHis", strtotime('-1 year')) ?>">Τελ. χρόνος</option>
                <option value="0">Όλα</option>
            </select>
            
            <?php
            if (($_SESSION['user_profile']==3)) {
                $chk_allconversations = new checkbox("chk_allconversations", "All conversations", 0);
                echo " &nbsp; ".$chk_allconversations->checkboxSimple()." All conversations";
            }
            ?>
            
        </div>
    </div>
    
    <div class="clear"></div>
    
    <div class="col-3 allmessages-users-container">       
        <?php
        echo "<div id=\"all-users\" data-id=\"0\" class=\"allmessages-user\">ALL USERS</div>";
        for ($i = 0; $i < count($rsUsers); $i++) {
            $user = new USERS($db1, $rsUsers[$i]['id'], $rsUsers);
            echo "<div data-id=\"".$user->get_id()."\" class=\"allmessages-user\">".$user->get_fullname()."</div>";
        }
        ?>        
    </div>
    
    <div id="allmessages-container" class="col-9">
        <div id="message-dock-wait" class="align-center invisible padding-20"><img src="img/wait.gif" width="36" height="36" alt="wait"/>
        </div>
        <div id="allmessages"></div>
        
    </div>
    
    <div class="clear"></div>
    
    
</body>
    
</html>