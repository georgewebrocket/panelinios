<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("DISCOUNTS",$lang,$db1);

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
    #gridDiscounts {
        max-width: 800px;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h2><?php echo $l->l("list_discounts"); ?></h2>
        
        <div class="toolbar">
            <a class="button fancybox" href="editdiscount.php?id=0&<?php echo $ltoken; ?>"><?php echo $lg->l("add"); ?></a> 
        </div> 
        
        <div class="col-10">
                
                <?php
                
                $gridDiscounts = new datagrid("gridDiscounts", $db1, 
                        "SELECT * FROM DISCOUNTS", 
                        array("package","discount","datestart","datestop","active"), 
                        array($l->l("list_package"),$l->l("list_discount"), $l->l("list_datestart"), 
                            $l->l("list_datestop"), $l->l("list_active")),
                        $ltoken, 0, 
                        TRUE,"editdiscount.php",$lg->l("edit"),
                        TRUE,"deldiscount.php",$lg->l("del")
                        );
                $gridDiscounts->set_colWidths(array("200","100","100","100","50","100","100"));
                $gridDiscounts->col_vlookup("package","package","PACKAGES","description", $db1);                                     
                $gridDiscounts->set_colsFormat(array('','CURRENCY','DATE','DATE','YESNO','',''));
                $gridDiscounts->get_datagrid();
              
                ?>
                
            </div>
            
            <div style="clear: both"></div>
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>