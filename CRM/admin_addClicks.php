<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once '../php/config.php';
require_once '../php/utils.php';
require_once '../php/db.php';
require_once '../php/dataobjects.php';
require_once '../php/start.php';

$curMinute = date("i"); //minutes
//$curMinute = 0;

$sql = "SELECT COUNT(*) AS MYCOUNT FROM companies WHERE package>1";
$rsCount = $db1->getRS($sql);
$count = $rsCount[0]['MYCOUNT'];
echo $count . "<br/>";
$step = ceil($count / 50);

$start = $curMinute * $step;

$sql = "SELECT * FROM companies WHERE package>1 LIMIT $step OFFSET $start ";
echo $sql . "<br/>";

$rs = $db1->getRS($sql);
if ($rs) {
    for ($i = 0; $i < count($rs); $i++) {
        $company = new companies($db1, $rs[$i]['id'], $rs);
        
        $sqlAct = "SELECT * FROM company_activity WHERE company=? AND activity_date=? ";
        $rsAct = $db1->getRS($sqlAct, array($company->get_id(), date("Ymd000000")));
        $realClicks = 0;
        if ($rsAct) {
            $clicksToAdd = mt_rand(4,6);
            $realClicks = $rsAct[0]['clicks'];
        }            
        else {
            $clicksToAdd = mt_rand(1,3);
        }
        
        echo $company->get_id() . ": " .  $realClicks . " -> " . $clicksToAdd . "<br/>";
        $company->AddClick($clicksToAdd);
        
        
    }
}
