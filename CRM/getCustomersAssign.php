<?php

/* 
 epistrefei ton arithmo tvn etaireiwn poy ikanopoioun orismena kritiria
 xrisimopoieitai sto assignCustToUsers
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

include_once "php/configEpag.php";
$dboEpag = new DB(conn_epag::$connstr, conn_epag::$username, conn_epag::$password);

$sql = "SELECT COMPANIES.id FROM COMPANIES ";

$statusEpagS = $_POST['t_statusEpagS'];
$statusEpagP = $_POST['t_statusEpagP'];
$statusEpagDateFrom = textbox::getDate($_POST['t_statusEpagDateFrom'], $locale);
$statusEpagDateTo = textbox::getDate($_POST['t_statusEpagDateTo'], $locale);
$statusEpagDateTo = substr($statusEpagDateTo, 0, 8) . "235959";
    
if ($_POST['c_status']>0 && $_POST['c_product']>0  || ($statusEpagS + $statusEpagP > 0)) {
    $sql .= "INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ";
    $companystatus = 2;
    $sql .= " WHERE (COMPANIES.status <> 1 OR COMPANIES.status IS NULL) ";
}
else {
    $sql .= " WHERE COMPANIES.status = 1 ";
}


$params = array();

if ($_POST['t_expireDate1']!="" && $_POST['t_expireDate2']!="") {
    $expires1 = textbox::getDate($_POST['t_expireDate1'], $locale); 
    $expires2 = textbox::getDate($_POST['t_expireDate2'], $locale); 
    $sql .= " AND COMPANIES.expires >= ? AND COMPANIES.expires <= ? ";
    array_push($params, $expires1);
    array_push($params, $expires2);
}

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

if ($_POST['c_status']>0) {
    $status = $_POST['c_status'];    
    $sql .= " AND COMPANIES_STATUS.status = ? ";
    array_push($params, $status);
}

if ($_POST['c_product']>0) {
    $product = $_POST['c_product'];    
    $sql .= " AND COMPANIES_STATUS.productcategory = ? ";
    array_push($params, $product);
}

if ($_POST['c_user']>0) {
    $user = $_POST['c_user'];    
    $sql .= " AND COMPANIES_STATUS.userid = ? ";
    array_push($params, $user);
}

if ($_POST['t_statusDateFrom']!="" ) {
    $statusDateFrom = $_POST['t_statusDateFrom'];
    $sql .= " AND COMPANIES_STATUS.csdatetime >= ? ";
    $str14Date = substr(func::dateTo14str($statusDateFrom), 0, 8)."000000";
    array_push($params, $str14Date);
} 

if ($_POST['t_statusDateTo']!="" ) {
    $statusDateTo = $_POST['t_statusDateTo'];
    $sql .= " AND COMPANIES_STATUS.csdatetime <= ? ";
    $str14Date = substr(func::dateTo14str($statusDateTo), 0, 8)."235959";
    array_push($params, $str14Date);
}


if ($_POST['t_assignDateFrom']!="" && $_POST['t_assignDateTo']!="") {
    $assignDateFrom = $_POST['t_assignDateFrom'];
    $assignDateTo = $_POST['t_assignDateTo'];
    $sql .= " AND COMPANIES.id NOT IN (SELECT company FROM ACTIONS WHERE atimestamp>=? AND atimestamp<=?) ";
    $str14Date1 = func::grdate_to_date($assignDateFrom)." 00:00:00";
    array_push($params, $str14Date1);
    $str14Date2 = func::grdate_to_date($assignDateTo)." 23:59:59";
    array_push($params, $str14Date2);
} 




/*epag status*/
if ($statusEpagS==1 || $statusEpagP==1) {
    $sEpagDateFromDT = func::str14toDateTime($statusEpagDateFrom, "-", "EN");
    $sEpagDateToDT = func::str14toDateTime($statusEpagDateTo, "-", "EN");
    $statusIds = "";
    if ($statusEpagS==1) { $statusIds = "5"; }
    if ($statusEpagP==1) { $statusIds = $statusIds==""? "9": $statusIds . ",9"; }
    $sqlEpag = "SELECT COMPANIES.id FROM COMPANIES INNER JOIN ACTIONS "
            . "ON COMPANIES.id = ACTIONS.company "
            . "WHERE ACTIONS.status2 IN ($statusIds) "
            . "AND atimestamp >= '$sEpagDateFromDT' AND atimestamp<='$sEpagDateToDT'";   
    $rsEpag = $dboEpag->getRS($sqlEpag);
    
    $EpagIds = "";
    for ($i = 0; $i < count($rsEpag); $i++) {
        if ($rsEpag[$i]['id']!="") {
            $EpagIds .= $rsEpag[$i]['id'];
            if ($i < count($rsEpag)-1) { $EpagIds .= ","; }
        }
    }
    $sql .= " AND COMPANIES.epag_id IN ($EpagIds) AND COMPANIES_STATUS.status NOT IN (3,5,9,6) AND COMPANIES_STATUS.productcategory=1 ";
    $t_statusPanel = $_REQUEST['t_statusPanel'];
    if ($t_statusPanel!="") {
        $sql .= " AND COMPANIES_STATUS.status IN ($t_statusPanel) ";
    }

}
/*epag status END*/


$rsCompanies = $db1->getRS($sql, $params);

$ids = "";
for ($i = 0; $i < count($rsCompanies); $i++) {
    $ids .= $rsCompanies[$i]['id'];
    if ($i<count($rsCompanies)-1) {
        $ids .= ",";
    }
}

echo "<a class=\"fancybox\" href=\"showCustomersAssign.php?mytype=ids&ids=$ids\">" .  count($rsCompanies) . " companies</a>";