<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$start = 700000;
$stop = 734876;

$sql = "SELECT id FROM COMPANIES_EPAG11 WHERE id>=$start AND id<$stop ORDER BY id";
$rs = $db1->getRS($sql);

for ($i = $start; $i < $stop; $i++) {
    echo $i . "-". $rs[$i - $start]['id'] . "<br/>";
}