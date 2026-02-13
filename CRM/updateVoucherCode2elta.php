<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Athens');

header("Content-Type: text/html; charset=utf-8");

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

require_once 'phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel/IOFactory.php';

//$thisURL = "readExcelTest.php";
$data = FALSE;
$msg = "";


 //Check valid spreadsheet has been uploaded
if(isset($_FILES['spreadsheet'])){
if($_FILES['spreadsheet']['tmp_name']){
    
    //var_dump($_FILES['spreadsheet']);
    
if(!$_FILES['spreadsheet']['error'])
{
    $inputFile = $_FILES['spreadsheet']['tmp_name'];
    $inputFileName = $_FILES['spreadsheet']['name'];
    $extension = strtoupper(pathinfo($inputFileName, PATHINFO_EXTENSION));
    //echo "EXTENSION:$extension<br/>";
    if( $extension == 'XLSX' || $extension == 'XLS' || $extension == 'ODS'){
        
        //Read spreadsheeet workbook
        try {
             $inputFileType = PHPExcel_IOFactory::identify($inputFile);
             $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                 $objPHPExcel = $objReader->load($inputFile);
        } catch(Exception $e) {
                die($e->getMessage());
        }
        
        
        //Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        
        //Loop through each row of the worksheet in turn
        
        $data = array();
        
        for ($row = 1; $row <= $highestRow; $row++){ 
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            
            if (is_numeric($rowData[0][1])) {                
                array_push($data, $rowData[0]);
            }
         
        }
        
        $_SESSION['data-excel'] = $data;
        
        
        
    }
    else {
        echo "Wrong file extension " . $extension . "<br/>";
    }
    
    
}
else {
    echo "_FILES['spreadsheet']['error']" . "<br/>";
}

}
else {
    //echo "_FILES['spreadsheet']['tmp_name']";
}

}
else {
    //echo "NOT_set(_FILES['spreadsheet'])" . "<br/>";
}


$companyIds = "";
if (isset($_POST['confirm'])) {
    $dataExcel = $_SESSION['data-excel'];
    $_SESSION['data-excel'] = "";
    
    //print("<pre>".print_r($dataExcel,true)."</pre>");
    
    for ($i = 0; $i < count($dataExcel); $i++) {
        $sql = "SELECT * FROM VOUCHERS WHERE vcode LIKE ? AND courier=2"; //elta
        $rsV = $db1->getRS($sql, array($dataExcel[$i][1]));
        if ($rsV) {
            //update voucher
            $voucher = new VOUCHERS($db1, $rsV[0]['id'], $rsV);
            $voucher->set_vcode4(trim($dataExcel[$i][0]));
            
            //set followup date --START
            $dayOfWeek = date("N");
            $daysPlus = 2; //+2 hmeres
            if ($dayOfWeek==4 || $dayOfWeek==5) { //For Thursday, Friday + 4 days=>Monday, Tuesday 
                $daysPlus = 4;
            }
            $date2 = strtotime(date("Y-m-d") . " + $daysPlus days");
            $date2str = date("d/m/Y", $date2);
            $date2str14 = func::dateTo14str($date2str);
            
            $voucher->set_followup_date($date2str14);
            $voucher->set_followup_time(1);
            //set followup date ---END
            
            $voucher->Savedata();
            
        }
        
    }
    
    $msg = "Οι κωδικοί των voucher ενημερώθηκαν";
    
}

?>
<html>
    <head>
        <title>Panellinios - CRM</title>
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />
        <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>        
        <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
        <script type="text/javascript" src="js/code.js"></script>
        
        <style>
            
            body, table {
                font-family: sans-serif;
                font-size: 14px;
            }
            
            .table td {
                padding:5px;
                border:none;
                background-color: rgb(240,240,240);
                border:1px solid white;
            }   
            
            
            .alert {
                background-color: rgb(240,200,200) !important;
            }
            
            .payed {
                background-color: rgb(200,240,200) !important;
            }
            
            .button {
                background-color: rgb(200,240,200) ;
                cursor: pointer;
                padding: 10px 20px;
                margin-bottom: 80px;
            }
            
            form {
                max-width: 500px;
            }
            
        </style>
        
    </head>
    <body>
        
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?>

        <div class="main">
          
          <h1>Ενημέρωση κωδικών Voucher / ΕΛΤΑ</h1>
          
          <?php
          if ($msg!="") {
            echo "<p>$msg</p>";
          }
          
          ?>
          
        
        <?php
        
        
        if ($data) {
            echo "<table class=\"table\" cellpadding=\"1\">";
            $sumPanel = 0;
            $sumCourier = 0;
            for ($i = 0; $i < count($data); $i++) {
                
                
                $sql = "SELECT * FROM VOUCHERS WHERE vcode LIKE ? AND courier=2"; //elta
                $rsV = $db1->getRS($sql, array(trim($data[$i][1])));
                $amountPanel = 0;
                $companyId = 0;
                if ($rsV) {
                    $companyId = $rsV[0]['customer'];
                    //$amountPanel = $rsV[0]['courier_status']==1? round($rsV[0]['amount'],2): 0;
                    //$amountPanel = round($rsV[0]['amount'],2);
                    //$sumPanel += $amountPanel; 
                    $tdClass ="normal";
                    //if ($rsV[0]['courier_status']==2) {
                    //    $tdClass = "payed";
                    //}
                }
                else {
                    //$tdClass = "payed";
                    
                }
                
                echo "<tr>";
                echo "<td class=\"$tdClass\">" . $data[$i][1] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][0] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][3] . "</td>";                
                echo "<td class=\"$tdClass\">" . $data[$i][4] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][5] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][6] . "</td>";
                echo "<td class=\"$tdClass\">" . $companyId . "</td>";
                echo "</tr>";
                
                //$sumCourier += $data[$i][4];

            }
            echo "</table>";
            //echo "<h3>Total Courier $sumCourier </h3>";
            //echo "<h3>Total Panelinios $sumPanel </h3>";
            
            
            echo "<form method=\"post\" style=\"border:none\">";
            echo '<input name="confirm" type="submit" value="IMPORT" />';
            echo "</form>";
            echo "<div style=\"clear:both; height:40px\"></div>";
            
        }
        
        
        if ($companyIds!="") {
            echo "COMPANIES: ".$companyIds;
        }
        
        
        ?>
            
        
        
        <form method="post" enctype="multipart/form-data">
        Upload File: <input type="file" name="spreadsheet"/>
        <br/>
        <input type="submit" name="submit" value="Submit" />
        </form>
            
        
            
            
        </div>
        
        <?php include "blocks/footer.php"; ?> 
        
        
        
    </body>
</html>
