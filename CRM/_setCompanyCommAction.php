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
$companyId = $_REQUEST['companyid'];
$userId = $_SESSION['user_id'];
$comment = $_REQUEST['comment'];

$action = new ACTIONS($db1, 0);
$action->set_company($companyId);
$action->set_user($userId);
$action->set_status1(0);
$action->set_status2(14); //epikoinonia
$action->set_comment($comment);
$action->set_voucherid($voucherId);

$voucher = new VOUCHERS($db1, $voucherId);
$voucher->set_lastcommnotes($comment);
$voucher->set_lastcommdate(date("YmdHis"));
$voucher->Savedata();

$action->Savedata();
if ($action->get_id()>0) {
    echo "ok";
}
else {
    echo "error";
}
