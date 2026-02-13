<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once('php/db.php');
require_once('php/utils.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

$id = $_REQUEST['id'];
$fields = $_REQUEST['fields'];
$vals = $_REQUEST['vals'];

$strFields = ""; 
for ($i=0;$i<count($fields);$i++) {
	$strFields .= $fields[$i]."=?, ";
}
$strFields = substr($strFields, 0, strlen($strFields)-2);

$sql = "UPDATE COMPANIES SET $strFields WHERE id=?";
array_push($vals, $id);
$res = $db1->execSQL($sql, $vals);

echo $res;

//echo $sql;
//print_r($vals);

?>