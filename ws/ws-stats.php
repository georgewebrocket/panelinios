<?php
// Allow requests from your CRM subdomain
header("Access-Control-Allow-Origin: https://crm.panelinios.gr");
// Optional: allow other headers/methods if needed
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once 'php/config.php';
require_once 'php/dataobjects.php';
require_once 'php/controls.php';
require_once 'php/utils.php';
require_once 'php/db.php';
require_once 'php/start.php';

$company = $_GET['id'];

if (isset($_GET['datestart']) && isset($_GET['datestop'])) {
    $dateStart = $_GET['datestart'];
    $dateStop = $_GET['datestop'];
}
else {
    $dateStart = date('Ymd',strtotime("-3 month")).'000000';
    $dateStop = date('Ymd').'000000';
}

$sql = "SELECT SUM(impressions) AS timp, SUM(clicks) AS tcl FROM company_activity WHERE company = ".$company." AND "
        . "(activity_date >= ".$dateStart." AND activity_date <= ".$dateStop.")";
$rsStats = $db1->getRS($sql);

if (count($rsStats)>0) {
    echo "<ul style=\"list-style:inside disc\"><li>IMPRESSIONS: ".$rsStats[0]['timp']."</li>";
    echo "<li>CLICKS: ".$rsStats[0]['tcl']."</li></ul>";
}