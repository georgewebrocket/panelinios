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

function updateCompanySites($db, $companyId, $sitesVal, $delimiterRows = "///", $delimiterColumns = "||") {
    $companyId = intval($companyId);
    $db->execSQL("DELETE FROM COMPANY_SITES WHERE company_id=?", array($companyId));

    if (trim($sitesVal)=="") {
        return;
    }

    $rows = explode($delimiterRows, $sitesVal);
    for ($i=0; $i<count($rows); $i++) {
        $row = trim($rows[$i]);
        if ($row=="") {
            continue;
        }

        $cols = explode($delimiterColumns, $row);
        $address = isset($cols[0]) ? trim($cols[0]) : "";
        $phone = isset($cols[1]) ? trim($cols[1]) : "";
        $mapX = isset($cols[2]) ? trim($cols[2]) : "";
        $mapY = isset($cols[3]) ? trim($cols[3]) : "";
        $cityId = isset($cols[4]) && trim($cols[4])!="" ? intval($cols[4]) : 0;
        $areaId = isset($cols[5]) && trim($cols[5])!="" ? intval($cols[5]) : 0;

        if ($address=="" && $phone=="" && $mapX=="" && $mapY=="" && $cityId==0 && $areaId==0) {
            continue;
        }

        $sql = "INSERT INTO COMPANY_SITES (company_id, address, phone, map_x, map_y, city_id, area_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $db->execSQL($sql, array($companyId, $address, $phone, $mapX, $mapY, $cityId, $areaId));
    }
}

$id = $_REQUEST['id'];

$fields = $_REQUEST['fields'];
$vals = $_REQUEST['vals'];

$sitesVal = "";
for ($i=0;$i<count($fields);$i++) {
    if ($fields[$i]=="sites") {
        $sitesVal = $vals[$i];
        break;
    }
}

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

        updateCompanySites($db1, $res, $sitesVal);

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

    updateCompanySites($db1, $id, $sitesVal);
        
}

echo $res;