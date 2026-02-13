<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$invoiceId = $_REQUEST['invoiceId'];
$status = $_REQUEST['status'];
$timesread = $status == "opened"? 1: 0;

try {
    $invoice = new INVOICEHEADERS($db1, $invoiceId);
    $invoice->set_timesread($timesread);
    $invoice->Savedata();
    echo "OK";
} catch (Exception $ex) {
    echo "ERROR";
}
