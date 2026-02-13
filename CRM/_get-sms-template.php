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

$templateid = $_REQUEST['templateid'];
$customerid = $_REQUEST['customer_id'];

$smsTemplate = new SMS_TEMPLATES($db1, $templateid);



$str = $smsTemplate->get_bodytext();


$customer = new COMPANIES($db1, $customerid);
$str = str_replace("[CRMID]", $customer->get_id(), $str);

echo $str;