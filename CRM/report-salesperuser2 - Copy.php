<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$criteria = "";
$msg = "";
$sdateFrom="";$sdateTo="";

if (isset($_GET['search'])) {
    if ($_POST['txtDateFrom']!="") {
        $sdateFrom = $_POST['txtDateFrom'];
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        $yy = substr($dateFrom, 0, 4);
        $mm = substr($dateFrom, 4, 2);
        $dd = substr($dateFrom, 6, 2);
        $dateFrom = $yy."-".$mm."-".$dd." 00:00:00";
        
        if ($_POST['txtDateTo']!="") {
            $sdateTo = $_POST['txtDateTo'];
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
            $yy = substr($dateTo, 0, 4);
            $mm = substr($dateTo, 4, 2);
            $dd = substr($dateTo, 6, 2);
        }        
        $dateTo = $yy."-".$mm."-".$dd." 59:59:59";
        
        //total sales
        $sql_1 = "SELECT `ACTIONS`.`user` AS USER, COUNT(`ACTIONS`.id) AS MYCOUNT, '' AS MYLINK                
                FROM `ACTIONS` INNER JOIN `COMPANIES` ON  `ACTIONS`.`company`= `COMPANIES`.`id`
                WHERE `status2`=5 AND `atimestamp`>='".$dateFrom."' AND `atimestamp`<='".$dateTo."'
                GROUP BY `ACTIONS`.`user`
                ORDER BY `ACTIONS`.`user`";
        $rs_1 = $db1->getRS($sql_1);
        for ($i=0;$i<count($rs_1);$i++) {
            $cuser = $rs_1[$i]['USER'];            
            $rs_1[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo\">view</a>";
        }
        
        //clear sales
        $sql_2 = "SELECT `ACTIONS`.`user` AS USER, COUNT(`ACTIONS`.id) AS MYCOUNT, 
                SUM(`price`) AS PRICE, '' AS MYLINK 
                FROM `ACTIONS` INNER JOIN `COMPANIES` ON  `ACTIONS`.`company`= `COMPANIES`.`id`
                WHERE `status` IN (9,10) AND `status2`=5 AND `atimestamp`>='".$dateFrom."' AND `atimestamp`<='".$dateTo."'
                GROUP BY `ACTIONS`.`user`
                ORDER BY `ACTIONS`.`user`";
        $rs_2 = $db1->getRS($sql_2);
        for ($i=0;$i<count($rs_2);$i++) {
            $cuser = $rs_2[$i]['USER'];
            $cstatus = "9,10";
            $rs_2[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus\">view</a>";
        }
        
        //detailed data
        $sql = "SELECT `ACTIONS`.`user` AS USER, COUNT(`ACTIONS`.id) AS MYCOUNT, 
                `package` AS PACKAGE, 
                CASE `status` 
                WHEN 5 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ' 
                WHEN 6 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ' 
                WHEN 2 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ'
                WHEN 3 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ'
                WHEN 4 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ'
                WHEN 9 THEN 'ΠΛΗΡΩΣΕ' WHEN 10 THEN 'ΠΛΗΡΩΣΕ'  
                WHEN 8 THEN 'ΕΠΙΣΤΡΟΦΗ' END AS MYSTATUS, 
                CASE `status` WHEN 5 THEN '2,3,4,5,6' WHEN 6 THEN '2,3,4,5,6' 
                WHEN 2 THEN '2,3,4,5,6' WHEN 3 THEN '2,3,4,5,6' WHEN 4 THEN '2,3,4,5,6'
                WHEN 9 THEN '9,10' WHEN 10 THEN '9,10'  
                WHEN 8 THEN '8' END AS STATUSID,
                SUM(`price`) AS PRICE, '' AS MYLINK
                FROM `ACTIONS` INNER JOIN `COMPANIES` ON  `ACTIONS`.`company`= `COMPANIES`.`id`
                WHERE `status2`=5 AND `atimestamp`>='".$dateFrom."' AND `atimestamp`<='".$dateTo."'
                GROUP BY `ACTIONS`.`user`, `package`, `MYSTATUS`, `STATUSID` 
                ORDER BY `ACTIONS`.`user`, `status`, `package`";
        $rs = $db1->getRS($sql);
        for ($i=0;$i<count($rs);$i++) {
            $cuser = $rs[$i]['USER'];
            $cstatus = $rs[$i]['STATUSID'];
            $cpackage = $rs[$i]['PACKAGE'];
            $rs[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&package=$cpackage\">view</a>";
        }
        
    }
    else {
        $msg = "select-date";
        $sql="";
    }
    
    
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
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 550 });	
});
</script>

<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script>        
    $(function() {
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

</script>



<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReport {
        max-width: 1100px;
        margin-left: 1em;
    }
    #gridReport1{
        max-width: 400px;
        margin-left: 1em;
    }
    
    #gridReport2{
        max-width: 600px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    form {
/*        margin: 0px;
        margin-bottom: 1em;*/
    }
    
    table.datagrid td.even {
        background-color: rgb(180,220,250);
    }
    
</style>


</head>
    
    <body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>

        <div class="main">
        
        <div class="col-3"><h2 style="margin-left:1em">ΠΩΛΗΣΕΙΣ ΑΝΑ ΧΡΗΣΤΗ</h2></div>
        <div style="clear: both"></div>        
        <div class="col-8">
            
            <div class="form-container">

                <form action="report-salesperuser2.php?search=1" method="POST">
                    <div class="col-8 col-md-12 col-sm-12">
                        <?php
                        $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), $sdateFrom);
                        $txtDateFrom->set_format("DATE");
                        $txtDateFrom->set_locale($locale);                        
                        $txtDateFrom->get_Textbox();
                        
                        $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), $sdateTo);
                        $txtDateTo->set_format("DATE");
                        $txtDateTo->set_locale($locale);                        
                        $txtDateTo->get_Textbox();                 
                        
                        
                        ?> 
                        
                    </div>
                    <div style="clear: both"></div>
                    

                    <input name="BtnSearch" type="submit" value="SEARCH" />
                    <input type="reset" value="RESET" />

                </form>
            </div>
            
            
            <?php if ($sql!="") { ?>
            <!--<h2 class="search-results"><?php //echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>-->

                <?php
                echo "<h2 style=\"margin-left:1em\">TOTAL SALES</h2>";    
                $gridReport1 = new datagrid("gridReport1", $db1, 
                    "", 
                    array("USER","MYCOUNT","MYLINK"), 
                    array("USER","COUNT","VIEW"),
                    $ltoken, 0);
                $gridReport1->set_rs($rs_1);
                $gridReport1->col_vlookup("USER","USER","USERS","fullname", $db1);               
                $gridReport1->set_colWidths(array("150","50","50"));                                
                $gridReport1->get_datagrid();
                $count1 = 0;
                for ($i=0;$i<count($rs_1);$i++) {
                    $count1 += $rs_1[$i]['MYCOUNT'];
                }
                echo "<h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count1."</h3>";
                
                echo "<br/><h2 style=\"margin-left:1em\">CLEAR SALES</h2>";
                //echo $sql_2;
                $gridReport2 = new datagrid("gridReport2", $db1, 
                    "", 
                    array("USER","MYCOUNT","PRICE","MYLINK"), 
                    array("USER","COUNT","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport2->set_rs($rs_2);
                $gridReport2->col_vlookup("USER","USER","USERS","fullname", $db1);               
                $gridReport2->set_colWidths(array("150","50","100","50"));
                $gridReport2->set_colsFormat(array("","","CURRENCY",""));                
                $gridReport2->get_datagrid();
                $count2 = 0;
                $price2 = 0;
                for ($i=0;$i<count($rs_2);$i++) {
                    $count2 += $rs_2[$i]['MYCOUNT'];
                    $price2 += $rs_2[$i]['PRICE'];
                }
                $price2 = func::format($price2, "CURRENCY", "GR");
                echo "<h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count2." / ".$price2." €</h3>";
                
                echo "<br/><h2 style=\"margin-left:1em\">DETAILS</h2>";
                $gridReport = new datagrid("gridReport", $db1, 
                    "", 
                    array("USER","MYSTATUS","PACKAGE","MYCOUNT","PRICE","MYLINK"), 
                    array("USER","STATUS","PACKAGE","COUNT","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport->set_rs($rs);
                $gridReport->col_vlookup("USER","USER","USERS","fullname", $db1);  
                $gridReport->col_vlookup("PACKAGE","PACKAGE","PACKAGES","description", $db1); 
                $gridReport->set_colWidths(array("150","150","150","50","100","50"));
                $gridReport->set_colsFormat(array("","","","","CURRENCY"));
                $gridReport->removeDuplicates("USER");
                

                $gridReport->get_datagrid();
                $count3 = 0;
                $price3 = 0;
                for ($i=0;$i<count($rs);$i++) {
                    $count3 += $rs[$i]['MYCOUNT'];
                    $price3 += $rs[$i]['PRICE'];
                }
                $price3 = func::format($price3, "CURRENCY", "GR");
                echo "<h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count3." / ".$price3." €</h3>";
                    
                    //echo "<br/><h3>ΣΥΝΟΛΟ ΚΑΤΑΧΩΡΗΣΕΩΝ ".count($gridReport->get_rs())."</h3>";
                    //echo "<h3>ΣΥΝΟΛΙΚΟ ΠΟΣΟ ".func::format(func::rsSum($gridReportNE->get_rs(),"price"), "CURRENCY")."</h3>";
                                        
            }
            else{
                echo '<h2 class="search-results">'.$msg.'</h2>';
            }
            ?>
        </div>       
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>