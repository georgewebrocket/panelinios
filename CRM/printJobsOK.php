<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
header('Content-Type: text/html; charset=utf-8');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

$ids = $_GET['ids'];

$sql = "UPDATE PRINTJOB SET pstatus=2 WHERE id IN (".$ids.")";
$res = $db1->execSQL($sql);

echo $res;

?>

