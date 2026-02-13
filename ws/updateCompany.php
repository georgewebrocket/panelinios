<?php

header('Access-Control-Allow-Origin: https://panelinios.gr');
//header('Access-Control-Allow-Origin: https://www.panelinios.gr');

header("Access-Control-Allow-Origin: https://crm.panelinios.gr");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'php/config.php';
require_once 'php/db.php';
require_once 'php/utils.php';

header('Content-Type: text/html; charset=utf-8');

$db1 = new DB(conn1::$connstr, conn1::$username, conn1::$password);

$id = $_REQUEST['id'];

$fields = $_REQUEST['fields'];
$vals = $_REQUEST['vals'];

//var_dump($fields);
//var_dump($vals);

if ($id==0) {
    $strFields = ""; $requests = "";
    for ($i=0;$i<count($fields);$i++) {
        $strFields .= $fields[$i].", ";
        $requests .= "?, ";
    }
    $strFields .= "active";
    $requests .= "?";
    array_push($vals, "1");

    $sql = "INSERT INTO companies ($strFields) VALUES ($requests)";
    //array_push($vals, $id);
    $res = $db1->execSQL($sql, $vals);

    if ($res>0) {
        $pid = $res * 2 + 7128;
        $sql2 = "UPDATE companies SET p_id = ? WHERE id= ?";
        $res2 = $db1->execSQL($sql2, array($pid, $res));

    }
        
}
else {
    $strFields = "";
    for ($i=0;$i<count($fields);$i++) {
            $strFields .= $fields[$i]."=?, ";
    }
    $strFields = substr($strFields, 0, strlen($strFields)-2);

    $sql = "UPDATE companies SET $strFields WHERE id=?";
    array_push($vals, $id);
    $res = $db1->execSQL($sql, $vals);

    $pid = $id * 2 + 7128;
    $sql2 = "UPDATE companies SET p_id = ? WHERE id= ?";
    $res2 = $db1->execSQL($sql2, array($pid, $id));
        
}

echo $res;