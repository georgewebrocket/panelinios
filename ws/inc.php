<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
//header('Content-Type: text/html; charset=utf-8');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);
$db2 = new DB(conn2::$connstr2,conn2::$username2,conn2::$password2);
require_once('php/language.php');
$ltoken = "l=gr"; $lang = "gr";
if(isset($_GET['l'])){
    $lang = $_GET['l'];
    $ltoken = "l=".$_GET['l'];    
}
$locale = "GR";

?>