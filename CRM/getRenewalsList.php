<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$expires1 = $_REQUEST['expires1'];
$expires2 = $_REQUEST['expires2'];
$product = $_REQUEST['product'];
$assignCount = $_REQUEST['assignCount'];

if ($product==0) {die("SELECT PRODUCT");}
if ($expires1=="") {die("SELECT DATE 1");}
if ($expires2=="") {die("SELECT DATE 2");}

switch($product) {
    case 1: $productExpField = "expires"; break;
    case 2: $productExpField = "domain_expires"; break;
    case 3: $productExpField = "domain_expires"; break;
    case 4: $productExpField = "domain_expires"; break;
    default: $productExpField = "expires"; break;
}

$expires1 = func::dateTo14str($expires1); 
$expires2 = func::dateTo14str($expires2);

$sql = "SELECT DISTINCT COMPANIES.id, COMPANIES.companyname, '' AS HISTORY, '' AS MYUSERS  FROM COMPANIES INNER JOIN "
    . "(SELECT * FROM COMPANIES_STATUS WHERE productcategory=? AND status =9) CS "
    . "ON COMPANIES.id = CS.companyid "
    . "WHERE  COMPANIES.$productExpField >= ? AND COMPANIES.$productExpField <= ? LIMIT $assignCount";
    
$rsCompanies = $db1->getRS($sql, array($product, $expires1,$expires2));


if ($rsCompanies) {
    $company_ids = [];
    foreach ($rsCompanies as $company) {
        $company_ids[] = $company['id'];
    }
    $company_ids_str = implode(",", $company_ids);

    $sql = "SELECT company, STR_TO_DATE(TRANSACTIONS.tdatetime, '%Y%m%d%H%i%s') AS MYDATE, USERS.fullname AS MYUSER, 
        TRANSACTION_STATUS.description AS MYSTATUS, 'ΧΡΕΩΣΗ' AS MYTYPE 
        FROM TRANSACTIONS INNER JOIN USERS ON TRANSACTIONS.seller = USERS.id
        INNER JOIN TRANSACTION_STATUS ON TRANSACTIONS.status = TRANSACTION_STATUS.id 
        WHERE company IN ($company_ids_str) 
        AND transactiontype=1
        AND package IN (SELECT id FROM PACKAGES WHERE product_category=$product) 
        UNION
        SELECT company, ACTIONS.atimestamp AS MYDATE, USERS.fullname AS MYUSER, 'ΑΡΝ. ΑΝΑΝ.' AS MYSTATUS, '' AS MYTYPE FROM ACTIONS
        INNER JOIN USERS ON ACTIONS.user = USERS.id
        WHERE company IN ($company_ids_str) AND status2=15 
        AND product_categories LIKE '%[$product]%'

        ORDER BY MYDATE";
    
    $rsTransactions = $db1->getRS($sql); //xreoseis

    //echo $sql;

    $sql = "SELECT t.company AS id, t.seller, t.tdatetime
        FROM TRANSACTIONS t
        WHERE t.tdatetime = (
            SELECT MAX(tdatetime)
            FROM TRANSACTIONS t2
            WHERE t2.company = t.company
            AND t2.transactiontype=1
        ) 
        AND t.company IN ($company_ids_str) ";
    $rsLastCharge = $db1->getRS($sql);

    //echo $sql;
    
    $index = 0;
    foreach ($rsCompanies as &$company) {        
        $my_transactions = array_filter($rsTransactions, function($transaction) use ($company) {
            return $transaction['company'] == $company['id'];
        });
        $my_history = [];
        foreach ($my_transactions as $my_transaction) {
            $my_date = new DateTime($my_transaction['MYDATE']);
            $my_formattedDate = $my_date->format('d-m-Y');
            if ($my_transaction['MYTYPE']=="") {
                $my_history[] = "<span style=\"color:#a0f\">" . $my_formattedDate . " - "
                . $my_transaction['MYUSER'] . " - " . $my_transaction['MYSTATUS'] . " " . $my_transaction['MYTYPE'] . "</span>";
            }
            else if ($my_transaction['MYSTATUS']=="Cancelled") {
                $my_history[] = "<span style=\"color:#f00\">" . $my_formattedDate . " - "
                . $my_transaction['MYUSER'] . " - " . $my_transaction['MYSTATUS'] . " " . $my_transaction['MYTYPE'] . "</span>";
            }
            else {
                $my_history[] = $my_formattedDate . " - "
                . $my_transaction['MYUSER'] . " - " . $my_transaction['MYSTATUS'] . " " . $my_transaction['MYTYPE'];
            }
            
        }
        
        $company['HISTORY'] = implode("<hr style=\"border-top:1px dashed #ccc;border-bottom:none\">", $my_history);

        $last_charge_user = func::vlookupRS("seller", $rsLastCharge, $company['id']);
        $c_users = new comboBox("USER_$index", $db1, 
            "SELECT * FROM USERS WHERE active=1", 
            "id", "fullname", 
            $last_charge_user);

        $company['MYUSERS'] = $c_users->comboBox_simple();

        $company['companyname'] = "<a target=\"_blank\" href=\"editcompany.php?id={$company['id']}\">{$company['companyname']}</a>";

        $index++;
        
    }
    unset($company);

    $datagrid = new datagrid("grid_companies", $db1, "",
        ['id', 'companyname', 'HISTORY', 'MYUSERS'],
        ['ID', 'ΠΕΛΑΤΗΣ', 'ΙΣΤΟΡΙΚΟ', 'ΑΝΑΘΕΣΗ']);
        
    $datagrid->set_rs($rsCompanies);

    echo "<h2>".count($rsCompanies)." ανανεώσεις</h2>";
    
    $datagrid->get_datagrid();

    


}

