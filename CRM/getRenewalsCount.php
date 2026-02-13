<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$expires1 = $_REQUEST['expires1'];
$expires2 = $_REQUEST['expires2'];
$product = $_REQUEST['product'];
$assignCount = $_REQUEST['assignCount'];

if ($product==0) {die("SELECT PRODUCT");}
if ($expires1=="") {die("SELECT DATE 1");}
if ($expires2=="") {die("SELECT DATE 2");}

switch($product) {
    case 1: $productExpField = "expires"; break;
    case 2: $productExpField = "domain_expires"; break;
    case 3: $productExpField = "domain_expires"; break;
    case 4: $productExpField = "domain_expires"; break;
    default: $productExpField = "expires"; break;
}

$expires1 = func::dateTo14str($expires1); 
$expires2 = func::dateTo14str($expires2);

$sql = "SELECT COMPANIES.id FROM COMPANIES INNER JOIN "
    . "(SELECT * FROM COMPANIES_STATUS WHERE productcategory=? AND status =9) CS "
    . "ON COMPANIES.id = CS.companyid "
    . "WHERE  COMPANIES.$productExpField >= ? AND COMPANIES.$productExpField <= ? ";
//echo $sql;    
$rsCompanies = $db1->getRS($sql, array($product, $expires1,$expires2));

if ($rsCompanies) {
    $totalCustomers = count($rsCompanies);
    echo "<h2>Συνολικές ανανεώσεις: $totalCustomers</h2>";  
    
}
else {
    //echo "";
}


$sql = "SELECT COUNT(COMPANIES.id) AS MYCOUNT, USERS.id AS USERID, "
    . "USERS.fullname AS USER, USERS.active, USERS.is_agent "
    . "FROM (SELECT COMPANIES.id, CS.last_user5 FROM COMPANIES INNER JOIN "
    . "(SELECT * FROM COMPANIES_STATUS WHERE productcategory=? AND status =9) CS "
    . "ON COMPANIES.id = CS.companyid WHERE  COMPANIES.$productExpField >= ? "
    . "AND COMPANIES.$productExpField <= ? ORDER BY $productExpField LIMIT $assignCount) "
    . "COMPANIES "
    . "INNER JOIN USERS ON COMPANIES.last_user5=USERS.id "
    . "GROUP BY USERS.id, USERS.fullname, USERS.active, USERS.is_agent "
    . "ORDER BY USERS.fullname";


//echo $sql;    
$rsCompanies = $db1->getRS($sql, array($product,$expires1,$expires2));

$userids = "";
$renewals = 0; $assigns = 0;

if ($rsCompanies) {
    
    echo "<table class=\"renewals\">";
    echo "<tr><th>User</th><th>Renewals</th><th width=\"100\">Assign</th></tr>";
    
    for ($i = 0; $i < count($rsCompanies); $i++) {
        $activeagent = $rsCompanies[$i]['active'] == 1 && $rsCompanies[$i]['is_agent']==1? "": "*";
        
        $myCount = $activeagent=="*"? "": $rsCompanies[$i]['MYCOUNT'];
        $userId = $rsCompanies[$i]['USERID'];
        
        echo "<tr>";
        echo "<td>" . $rsCompanies[$i]['USER'] . "$activeagent</td>";
        echo "<td>" . $rsCompanies[$i]['MYCOUNT'] . "</td>";
        echo "<td>";
        if ($activeagent=="") {
            echo "<input class=\"assign\" type=\"text\" value=\"$myCount\" data-userid=\"$userId\" />";
        }
        echo "</td>";
        echo "</tr>";
        
        $userids .= $rsCompanies[$i]['USERID'];
        if ($i<count($rsCompanies)-1) {$userids .= ","; }
        
        $renewals += $rsCompanies[$i]['MYCOUNT'];
        $myCount = $myCount==""? 0: $myCount;
        $assigns += $myCount; 
        
        
    }
    
    $sql = "SELECT '' AS MYCOUNT, USERS.fullname AS USER, USERS.active, USERS.is_agent, USERS.id AS USERID FROM USERS WHERE id NOT IN ($userids) AND active=1 AND is_agent=1 ORDER BY fullname";
    //echo $sql;
    $rsUsers = $db1->getRS($sql);
    
    for ($i = 0; $i < count($rsUsers); $i++) {
        $userId = $rsUsers[$i]['USERID'];
        echo "<tr>";
        echo "<td>" . $rsUsers[$i]['USER'] . "</td>";
        echo "<td>" . $rsUsers[$i]['MYCOUNT'] . "</td>";
        echo "<td><input type=\"text\" class=\"assign\" value=\"\" data-userid=\"$userId\" /></td>";
        
        echo "</tr>";
        
        
        
    }
    
    echo "<tr class=\"footer-renewals\">";
    echo "<td>ΣΥΝΟΛΑ</td>";
    echo "<td>$renewals</td>";
    echo "<td id=\"assign-count\">$assigns</td>";
    
    echo "</tr>";
    
    echo "</table>";
    
}
