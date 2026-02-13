<?php

//panelinios

$statusSQL = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=?";
$rsCompanyStatus = $db1->getRS($statusSQL, array($customer_id, $product_category));
if ($rsCompanyStatus) {
    $company_status = new COMPANIES_STATUS($db1, $rsCompanyStatus[0]['id']);
    $new_user_id = $company_status->get_last_user5();               
}
else {
    $company_status = new COMPANIES_STATUS($db1, 0);
    $company_status->set_companyid($customer_id);
    $company_status->set_productcategory($product_category);
    $company_status->set_last_user5(0);
    $new_user_id = $voucher_user_id;
}
//αν το status δεν ειναι recall να το αλλάξει σε recall
if ($company_status->get_status()!=3) {
    $company_status->set_status(3); //recall
    $company_status->set_csdatetime(date("YmdHis"));
    $company_status->set_recalldate(date("Ymd", strtotime("+1 day")) . "000000");
    $company_status->set_recalltime(13); //12.00
    $company_status->set_userid($new_user_id); 
    $company_status->Savedata(); 
    
    $company = new COMPANIES($db1, $customer_id);
    include "updateCompanyData.php";

}

//action epistrofi
$action = new ACTIONS($db1, 0);
$action->set_company($customer_id);
$action->set_user(USER_RED_API);
$action->set_status1(0);
$action->set_status2(8); //epistrofi
$action->set_product_categories("[{$product_category}]");
$action->set_voucherid($voucherid);
$action->set_product_cat($product_category);
$action->Savedata();

//action recall
$action = new ACTIONS($db1, 0);
$action->set_company($customer_id);
$action->set_user(USER_RED_API);
$action->set_status1(0);
$action->set_status2(3); //recall
$action->set_product_categories("[{$product_category}]");
$action->set_voucherid($voucherid);
$action->set_product_cat($product_category);
$action->Savedata();