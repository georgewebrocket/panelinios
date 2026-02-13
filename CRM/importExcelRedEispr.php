<?php

// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
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
    
    //var_dump($dataExcel);
    //print("<pre>".print_r($dataExcel,true)."</pre>");
    
    for ($i = 0; $i < count($dataExcel); $i++) {
        $sql = "SELECT * FROM VOUCHERS WHERE id = ?";
        $rsV = $db1->getRS($sql, array($dataExcel[$i][5]));
        if ($rsV) {
            //update voucher
            $voucher = new VOUCHERS($db1, $rsV[0]['id'], $rsV);
            
            if ($voucher->get_courier_status() == 1 
                    || $voucher->get_courier_status() == 5
                    || $voucher->get_courier_status() == 6) {
                
                //echo "VOUCHERID " . $voucher->get_id(). " : ";
                                
                $voucher->set_courier_status(2);
                $voucher->Savedata();            

                $companyId = $voucher->get_customer();

                //update transactions
                $sql = "SELECT * FROM TRANSACTIONS WHERE company=? AND status=1 AND transactiontype=1 ";
                $rsTrans = $db1->getRS($sql, array($companyId)); 

                $pendingAmount = 0;
                if ($rsTrans) {
                    for ($k = 0; $k < count($rsTrans); $k++) {
                        $pendingTransaction = new TRANSACTIONS($db1, $rsTrans[$k]['id'], $rsTrans);
                        $pendingTransaction->set_status(2);
                        $payedamount = $pendingTransaction->get_amount() +      $pendingTransaction->get_vat();
                        $pendingTransaction->set_payedamount($payedamount);
                        $pendingTransaction->Savedata();
                        $amount = $pendingTransaction->get_amount() + $pendingTransaction->get_vat();
                        $pendingAmount += $amount;

                    }


                    //create eispraski
                    $transaction = new TRANSACTIONS($db1, 0);
                    $transaction->set_tdatetime(date('Ymd')."000000");
                    $transaction->set_transactiontype(2);
                    $transaction->set_status(2);
                    $transaction->set_seller(0);
                    $transaction->set_company($companyId);                    
                    $transaction->set_package(0);
                    $transaction->set_price(0);
                    $transaction->set_discount(0);
                    $transaction->set_amount($pendingAmount);
                    $transaction->set_vat(0);
                    $transaction->set_vatpercentage(0);
                    $transaction->set_payedamount(0);
                    $transaction->set_comment("");
                    $transaction->set_newsales(0);
                    $transaction->set_resell(0);
                    $transaction->set_resend(0);
                    $transaction->set_returned(0);
                    $transaction->Savedata();

                }

                //update status epikoinonias ektypothike=>plirose
                $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND status=6 ";
                $rsStatus = $db1->getRS($sql,array($companyId));
                if ($rsStatus) {
                    for ($m = 0; $m < count($rsStatus); $m++) {
                        $companyStatus = new COMPANIES_STATUS($db1, 
                                $rsStatus[$m]['id'], $rsStatus);
                        $companyStatus->set_status(9);
                        $companyStatus->set_csdatetime(date("YmdHis"));
                        $companyStatus->set_userid($_SESSION['user_id']);
                        $companyStatus->Savedata();
                        
                        //ACTION
                        $action = new ACTIONS($db1, 0);
                        $action->set_company($companyId);
                        $action->set_user($_SESSION['user_id']);
                        $action->set_product_categories("[" . 
                                $companyStatus->get_productcategory() . "]");
                        $action->set_status1(6);
                        $action->set_status2(9);
                        $action->Savedata();
                        
                    
                    }
                }
                
                
                //echo "Updating company No ".$companyId . "<br/>";
                $companyIds .= "$companyId, ";
                $company = new COMPANIES($db1, $companyId);
                $id = $company->get_id();
                include "updateCompanyData.php";
                echo " / ";
                
            }
            
            /*$companyId = $voucher->get_customer();
            echo "Updating company No ".$companyId . "<br/>";
            $company = new COMPANIES($db1, $companyId);
            include "updateCompanyData.php"; //xxxx
            */
            
        }
        
    }
    
}

?>
<html>
    <head>
        <title>PANELINIOS - CRM</title>
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
        
        <?php
        
        
        if ($data) {
            echo "<table class=\"table\" cellpadding=\"1\">";
            $sumPanel = 0;
            $sumCourier = 0;
            for ($i = 0; $i < count($data); $i++) {                
                $sql = "SELECT * FROM VOUCHERS WHERE id = ?";
                $rsV = $db1->getRS($sql, array($data[$i][5]));
                $amountPanel = 0;
                $companyId = 0;
                if ($rsV) {
                    $companyId = $rsV[0]['customer'];
                    $amountPanel = round($rsV[0]['amount'],2);
                    $sumPanel += $amountPanel; 
                    $tdClass = $data[$i][4]==$amountPanel? "normal": "alert";
                    if ($rsV[0]['courier_status']==2) {
                        $tdClass = "payed";
                    }
                }
                else {
                    $tdClass = "alert";
                    
                }
                
                echo "<tr>";
                echo "<td class=\"$tdClass\">" . $data[$i][0] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][1] . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][2] . "</td>";                
                echo "<td class=\"$tdClass\">" . $data[$i][4] . "</td>";
                echo "<td class=\"$tdClass\">" . $amountPanel . "</td>";
                echo "<td class=\"$tdClass\">" . $data[$i][5] . "</td>";
                echo "<td class=\"$tdClass\">" . $companyId . "</td>";
                echo "</tr>";
                
                $sumCourier += $data[$i][4];

            }
            echo "</table>";
            echo "<h3>Total Courier $sumCourier </h3>";
            echo "<h3>Total Epagelmatias $sumPanel </h3>";
            
            
            echo "<form method=\"post\">";
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
