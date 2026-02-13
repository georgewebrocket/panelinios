<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTFREELISTINGS",$lang,$db1);

$sql="SELECT CATEGORIES2.id, CATEGORIES2.description, T2.countcompanies 
    FROM (SELECT '0' AS id,'-' AS description FROM CATEGORIES UNION SELECT id, description FROM CATEGORIES) CATEGORIES2 
    INNER JOIN 
    (SELECT basiccategory, COUNT(id) AS countcompanies FROM COMPANIES WHERE status=1 OR status IS NULL OR status = 0 GROUP BY basiccategory) T2
    ON CATEGORIES2.id = T2.basiccategory ORDER BY T2.countcompanies DESC";

if (isset($_REQUEST['active'])) {
    $sql="SELECT CATEGORIES2.id, CATEGORIES2.description, T2.countcompanies 
    FROM (SELECT '0' AS id,'-' AS description FROM CATEGORIES UNION SELECT id, description FROM CATEGORIES) CATEGORIES2 
    INNER JOIN 
    (SELECT basiccategory, COUNT(id) AS countcompanies FROM COMPANIES WHERE status=2 GROUP BY basiccategory) T2
    ON CATEGORIES2.id = T2.basiccategory ORDER BY T2.countcompanies DESC";
}

if (isset($_REQUEST['inactive'])) {
    $sql="SELECT CATEGORIES2.id, CATEGORIES2.description, T2.countcompanies 
    FROM (SELECT '0' AS id,'-' AS description FROM CATEGORIES UNION SELECT id, description FROM CATEGORIES) CATEGORIES2 
    INNER JOIN 
    (SELECT basiccategory, COUNT(id) AS countcompanies FROM COMPANIES WHERE status=3 GROUP BY basiccategory) T2
    ON CATEGORIES2.id = T2.basiccategory ORDER BY T2.countcompanies DESC";
}

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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="js/code.js"></script>

<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 550 });	
});
</script>


<style>    
    #gridBaseStatistics {
        max-width: 800px;
        margin-left: 1em;
    }
    h1,h3 {
        margin-left: 1em;
    }
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        <div style="padding-left:20px">
            <?php if (isset($_REQUEST['active'])) { 
                $status = 2;
                ?>
            <div class="col-12"><h2 style="margin-bottom: 20px;">Ενεργές καταχωρήσεις ανά κατηγορία</h2></div>
            <div style="clear: both;"></div>
            <a class="button" href="reportFreeListingsPerCat.php">Ελεύθερες καταχωρήσεις</a> &nbsp;
            <a class="button" href="reportFreeListingsPerCat.php?inactive=1">Ανενεργές καταχωρήσεις</a>
            <?php } else if (isset($_REQUEST['inactive'])) { 
                $status = 3;
                ?>
            <div class="col-12"><h2 style="margin-bottom: 20px;">Ανενεργές καταχωρήσεις ανά κατηγορία</h2></div>
            <div style="clear: both"></div>
            <a class="button" href="reportFreeListingsPerCat.php">Ελεύθερες καταχωρήσεις</a> &nbsp;
            <a class="button" href="reportFreeListingsPerCat.php?active=1">Ενεργές καταχωρήσεις</a>
            <?php } else { 
                $status = 0;
                ?>
            <div class="col-12"><h2 style="margin-bottom: 20px;">Ελεύθερες καταχωρήσεις ανά κατηγορία</h2></div>
            <div style="clear: both"></div>
            <a class="button" href="reportFreeListingsPerCat.php?active=1">Ενεργές καταχωρήσεις</a> &nbsp;
            <a class="button" href="reportFreeListingsPerCat.php?inactive=1">Ανενεργές καταχωρήσεις</a>
            <?php } ?>
        </div>
            
        
        
        <div style="clear: both; height: 30px"></div> 

        <?php
        
        $rs = $db1->getRS($sql);
        for ($i = 0; $i < count($rs); $i++) {
            $cat = $rs[$i]['id'];
            $rs[$i]['description'] = "<a class=\"fancybox\" href=\"getCompaniesBaseStats.php?cat=$cat&status=$status\">" . 
                    $rs[$i]['description'] . "</a>";
        }
        
        $gridBaseStatistics = new datagrid("gridBaseStatistics", $db1, 
                    "", 
                    array("id", "description","countcompanies"), 
                    array("ID", "Κατηγορία","Αριθμ. καταχωρήσεων"),
                    $ltoken, 0
                    );
        
        $gridBaseStatistics->set_rs($rs);
        
            $arrBaseStatistics = $gridBaseStatistics->get_rs();
            //αλλαγή στο "$gridBaseStatistics->get_rs()" προσθήκη id που δεν υπάρχούν
            for($i=0;$i<count($arrBaseStatistics);$i++){
                $arrBaseStatistics[$i]['id'] = $i+1;
            }            
            $gridBaseStatistics->set_rs($arrBaseStatistics);
            $gridBaseStatistics->set_colWidths(array("50","300","50"));
            //$gridBaseStatistics->set_colsFormat(array('',''));
            $gridBaseStatistics->get_datagrid();
            
            echo "<br/><h3>ΣΥΝΟΛΟ ".func::rsSum($gridBaseStatistics->get_rs(),"countcompanies")."</h3>";
            
        ?>                        
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>
