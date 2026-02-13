<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT * FROM PROFESSIONS";
$search = "";
if (isset($_REQUEST['t_search'])) {
    $search = $_REQUEST['t_search'];
    $sql .= " WHERE description LIKE '%$search%'";
}

$fields = "";
$orderBy = "ORDER BY description";
$rowsperpage = 50;
$curPage = isset($_GET['page'])? $_GET['page']: 0; 
$params = NULL;
$myURL = "http://".$_SERVER[HTTP_HOST].$_SERVER['REQUEST_URI'];
$pos = strpos($myURL, "&page=");
if ($pos) {
   $link = substr($myURL, 0, $pos)."&page="; 
}
else {
   $link = $_SERVER['REQUEST_URI']."&page=";  
}
$rsPage = new RS_PAGE($db1, $sql, $fields, $orderBy, $rowsperpage, $curPage, $params, $link);
$rs = $rsPage->getRS();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<?php include "blocks/head.php"; ?>

<style>
    #grid {
        max-width: 800px;
    }
    .paginate {
        padding:5px;
        background-color: rgb(220,220,220);
        margin:3px;
    }
    .paginate-current {
        padding:5px;
        background-color: rgb(100,150,200);
    }

</style>
</head>
    
<body>
    
<?php include "blocks/header.php"; ?>
<?php include "blocks/menu.php"; ?> 
    
<div class="main">
    
<h2>ΕΠΑΓΓΕΛΜΑΤΑ</h2>

<div class="col-4">
    <form action="professions.php" method="get" style="margin-left:0px">
    <?php
    
    $t_search = new textbox("t_search", "Search", $search);
    echo $t_search->textboxSimple();
    echo "&nbsp;";
    
    $btnOk = new button("btnOk", "Search");
    echo $btnOk->get_button_simple();
    
    ?>
</form>
</div>
<div class="clear"></div>

<div class="toolbar">
    <a class="button fancybox" href="editProfession.php?id=0&<?php echo $ltoken; ?>">
        Προσθήκη επαγγέλματος
    </a> 
</div>
    
<?php

$gridCities = new datagrid("grid", $db1, 
        "", 
        array("id","description"), 
        array("ID", "ΠΕΡΙΓΡΑΦΗ"),
        $ltoken, 0, 
        TRUE, "editProfession.php", "ΑΝΟΙΓΜΑ",
        TRUE, "delProfession.php", "ΔΙΑΓΡΑΦΗ"
        );
$gridCities->set_rs($rs);
$gridCities->set_colWidths(array("100","600","100","100"));
$gridCities->get_datagrid();

echo "<div style=\"padding:10px\">";                    
$rsPage->getFirst();
$rsPage->getPrev();
$rsPage->getPageLinks();
$rsPage->getNext();
$rsPage->getLast();
echo " ".$rsPage->getCount()." records ";
echo "</div>";

?>
    

</div>    
    
</body>
</html>    