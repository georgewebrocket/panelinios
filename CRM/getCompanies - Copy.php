<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$user = 0;
$status = 0;
$package = 0;
$dateFrom = "";
$dateTo = "";

if (isset($_GET['user'])) {
    $user = $_GET['user'];
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}
if (isset($_GET['package'])) {
    $package = $_GET['package'];
}
if (isset($_GET['dateFrom'])) {
    $dateFrom  = $_GET['dateFrom'];
}
if (isset($_GET['dateTo'])) {
    $dateTo = $_GET['dateTo'];
}

$sqluser = "SELECT DISTINCT company FROM ACTIONS WHERE user=$user "
        . "AND `status2`=5 AND `atimestamp`>='".$dateFrom."' "
        . "AND `atimestamp`<='".$dateTo."'";
$sql = "SELECT id, companyname, area, basiccategory, status, package, id as mylink FROM COMPANIES T1 INNER JOIN ($sqluser) T2 ON T1.id = T2.company WHERE id>0 ";
if ($status>0) {
    $sql .= "AND status IN ($status) ";
}
if ($package>0) {
    $sql .= "AND package=$package ";
}

$sql .= "ORDER BY companyname";

//echo $sql;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    
    </head>

    <body class="form">
        <div class="form-container">
            
            <?php
            $gridCompanies = new datagrid("gridCompanies", $db1, $sql, 
                array("id","companyname","area","basiccategory","status","mylink"), 
                array("ID","ΕΠΩΝΥΜΙΑ","ΠΕΡΙΟΧΗ","ΚΑΤΗΓΟΡΙΑ","Status","OPEN"),
                $ltoken, 0
                );
            //$gridCompanies->set_select($l->l("select"));
            $gridCompanies->set_colWidths(array("50","200","100","100","100","50"));
            $gridCompanies->col_vlookup("status","status","STATUS","description", $db1);  
            //$gridCompanies->col_vlookup("user","user","USERS","fullname", $db1);
            //$gridCompanies->col_vlookup("recalltime","recalltime","TIMES","description", $db1);
            $gridCompanies->col_vlookup("area","area","AREAS","description", $db1); 
            $gridCompanies->col_vlookup("basiccategory","basiccategory","CATEGORIES","description", $db1);
            //$gridCompanies->set_colsFormat(array('','','','','','','','DATE',''));
            $gridCompanies->col_func("mylink", "mylink", "<a href=\"editcompany.php?id=??\">OPEN</a>", "??");

            $gridCompanies->get_datagrid();

            ?>
            
            
            
        </div>
        
        
    </body>
    
</html>