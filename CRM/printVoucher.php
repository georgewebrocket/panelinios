<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$msg = "";


$userid = $_SESSION['user_id'];

$id = $_GET['id']; //voucher id

$voucher = new VOUCHERS($db1, $id);
$company = new COMPANIES($db1, $voucher->get_customer()) ;
$city = $company->get_city();

$print = new PRINTJOB($db1,0);
        
$courier = new COURIER($db1, $company->get_courier());

$printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-voucher'");
$printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);

$print->set_ptemplate($courier->get_vouchertemplate());
$printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);

$print->set_printername($printername); ///
$print->set_user($_SESSION['user_id']);

if ($print->Savedata()) {

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("COMPANYNAME"); $printdetail->set_ptext($company->get_companyname());
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("ADDRESS"); $printdetail->set_ptext($company->get_address());
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("CITYANDZIP"); $printdetail->set_ptext($city." - ".$company->get_zipcode());
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $area = func::vlookup("description", "AREAS", "id=".$company->get_area(), $db1);
    $printdetail->set_bookmark("AREA"); $printdetail->set_ptext($area);
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
    $printdetail->set_bookmark("PROFESSION"); $printdetail->set_ptext($profession);
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("PHONE"); $printdetail->set_ptext($company->get_phone1());
    $printdetail->Savedata();

    $vat = func::vlookup("value", "VAT", "zone=1", $db1);
    //$finalprice = $company->get_price()*(1+$vat/100);
    
    $finalprice = 0;
    /*$sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype = 1 AND `status`=1 AND company= " . $company->get_id();
    $rsTrans = $db1->getRS($sql);
    for ($i = 0; $i < count($rsTrans); $i++) {
        $finalprice += $rsTrans[$i]['amount'] + $rsTrans[$i]['vat'];    
    } */
    
    $finalprice = $voucher->get_amount();
    $finalprice = func::format($finalprice, "CURRENCY");
    
    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("AMOUNT"); $printdetail->set_ptext($finalprice); //....
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $deliverydate = func::format($company->get_DeliveryDate(), "DATE", $locale); 

    $printdetail->set_bookmark("DATE"); 
    //$printdetail->set_ptext(date("j/n/Y")); //....
    $printdetail->set_ptext(func::str14toDate($voucher->get_vdate()));

    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $deliverydatetime = $deliverydate."-".$company->get_DeliveryTime();
    $printdetail->set_bookmark("DELIVERYDATE"); $printdetail->set_ptext($deliverydatetime); //....
    $printdetail->Savedata();

    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $deliverynote = $company->get_DeliveryNotes();
    $printdetail->set_bookmark("DELIVERYNOTE"); $printdetail->set_ptext($deliverynote);
    $printdetail->Savedata();

    $vouchercode = $voucher->get_vcode();
    $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
    $printdetail->set_bookmark("VOUCHERID"); $printdetail->set_ptext($vouchercode);
    $printdetail->Savedata();
    
    //2o antigrafo =======================================================
    if ($courier->get_copies()>=2) {            

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("COMPANYNAME2"); $printdetail->set_ptext($company->get_companyname());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("ADDRESS2"); $printdetail->set_ptext($company->get_address());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("CITYANDZIP2"); $printdetail->set_ptext($city." - ".$company->get_zipcode());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("AREA2"); $printdetail->set_ptext($area);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        //$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
        $printdetail->set_bookmark("PROFESSION2"); $printdetail->set_ptext($profession);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("PHONE2"); $printdetail->set_ptext($company->get_phone1());
        $printdetail->Savedata();

        //$vat = func::vlookup("value", "VAT", "zone=1", $db1);
        //$finalprice = $company->get_price()*(1+$vat/100);
        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("AMOUNT2"); $printdetail->set_ptext($finalprice); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        //$vdate = date("d-m-Y");
        $printdetail->set_bookmark("DATE2"); $printdetail->set_ptext(date("j/n/Y")); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("DELIVERYDATE2"); $printdetail->set_ptext($deliverydatetime); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $deliverynote = $company->get_DeliveryNotes();
        $printdetail->set_bookmark("DELIVERYNOTE2"); $printdetail->set_ptext($deliverynote);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("VOUCHERID2"); $printdetail->set_ptext($vouchercode);
        $printdetail->Savedata();

    }


    //3o antigrafo =======================================================
    if ($courier->get_copies()>=3) {
        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("COMPANYNAME3"); $printdetail->set_ptext($company->get_companyname());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("ADDRESS3"); $printdetail->set_ptext($company->get_address());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("CITYANDZIP3"); $printdetail->set_ptext($city." - ".$company->get_zipcode());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("AREA3"); $printdetail->set_ptext($area);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        //$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
        $printdetail->set_bookmark("PROFESSION3"); $printdetail->set_ptext($profession);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("PHONE3"); $printdetail->set_ptext($company->get_phone1());
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("AMOUNT3"); $printdetail->set_ptext($finalprice); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        //$vdate = date("d-m-Y");
        $printdetail->set_bookmark("DATE3"); $printdetail->set_ptext(date("j/n/Y")); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("DELIVERYDATE3"); $printdetail->set_ptext($deliverydatetime); //....
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $deliverynote = $company->get_DeliveryNotes();
        $printdetail->set_bookmark("DELIVERYNOTE3"); $printdetail->set_ptext($deliverynote);
        $printdetail->Savedata();            

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("VOUCHERID3"); $printdetail->set_ptext($vouchercode);
        $printdetail->Savedata();

    }




    //.....
    $msg .= "VOUCHER PRINT OK / ";

}
else {
    $msg .= "VOUCHER PRINT ERROR / ";
}


echo $msg;