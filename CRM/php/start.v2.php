<?php
require_once 'onoff.php';
if ($onoff==0) {
    header('Location: https://www.panelinios.gr');
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password); //crm epag

$db2 = new DB(conn2::$connstr,conn2::$username,conn2::$password); //crm panel

$dbSite = new DB(connSiteOld::$connstr,connSiteOld::$username,connSiteOld::$password); //old site epag

$ltoken = "l=gr"; $lang = "gr";
$locale = "GR";
$myHost = "https://crm.panelinios.gr";

define("PRODUCT_DOMAIN", 15);
define("APP_HOST", "https://crm.panelinios.gr");

