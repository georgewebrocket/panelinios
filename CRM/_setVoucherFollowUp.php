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
$followup_date = textbox::getDate($_REQUEST['followup_date'], $locale);
$followup_time = $_REQUEST['followup_time'];

$voucher = new VOUCHERS($db1, $voucherId);

$voucher->set_followup_date($followup_date);
$voucher->set_followup_time($followup_time);

$voucher->Savedata();

echo "Η ημερομηνία και ώρα follow up αποθηκεύτηκαν.";