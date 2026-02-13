<?php


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/config.php');
require_once('php/dataobjects.php');
require_once('php/utils.php'); /////////////////////////////
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$id=$_GET['id'];
$companyid = $_GET['id'];
$company = new COMPANIES($db1, $companyid);

$err = 0;
$msg = "";

$defaultLetter1 = 0;
$sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status = 5 AND productcategory=1"; // ektyposi
$rs = $db1->getRS($sql);
if ($rs) {
    $defaultLetter1 = 1;
}

$defaultLetter2 = 0;
$sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status = 5 AND productcategory=2"; // ektyposi
$rs = $db1->getRS($sql);
if ($rs) {
    $defaultLetter2 = 1;
}




function CreateVoucher($company, $db1, $publisher=0) {
    $voucher = new VOUCHERS($db1, 0);
    $voucher->set_vdate(date('Ymd')."000000");
    $voucher->set_courier($company->get_courier());
    
    //voucher code
    $courier = new COURIER($db1, $company->get_courier());
    $vouchercode = $courier->get_vouchercount();
    $vouchercode++;
    $courier->set_vouchercount($vouchercode);
    
    //$company = new COMPANIES($db1, $companyid);
    $voucher->set_customername($company->get_companyname());
    $voucher->set_publisher($publisher);
    
    $courier->Savedata();
    $voucher->set_vcode($vouchercode); ///
    $company->set_voucherid($vouchercode); ////
    $company->Savedata();
    
    $finalprice = 0;
    $transactionIds = "";
    $sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype = 1 AND `status`=1 AND vouchered=0 AND company= " . $company->get_id();
    $rsTrans = $db1->getRS($sql);
    for ($i = 0; $i < count($rsTrans); $i++) {
        $finalprice += $rsTrans[$i]['amount'] + $rsTrans[$i]['vat'];
        $mytrans = new TRANSACTIONS($db1, $rsTrans[$i]['id'], $rsTrans);
        $mytrans->set_vouchered(1);
        $mytrans->Savedata();
        $transactionIds .= $mytrans->get_id();
        if ($i<count($rsTrans)-1) { $transactionIds .= ","; }
    }    
    //$finalprice = func::format($finalprice, "CURRENCY");
        
    $voucher->set_amount($finalprice);
    $voucher->set_customer($company->get_id());    
    $voucher->set_deliverydate($company->get_DeliveryDate());
    $voucher->set_deliverynotes($company->get_DeliveryNotes());
    $voucher->set_deliverytime($company->get_DeliveryTime());
    $voucher->set_courier_ok(0);
    $voucher->set_courier_notes("");
    $voucher->set_courier_delivery_date("");
    $voucher->set_courier_return(0);
    $voucher->set_courier_status(1);
    
    $voucher->set_export_to_excel(0);
    $voucher->set_exported_to_excel(0);
    $voucher->set_transactionids($transactionIds);
    
    $mySql = "SELECT company, 
        SUBSTRING_INDEX(GROUP_CONCAT(user ORDER BY atimestamp DESC), ',', 1) 
        AS user5 ,    
        GROUP_CONCAT(user ORDER BY atimestamp DESC),
        GROUP_CONCAT(atimestamp ORDER BY atimestamp DESC)
        FROM ACTIONS WHERE status2=5 AND company=? GROUP BY `company`";
    $myRS = $db1->getRS($mySql, array($company->get_id()));
    $voucher->set_userid($myRS[0]['user5']);
    
    $voucher->Savedata();
    return $voucher->get_id();

}


function PrintVoucher($id, $db1, $locale) {
    $voucher = new VOUCHERS($db1, $id);
    $finalprice = func::format($voucher->get_amount(), "CURRENCY");
    
    $company = new COMPANIES($db1, $voucher->get_customer()) ;
    
    $myAddress = $company->get_courier_address()!=""? $company->get_courier_address(): $company->get_address();
    $courierCity = $company->get_courier_city_descr();
    $myCity = $courierCity!=""? $courierCity: $company->get_city();
    $myZipcode = $company->get_courier_zipcode()!=""? $company->get_courier_zipcode(): $company->get_zipcode();
    $myPhone = $company->get_courier_phone()!=""? $company->get_courier_phone(): fn::noSpaces($company->get_mobilephone()) . " " . fn::noSpaces($company->get_phone1())  . " " . fn::noSpaces($company->get_phone2());

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
        $printdetail->set_bookmark("ADDRESS"); $printdetail->set_ptext($myAddress);
        $printdetail->Savedata();

        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark("CITYANDZIP"); $printdetail->set_ptext($myCity .
                " - ". $myZipcode);
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
        $printdetail->set_bookmark("PHONE"); $printdetail->set_ptext($myPhone);
        $printdetail->Savedata();

        
    
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
            $printdetail->set_bookmark("ADDRESS2"); $printdetail->set_ptext($myAddress);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CITYANDZIP2"); $printdetail->set_ptext($myCity." - ".$myZipcode);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("AREA2"); $printdetail->set_ptext($area);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
            $printdetail->set_bookmark("PROFESSION2"); $printdetail->set_ptext($profession);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PHONE2"); $printdetail->set_ptext($myPhone);
            $printdetail->Savedata();

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
            $printdetail->set_bookmark("ADDRESS3"); $printdetail->set_ptext($myAddress);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CITYANDZIP3"); $printdetail->set_ptext($myCity." - ".$myZipcode);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("AREA3"); $printdetail->set_ptext($area);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
            $printdetail->set_bookmark("PROFESSION3"); $printdetail->set_ptext($profession);
            $printdetail->Savedata();

            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PHONE3"); $printdetail->set_ptext($myPhone);
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
        //$msg .= "VOUCHER PRINT OK / ";

    }
    else {
        //$msg .= "VOUCHER PRINT ERROR / ";
    }

}



if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    //session_start();
    
    //$company = new COMPANIES($db1, $companyid);

    /******* LETTER ********/ 
    $anytime_code = "";   
    if (isset($_POST['chkLetter']) && $_POST['chkLetter']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-letter'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //echo "PRINTSETTING-ID=".$printsetting->get_id();
        $print->set_ptemplate($printsetting->get_template());
        $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
        $print->set_printername($printername); ///
        $print->set_user($_SESSION['user_id']);

        //anytime
        // $sql = "SELECT * FROM ANYTIME WHERE has_been_sent=0 ORDER BY id LIMIT 1";
        // $rsAnytime = $db1->getRS($sql);
        // if ($rsAnytime) {
        //     $anytime = new ANYTIME($db1, $rsAnytime[0]['id'], $rsAnytime);
        //     $anytime_code = $anytime->a_code();
        //     $anytime->customer_id($companyid);
        //     $anytime->date_sent(date('YmdHis'));
        //     $anytime->has_been_sent(1);
        //     $anytime->Savedata();
        // }
        
        if ($print->Savedata()) {
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$pid = $company->get_catalogueid() * 2 + 7128;
            $pid = func::getCompanyPId($company);
            $link = "https://www.panelinios.gr/company/$pid";
            $printdetail->set_bookmark("ONLINECATALOGUEID"); $printdetail->set_ptext($link);
            $printdetail->Savedata();

            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("COMPANYNAME"); $printdetail->set_ptext($company->get_companyname());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("ADDRESS"); $printdetail->set_ptext($company->get_address());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CONTACTPERSON"); $printdetail->set_ptext($company->get_contactperson());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("USERNAME"); $printdetail->set_ptext($company->get_username());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PASSWORD"); $printdetail->set_ptext($company->get_password());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$agent = $_SESSION['user_fullname'];
            $sql = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(CAST(user AS CHAR) "
                    . "ORDER BY `atimestamp` DESC), ',', 1 ) AS user "
                    . "FROM `ACTIONS` WHERE `status2` = 5 AND `company` = ? "
                    . "AND `product_categories` LIKE '%[1]%'";
            $rsUser = $db1->getRS($sql, array($companyid));
            $agent = "";
            if ($rsUser) {
                $agentId = $rsUser[0]['user'];
                $agent = func::vlookup("fullname", "USERS", "id=$agentId", $db1);
            }
            $printdetail->set_bookmark("AGENT"); $printdetail->set_ptext($agent);
            $printdetail->Savedata();
            
            //.....
            
            $msg .= "LETTER PRINT OK / ";
            
        }
        else {
            $msg .= "LETTER PRINT ERROR / ";
        }
        
    }
    
    
    /******* LETTER2 ********/
    if (isset($_POST['chkLetter2']) && $_POST['chkLetter2']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'domain-letter'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //echo "PRINTSETTING-ID=".$printsetting->get_id();
        $print->set_ptemplate($printsetting->get_template());
        $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
        $print->set_printername($printername); ///
        $print->set_user($_SESSION['user_id']);
        
        if ($print->Savedata()) {
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("USERNAME"); 
            $printdetail->set_ptext($company->get_username());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PASSWORD"); 
            $printdetail->set_ptext($company->get_password());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("EPONIMIA"); 
            $printdetail->set_ptext($company->get_companyname());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CONTACTPERSON"); 
            $printdetail->set_ptext($company->get_contactperson());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("ADDRESS");
            $city = func::vlookup("description", "EP_CITIES", "id=". $company->get_city_id(), $db1);
            $fullAddress = $company->get_address() . " " . $city;
            $printdetail->set_ptext($fullAddress);
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); 
            $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PHONE"); 
            $phones = $company->get_phone1();
            if ($company->get_phone2()!="") {
                $phones .= ", ".$company->get_phone2();
            }
            if ($company->get_mobilephone()!="") {
                $phones .= ", ".$company->get_mobilephone();
            }
            $printdetail->set_ptext($phones);
            $printdetail->Savedata();
            
            
            
            //.....
            
            $msg .= "LETTER PRINT OK / ";
            
        }
        else {
            $msg .= "LETTER PRINT ERROR / ";
        }
        
    }
    
    
    
    /******* LABEL ********/
    if (isset($_POST['chkLabel']) && $_POST['chkLabel']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-label'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //$printsetting = new PRINTSETTINGS($db1,0,NULL,"SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-label'");
        
        $print->set_ptemplate($printsetting->get_template());
        $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
        $print->set_printername($printername); ///
        $print->set_user($_SESSION['user_id']);
        
        if ($print->Savedata()) {
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("COMPANY"); $printdetail->set_ptext($company->get_companyname());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("ADDRESS"); $printdetail->set_ptext($company->get_address());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CONTACT"); $printdetail->set_ptext($company->get_contactperson());
            $printdetail->Savedata();
            
            //.....
            $msg .= "LABEL PRINT-OK / ";
            
        }
        else {
            $msg .= "LABEL PRINT OK / ";
        }
        
    }
    
    
    /******* VOUCHER ********/
    if (isset($_POST['chkVoucher']) && $_POST['chkVoucher']==1) {
        //create new voucher

        $publisher = $_POST['c_publisher'];

        $myVoucherId = CreateVoucher($company, $db1, $publisher);
        $voucher = new VOUCHERS($db1, $myVoucherId);
        $vouchercode = $voucher->get_vcode();
        
        //print the voucher
        $courier = new COURIER($db1, $company->get_courier());
        if ($courier->get_printvoucher()==1) {
            PrintVoucher($myVoucherId, $db1, $locale);
        }
        //Mark the voucher to export to Excel
        if ($courier->get_voucher_export_to_excel()==1) {
            $voucher->set_export_to_excel(1);
            $voucher->Savedata();
        }
        
    }


      
    
    
    
    
    //action + company
    $status2 = 6; //ektipothike...
    $userid = $_SESSION['user_id'];
    
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$companyid AND status=5"; //symfonise
    $rs = $db1->getRS($sql);
    $productCategoriesIds = "";
    for ($i = 0; $i < count($rs); $i++) {        
        $companystatus = new COMPANIES_STATUS($db1, $rs[$i]['id'], $rs);
        $companystatus->set_status($status2);
        $companystatus->set_userid($userid);
        $companystatus->set_csdatetime(date("YmdHis"));
        $companystatus->Savedata();
        $productCategoriesIds .= func::ConcatSpecial($productCategoriesIds, 
                "[".$companystatus->get_productcategory()."]", ",") ;
    }
    
    $action = new ACTIONS($db1, 0);
    $action->set_company($company->get_id());    
    $action->set_status1($company->get_status());
    $action->set_status2($status2);     
    $action->set_user($userid);
    $action->set_product_categories($productCategoriesIds);
    
    if ($action->Savedata()) {        
        
        if (isset($_POST['chkVoucher']) && $_POST['chkVoucher']==1) {
            $company->set_voucherid($vouchercode);
        }
        
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=?";
        $rs2 = $db1->getRS($sql, array($company->get_id()));
        
        $strComm = "";
        $myRecallDate = ""; 
        $myRecallTime = 0;
        
        for ($k = 0; $k < count($rs2); $k++) {
            $strComm .= $rs2[$k]['id']."|";
            $strComm .= $rs2[$k]['productcategory']."|";
            $strComm .= $rs2[$k]['status']."|";
            $strComm .= $rs2[$k]['userid']."|";
            $strComm .= $rs2[$k]['recalldate']."|";
            $strComm .= $rs2[$k]['recalltime'];
            if ($k<count($rs2)-1) {
                $strComm .= "/";
            }
            
            switch ($rs2[$k]['productcategory']) {
                case 1:
                    $company->set_lastactiondate1(date("YmdHis"));
                    break;
                case 2:
                    $company->set_lastactiondate2(date("YmdHis"));
                    break;
                case 3:
                    $company->set_lastactiondate3(date("YmdHis"));
                    break;
                default:
            }

            if ($myRecallDate=="" && $rs2[$k]['recalldate']!="") {
                $myRecallDate = $rs2[$k]['recalldate'];
                $myRecallTime = $rs2[$k]['recalltime'];
            }
            
        }
        $company->set_commstatus($strComm);
        $company->set_recalldate($myRecallDate);
        $company->set_recalltime($myRecallTime);
        
        //$company->Savedata();
        
        if ($company->Savedata()) {
            $msg = $lg->l("save-ok");
        }
        else {
            $msg = $lg->l("error");
        }
    }
    else {
        $msg = $lg->l("error");       
    }
    
    
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PANELINIOS - CRM</title>
    
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />

    </head>
    
    <body style="background-color: #ddd; padding: 30px;">
        
        <div style="max-width: 1200px; margin: auto; background-color: #fff;a;padding:40px">
            
            <form action="companyPrint.php?id=<?php echo $companyid; ?>&confirm=1" method="POST">
                
                <h1 style="margin-bottom:30px">ΕΚΤΥΠΩΣΗ - <?php echo $company->get_companyname() ?></h1>
                 
                
                <?php if ($msg!="") { echo "<h2>".$msg . "</h2><br/><br/>";} ?>
                
                <?php
                if ($company->get_courier()==0) {
                    echo "<h2 style=\"color:red\">Προσοχή! Δεν έχετε επιλέξει courier</h2>";
                }
                
                ?>
                
                <?php                 
                $checkLetter1 = $defaultLetter1==1? "checked=\"checked\"": "";
                ?>
                <div class="col-3"><input type="checkbox" name="chkLetter" value="1" <?php echo $checkLetter1; ?> /></div>
                <div class="col-9"><?php echo $l->l("letter"); ?></div>
                <div class="clear"></div>
                
                <?php                 
                $checkLetter2 = $defaultLetter2==1? "checked=\"checked\"": "";
                ?>
                <div class="col-3"><input type="checkbox" name="chkLetter2" value="1" <?php echo $checkLetter2; ?> /></div>
                <div class="col-9">Letter 2 (domain)</div>
                <div class="clear"></div>
                
                <div class="col-3"><input type="checkbox" name="chkVoucher" value="1" checked="checked" /></div>
                <div class="col-9">Voucher (Create and print)</div>
                <div class="clear"></div>
                
                <div class="col-3"><input type="checkbox" name="chkLabel" value="1" /></div>
                <div class="col-9"><?php echo $l->l("label"); ?></div>
                <div class="clear"></div>

                <!-- <div class="col-3"><input type="checkbox" name="chkAnytimeEmail" value="1" /></div>
                <div class="col-9">Email Anytime</div>
                <div class="clear"></div> -->

                <?php
                $c_publisher = new comboBox("c_publisher", $db1, "SELECT * FROM invoice_publishers WHERE active=1", 
                "id", "publisher_name", 2, "ΕΚΔΟΤΗΣ ΠΑΡ/ΚΟΥ");
                $c_publisher->set_enableNoChoice(FALSE);                
                $c_publisher->get_comboBox();
                ?>
                

                
                <?php                 
                $btnOK = new button("BtnOk", $lg->l('confirm'));
                $btnOK->get_button();                
                ?>
                
                <div class="clear"></div>
            
            </form>
            
            
            <div style="padding-left:15px">
            <a class="button" href="editcompany.php?id=<?php echo $id; ?>">Επιστροφή στην καρτέλα πελάτη</a>
            
            <?php if ($_SESSION['user_profile']>1) { ?>
                    <?php if ($company->get_catalogueid()!="") {  ?>
                        <a class="button" href="syncCompany.php?companyid=<?php echo $id; ?>">
                        Ενημέρωση online καταχώρησης
                        </a>&nbsp;
                    <?php } else { ?>
                        <a class="button" href="syncCompany.php?companyid=<?php echo $id; ?>">
                            Προσθήκη καταχώρησης online
                        </a>&nbsp;                
                    <?php } ?>
            
                <?php } ?>
                
            
            </div>
            
            
        </div>
        
        <div style="position: fixed; top:20px; right:20px;font-size: 30px;">
            <a href="editcompany.php?id=<?php echo $companyid; ?>">
                <span class="fa fa-close"></span>
            </a>
        </div>
        
    </body>
    
</html>

