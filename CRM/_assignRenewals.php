<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$expires1 = $_REQUEST['expires1'];
$expires2 = $_REQUEST['expires2'];
$product = $_REQUEST['product'];
//echo $product . "<br/>";
$assignCount = $_REQUEST['assignCount'];

$userIds = $_REQUEST['users'];
//echo $userIds . "<br/>";
$assigns = $_REQUEST['assigns'];
//echo $assigns . "<br/>";

$users = explode(",", $userIds);
$userAssign = explode(",", $assigns);
$userAssigned = array();
$userCompanies = array();
for ($i = 0; $i < count($userAssign); $i++) {
    if ($userAssign[$i]=='') {
        $userAssign[$i]=0;
    }
    $userAssigned[$i] = 0;
    $userCompanies[$i] = "";
}


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

$sql = "SELECT COMPANIES.id, CS.last_user5, '' AS ASSIGNED "
    . "FROM COMPANIES INNER JOIN "
    . "(SELECT * FROM COMPANIES_STATUS WHERE productcategory=? AND status =9) CS "
    . "ON COMPANIES.id = CS.companyid WHERE  COMPANIES.$productExpField >= ? "
    . "AND COMPANIES.$productExpField <= ? ORDER BY $productExpField LIMIT $assignCount";
//echo $sql;

$rsCompanies = $db1->getRS($sql, array($product,$expires1,$expires2));
//var_dump($rsCompanies);

//Πρώτα κάνω assign τα renewals του κάθε χρήστη
//εφόσον ο χρήστης δεν έχει ξεπεράσει τον αριθμό που έχω ορίσει. 
for ($i = 0; $i < count($rsCompanies); $i++) {
    $key = array_search($rsCompanies[$i]['last_user5'], $users);
    //var_dump($key);
    if ($key!==FALSE) {
        //echo   $userAssigned[$key] . "-" . $userAssign[$key] . "<br/>";  
        if ($userAssigned[$key]<$userAssign[$key]) {
            //assign to the same user
            assignCompanyToUser($rsCompanies[$i]['id'], $users[$key], 
                $product, $db1);
            
            $rsCompanies[$i]['ASSIGNED'] = $users[$key];
            $userCompanies[$key] = $userCompanies[$key]==""? $rsCompanies[$i]['id']: $userCompanies[$key] . ", " . $rsCompanies[$i]['id'] ;
            $userAssigned[$key]++;
        }
    }
    
}

//Δεύτερο πέρασμα για όσες δεν έχουν γίνει assigned 
//var_dump($rsCompanies);
$key = 0;
for ($i = 0; $i < count($rsCompanies); $i++) {
    if ($rsCompanies[$i]['ASSIGNED']=="") {
        //echo $rsCompanies[$i]['id'].":<br/>";
        //echo $userAssigned[$key] . "-" . $userAssign[$key] . "<br/>"; 
        if ($userAssigned[$key]<$userAssign[$key]) {
            //assign to user
            assignCompanyToUser($rsCompanies[$i]['id'], $users[$key], 
                $product, $db1);
            $rsCompanies[$i]['ASSIGNED'] = $users[$key];
            $userCompanies[$key] = $userCompanies[$key]==""? $rsCompanies[$i]['id']: $userCompanies[$key] . ", " . $rsCompanies[$i]['id'] ;
            $userAssigned[$key]++;
        }
        else {
            if ($key<count($users)-1) {
                $key++;
                $i--; // επανέρχομαι στην ίδια εταιρεία με τον επόμενο χρήστη
            }
            else {
                //exit();
            }
            
        }
    }
}

$log_user_assignments = "";

for ($key = 0; $key < count($users); $key++) {    
    if ($userCompanies[$key]!="") {
        $user = new USERS($db1, $users[$key]);
        echo "<h4>" . $user->get_fullname() . "</h4>";
        echo $userCompanies[$key] . "<br/><br/>";
        $log_user_assignments .= $user->get_fullname() . " " . $userCompanies[$key] . "<br/>";
    }
    
}


//log
$assignment = new CUSTOMER_ASSIGNMENTS($db1, 0);
$assignment->ca_datetime(date("YmdHis"));
$assignment->title("ΑΝΑΘΕΣΗ ΑΝΑΝΕΩΣΕΩΝ");

$expires1date = func::str14toDate($expires1);
$expires2date = func::str14toDate($expires2);
$product_name = func::vlookup("description", "PRODUCT_CATEGORIES", "id=$product", $db1);


$assignment->details("ΗΜΕΡ. ΛΗΞΗΣ $expires1date  - $expires2date.<br/> ΠΡΟΪΟΝ $product_name<br/>ΑΡΙΘΜ. ΑΝΑΝΕΩΣΕΩΝ $assignCount <br/> $log_user_assignments");
$user_id = $_SESSION['user_id'];
$assignment->user($user_id);
$assignment->Savedata();




function assignCompanyToUser($companyId, $userId, $product, $dbo) {
    $company = new COMPANIES($dbo, $companyId);
    
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=?";
    $rsStatus = $dbo->getRS($sql, array($company->get_id(), $product));
    if ($rsStatus) {
        $company_status = new COMPANIES_STATUS($dbo, $rsStatus[0]['id'], $rsStatus);
    }
    else {
        $company_status = new COMPANIES_STATUS($dbo, 0);
    }

    if ($company_status->get_id()==0) {
        $company_status->set_companyid($company->get_id());
        $company_status->set_productcategory($product);
    }

    $company_status->set_status(3); //recall
    $company_status->set_recalldate(date("Ymd")."000000");
    $company_status->set_csdatetime(date("Ymd")."000000");
    $company_status->set_recalltime(9); //10:00    
    $company_status->set_userid($userId);
    $company_status->Savedata();

    $company->set_for_renewal(1);
    $company->Savedata();
    
    //create action
    $action = new ACTIONS($dbo, 0);
    $action->set_company($company->get_id());
    $action->set_user($userId);
    $action->set_status1(0);
    $action->set_status2(19);
    $action->set_product_categories("[" . $product . "]");
    $action->set_comment("Assigned by " . $_SESSION['user_fullname']);
    $action->Savedata();
    
    $db1 = $dbo;
    $id = $companyId;
    
    $printPanelId = FALSE;
    
    include "updateCompanyData.php";
    
}