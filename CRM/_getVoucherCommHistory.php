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

$sql = "SELECT * FROM ACTIONS WHERE voucherid=?";
$rsVoucherActions = $db1->getRS($sql, array($voucherId));

if ($rsVoucherActions) {
    for ($i = 0; $i < count($rsVoucherActions); $i++) {
        $myDate = new DateTime($rsVoucherActions[$i]['atimestamp']);
        $myDate = $myDate->format("d/m/Y");
        $myComment = $rsVoucherActions[$i]["comment"];
        echo "<div class=\"col-3\" style=\"font-weight:bold\">$myDate</div>";
        echo "<div class=\"col-9\">$myComment</div>";
        echo "<div class=\"clear\"></div>";
    
    }
    echo "<div class=\"clear\" style=\"height:20px\"></div>";
}