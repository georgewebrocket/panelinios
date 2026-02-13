<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

if (strpos($_SESSION['user_access'],"1")===false) {                   
	header('Location: index.php');
}

$sql = ""; $sql_1 = "";
$criteria = "";
$msg = "";
$sdateFrom=""; $sdateTo="";

if (isset($_GET['search'])) {
    
    if ($_POST['txtDateFrom']!="") {
        $sdateFrom = $_POST['txtDateFrom'];
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        $yy1 = substr($dateFrom, 0, 4);
        $mm1 = substr($dateFrom, 4, 2);
        $dd1 = substr($dateFrom, 6, 2);
        $dateFrom1 = $yy1."-".$mm1."-".$dd1." 00:00:00";

        if ($_POST['txtDateTo']!="") {
            $sdateTo = $_POST['txtDateTo'];
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
            $yy2 = substr($dateTo, 0, 4);
            $mm2 = substr($dateTo, 4, 2);
            $dd2 = substr($dateTo, 6, 2);
        }
        else {
            $dateTo = substr($dateFrom,0,8)."999999";
            $yy2 = substr($dateTo, 0, 4);
            $mm2 = substr($dateTo, 4, 2);
            $dd2 = substr($dateTo, 6, 2);
        }
        $dateTo1 = $yy2."-".$mm2."-".$dd2." 23:59:59";
        
        $sqlProdCat = "";
        $productCategories = selectList::getVal("l_productcategories", $_POST);
        if ($productCategories!="") {            
            $productCategoriesIds = str_replace(array("[", "]"), "", $productCategories);
            $sqlProdCat = " AND TRANSACTIONS.package IN (SELECT id FROM PACKAGES WHERE product_category IN ($productCategoriesIds)) ";
        }
        
        $date0 = date("Y-m-d", strtotime($dateFrom1));
        $arD = array();   
        $arD14 = array();  
        $i = 0;
        $arD[$i] = $date0;
        //print $arD[$i];
        $arD14[$i] = func::dateTo14str($arD[$i],array("/","-","."),"DATE");
        $sqlFields = "SUM(IF(tdatetime='$arD14[$i]', 1, 0)) AS D$i";
        $sqlFields2 = "SUM(IF(tdatetime='$arD14[$i]', amount, 0)) AS D$i";
        $gridDayCols = array(); $gridDayColHeaders = array(); $gridFormat = array();
        $gridDayCols[$i] = "D$i";
        $gridDayColHeaders[$i] = func::DateToShortDate($arD[$i]);
        $grid3Format[0] = "";
        $dFields = 0;
        while ($arD[$i]<$dateTo1) {            
            $i++;
            $arD[$i] = date('Y-m-d', strtotime($arD[$i-1] . ' + 1 day'));
            $arD14[$i] = func::dateTo14str($arD[$i],array("/","-","."),"DATE");
            $sqlField = "SUM(IF(tdatetime='$arD14[$i]', 1, 0)) AS D$i";
            $sqlField2 = "SUM(IF(tdatetime='$arD14[$i]', amount, 0)) AS D$i";
            $sqlFields = func::ConcatSpecial($sqlFields, $sqlField, ",");
            $sqlFields2 = func::ConcatSpecial($sqlFields2, $sqlField2, ",");
            $gridDayCols[$i] = "D$i";
            $gridDayColHeaders[$i] = func::DateToShortDate($arD[$i]);
            $grid3Format[$i] = "CURRENCY";
            $dFields = $i;
        }
        
        //poliseis
        $sql_1 = "SELECT `seller` AS id, $sqlFields           
            FROM `TRANSACTIONS`
            WHERE `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
            AND transactiontype=1
            $sqlProdCat
            GROUP BY `seller`";
        //print $sql_1;
           //echo "<!--".$sql_1."-->";
        $rs_1 = $db1->getRS($sql_1);
        //processing recordset
        $arSum1 = array();
        $arSum1["user"] = 0;
        for ($i=0;$i<count($rs_1);$i++) {
            $cuser = $rs_1[$i]["id"];
            $arSum1["user"] += 1;
            $cstatus = 0; 
            for ($k=0;$k<count($arD)-1;$k++) {        
                $cDateFrom = $arD14[$k];
                $cDateTo = $arD14[$k];
                $myIndex = $k;
                $arSum1["D$myIndex"] += $rs_1[$i]["D$myIndex"];
                $rs_1[$i]["D$myIndex"] = 
                    "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser"
                    . "&dateFrom=$cDateFrom&dateTo=$cDateTo&status=$cstatus\">".
                    $rs_1[$i]["D$myIndex"]."</a>";
            }
        }
        //poliseis posa
        $sql_1a = "SELECT `TRANSACTIONS`.`seller` AS id, $sqlFields2           
            FROM `TRANSACTIONS` INNER JOIN COMPANIES ON TRANSACTIONS.company = COMPANIES.id
            WHERE `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
            AND transactiontype=1 
            $sqlProdCat
            GROUP BY `TRANSACTIONS`.`seller`";
           //echo "<!--".$sql_1a."-->";
        //echo $sql_1a;
        $rs_1a = $db1->getRS($sql_1a);
        //processing recordset...
        $arSum3 = array();
        $arSum3["user"] = 0;
        for ($i=0;$i<count($rs_1a);$i++) {
            $arSum3["user"] += 1;
            for ($k=0;$k<count($arD)-1;$k++) {                 
                $myIndex = $k;
                $arSum3["D$myIndex"] += $rs_1a[$i]["D$myIndex"];               
            }
        }
        
        //akyroseis
        $sql_2 = "SELECT `TRANSACTIONS`.`seller` AS id, $sqlFields , TRANSACTIONS.status            
            FROM `TRANSACTIONS` INNER JOIN COMPANIES ON TRANSACTIONS.company = COMPANIES.id
            WHERE `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
            AND transactiontype=1
            AND TRANSACTIONS.status=3
            $sqlProdCat
            GROUP BY `TRANSACTIONS`.`seller`";
        
        $rs_2 = $db1->getRS($sql_2);
        //processing RS
        $arSum2 = array();
        $arSum2["user"] = 0;
        for ($i=0;$i<count($rs_2);$i++) {
            $cuser = $rs_2[$i]["id"];
            $arSum2["user"] += 1;
            $cstatus = $rs_2[$i]["status"];            
            for ($k=0;$k<count($arD)-1;$k++) {                
                $cDateFrom = $arD14[$k];
                $cDateTo = $arD14[$k];
                $myIndex = $k;
                $arSum2["D$myIndex"] += $rs_2[$i]["D$myIndex"];
                $rs_2[$i]["D$myIndex"] = 
                    "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser"
                    . "&dateFrom=$cDateFrom&dateTo=$cDateTo&status=$cstatus\">".
                    $rs_2[$i]["D$myIndex"]."</a>";
            }
        }
        //akyroseis posa
        $sql_2a = "SELECT `TRANSACTIONS`.`seller` AS id, $sqlFields2 , TRANSACTIONS.status            
            FROM `TRANSACTIONS` INNER JOIN COMPANIES ON TRANSACTIONS.company = COMPANIES.id
            WHERE `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
            AND transactiontype=1
            AND TRANSACTIONS.status=3
            $sqlProdCat
            GROUP BY `TRANSACTIONS`.`seller`";
        //print $sql_2a;
        $rs_2a = $db1->getRS($sql_2a);
        //processing RS
        $arSum4 = array();
        $arSum4["user"] = 0;
        for ($i=0;$i<count($rs_2a);$i++) {
            $cuser = $rs_2[$i]["id"];
            $arSum4["user"] += 1;           
            for ($k=0;$k<count($arD)-1;$k++) {                
                $myIndex = $k;
                $arSum4["D$myIndex"] += $rs_2a[$i]["D$myIndex"];                
            }
        }
        
        
    }
    else {
        $msg = "select-date";
        $sql = "";
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
    #gridReport1, #gridReport2, #gridReport3, #gridReport4{
        width: 100%;
/*        max-width: 1200px;*/
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
        /*background-color: rgb(180,220,250);*/
    }
    
    #l_productcategories {
        margin-top: 10px;
        margin-bottom: 10px;
        width: 60%;

    }
    #l_productcategories div.col-10, #l_productcategories div.col-2 {
        border-top: 1px dotted rgb(200,200,200);
        padding-top: 5px;
        padding-bottom: 5px;
    }
    
</style>


</head>
    
    <body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>

        <div class="main">
        
        <div class="col-12"><h2 style="margin-left:1em">ΗΜΕΡΗΣΙΕΣ ΠΩΛΗΣΕΙΣ ΑΝΑ ΧΡΗΣΤΗ</h2></div>
        <div style="clear: both"></div>        
        <div class="col-8">
            
            <div class="form-container">

                <form action="reportSalesPerUser3.php?search=1" method="POST">
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
                        
                        $l_productcategories = new selectList("l_productcategories", 
                                "PRODUCT_CATEGORIES", $productCategories, $db1);
                        $l_productcategories->set_descrField("description");
                        $l_productcategories->set_orderby("id");
                        $l_productcategories->set_label("Κατηγορίες προϊόντων");
                        $l_productcategories->getList();
                        
                        
                        ?> 
                        
                    </div>
                    <div style="clear: both"></div>
                    

                    <input name="BtnSearch" type="submit" value="SEARCH" />
                    <input type="reset" value="RESET" />

                </form>
            </div>
            
            
            <?php if ($sql_1!="") { ?>            

                <?php
                $arGridCols = array(); $arGridColHeaders = array(); $arColWidths = array();
                $arGridCols[0] = "id"; $arGridColHeaders[0] = "USER"; $arColWidths[0] = "150";
                for ($i=0;$i<count($gridDayCols)-1;$i++) {
                    $arGridCols[$i+1] = $gridDayCols[$i];
                    $arGridColHeaders[$i+1] = $gridDayColHeaders[$i];
                    $arColWidths[$i+1] = "50";
                }                
                //$arGridCols[$i+1] = "MYLINK"; $arGridColHeaders[$i+1] = "VIEW"; $arColWidths[$i+1] = "50";
                
                echo "<h2 style=\"margin-left:20px\">ΗΜΕΡΗΣΙΕΣ ΠΩΛΗΣΕΙΣ</h2>"; 
                if ($rs_1) {
                    $gridReport1 = new datagrid("gridReport1", $db1, 
                        "", 
                        $arGridCols, 
                        $arGridColHeaders,
                        $ltoken, 0);
                    $gridReport1->set_rs($rs_1);
                    $gridReport1->col_vlookup("id","id","USERS","fullname", $db1);               
                    $gridReport1->set_colWidths($arColWidths); 
                    $gridReport1->setFooter($arSum1);
                    $gridReport1->get_datagrid();
                }
                else {
                    echo "<h3 style=\"margin-left:20px\">Δεν υπάρχουν στοιχεία</h3>";
                }
                    
                
                echo "<br/><br/><h2 style=\"margin-left:20px\">ΑΚΥΡΩΣΕΙΣ</h2>";
                
                if ($rs_2) {
                    $gridReport2 = new datagrid("gridReport2", $db1, 
                        "", 
                        $arGridCols, 
                        $arGridColHeaders,
                        $ltoken, 0);
                    $gridReport2->set_rs($rs_2);
                    $gridReport2->col_vlookup("id","id","USERS","fullname", $db1);               
                    $gridReport2->set_colWidths($arColWidths); 
                    $gridReport2->setFooter($arSum2);
                    $gridReport2->get_datagrid();
                }
                else {
                    echo "<h3 style=\"margin-left:20px\">Δεν υπάρχουν στοιχεία</h3>";
                }
                    
                
                //////////////////////////////
                echo "<br/><br/><h2 style=\"margin-left:20px\">ΗΜΕΡΗΣΙΕΣ ΠΩΛΗΣΕΙΣ (ΠΟΣΑ)</h2>";  
                
                if ($rs_1a) {
                    $gridReport3 = new datagrid("gridReport3", $db1, 
                        "", 
                        $arGridCols, 
                        $arGridColHeaders,
                        $ltoken, 0);
                    $gridReport3->set_rs($rs_1a);
                    $gridReport3->col_vlookup("id","id","USERS","fullname", $db1);
                    $gridReport3->set_colsFormat($grid3Format);
                    $gridReport3->set_colWidths($arColWidths); 
                    $gridReport3->setFooter($arSum3);
                    $gridReport3->get_datagrid();
                }
                else {
                    echo "<h3 style=\"margin-left:20px\">Δεν υπάρχουν στοιχεία</h3>";
                }
                
                    
                
                echo "<br/><br/><h2 style=\"margin-left:20px\">ΑΚΥΡΩΣΕΙΣ (ΠΟΣΑ)</h2>";
                
                if ($rs_2a) {
                    $gridReport4 = new datagrid("gridReport4", $db1, 
                        "", 
                        $arGridCols, 
                        $arGridColHeaders,
                        $ltoken, 0);
                    $gridReport4->set_rs($rs_2a);
                    $gridReport4->col_vlookup("id","id","USERS","fullname", $db1);               
                    $gridReport4->set_colWidths($arColWidths);
                    $gridReport4->set_colsFormat($grid3Format);
                    $gridReport4->setFooter($arSum4);
                    $gridReport4->get_datagrid();
                }
                else {
                    echo "<h3 style=\"margin-left:20px\">Δεν υπάρχουν στοιχεία</h3>";
                }
                                
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