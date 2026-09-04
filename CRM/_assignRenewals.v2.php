<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


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




$product = $_REQUEST['product'];
$expires1 = isset($_REQUEST['expires1'])? $_REQUEST['expires1']: "";
$expires2 = isset($_REQUEST['expires2'])? $_REQUEST['expires2']: "";
$assignCount = isset($_REQUEST['assignCount'])? $_REQUEST['assignCount']: 0;
$user = $_REQUEST['user'];
$users = explode(",",  $_REQUEST['users']);
$customers = explode(",",  $_REQUEST['customers']);

$userCompanies = array();
$assignedCount = 0;

for ($i=0; $i < count($users); $i++) { 
    if (!isset($customers[$i]) || $customers[$i]=="" || $users[$i]=="") {
        continue;
    }

    assignCompanyToUser($customers[$i], $users[$i], $product, $db1);

    if (!isset($userCompanies[$users[$i]])) {
        $userCompanies[$users[$i]] = array();
    }
    array_push($userCompanies[$users[$i]], $customers[$i]);
    $assignedCount++;
}

if ($assignedCount>0) {
    $log_user_assignments = "";
    foreach ($userCompanies as $userId => $companyIds) {
        $iUser = new USERS($db1, $userId);
        $log_user_assignments .= $iUser->get_fullname() . " " . implode(", ", $companyIds) . "<br/>";
    }

    //log
    $assignment = new CUSTOMER_ASSIGNMENTS($db1, 0);
    $assignment->ca_datetime(date("YmdHis"));
    $assignment->title("ΑΝΑΘΕΣΗ ΑΝΑΝΕΩΣΕΩΝ");

    $expires1date = $expires1!=""? func::str14toDate(func::dateTo14str($expires1)): "";
    $expires2date = $expires2!=""? func::str14toDate(func::dateTo14str($expires2)): "";
    $product_name = func::vlookup("description", "PRODUCT_CATEGORIES", "id=$product", $db1);

    $assignment->details("ΗΜΕΡ. ΛΗΞΗΣ $expires1date  - $expires2date.<br/> ΠΡΟΪΟΝ $product_name<br/>ΑΡΙΘΜ. ΑΝΑΝΕΩΣΕΩΝ $assignedCount <br/> $log_user_assignments");
    $user_id = $_SESSION['user_id'];
    $assignment->user($user_id);
    $assignment->Savedata();
}

echo $_REQUEST['customers'];
