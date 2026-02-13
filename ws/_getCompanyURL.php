<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'php/config.php';
require_once 'php/utils.php';
require_once 'php/db.php';
require_once 'php/dataobjects.php';
require_once 'php/controls.php';
require_once 'php/wp.php';
require_once 'php/start.php';

$pid = $_GET['pid'];

$sql = "SELECT * FROM companies WHERE p_id=?";
$rsCompany = $db1->getRS($sql, array($pid));
if ($rsCompany) {
    $companyid = $rsCompany[0]['id'];
    $company = new companies($db1, $rsCompany[0]['id'], $rsCompany);
    $pid = $company->get_p_id();
    $mySeoUrl = func::getSeoURL($company);
    $canonical = HOST . "/company/$pid/$mySeoUrl";
    echo $canonical;
}