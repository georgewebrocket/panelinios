<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$basicCategory = $_GET['cat'];
$status = $_GET['status'];

if ($status==0) {
    $sql = "SELECT id AS id, companyname AS companyname, area AS area, profession, N'' AS product_category, status, id AS mylink, zipcode, subcategory, CONCAT(phone1,' ', phone2, ' ', mobilephone, ' ', fax) AS phones FROM COMPANIES WHERE basiccategory = $basicCategory AND (status=1 OR status IS NULL OR status=0) ORDER BY companyname"; 
    //echo $sql;
}
else {
    $sql = "SELECT id AS id, companyname AS companyname, area AS area, profession, N'' AS product_category, status, id AS mylink, zipcode, subcategory, CONCAT(phone1,' ', phone2, ' ', mobilephone, ' ', fax) AS phones FROM COMPANIES WHERE basiccategory = $basicCategory AND status=$status ORDER BY companyname"; 
}


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
    
    <link href="css/tableexport.css" rel="stylesheet" type="text/css">
    <script src="js/FileSaver.min.js"></script>
    <script src="js/Blob.min.js"></script>
    <script src="js/xls.core.min.js"></script>
    <script src="js/tableexport.js"></script>
    
    </head>

    <body class="form">
        <div class="form-container">
            
            <?php
            $basicCategoryDescr = func::vlookup("description", "CATEGORIES", "id=$basicCategory", $db1);
            echo "<h1>$basicCategoryDescr</h1>";
            ?>
            
            <?php
            $gridCompanies = new datagrid("gridCompanies2", $db1, $sql, 
                array("id","companyname","area", "zipcode", "phones", "profession","mylink"), 
                array("ID","ΕΠΩΝΥΜΙΑ","ΠΕΡΙΟΧΗ","ΤΚ", "ΤΗΛ", "ΕΠΑΓΓΕΛΜΑ","OPEN"),
                $ltoken, 0
                );
            
            $gridCompanies->set_colWidths(array("50","200","100","100","100","100","50"));
                      
            $gridCompanies->col_vlookup("area","area","AREAS","description", $db1);
            $gridCompanies->col_vlookup("profession","profession","PROFESSIONS","description", $db1);
            $gridCompanies->col_func("mylink", "mylink", "<a href=\"editcompany.php?id=XX\">OPEN</a>", "XX");

            $gridCompanies->get_datagrid();

            ?>
            
            
            
        </div>
        
        <script>
            $(function() {
                $("#gridCompanies2").tableExport({formats: ["xlsx","xls", "csv"]});
                
            });

        </script>
        
        
    </body>
    
</html>