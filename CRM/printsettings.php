<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("PRINTSETTINGS",$lang,$db1);

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
    #gridPrintsettings {
        max-width: 850px;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h2><?php echo $l->l("list_printsettings"); ?></h2>
        
        <div class="toolbar">
            <a class="button fancybox" href="editprintsetting.php?id=0&<?php echo $ltoken; ?>"><?php echo $lg->l("add"); ?></a> 
        </div> 
        
        <div class="col-10">
                
                <?php
                
                $gridPrintsettings = new datagrid("gridPrintsettings", $db1, 
                        "SELECT * FROM PRINTSETTINGS", 
                        array("description","printer"), 
                        array($l->l("list_description"),$l->l("list_printer")),
                        $ltoken, 0, 
                        TRUE,"editprintsetting.php",$lg->l("edit"),
                        TRUE,"delprintsetting.php",$lg->l("del")
                        );
                $gridPrintsettings->set_colWidths(array("300","350","100","100"));
                $gridPrintsettings->col_vlookup("printer","printer","PRINTERS","printername", $db1);
                $gridPrintsettings->get_datagrid();
              
                ?>
                
            </div>
            
            <div style="clear: both"></div>
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>