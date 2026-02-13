<?php

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTBASESTATISTICS",$lang,$db1);

$sql="SELECT S.description, COUNT( C.id ) AS countcompanies FROM STATUS AS S INNER JOIN COMPANIES_STATUS AS C ON C.status = S.id WHERE C.productcategory=1 GROUP BY S.description";

$sql_2="SELECT S.description, COUNT( C.id ) AS countcompanies FROM STATUS AS S INNER JOIN COMPANIES_STATUS AS C ON C.status = S.id WHERE C.productcategory=2 GROUP BY S.description";

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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>-->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="js/code.js"></script>

<style>    
    #gridBaseStatistics, #gridBaseStatistics2 {
        max-width: 600px;
        margin-left: 20px;
    }
</style>


<link href="css/tableexport.css" rel="stylesheet" type="text/css">
<script src="js/FileSaver.min.js"></script>
<script src="js/Blob.min.js"></script>
<script src="js/xls.core.min.js"></script>
<script src="js/tableexport.js"></script>




</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-12">
            
            <h1 style="margin-left:20px; margin-bottom: 20px;">Στατιστικά βάσης</h1>
            
            <h2 style="margin-left:20px">Online καταχώρηση</h2></div>
            <div style="clear: both"></div>        

            <?php
            $gridBaseStatistics = new datagrid("gridBaseStatistics", $db1, 
                    $sql, 
                    array("description","countcompanies"), 
                    array("Status","Αρ. πελατών"),
                    $ltoken, 0
                    );
            $arrBaseStatistics = $gridBaseStatistics->get_rs();
            //αλλαγή στο "$gridBaseStatistics->get_rs()" προσθήκη id που δεν υπάρχούν
            for($i=0;$i<count($arrBaseStatistics);$i++){
                $arrBaseStatistics[$i]['id'] = $i+1;
            }            
            $gridBaseStatistics->set_rs($arrBaseStatistics);
            $gridBaseStatistics->set_colWidths(array("300","50"));
            $gridBaseStatistics->set_colsFormat(array('',''));
            $gridBaseStatistics->col_sum('countcompanies', '');
            $gridBaseStatistics->get_datagrid();
            
            echo "<br/><br/>";
            echo '<h2 style="margin-left:1em">Domain</h2>';
            
            $gridBaseStatistics2 = new datagrid("gridBaseStatistics2", $db1, 
                    $sql_2, 
                    array("description","countcompanies"), 
                    array("Status","Αρ. πελατών"),
                    $ltoken, 0
                    );
            $arrBaseStatistics2 = $gridBaseStatistics2->get_rs();
            
            //echo $sql2;
            //var_dump($arrBaseStatistics2);
            
            if ($arrBaseStatistics2) {
                //αλλαγή στο "$gridBaseStatistics->get_rs()" προσθήκη id που δεν υπάρχούν
                for($i=0;$i<count($arrBaseStatistics2);$i++){
                    $arrBaseStatistics2[$i]['id'] = $i+1;
                }            
                $gridBaseStatistics2->set_rs($arrBaseStatistics2);
                $gridBaseStatistics2->set_colWidths(array("300","50"));
                $gridBaseStatistics2->set_colsFormat(array('',''));
                $gridBaseStatistics2->col_sum('countcompanies', '');
                $gridBaseStatistics2->get_datagrid();
            }
            
            $new = func::vlookup("count(*)", "COMPANIES", "status=1 OR status IS NULL OR status=0 OR status=''", $db1);
            echo "<br/><br/>";
            echo "<h2 style=\"margin-left:1em\">Νέες καταχωρήσεις: $new</h2>";
            echo "<br/><br/>";
            
            
            ?>                        
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>  
    
 
    
</body>
</html>
