<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("USERS",$lang,$db1);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 800, 'height' : 450 });	
});

</script>

<style>
    #gridUsers {
        max-width: 800px;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h2><?php echo $l->l("list_users"); ?></h2>
        
        <div class="toolbar">
            <a class="button fancybox" href="edituser.php?id=0&<?php echo $ltoken; ?>"><?php echo $lg->l("add"); ?></a> 
        </div> 
        
        <div class="col-10">
                
                <?php
                
                $gridUsers = new datagrid("gridUsers", $db1, 
                        "SELECT * FROM USERS ORDER BY fullname", 
                        array("fullname","username","active", "is_agent", "userprofile"), 
                        array($l->l("list_fullname"),$l->l("list_username"), $l->l("list_active"), "Agent", $l->l("list_profile")),
                        $ltoken, 0, 
                        TRUE,"edituser.php",$lg->l("edit")
                        );
                $gridUsers->set_colWidths(array("300","200","50", "50", "150","100"));
                $gridUsers->col_vlookup("userprofile","userprofile","USER_PROFILES","description", $db1);                                     
                $gridUsers->set_colsFormat(array('','','YESNO', 'YESNO',  '',''));
                $gridUsers->get_datagrid();
              
                ?>
                
            </div>
            
            <div style="clear: both"></div>
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>