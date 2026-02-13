<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$criteria = "";
$msg = "";
$sdateFrom = date("d/m/Y", strtotime("-1 month")); 
$sdateTo = date("d/m/Y");

$dates = "";
$salesDataAll = "";
$salesDataPayed = "";
$salesDataPending = "";
$salesDataCancel = "";

$countAll = 0; $sumAll = 0;
$countPayed = 0; $sumPayed = 0;
$countPending = 0; $sumPending = 0;
$countCancel = 0; $sumCancel = 0;


$token = isset($_POST['txtToken'])? $_POST['txtToken']: "";

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
        
        $sql = "SELECT `tdatetime`, 
            COUNT(id) AS COUNT_ALL, SUM(amount) AS SUM_ALL, 
            SUM(IF(status = 2, 1, 0)) AS COUNT_PAYED,
            SUM(IF(status = 2, amount, 0)) AS SUM_PAYED,
            SUM(IF(status = 1, 1, 0)) AS COUNT_PENDING,
            SUM(IF(status = 1, amount, 0)) AS SUM_PENDING,
            SUM(IF(status = 3, 1, 0)) AS COUNT_CANCEL,
            SUM(IF(status = 3, amount, 0)) AS SUM_CANCEL
            FROM `TRANSACTIONS` WHERE `transactiontype`=1 
            AND tdatetime> '$dateFrom' AND `tdatetime`<='$dateTo'
            GROUP BY `tdatetime`
            ORDER BY `tdatetime`";
        //echo $sql;
        
        $rs = $db1->getRS($sql);
        //var_dump($rs);
        if ($rs) {
            for ($i = 0; $i < count($rs); $i++) {
                $dates .= "'" .func::str14toDate($rs[$i]['tdatetime'], "/")  . "'";
                $salesDataAll .= $rs[$i]['SUM_ALL'];
                $salesDataPayed .= $rs[$i]['SUM_PAYED'];
                $salesDataPending .= $rs[$i]['SUM_PENDING'];
                $salesDataCancel .= $rs[$i]['SUM_CANCEL'];
                
                if ($i<count($rs)-1) {
                    $dates .= ",";
                    $salesDataAll .= ",";
                    $salesDataPayed .= ",";
                    $salesDataPending .= ",";
                    $salesDataCancel .= ",";
                }
                
                $countAll += $rs[$i]['COUNT_ALL'];
                $sumAll += $rs[$i]['SUM_ALL'];
                $countPayed += $rs[$i]['COUNT_PAYED'];
                $sumPayed += $rs[$i]['SUM_PAYED'];
                $countPending += $rs[$i]['COUNT_PENDING'];
                $sumPending += $rs[$i]['SUM_PENDING'];
                $countCancel += $rs[$i]['COUNT_CANCEL'];
                $sumCancel += $rs[$i]['SUM_CANCEL'];
                
                
                $myDate1 = $rs[$i]['tdatetime'];
                $myDate2 = $rs[$i]['tdatetime'];
                
                $rs[$i]['COUNT_ALL'] = "<a class=\"fancybox\" href=\"getCompanies.php?dateFrom=$myDate1&dateTo=$myDate2\">" . $rs[$i]['COUNT_ALL'] . "</a>";
                $rs[$i]['COUNT_PAYED'] = "<a class=\"fancybox\" href=\"getCompanies.php?dateFrom=$myDate1&dateTo=$myDate2&status=2\">" . $rs[$i]['COUNT_PAYED'] . "</a>";
                $rs[$i]['COUNT_PENDING'] = "<a class=\"fancybox\" href=\"getCompanies.php?dateFrom=$myDate1&dateTo=$myDate2&status=1\">" . $rs[$i]['COUNT_PENDING'] . "</a>";
                $rs[$i]['COUNT_CANCEL'] = "<a class=\"fancybox\" href=\"getCompanies.php?dateFrom=$myDate1&dateTo=$myDate2&status=3\">" . $rs[$i]['COUNT_CANCEL'] . "</a>";
                
                
                
            }


            $sumAll = func::nrToCurrency($sumAll);
            $sumPayed = func::nrToCurrency($sumPayed);
            $sumPending = func::nrToCurrency($sumPending);
            $sumCancel = func::nrToCurrency($sumCancel);

        }
        
    } 
    else {
        $msg = "Please select date";
        $sql="";
    }
    
}


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include "_head.php"; ?>
    
    
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<?php if ($rs) { ?>
<script>


$(function() {
    
    Highcharts.chart('chart1', {
        chart: {
          type: 'column'
        },
        title: {
          text: 'Ημερήσιες πωλήσεις'
        },
        subtitle: {
          text: '<?php echo $sdateFrom . " - " . $sdateTo ?>'
        },
        xAxis: {
          categories: [
            <?php echo $dates ?>
          ],
          crosshair: true
        },
        yAxis: {
          min: 0,
          title: {
            text: 'Sales (euros)'
          }
        },
        tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0
          }
        },
        series: [{
            name: 'All sales',
            data: [<?php echo $salesDataAll ?>]
        },
        {
            name: 'Clear Sales',
            data: [<?php echo $salesDataPayed ?>]
        },
        {
            name: 'Pending',
            data: [<?php echo $salesDataPending ?>]
        },
        {
            name: 'Cancel',
            data: [<?php echo $salesDataCancel ?>]
        }
        ]
});
    
    
});


</script>
<?php } ?>

<style>
    
    #grid1 tr td:nth-child(2) { text-align: center;}
    #grid1 tr td:nth-child(4) { text-align: center;}
    #grid1 tr td:nth-child(6) { text-align: center;}
    #grid1 tr td:nth-child(8) { text-align: center;}
    
    
</style>
    

</head>

<body>

<?php include "blocks/header.php"; ?>
<?php include "blocks/menu.php"; ?>

    <div class="main">
        
        <h2 style="margin-left:0px">Ημερήσιες πωλήσεις</h2>
        
        <form action="reportSalesPerDay.php?search=1" method="POST" style="margin:0px; max-width: 500px">
            <?php
            
            $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), $sdateFrom);
            $txtDateFrom->set_format("DATE");
            $txtDateFrom->set_locale($locale);                        
            $txtDateFrom->get_Textbox();

            $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), $sdateTo);
            $txtDateTo->set_format("DATE");
            $txtDateTo->set_locale($locale);                        
            $txtDateTo->get_Textbox(); 
            
            $txtToken = new textbox("txtToken", "CODE", $token);
            $txtToken->set_type("password");
            $txtToken->get_Textbox();
            
            ?>
            <div style="clear: both"></div>
            
            <input name="BtnSearch" type="submit" value="SEARCH" />
            <input type="reset" value="RESET" />
                
                
        </form>
        
        
        <h2></h2>
        
        <div class="col-6 col-sm-12">
        <?php
        if ($rs) {
            $grid1 = new datagrid("grid1", $db1, "", 
                    array("tdatetime", "COUNT_ALL", "SUM_ALL", "COUNT_PAYED", "SUM_PAYED", 
                        "COUNT_PENDING", "SUM_PENDING", "COUNT_CANCEL", "SUM_CANCEL"), 
                    array("ΗΜΕΡ.", "Όλες (τεμ)", "(€)", "Καθαρές (τεμ)", "(€)",
                        "Εκκρεμ. (τεμ)", "(€)", "Ακυρ. (τεμ)", "(€)"), 
                    $ltoken);
            $grid1->set_rs($rs);
            $grid1->set_colsFormat(array("DATE", "", "CURRENCY",  "", "CURRENCY" , "", "CURRENCY", "", "CURRENCY"));
            $grid1->get_datagrid();
            
            echo "<div class=\"spacer-30\"></div>";
            echo "<h2>ΣΥΝΟΛΑ</h2>";
            echo "<h3>Όλες οι πωλήσεις: " . $countAll . " τεμ / " . $sumAll . " €</h3>";
            echo "<h3>Καθαρές πωλήσεις: " . $countPayed . " τεμ / " . $sumPayed . " €</h3>";
            echo "<h3>Σε εκκρεμότητα: " . $countPending . " τεμ / " . $sumPending . " €</h3>";
            echo "<h3>Ακυρωμένες: " . $countCancel . " τεμ / " . $sumCancel . " €</h3>";
            
            
            
        }
        
        
        ?>
        </div>
        
        <div class="col-6 col-sm-12" style="padding:0px 20px; box-sizing: border-box">
            <?php if ($rs) { ?>
            <div id="chart1" style="min-width: 310px; height: 600px; margin: 0 auto"></div>
            <?php } ?>
        </div>
        <div class="spacer-30"></div>
        
        
        
    </div>
    
    
    <?php include "blocks/footer.php"; ?>
    
    
    
<script>
$(function() {
    $("#grid1").tableExport({formats: ["xlsx","xls", "csv"]});
    
});

</script>
    
    
</body>
    
</html>