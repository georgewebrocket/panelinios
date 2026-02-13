<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT * FROM COMPANIES_STATUS WHERE status IN (6,7)";
//$sql = "SELECT * FROM COMPANIES_STATUS WHERE status IN (8)";
$rs = $db1->getRS($sql);

for ($i = 0; $i < count($rs); $i++) {
    $sql = "SELECT * FROM VOUCHERS WHERE customer=? ORDER BY vdate DESC";
    $rsVoucher = $db1->getRS($sql, array($rs[$i]['companyid']));
    if ($rsVoucher) {
        $voucher = new VOUCHERS($db1, $rsVoucher[0]['id'], $rsVoucher);
        $voucher->set_courier_status(1);
        //$voucher->set_courier_status(3);
        $voucher->Savedata();
        echo $rsVoucher[0]['id']. "<br/>";
    }
}