<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("CATEGORIES",$lang,$db1);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
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
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 650 });	
});

</script>

<style>
    #gridCategories {
        max-width: 630px;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h2>Κατηγορίες</h2>
        
        <div class="col-10">
                <div class="articles">
                    <?php

                    $gridCategories = new datagrid("gridCategories", $db1,
                            "SELECT * FROM CATEGORIES WHERE parentid=0 ORDER BY description", 
                            array("description"), 
                            array($l->l("list_category_description")),
                            $ltoken, 0, 
                            TRUE,"editCategory.php",$lg->l("edit"), 
                            TRUE,"delCategory.php",$lg->l("del")
                            );
                    $gridCategories->set_treeParams(TRUE,"addsubcategory.php",$lg->l("add"),"parentid","CATEGORIES","getCategorygrid.php");
                    $gridCategories->set_colWidths(array("30","300","100","100","100"));
                    $gridCategories->get_datagrid();
                   
                    ?>
                </div>
                
            </div>
            
            <div style="clear: both"></div>
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>