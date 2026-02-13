<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("CATEGORIES",$lang,$db1);

?>
<html>
    <head>
        <title>PANELINIOS- CRM</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />  
        
        <style>
            #gridCategories {
                max-width: 630px;
            }
        </style>
        
    </head>
    
    <body>
        
<?php
$parentid= $_GET['id'];                
$gridCategories = new datagrid("gridCategories", $db1,
        "SELECT * FROM CATEGORIES WHERE parentid=".$parentid." ORDER BY description", 
        array("description"), 
        array($l->l("list_category_description")),
        $ltoken, 0, 
        TRUE,"editCategory.php",$lg->l("edit"), 
        TRUE,"delCategory.php",$lg->l("del")
        );
$gridCategories->set_hasheaders(FALSE);
$gridCategories->set_treeParams(TRUE,"addsubcategory.php",$lg->l("add"),"parentid","CATEGORIES","getCategorygrid.php");
$gridCategories->set_colWidths(array("30","300","100","100","100"));
$gridCategories->get_datagrid();
                
                
                

?>

        </body>
    
</html>
