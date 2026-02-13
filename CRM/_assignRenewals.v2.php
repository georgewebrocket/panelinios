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
$user = $_REQUEST['user'];
$users = explode(",",  $_REQUEST['users']);
$customers = explode(",",  $_REQUEST['customers']);

for ($i=0; $i < count($users); $i++) { 
    assignCompanyToUser($customers[$i], $users[$i], $product, $db1);
}

echo $_REQUEST['customers'];