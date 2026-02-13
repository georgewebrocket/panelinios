<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$voucherId = $_REQUEST['voucherid'];
$courierStatus = $_REQUEST['courierstatus'];

$voucher = new VOUCHERS($db1, $voucherId);

$voucher->set_courier_status($courierStatus);

$voucher->Savedata();

$statusDescription = func::vlookup("description", "COURIERSTATUS", "id=$courierStatus", $db1);

echo "Νέο status: $statusDescription";