<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");

//require_once('php/session.php');
session_start();
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

// $companyid = filter_input(INPUT_GET, 'company', FILTER_VALIDATE_INT);
// $productid = filter_input(INPUT_GET, 'product', FILTER_VALIDATE_INT);

// $userid = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);

$companyid = $_REQUEST['company'];
$productid = $_REQUEST['product'];
$userid = $_REQUEST['user'];

$msg = "";

$theVatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);

if (!$companyid || !$productid || !$userid) {
    die('ERROR');
}

$company = new COMPANIES($db1, $companyid);
$package = new PACKAGES($db1, $productid);
$companyPrice = 0;
if ($package->get_product_category()==1) {
    $discountid = $company->get_discount();
    $companyPrice = $company->get_price();
}
elseif ($package->get_product_category()==2) {
    $discountid = $company->get_discount2();
    $companyPrice = $company->get_price2();
}
elseif ($package->get_product_category()==4) {
    $discountid = $company->get_fb_discount();
    $companyPrice = $company->get_fb_price();
}
elseif ($package->get_product_category()==5) {
    $discountid = $company->get_ga_discount();
    $companyPrice = $company->get_ga_price();
}

echo $companyPrice;
$discount = new DISCOUNTS($db1, $discountid);

$transaction = new TRANSACTIONS($db1, 0);

$transaction->set_tdatetime(date('Ymd')."000000");
$transaction->set_transactiontype(1);
$transaction->set_status(1);
$transaction->set_seller($userid);
$transaction->set_company($companyid);
$transaction->set_package($productid);

/****/
if ($package->get_price()>0  && in_array($package->get_product_category(), array(1,2))) {
    $transaction->set_price($package->get_price());
    $transaction->set_discount($discount->get_discount());
    $amount = $package->get_price() * (100 - $discount->get_discount())/100;
    $transaction->set_amount($amount);
}
else {
    $transaction->set_price($companyPrice);
    $transaction->set_discount(0);
    $amount = $companyPrice;
    $transaction->set_amount($amount);
}
/****/

$vat = $amount * $theVatPercentage / 100;
$transaction->set_vat($vat);
$transaction->set_vatpercentage($theVatPercentage);
$transaction->set_payedamount(0);
$transaction->set_comment("");
$transaction->set_newsales(0);
$transaction->set_resell(0);
$transaction->set_resend(0);
$transaction->set_returned(0);

if ($transaction->Savedata()) {
    $msg .= " / Transaction OK ";
    $productCat = $package->get_product_category();
    include('transResellResend3.php');
}
else {
    $msg .= " / Transaction ERROR ";
}

echo $msg;

echo "<br/><br/>";

echo "<a class=\"button\" href=\"editcompany.php?id=$companyid\">Επιστροφή στην εταιρεία.</a>";

?>