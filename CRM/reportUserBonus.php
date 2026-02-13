<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$criteria = "";
$msg = "";

$dateFromDate = "";
$dateToDate = "";
$seller = 0;
$rs = FALSE;

if (isset($_GET['search']) && $_GET['search']==1) {
    
    if ($_POST['txtDateFrom']!="") {
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); 
        $dateFromDate = $_POST['txtDateFrom'];
        
        if ($_POST['txtDateTo']!="") {
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale);             
        }
        else {
            $dateTo = $dateFrom;
        }        
        $dateTo = substr($dateTo, 0, 8)."235959";
        $dateToDate = $_POST['txtDateTo'];
    }
    else {
        $msg = "Please select dates";
        $sql="";
    }
    
    if ($_POST['cUser']<>0) {
        $seller = $_POST['cUser'];         
    }
    
    /*$sql = "SELECT TRANSACTIONS.tdatetime, COUNT(TRANSACTIONS.company), SUM(TRANSACTIONS.amount) AS price, USERS.fullname AS AGENT "
            . "FROM TRANSACTIONS INNER JOIN USERS ON TRANSACTIONS.seller=USERS.id "
            . "WHERE transactiontype=1 AND tdatetime>='".$dateFrom."' AND tdatetime<='".$dateTo."' "
            . "AND (TRANSACTIONS.`status` =2) "
            . "GROUP BY TRANSACTIONS.tdatetime "; */
    
    $sql = "SELECT '' AS id, TRANSACTIONS.tdatetime, COUNT(TRANSACTIONS.company) AS MYCOUNT, "
            . "SUM(TRANSACTIONS.amount) AS NETTZIROS, "
            . "'0' AS DAILYBONUS "
            . "FROM TRANSACTIONS "
            . "WHERE transactiontype=1 AND tdatetime>='".$dateFrom."' AND tdatetime<='".$dateTo."' "
            . "AND (TRANSACTIONS.`status` =2) AND TRANSACTIONS.seller = $seller "
            . "GROUP BY TRANSACTIONS.tdatetime "; 
    
    //echo $sql;
    $rs = $db1->getRS($sql);
    
    $totalTziros = 0;
    $totalDailyBonus = 0;
    
    for ($i = 0; $i < count($rs); $i++) {
        if ($rs[$i]['MYCOUNT']>=7 && $rs[$i]['MYCOUNT']<=11) {
            $rs[$i]['DAILYBONUS'] = 20;
        }
        elseif ($rs[$i]['MYCOUNT']>=12) {
            $rs[$i]['DAILYBONUS'] = 40;
        }
        
        $totalDailyBonus += $rs[$i]['DAILYBONUS'];
        $totalTziros += $rs[$i]['NETTZIROS'];
        
    }
    $totalDailyBonus = func::nrToCurrency($totalDailyBonus);
    
    if ($totalTziros<4000) {
        $monthlyBonusPerc = 6;
    }
    elseif ($totalTziros>=4000 && $totalTziros<5000) {
        $monthlyBonusPerc = 7;
    }
    elseif ($totalTziros>=5000) {
        $monthlyBonusPerc = 8;
    }    
    $monthlyBonus = $monthlyBonusPerc * $totalTziros / 100;
    
    $totalTziros = func::nrToCurrency($totalTziros);
    $monthlyBonus = func::nrToCurrency($monthlyBonus);
    
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include "_head.php"; ?>

<style>
    
    .form-container {
        max-width: 500px;
        min-height: 0px;
    }
    
    #grid {
        max-width: 800px;
        margin-left: 1em;
    }
    
    #grid th:hover {
        cursor:pointer;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    form {
/*        margin: 0px;
        margin-bottom: 1em;*/
    }
    
    

    
</style>


</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-3"><h2 style="margin-left:1em">User bonus </h2></div>
        <div style="clear: both"></div> 
        
        <div class="col-12">
            
            <div class="form-container">

                <form action="reportUserBonus.php?search=1" method="POST">
                    <div class="col-12">
                        <?php
                        $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), $dateFromDate);
                        $txtDateFrom->set_format("DATE");
                        $txtDateFrom->set_locale($locale);                        
                        $txtDateFrom->get_Textbox();
                        
                        $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), $dateToDate);
                        $txtDateTo->set_format("DATE");
                        $txtDateTo->set_locale($locale);                        
                        $txtDateTo->get_Textbox();
                        
                        $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS WHERE is_agent=1 AND active=1", 
                                "id","fullname", $seller, "Agent");
                        $cUser->get_comboBox();
                        
                        
                        
                        
                        ?> 
                        
                    </div>
                    <div style="clear: both"></div>
                    

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

                </form>
            </div>
            
            
            
            <?php
            if ($rs) {
                
                $grid = new datagrid("grid", $db1, "", 
                        array("tdatetime", "MYCOUNT", "NETTZIROS", "DAILYBONUS"), 
                        array("Ημερ/νία", "Αρ. Καταχ/σεων", "Καθ. τζίρος", "Ημερ. bonus"), 
                        $ltoken);
                $grid->set_rs($rs);
                $grid->set_colsFormat(array("DATE", "", "CURRENCY", "CURRENCY"));
                
                $grid->get_datagrid();
                
                echo "<div style=\"padding:1em\">";
                echo "<h2>Συνολο ημερ. bonus $totalDailyBonus &euro;</h2>";
                echo "<h2>Συνολο τζίρου $totalTziros &euro;</h2>";
                echo "<h2>Μηνιαίο bonus $monthlyBonusPerc % = $monthlyBonus &euro;</h2>";
                echo "</div>";
            }
            ?>
            
            
            
            
            
        </div>
        
        <div style="clear: both"></div>   
        
        
    </div>

    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>
    
    <script>
        $(function() {
            $("#grid").tableExport({formats: ["xlsx","xls", "csv"]});
            
        });

    </script>
    
</body>
</html>    