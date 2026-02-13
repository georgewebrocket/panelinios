<?php

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$criteria = "";
$msg = "";
$sdateFrom="";$sdateTo="";
$productCategories = "";

$view_icon = "<div style=\"text-align:center\"><img src=\"img/eye.svg\" style=\"width:20px\" /></div>";

if (isset($_GET['search'])) {
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
        
        $sqlProdCat = "";
        $productCategories = selectList::getVal("l_productcategories", $_POST);
        if ($productCategories!="") {            
            $productCategoriesIds = str_replace(array("[", "]"), "", $productCategories);
            $sqlProdCat = " AND TRANSACTIONS.package IN (SELECT id FROM PACKAGES WHERE product_category IN ($productCategoriesIds)) ";            
            
        }
        
        
        //total sales
        $sql_1 = "SELECT `TRANSACTIONS`.`seller` AS USER, COUNT(`TRANSACTIONS`.id) AS MYCOUNT, 
                '' AS MYLINK, SUM(resell) AS MYRESELL, '' AS MYRESELLLINK, 
                SUM(resend) AS MYRESEND, '' AS MYRESENDLINK                 
                FROM `TRANSACTIONS` INNER JOIN `COMPANIES` ON  `TRANSACTIONS`.`company`= `COMPANIES`.`id`
                WHERE TRANSACTIONS.`transactiontype`=1
                AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'
                $sqlProdCat    
                GROUP BY `TRANSACTIONS`.`seller`
                ORDER BY `TRANSACTIONS`.`seller`";
        //echo "<!--".$sql_1."-->";
        $rs_1 = $db1->getRS($sql_1);
        for ($i=0;$i<count($rs_1);$i++) {
            $cuser = $rs_1[$i]['USER'];            
            $rs_1[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&productCats=$productCategoriesIds\">$view_icon</a>";
            $rs_1[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resell=1&productCats=$productCategoriesIds\">$view_icon</a>";
            $rs_1[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&resend=1&productCats=$productCategoriesIds\">$view_icon</a>";
            
        }
        
        $factorFPA = (100 + config::$fpa) / 100; ///***
        
        //clear sales
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
        //echo "<!--".$sql_2."-->";
        $rs_2 = $db1->getRS($sql_2);
        for ($i=0;$i<count($rs_2);$i++) {
            $cuser = $rs_2[$i]['USER'];
            //$cstatus = "9,10";
            $cstatus = "2";
            $rs_2[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&productCats=$productCategoriesIds\">$view_icon</a>";
            $rs_2[$i]['MYRESELLLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resell=1&productCats=$productCategoriesIds\">$view_icon</a>";
            $rs_2[$i]['MYRESENDLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&resend=1&productCats=$productCategoriesIds\">$view_icon</a>";
        }
        
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
                //echo "<!--".$sql."-->";
        $rs = $db1->getRS($sql);
        for ($i=0;$i<count($rs);$i++) {
            $cuser = $rs[$i]['USER'];
            $cstatus = $rs[$i]['STATUSID'];
            $cpackage = $rs[$i]['PACKAGE'];
            $rs[$i]['MYLINK'] = "<a class=\"fancybox\" href=\"getCompanies.php?user=$cuser&dateFrom=$dateFrom&dateTo=$dateTo&status=$cstatus&package=$cpackage\">$view_icon</a>";
            if ($rs[$i]['returned'] == 1) {
                $rs[$i]['MYSTATUS'] = "ΕΠΙΣΤΡΟΦΗ";
            }
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
<?php include "_head.php"; ?>



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

    #gridReport1 td:nth-child(2),
    #gridReport1 td:nth-child(4),
    #gridReport1 td:nth-child(6) {
        text-align:center;
    }

    #gridReport2 td:nth-child(2),
    #gridReport2 td:nth-child(5),
    #gridReport2 td:nth-child(8) {
        text-align:center;
    }
    #gridReport2 td:nth-child(3),
    #gridReport2 td:nth-child(6),
    #gridReport2 td:nth-child(9) {
        text-align:right;
    }

    #gridReport td:nth-child(4) {
        text-align:center;
    }

    #gridReport td:nth-child(5) {
        text-align:right;
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
            
            
            <?php if ($sql!="") { ?>
            <!--<h2 class="search-results"><?php //echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>-->

                <?php
                echo "<h2 style=\"margin-left:1em\">ΣΥΝΟΛΟ ΠΩΛΗΣΕΩΝ</h2>";    
                $gridReport1 = new datagrid("gridReport1", $db1, 
                    "", 
                    array("USER","MYCOUNT","MYLINK", "MYRESELL", "MYRESELLLINK", 
                        "MYRESEND", "MYRESENDLINK"), 
                    array("USER","ΣΥΝΟΛΟ","VIEW", "ΑΝΑΝ.", "VIEW", "ΕΠΑΝ/ΔΡΟΜ.", "VIEW"),
                    $ltoken, 0);
                $gridReport1->set_rs($rs_1);
                $gridReport1->col_vlookup("USER","USER","USERS","fullname", $db1);               
                $gridReport1->set_colWidths(array("150","50","50", "50","50", "50", "50"));
                
                $gridReport1->col_sum('MYCOUNT', '');
                $gridReport1->col_sum('MYRESELL', '');
                $gridReport1->col_sum('MYRESEND', '');


                $gridReport1->get_datagrid();
                $count1 = 0;$count1a = 0;$count1b = 0;
                for ($i=0;$i<count($rs_1);$i++) {
                    $count1 += $rs_1[$i]['MYCOUNT'];
                    $count1a += $rs_1[$i]['MYRESELL'];
                    $count1b += $rs_1[$i]['MYRESEND'];
                }

                // echo "<br/><br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count1." / ".$count1a. 
                //         " / ". $count1b . "</h3>";
                
                echo "<br/><h2 style=\"margin-left:1em\">ΚΑΘΑΡΕΣ ΠΩΛΗΣΕΙΣ</h2>";
                //echo $sql_2;
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
                
                $gridReport2->col_sum('MYCOUNT', '');
                $gridReport2->col_sum('PRICE', 'CURRENCY');
                $gridReport2->col_sum('MYRESELL', '');
                $gridReport2->col_sum('PRICERESELL', 'CURRENCY');
                $gridReport2->col_sum('MYRESEND', '');
                $gridReport2->col_sum('PRICERESEND', 'CURRENCY');

                $gridReport2->get_datagrid();
                $count2 = 0;
                $price2 = 0;
                for ($i=0;$i<count($rs_2);$i++) {
                    $count2 += $rs_2[$i]['MYCOUNT'];
                    $price2 += $rs_2[$i]['PRICE'];
                }
                $price2 = func::format($price2, "CURRENCY", "GR");
                // echo "<br/><br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count2." / ".$price2." €</h3>";
                
                echo "<br/><h2 style=\"margin-left:1em\">ΑΝΑΛΥΤΙΚΑ</h2>";
                $gridReport = new datagrid("gridReport", $db1, 
                    "", 
                    array("USER","MYSTATUS","PACKAGE","MYCOUNT","PRICE","MYLINK"), 
                    array("Χρήστης","Status","Πακέτο","Πλήθος","Ποσό","VIEW"),
                    $ltoken, 0);
                $gridReport->set_rs($rs);
                $gridReport->col_vlookup("USER","USER","USERS","fullname", $db1);  
                $gridReport->col_vlookup("PACKAGE","PACKAGE","PACKAGES","description", $db1); 
                $gridReport->set_colWidths(array("150","150","150","50","100","50"));
                $gridReport->set_colsFormat(array("","","","","CURRENCY"));
                $gridReport->removeDuplicates("USER");

                $gridReport->col_sum('MYCOUNT', '');
                $gridReport->col_sum('PRICE', 'CURRENCY');
                

                $gridReport->get_datagrid();
                $count3 = 0;
                $price3 = 0;
                for ($i=0;$i<count($rs);$i++) {
                    $count3 += $rs[$i]['MYCOUNT'];
                    $price3 += $rs[$i]['PRICE'];
                }
                $price3 = func::format($price3, "CURRENCY", "GR");
                // echo "<br/><br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".$count3." / ".$price3." €</h3>";
                    
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
    
    
    <script>
    $(function() {
        $("#gridReport1").tableExport({formats: ["xlsx","xls", "csv"]});
        $("#gridReport2").tableExport({formats: ["xlsx","xls", "csv"]});
        $("#gridReport").tableExport({formats: ["xlsx","xls", "csv"]});
    });

</script>
    
    
    
    
</body>
</html>