<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("AREAS",$lang,$db1);

?>
<html>
    <head>
        <title>PANELINIOS- CRM</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />  
        
        <style>
            #gridArea {
                max-width: 630px;
            }
        </style>
        
    </head>
    
    <body>
        
<?php
$parentid= $_GET['id'];                
$gridAreas = new datagrid("gridAreas", $db1,
        "SELECT * FROM AREAS WHERE parentid=".$parentid." ORDER BY description", 
        array("description"), 
        array($l->l("list_area_description")),
        $ltoken, 0, 
        TRUE,"editArea.php",$lg->l("edit"), 
        TRUE,"delArea.php",$lg->l("del")
        );
$gridAreas->set_hasheaders(FALSE);
$gridAreas->set_treeParams(TRUE,"addsubarea.php",$lg->l("add"),"parentid","AREAS","getAreagrid.php");
$gridAreas->set_colWidths(array("30","300","100","100","100"));
$gridAreas->get_datagrid();
                
                
                

?>

        </body>
    
</html>
