<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$user = 0;
$status = 0;
$package = -1;
$dateFrom = "";
$dateTo = "";
$productCats ="";
$ids = "";

$type = "transactions";

if (isset($_GET['user'])) {
    $user = $_GET['user'];
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
}
if (isset($_GET['package'])) {
    $package = $_GET['package'];
}
if (isset($_GET['dateFrom'])) {
    $dateFrom  = $_GET['dateFrom'];
}
if (isset($_GET['dateTo'])) {
    $dateTo = $_GET['dateTo'];
}
if (isset($_GET['productCats'])) {
    $productCats = $_GET['productCats'];
}


$resell = (isset($_GET['resell'])? 1: 0 );
$resend = (isset($_GET['resend'])? 1: 0 );

$sql = "SELECT company AS id, company AS companyname, company AS area, company AS basiccategory, product_category, status, company AS mylink FROM TRANSACTIONS INNER JOIN PACKAGES ON TRANSACTIONS.package=PACKAGES.id WHERE "
        . " `transactiontype`=1 "
        . "AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'";

if ($user>0) {
    $sql .= " AND seller = $user ";
}

if ($resell==1) {
    $sql .= " AND resell = 1";
}
if ($resend==1) {
    $sql .= " AND resend = 1";
}
if ($status>0) {
    $sql .= " AND `status` IN ($status) ";
}
if ($package>=0) {
    $sql .= " AND package=$package ";
}
if ($productCats!="") {
    $sql .= " AND package IN (SELECT id FROM PACKAGES WHERE product_category IN ($productCats)) ";
}
//echo "MYTYPE=" . $_GET['mytype'];


if (isset($_GET['mytype'])) {
    $type = $_GET['mytype'];
    
    if ($type=="newcalls") {
        $userId = $_GET['userid'];
        
        $sql = "SELECT COMPANIES.id AS id, COMPANIES.id AS companyname, 
            COMPANIES.id AS area, COMPANIES.id AS basiccategory, 
            COMPANIES_STATUS.productcategory AS product_category, COMPANIES_STATUS.status, 
            COMPANIES.id AS mylink 
            FROM COMPANIES 
            INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
            WHERE COMPANIES.active=1 
            AND (COMPANIES.commstatus IS NULL OR COMPANIES.commstatus='')
            AND COMPANIES_STATUS.status = 3
            AND COMPANIES_STATUS.userid = $userId
            ORDER BY COMPANIES.id ";       
        
    }
    
    if ($type=="recalls") {
        $userId = $_GET['userid'];
        
        $sql = "SELECT COMPANIES.id AS id, COMPANIES.id AS companyname, 
            COMPANIES.id AS area, COMPANIES.id AS basiccategory, 
            COMPANIES_STATUS.productcategory AS product_category, COMPANIES_STATUS.status, 
            COMPANIES.id AS mylink 
            FROM COMPANIES 
            INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
            WHERE COMPANIES.active=1 
            AND (COMPANIES.commstatus IS NOT NULL AND COMPANIES.commstatus<>'')
            AND COMPANIES_STATUS.status = 3
            AND COMPANIES_STATUS.userid = $userId
            ORDER BY COMPANIES.id ";
                
    }
    
    if ($type=="totalcalls") {
        $userId = $_GET['userid'];
        
        $sql = "SELECT COMPANIES.id AS id, COMPANIES.id AS companyname, 
            COMPANIES.id AS area, COMPANIES.id AS basiccategory, 
            COMPANIES_STATUS.productcategory AS product_category, COMPANIES_STATUS.status, 
            COMPANIES.id AS mylink 
            FROM COMPANIES 
            INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
            WHERE COMPANIES.active=1 
            AND COMPANIES_STATUS.status = 3
            AND COMPANIES_STATUS.userid = $userId
            ORDER BY COMPANIES.id ";
                
    }
    
    
    if ($type=="callswithstatus") {
        
        $userId = $_GET['userid'];
        $status = $_GET['status']; //companies_status
        $date1 = $_GET['date1'];
        $date2 = $_GET['date2'];
        $actionstatus = $_GET['actionstatus']; //recall / newcall
        
        $sql = "SELECT COMPANIES.id AS id, COMPANIES.id AS companyname, 
            COMPANIES.id AS area, COMPANIES.id AS basiccategory, 
            COMPANIES_STATUS.productcategory AS product_category, COMPANIES_STATUS.status, 
            COMPANIES.id AS mylink
            FROM `ACTIONS` 
            INNER JOIN COMPANIES ON ACTIONS.company = COMPANIES.id
            INNER JOIN COMPANIES_STATUS ON ACTIONS.company = COMPANIES_STATUS.companyid 
            AND ACTIONS.product_cat = COMPANIES_STATUS.productcategory
            WHERE ACTIONS.status2 = $actionstatus
            AND ACTIONS.atimestamp >='$date1' AND ACTIONS.atimestamp <='$date2'
            AND ACTIONS.user = $userId ";   
        
        if ($status!=0) {
            $sql .= "AND COMPANIES_STATUS.status IN ($status)";
        }
        
    }
    
    if ($type == "ids") {
        
        $ids = $_GET['ids'];
        
        $sql = "SELECT COMPANIES.id AS id, COMPANIES.id AS companyname, 
            COMPANIES.id AS area, COMPANIES.id AS basiccategory, 
            COMPANIES_STATUS.productcategory AS product_category, COMPANIES_STATUS.status, 
            COMPANIES.id AS mylink 
            FROM COMPANIES 
            INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
            WHERE COMPANIES.id IN ($ids)
            ORDER BY COMPANIES.id ";
        
    }
    
    
    if ($type=="salesperday") {
        
        $sql = "SELECT company AS id, company AS companyname, company AS area, company AS basiccategory, product_category, status, company AS mylink FROM TRANSACTIONS INNER JOIN PACKAGES ON TRANSACTIONS.package=PACKAGES.id  "
        . "WHERE `transactiontype`=1 "
        . "AND `tdatetime`>='$dateFrom' AND `tdatetime`<='$dateTo'";
        
        if ($status>0) {
            $sql .= " AND `status` IN ($status) ";
        }
        
        
    }
    
    
    
}




//echo "<!--$sql-->";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    
    <link href="css/tableexport.css" rel="stylesheet" type="text/css" />
    <script src="js/FileSaver.min.js"></script>
    <script src="js/Blob.min.js"></script>
    <script src="js/xls.core.min.js"></script>
    <script src="js/tableexport.js"></script>
    
    <style>
        
        #gridCompanies2 tr td:nth-child(7) {
            text-align: center;
        }
        
        
    </style>
    
    </head>

    <body class="form">
        <div class="form-container" style="padding:30px">
            
            <?php
            $gridCompanies = new datagrid("gridCompanies2", $db1, $sql, 
                array("id","companyname","area","basiccategory","product_category", "status", "mylink"), 
                array("ID","ΕΠΩΝΥΜΙΑ","ΠΕΡΙΟΧΗ","ΚΑΤΗΓΟΡΙΑ","ΚΑΤ.ΠΡ.", "Status", "OPEN"),
                $ltoken, 0
                );
            
            $gridCompanies->set_colWidths(array("50","200","100","100","100","100","50"));
            
            if ($type == "transactions") {
                $gridCompanies->col_vlookup("status","status","TRANSACTION_STATUS","description", $db1);
            }
             if ($type == "recalls" 
                     || $type == "newcalls" 
                     || $type == "totalcalls"
                     || $type = "callswithstatus") {
                $gridCompanies->col_vlookup("status","status","STATUS","description", $db1);
            }
             
            $gridCompanies->col_vlookup("companyname","companyname","COMPANIES","companyname", $db1);            
            $gridCompanies->col_vlookup("area","area","COMPANIES","area", $db1);
            $gridCompanies->col_vlookup("basiccategory","basiccategory","COMPANIES","basiccategory", $db1);
            $gridCompanies->col_vlookup("product_category","product_category","PRODUCT_CATEGORIES","shortdescription", $db1);            
                        
            $gridCompanies->col_vlookup("area","area","AREAS","description", $db1);
            $gridCompanies->col_vlookup("basiccategory","basiccategory","CATEGORIES","description", $db1);
            $gridCompanies->col_func("mylink", "mylink", "<a target=\"_blank\" href=\"editcompany.php?id=XX\"><span class=\"fa fa-edit fa-lg\"></span></a>", "XX");
            
            $gridCompanies->get_datagrid();

            ?>
            
            <h2>Total: <?php echo count($gridCompanies->get_rs()) ?></h2>
            
        </div>
        
        <script>
            $(function() {
                $("#gridCompanies2").tableExport({formats: ["xlsx","xls", "csv"]});
                
            });

        </script>
        
        
    </body>
    
</html>