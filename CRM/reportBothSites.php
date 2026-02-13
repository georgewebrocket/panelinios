<?php

/*
ini_set('display_errors',1); 
error_reporting(E_ALL);
*/

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

require_once('php/configPanel.php');
$dbPanel = new DB(conn_panel::$connstr,conn_panel::$username,conn_panel::$password);

$sql="";
$criteria = "";
$msg = "";
$sdateFrom = date("d/m/Y", strtotime("-1 month")); 
$sdateTo = date("d/m/Y");
$productCategories = "";

$token = isset($_POST['txtToken'])? $_POST['txtToken']: "";
$userMode = isset($_POST['t_usermode'])? $_POST['t_usermode']: 1;

if (isset($_GET['search']) && $token=="dtpmh12!") {    
    
    if ($_POST['txtDateFrom']!="") {
        
        $sdateFrom = $_POST['txtDateFrom'];
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        
        if ($_POST['txtDateTo']!="") {
            $sdateTo = $_POST['txtDateTo'];
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
        }
        else {
            $dateTo = $dateFrom;
        }
        
        $dateTo = substr($dateTo, 0, 8) . "999999";
        
        $sqlProdCat = "";
        $productCategories = selectList::getVal("l_productcategories", $_POST);
        if ($productCategories!="") {            
            $productCategoriesIds = str_replace(array("[", "]"), "", $productCategories);
            $sqlProdCat = " AND TRANSACTIONS.package IN (SELECT id FROM PACKAGES WHERE product_category IN ($productCategoriesIds)) ";            
            
        }
        
        
        //total sales
        if ($userMode==1) {
            $sql_1 = "SELECT `TRANSACTIONS`.`seller` AS USER, COUNT(`TRANSACTIONS`.id) AS MYCOUNT,
                SUM(TRANSACTIONS.returned=1) AS COUNTCANCEL,
                SUM(TRANSACTIONS.returned=1) / COUNT(`TRANSACTIONS`.id) * 100 AS CANCELPERCENT,
                '' AS MYLINK, SUM(resell) AS MYRESELL, '' AS MYRESELLLINK, 
                SUM(resend) AS MYRESEND, '' AS MYRESENDLINK, 
                USER_WORKDATA_1.HOURS, COUNT(`TRANSACTIONS`.id)/USER_WORKDATA_1.HOURS AS RATE
                FROM `TRANSACTIONS`
                LEFT JOIN 
                (SELECT `userid`, SUM(`timeout`-`timein`-`hoursoff`) AS HOURS FROM USER_WORKDATA WHERE mdate >='$dateFrom' AND mdate<='$dateTo' GROUP BY userid) USER_WORKDATA_1
                ON TRANSACTIONS.seller = USER_WORKDATA_1.userid
                WHERE TRANSACTIONS.`transactiontype`=1
                AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
                $sqlProdCat    
                GROUP BY `TRANSACTIONS`.`seller`
                ORDER BY `TRANSACTIONS`.`seller`";
        }
        else {
            $sql_1 = "SELECT USER_WORKDATA_1.userid AS USER, COUNT(`TRANSACTIONS`.id) AS MYCOUNT, 
                SUM(TRANSACTIONS.returned=1) AS COUNTCANCEL,
                SUM(TRANSACTIONS.returned=1) / COUNT(`TRANSACTIONS`.id) * 100 AS CANCELPERCENT,
                '' AS MYLINK, SUM(resell) AS MYRESELL, '' AS MYRESELLLINK, 
                SUM(resend) AS MYRESEND, '' AS MYRESENDLINK, 
                USER_WORKDATA_1.HOURS, COUNT(`TRANSACTIONS`.id)/USER_WORKDATA_1.HOURS AS RATE
                
                FROM 
                (SELECT `userid`, SUM(`timeout`-`timein`-`hoursoff`) AS HOURS FROM USER_WORKDATA WHERE mdate >='$dateFrom' AND mdate<='$dateTo' GROUP BY userid) USER_WORKDATA_1
                LEFT OUTER JOIN 
                (SELECT * FROM TRANSACTIONS WHERE TRANSACTIONS.`transactiontype`=1
                AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
                $sqlProdCat) TRANSACTIONS ON TRANSACTIONS.seller = USER_WORKDATA_1.userid                
                
                GROUP BY USER_WORKDATA_1.userid
                ORDER BY USER_WORKDATA_1.userid";
        }
        
        //echo $sql_1;
        //epagelmatias
        $rs_1 = $db1->getRS($sql_1);
        for ($i=0;$i<count($rs_1);$i++) {
            $cuser = $rs_1[$i]['USER'];            
            $rs_1[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&productCats=$productCategoriesIds\">".$rs_1[$i]['MYCOUNT']."</a>";
            $rs_1[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resell=1&productCats=$productCategoriesIds\">".$rs_1[$i]['MYRESELL']."</a>";
            $rs_1[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resend=1&productCats=$productCategoriesIds\">".$rs_1[$i]['MYRESEND']."</a>";
            
        }
        
        //panelinios
        $rs_1a = $dbPanel->getRS($sql_1);
        for ($i=0;$i<count($rs_1a);$i++) {
            $cuser = $rs_1a[$i]['USER'];            
            $rs_1a[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"https://crm.panelinios.gr/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&productCats=$productCategoriesIds\">".$rs_1a[$i]['MYCOUNT']."</a>";
            $rs_1a[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"https://crm.panelinios.gr/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resell=1&productCats=$productCategoriesIds\">".$rs_1a[$i]['MYRESELL']."</a>";
            $rs_1a[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"https://crm.panelinios.gr/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resend=1&productCats=$productCategoriesIds\">".$rs_1a[$i]['MYRESEND']."</a>";
            
        }
        
        
        
        
        
        //CLEAR SALES     ==============================================================
        $factorFPA = (100 + func::vlookup("value", "VAT", "zone=1", $db1)) / 100;
        
        $sql_2 = "SELECT `TRANSACTIONS`.`seller` AS USER, COUNT(`TRANSACTIONS`.id) AS MYCOUNT, 
                SUM(`TRANSACTIONS`.`payedamount`)/$factorFPA AS PRICE, '' AS MYLINK,
                SUM(resell) AS MYRESELL, SUM(IF(resell=1, payedamount/$factorFPA, 0)) AS PRICERESELL, 
                '' AS MYRESELLLINK, 
                SUM(resend) AS MYRESEND, SUM(IF(resend=1, payedamount/$factorFPA, 0)) AS PRICERESEND,
                '' AS MYRESENDLINK
                FROM `TRANSACTIONS` INNER JOIN `COMPANIES` ON  `TRANSACTIONS`.`company`= `COMPANIES`.`id`
                WHERE TRANSACTIONS.`transactiontype`=1 AND `TRANSACTIONS`.`status`=2 
                AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
                    $sqlProdCat
                GROUP BY `TRANSACTIONS`.`seller`
                ORDER BY `TRANSACTIONS`.`seller`";
        
        //epagelmatias
        $rs_2 = $db1->getRS($sql_2);
        for ($i=0;$i<count($rs_2);$i++) {
            $cuser = $rs_2[$i]['USER'];
            //$cstatus = "9,10";
            $cstatus = "2";
            $rs_2[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&productCats=$productCategoriesIds\">view</a>";
            $rs_2[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resell=1&productCats=$productCategoriesIds\">view</a>";
            $rs_2[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resend=1&productCats=$productCategoriesIds\">view</a>";
        }
        
        //panelinios
        $rs_2a = $dbPanel->getRS($sql_2);
        $crm2 = "http://crm.panelinios.gr";
        for ($i=0;$i<count($rs_2a);$i++) {
            $cuser = $rs_2a[$i]['USER'];
            //$cstatus = "9,10";
            $cstatus = "2";
            $rs_2a[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"$crm2/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&productCats=$productCategoriesIds\">view</a>";
            $rs_2a[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"$crm2/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resell=1&productCats=$productCategoriesIds\">view</a>";
            $rs_2a[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"$crm2/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resend=1&productCats=$productCategoriesIds\">view</a>";
        }
        
        
        
        //details ==============================================================
        //detailed data
        $sql = "SELECT `TRANSACTIONS`.`seller` AS USER, COUNT(`TRANSACTIONS`.id) AS MYCOUNT, 
                `TRANSACTIONS`.`package` AS PACKAGE, 
                CASE `TRANSACTIONS`.`status` 
                WHEN 1 THEN 'ΕΚΚΡΕΜΟΤΗΤΑ'                 
                WHEN 2 THEN 'ΠΛΗΡΩΣΕ'  
                WHEN 3 THEN 'ΑΚΥΡΩΣΗ' END AS MYSTATUS, 
                CASE `TRANSACTIONS`.`status` 
                WHEN 1 THEN '1' 
                WHEN 2 THEN '2'  
                WHEN 3 THEN '3' END AS STATUSID,
                SUM(`amount`) AS PRICE, '' AS MYLINK, returned
                FROM `TRANSACTIONS` INNER JOIN `COMPANIES` ON  `TRANSACTIONS`.`company`= `COMPANIES`.`id`
                WHERE TRANSACTIONS.`transactiontype`=1 
                AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
                    $sqlProdCat
                GROUP BY `TRANSACTIONS`.`seller`, `TRANSACTIONS`.`package`, `MYSTATUS`, `STATUSID`, returned 
                ORDER BY `TRANSACTIONS`.`seller`, TRANSACTIONS.`status`, `TRANSACTIONS`.`package`";
        
        //epag
        $rs_3 = $db1->getRS($sql);
        
        $sumPendingEpag = 0; $countPendingEpag = 0;
        $sumReturnEpag = 0; $countReturnEpag = 0;
        
        for ($i=0;$i<count($rs_3);$i++) {
            $cuser = $rs_3[$i]['USER'];
            $cstatus = $rs_3[$i]['STATUSID'];
            $cpackage = $rs_3[$i]['PACKAGE'];
            $rs_3[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&package=$cpackage\">view</a>";
            if ($rs_3[$i]['returned'] == 1) {
                $rs_3[$i]['MYSTATUS'] = "ΕΠΙΣΤΡΟΦΗ";
                $sumReturnEpag += $rs_3[$i]['PRICE'];
                $countReturnEpag += $rs_3[$i]['MYCOUNT'];
            }
            if ($rs_3[$i]['STATUSID'] == 1) {
                $sumPendingEpag += $rs_3[$i]['PRICE']; 
                $countPendingEpag += $rs_3[$i]['MYCOUNT'];
            }
        }
        
        //panel
        $rs_3a = $dbPanel->getRS($sql);
        
        $sumPendingPanel = 0; $countPendingPanel = 0;
        $sumReturnPanel = 0; $countReturnPanel = 0;
        
        for ($i=0;$i<count($rs_3a);$i++) {
            $cuser = $rs_3a[$i]['USER'];
            $cstatus = $rs_3a[$i]['STATUSID'];
            $cpackage = $rs_3a[$i]['PACKAGE'];
            $rs_3a[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"$crm2/getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&package=$cpackage\">view</a>";
            if ($rs_3a[$i]['returned'] == 1) {
                $rs_3a[$i]['MYSTATUS'] = "ΕΠΙΣΤΡΟΦΗ";
                $sumReturnPanel += $rs_3a[$i]['PRICE'];
                $countReturnPanel += $rs_3a[$i]['MYCOUNT'];
            }
            if ($rs_3a[$i]['STATUSID'] == 1) {
                $sumPendingPanel += $rs_3a[$i]['PRICE']; 
                $countPendingPanel += $rs_3a[$i]['MYCOUNT'];
            }
        }
        
    }
    else {
        $msg = "select-date";
        $sql="";
    }
    
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include "_head.php"; ?>

</head>

<body>

<?php include "blocks/header.php"; ?>
<?php include "blocks/menu.php"; ?>

<div class="main">

    <div class="col-12"><h2 style="margin-left:5px">ΠΩΛΗΣΕΙΣ ΑΝΑ ΧΡΗΣΤΗ</h2></div>
        
    <div class="col-12">
        
        <div class="form-container" style="min-height: 0px; max-width: 800px">

            <form action="reportBothSites.php?search=1" method="POST" style="margin:5px">
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
                    
                    $myRs = array(
                        array('id'=>1, 'description'=>'Sales'), 
                        array('id'=>2, 'description'=>'All')
                    );
                    $t_usermode = new comboBox("t_usermode", $db1, "","id","description", $userMode);
                    $t_usermode->set_rs($myRs);
                    $t_usermode->get_comboBox();
                    
                                       
                    $txtToken = new textbox("txtToken", "CODE", $token);
                    $txtToken->set_type("password");
                    $txtToken->get_Textbox();

                    ?> 

                </div>
                <div style="clear: both"></div>


                <input name="BtnSearch" type="submit" value="SEARCH" />
                <input type="reset" value="RESET" />

            </form>
            
        </div>
        
        
        <div style="clear: both; height:40px"></div>

        <!--Grid1-Epagelmatias-->
        <div class="col-6">
            <div style="padding:5px">
            <?php 

            if ($rs_1) { 

                echo "<h2 style=\"margin-left:5px\">TOTAL SALES EPAGELMATIAS</h2>";    
                $gridReport1 = new datagrid("gridReport1", $db1, 
                    "", 
                    array("USER", "MYLINK", "MYRESELLLINK", "MYRESENDLINK", "COUNTCANCEL", "CANCELPERCENT", "HOURS", "RATE"), 
                    array("USER","ΣΥΝΟΛΟ", "ΑΝΑΝ.", "ΕΠΑΝ/ΔΡΟΜ.", "ΕΠΙΣΤΡ.", "%ΕΠΙΣΤΡ", "ΩΡΕΣ", "ΠΩΛ/ΩΡΑ"),
                    $ltoken, 0);
                $gridReport1->set_rs($rs_1);
                $gridReport1->col_vlookup("USER","USER","USERS","fullname", $db1);               
                $gridReport1->set_colWidths(array("150","50","50", "50", "50", "50", "50", "50"));
                $gridReport1->set_colsFormat(array("", "", "", "", "", "CURRENCY", "CURRENCY", "CURRENCY"));
                $gridReport1->get_datagrid();
                $count1 = 0;$count1a = 0;$count1b = 0; $countHours = 0;
                for ($i=0;$i<count($rs_1);$i++) {
                    $count1 += $rs_1[$i]['MYCOUNT'];
                    $count1a += $rs_1[$i]['MYRESELL'];
                    $count1b += $rs_1[$i]['MYRESEND'];
                    $countHours += $rs_1[$i]['HOURS'];
                }
                echo "<br/><br/><h3>".$count1." sales / ".$count1a . " resells / ". $count1b . " resends</h3>"; 
                echo "<h3>" . func::nrToCurrency($countHours) . " hours / ". func::nrToCurrency($count1/$countHours) ." sales per hr</h3>";

            }

            ?>
            </div>

        </div>

        <!--Grid1-Panelinios-->
        <div class="col-6">
            <div style="padding:5px">
            <?php 

            if ($rs_1a) { 

                echo "<h2 style=\"margin-left:5px\">TOTAL SALES PANELINIOS</h2>";    
                $gridReport1a = new datagrid("gridReport1a", $dbPanel, 
                    "", 
                    array("USER", "MYLINK", "MYRESELLLINK", "MYRESENDLINK", "COUNTCANCEL", "CANCELPERCENT",  "HOURS", "RATE"), 
                    array("USER","ΣΥΝΟΛΟ","ΑΝΑΝ.", "ΕΠΑΝ/ΔΡΟΜ.", "ΕΠΙΣΤΡ.", "%ΕΠΙΣΤΡ",  "ΩΡΕΣ", "ΠΩΛ/ΩΡΑ"),
                    $ltoken, 0);
                $gridReport1a->set_rs($rs_1a);
                $gridReport1a->col_vlookup("USER","USER","USERS","fullname", $dbPanel);               
                $gridReport1a->set_colWidths(array("150","50","50", "50", "50", "50", "50", "50"));
                $gridReport1a->set_colsFormat(array("", "", "", "", "", "CURRENCY", "CURRENCY", "CURRENCY"));
                $gridReport1a->get_datagrid();
                $count1 = 0;$count1a = 0;$count1b = 0; $countHours = 0;
                for ($i=0;$i<count($rs_1a);$i++) {
                    $count1 += $rs_1a[$i]['MYCOUNT'];
                    $count1a += $rs_1a[$i]['MYRESELL'];
                    $count1b += $rs_1a[$i]['MYRESEND'];
                    $countHours += $rs_1a[$i]['HOURS'];
                }
                echo "<br/><br/><h3>".$count1." sales / ".$count1a . " resells / ". $count1b . " resends</h3>"; 
                echo "<h3>" . func::nrToCurrency($countHours) . " hours / ". func::nrToCurrency($count1/$countHours) ." sales per hr</h3>";

            }

            ?>
            </div>
        </div>

        <div style="clear: both; height: 50px"></div>
        
        
        <!--Grid2-Epag-->
        <div class="col-6">
            <div style="padding:5px">
            <?php 

            if ($rs_2) { 
                echo "<h2 style=\"margin-left:5px\">CLEAR SALES EPAGELMATIAS</h2>";
                $gridReport2 = new datagrid("gridReport2", $db1, 
                    "", 
                    array("USER","MYCOUNT","PRICE","MYLINK", 
                        "MYRESELL", "PRICERESELL", "MYRESELLLINK",
                        "MYRESEND", "PRICERESEND", "MYRESENDLINK"), 
                    array("USER","TOTAL","PRICE","VIEW", 
                        "ΑΝΑΝ/ΣΗ","PRICE","VIEW",
                        "ΕΠΑΝ.","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport2->set_rs($rs_2);
                $gridReport2->col_vlookup("USER","USER","USERS","fullname", $db1);               
                $gridReport2->set_colWidths(array("150","50","100","50", "50","100","50","50","100","50"));
                $gridReport2->set_colsFormat(array("","","CURRENCY","", "","CURRENCY","", "","CURRENCY",""));                
                $gridReport2->get_datagrid();
                $count2 = 0;
                $price2 = 0;
                for ($i=0;$i<count($rs_2);$i++) {
                    $count2 += $rs_2[$i]['MYCOUNT'];
                    $price2 += $rs_2[$i]['PRICE'];
                }
                $price2 = func::format($price2, "CURRENCY", "GR");
                echo "<br/><br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count2." / ".$price2." €</h3>";
            }
            
            ?>
            </div>
        </div>
            
        
        <!--Grid2-Panel-->
        <div class="col-6">
            <div style="padding:5px">
            <?php 

            if ($rs_2a) { 
                echo "<h2 style=\"margin-left:5px\">CLEAR SALES PANELINIOS</h2>";
                $gridReport2a = new datagrid("gridReport2a", $dbPanel, 
                    "", 
                    array("USER","MYCOUNT","PRICE","MYLINK", 
                        "MYRESELL", "PRICERESELL", "MYRESELLLINK",
                        "MYRESEND", "PRICERESEND", "MYRESENDLINK"), 
                    array("USER","TOTAL","PRICE","VIEW", 
                        "ΑΝΑΝ/ΣΗ","PRICE","VIEW",
                        "ΕΠΑΝ.","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport2a->set_rs($rs_2a);
                $gridReport2a->col_vlookup("USER","USER","USERS","fullname", $dbPanel);               
                $gridReport2a->set_colWidths(array("150","50","100","50", "50","100","50","50","100","50"));
                $gridReport2a->set_colsFormat(array("","","CURRENCY","", "","CURRENCY","", "","CURRENCY",""));                
                $gridReport2a->get_datagrid();
                $count2 = 0;
                $price2 = 0;
                for ($i=0;$i<count($rs_2a);$i++) {
                    $count2 += $rs_2a[$i]['MYCOUNT'];
                    $price2 += $rs_2a[$i]['PRICE'];
                }
                $price2 = func::format($price2, "CURRENCY", "GR");
                echo "<br/><br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count2." / ".$price2." €</h3>";
            }
            
            ?>
            </div>
        </div>
        
        
        
        <div style="clear: both; height: 50px"></div>
        
        
        <!--Grid3-Epag-->
        <div class="col-6">
            <div style="padding:5px">
            <?php
            if ($rs_3) { 
                //var_dump($rs3);
                
                echo "<br/><h2 >DETAILS EPAGELMATIAS</h2>";
                $gridReport3 = new datagrid("gridReport3", $db1, 
                    "", 
                    array("USER","MYSTATUS","PACKAGE","MYCOUNT","PRICE","MYLINK"), 
                    array("USER","STATUS","PACKAGE","COUNT","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport3->set_rs($rs_3);
                $gridReport3->col_vlookup("USER","USER","USERS","fullname", $db1);  
                $gridReport3->col_vlookup("PACKAGE","PACKAGE","PACKAGES","description", $db1); 
                $gridReport3->set_colWidths(array("150","150","150","50","100","50"));
                $gridReport3->set_colsFormat(array("","","","","CURRENCY"));
                $gridReport3->removeDuplicates("USER");                

                $gridReport3->get_datagrid();
                $count3 = 0;
                $price3 = 0;
                for ($i=0;$i<count($rs_3);$i++) {
                    $count3 += $rs_3[$i]['MYCOUNT'];
                    $price3 += $rs_3[$i]['PRICE'];
                }
                $price3 = func::format($price3, "CURRENCY", "GR");
                echo "<br/><br/><h3 >ΣΥΝΟΛΟ: ".$count3." / ".$price3." €</h3>";
                $sumPendingEpag = func::format($sumPendingEpag, "CURRENCY", "GR");
                $sumReturnEpag = func::format($sumReturnEpag, "CURRENCY", "GR");
                echo "<h3>Εκκρεμότητες $countPendingEpag ($sumPendingEpag) / "
                        . "Επιστροφές $countReturnEpag ($sumReturnEpag) </h3>";
            }
            
            ?>
            </div>
        </div>
        
        <!--Grid3-Panel-->
        <div class="col-6">
            <div style="padding:5px">
            <?php
            if ($rs_3a) { 
                echo "<br/><h2 >DETAILS PANELINIOS</h2>";
                $gridReport3a = new datagrid("gridReport3a", $dbPanel, 
                    "", 
                    array("USER","MYSTATUS","PACKAGE","MYCOUNT","PRICE","MYLINK"), 
                    array("USER","STATUS","PACKAGE","COUNT","PRICE","VIEW"),
                    $ltoken, 0);
                $gridReport3a->set_rs($rs_3a);
                $gridReport3a->col_vlookup("USER","USER","USERS","fullname", $dbPanel);  
                $gridReport3a->col_vlookup("PACKAGE","PACKAGE","PACKAGES","description", $dbPanel); 
                $gridReport3a->set_colWidths(array("150","150","150","50","100","50"));
                $gridReport3a->set_colsFormat(array("","","","","CURRENCY"));
                $gridReport3a->removeDuplicates("USER");                

                $gridReport3a->get_datagrid();
                $count3 = 0;
                $price3 = 0;
                for ($i=0;$i<count($rs_3a);$i++) {
                    $count3 += $rs_3a[$i]['MYCOUNT'];
                    $price3 += $rs_3a[$i]['PRICE'];
                }
                $price3 = func::format($price3, "CURRENCY", "GR");
                echo "<br/><br/><h3 >ΣΥΝΟΛΟ: ".$count3." / ".$price3." €</h3>";
                
                $sumPendingPanel = func::format($sumPendingPanel, "CURRENCY", "GR");
                $sumReturnPanel = func::format($sumReturnPanel, "CURRENCY", "GR");
                echo "<h3>Εκκρεμότητες $countPendingPanel ($sumPendingPanel) / "
                        . "Επιστροφές $countReturnPanel ($sumReturnPanel) </h3>";
            }
            
            ?>
            </div>
        </div>
        
        <div style="clear: both; height: 50px"></div>
        
        
        
    </div>

</div>
        
        
<div style="clear: both"></div>    
    
<?php include "blocks/footer.php"; ?>


<script>
$(function() {
    $("#gridReport1").tableExport({formats: ["xlsx","xls", "csv"]});
    $("#gridReport1a").tableExport({formats: ["xlsx","xls", "csv"]});
    $("#gridReport2").tableExport({formats: ["xlsx","xls", "csv"]});
    $("#gridReport2a").tableExport({formats: ["xlsx","xls", "csv"]});
    $("#gridReport3").tableExport({formats: ["xlsx","xls", "csv"]});
    $("#gridReport3a").tableExport({formats: ["xlsx","xls", "csv"]});
});

</script>
    
</body>
</html>