<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$companyid = filter_input(INPUT_GET, 'company', FILTER_VALIDATE_INT);
$productid = filter_input(INPUT_GET, 'product', FILTER_VALIDATE_INT);
$discountid = filter_input(INPUT_GET, 'discount', FILTER_VALIDATE_INT);
$userid = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT);
$msg = "";

$theVatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);

if (!$companyid || !$productid || !$userid) {
    die('ERROR');
    //echo "ERROR";
}

$company = new COMPANIES($db1, $companyid);
$package = new PACKAGES($db1, $productid);
$discount = new DISCOUNTS($db1, $discountid);

$transaction = new TRANSACTIONS($db1, 0);

$transaction->set_tdatetime(date('Ymd')."000000");
$transaction->set_transactiontype(1);
$transaction->set_status(1);
$transaction->set_seller($userid);
$transaction->set_company($companyid);
$transaction->set_package($productid);
$transaction->set_price($package->get_price());
$transaction->set_discount($discount->get_discount());
$amount = $package->get_price() * (100-$discount->get_discount())/100;
$transaction->set_amount($amount);
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
}
else {
    $msg .= " / Transaction ERROR ";
}

echo $msg;

echo "<br/><br/>";

echo "<a class=\"button\" href=\"editcompany.php?id=$companyid\">Επιστροφή στην εταιρεία</a>";

?>