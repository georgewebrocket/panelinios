<?php

/* 
 epistrefei ton arithmo tvn etaireiwn poy ikanopoioun orismena kritiria
 xrisimopoieitai sto assignCustToUsers
 */

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
include_once "php/configPanel.php";

$dboPanel = new DB(conn_panel::$connstr, conn_panel::$username, conn_panel::$password);

$sql = "SELECT DISTINCT COMPANIES.id FROM COMPANIES ".
		"INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ".
		"WHERE COMPANIES.status NOT IN(11,12) ";

$params = array();

if ($_POST['c_category']>0) {
    $category = $_POST['c_category'];    
    $sql .= " AND COMPANIES.basiccategory = ? ";
    array_push($params, $category);
}

if ($_POST['c_reference']>0) {
    $reference = $_POST['c_reference'];    
    $sql .= " AND COMPANIES.reference = ? ";
    array_push($params, $reference);
}

if ($_POST['c_area']>0) {
    $area = $_POST['c_area'];    
    $sql .= " AND area = ? ";
    array_push($params, $area);
}

if ($_POST['c_city']>0) {
    $city = $_POST['c_city'];    
    $sql .= " AND city_id = ? ";
    array_push($params, $city);
}


//$sql .= " ORDER BY COMPANIES.id ";
//echo $sql;
//print_r($params);
$rsCompanies = $db1->getRS($sql, $params);

//echo $rsCompanies[0]['MYCOUNT'] . " companies";

$ids = "";
for ($i = 0; $i < count($rsCompanies); $i++) {
    $ids .= $rsCompanies[$i]['id'];
    if ($i<count($rsCompanies)-1) {
        $ids .= ",";
    }
}

echo "<a class=\"fancybox\" href=\"getCompanies.php?mytype=ids&ids=$ids\">" .  count($rsCompanies) . " companies</a>";