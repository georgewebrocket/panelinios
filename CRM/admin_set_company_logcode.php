<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT DISTINCT company FROM `INVOICEHEADERS` WHERE idate>='20170101000000' AND series IN (1,2) ORDER BY idate";
$rs = $db1->getRS($sql);

for ($i = 0; $i < count($rs); $i++) {
    $iPlus = $i + 1;
    $logCode = "30.10." . str_pad($iPlus, 4, "0", STR_PAD_LEFT);
    $sql = "UPDATE COMPANIES SET log_code='$logCode' WHERE id=?";
    $ret = $db1->execSQL($sql, array($rs[$i]['company']));
    echo $logCode . "<br/>";
}