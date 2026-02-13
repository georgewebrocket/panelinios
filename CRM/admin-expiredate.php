<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once 'php/utils.php';
require_once('inc.php');

$sql = "SELECT * FROM `COMPANIES` WHERE reference=8";
$rs = $db1->getRS($sql);

for ($i=0;$i<count($rs) && $i<200;$i++) {
    $company = new COMPANIES($db1, $rs[$i]['id'], $rs);
    echo $company->get_vn_expires()."-".func::DATE_To14str($company->get_vn_expires())."<br/>";//
}


?>