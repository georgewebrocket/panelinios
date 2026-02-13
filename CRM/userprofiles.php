<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("USER_PROFILES",$lang,$db1);

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
    #gridUserprofiles {
        max-width: 900px;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h2><?php echo $l->l("list_userprofiles"); ?></h2>
        
        <div class="toolbar">
            <a class="button fancybox" href="edituserprofile.php?id=0&<?php echo $ltoken; ?>"><?php echo $lg->l("add"); ?></a> 
        </div> 
        
        <div class="col-10">
                
                <?php
                
                $gridUserprofiles = new datagrid("gridUserprofiles", $db1, 
                        "SELECT * FROM USER_PROFILES", 
                        array("description","flag1","flag2","flag3","flag4",
                            "flag5","flag6","flag7","flag8","flag9","flag10"), 
                        array($l->l("list_description"),$l->l("list_flag1"), $l->l("list_flag2"), 
                            $l->l("list_flag3"), $l->l("list_flag4"), $l->l("list_flag5"), 
                            $l->l("list_flag6"), $l->l("list_flag7"), $l->l("list_flag8"), 
                            $l->l("list_flag9"), $l->l("list_flag10")),
                        $ltoken, 0, 
                        TRUE,"edituserprofile.php",$lg->l("edit"),
                        TRUE,"deluserprofile.php",$lg->l("del")
                        );
                $gridUserprofiles->set_colWidths(array("300","50","50","50","50",
                    "50","50","50","50","50","50","100","100"));
                $gridUserprofiles->set_colsFormat(array('','YESNO','YESNO','YESNO',
                    'YESNO','YESNO','YESNO','YESNO','YESNO','YESNO','YESNO','',''));
                $gridUserprofiles->get_datagrid();
              
                ?>
                
            </div>
            
            <div style="clear: both"></div>
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>