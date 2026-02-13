<?php


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/utils.php'); /////////////////////////////
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$companyid = $_GET['id'];

$err = 0;
$msg = "";


if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    session_start();
    
    $company = new COMPANIES($db1, $companyid);
    
    if (isset($_POST['chkLetter']) && $_POST['chkLetter']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-letter'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //echo "PRINTSETTING-ID=".$printsetting->get_id();
        $print->set_ptemplate($printsetting->get_template());
        $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
        $print->set_printername($printername); ///
        $print->set_user($_SESSION['user_id']);
        
        if ($print->Savedata()) {
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("ONLINECATALOGUEID"); $printdetail->set_ptext($company->get_catalogueid());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("REGISTRATIONDATE"); $printdetail->set_ptext(date('d-m-Y'));
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
            $printdetail->set_bookmark("ZIPCODE"); $printdetail->set_ptext($company->get_zipcode());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PHONE1"); $printdetail->set_ptext($company->get_phone1());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("OTHERPHONES"); $printdetail->set_ptext($company->get_phone2().', '.$company->get_mobilephone());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("EMAIL"); $printdetail->set_ptext($company->get_email());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("WEBSITE"); $printdetail->set_ptext($company->get_website());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $basiccategory = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
            $printdetail->set_bookmark("CATEGORY"); $printdetail->set_ptext($basiccategory);
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("USERNAME"); $printdetail->set_ptext($company->get_username());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("PASSWORD"); $printdetail->set_ptext($company->get_password());
            $printdetail->Savedata();
            
            //.....
            
            $msg .= "LETTER PRINT OK / ";
            
        }
        else {
            $msg .= "LETTER PRINT ERROR / ";
        }
        
    }
    
    
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
    
    
    if (isset($_POST['chkVoucher']) && $_POST['chkVoucher']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-voucher'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //$printsetting = new PRINTSETTINGS($db1,0,NULL,"SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-voucher'");
        //echo $printsetting->get_template();
        $print->set_ptemplate($printsetting->get_template());
        $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
        //echo $printername;
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
            $printdetail->set_bookmark("CITYANDZIP"); $printdetail->set_ptext($company->get_zipcode());
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
            $finalprice = $company->get_price()*(1+$vat/100);
            $finalprice = func::format($finalprice, "CURRENCY");
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("AMOUNT"); $printdetail->set_ptext($finalprice); //....
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $deliverydate = func::format($company->get_DeliveryDate(), "DATE", $locale); 
            
            //$vdate = date("d-m-Y");
            $printdetail->set_bookmark("DATE"); $printdetail->set_ptext($deliverydate); //....
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$deliverydate = func::format($company->get_DeliveryDate(), "DATE", $locale); 
            //$deliverydate .= "-".$company->get_DeliveryTime();
            $deliverydatetime = $deliverydate."-".$company->get_DeliveryTime();
            $printdetail->set_bookmark("DELIVERYDATE"); $printdetail->set_ptext($deliverydatetime); //....
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $deliverynote = $company->get_DeliveryNotes();
            $printdetail->set_bookmark("DELIVERYNOTE"); $printdetail->set_ptext($deliverynote);
            $printdetail->Savedata();
            
            //VOUCHER-VRESNET ID
            $lastvoucherid = func::vlookup("keyvalue", "SETTINGS", "keycode LIKE 'VOUCHER-VRESNET'", $db1);
            $newvoucherid = $lastvoucherid + 1;
            $sql = "UPDATE SETTINGS SET keyvalue = ? WHERE keycode LIKE ?";
            $db1->execSQL($sql, array($newvoucherid, "VOUCHER-VRESNET"));
            $vouchercode = "EP-".$newvoucherid;
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("VOUCHERID"); $printdetail->set_ptext($vouchercode);
            $printdetail->Savedata();
            
            
            //2o antigrafo =======================================================
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("COMPANYNAME2"); $printdetail->set_ptext($company->get_companyname());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("ADDRESS2"); $printdetail->set_ptext($company->get_address());
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("CITYANDZIP2"); $printdetail->set_ptext($company->get_zipcode());
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
            $printdetail->set_bookmark("DATE2"); $printdetail->set_ptext($deliverydate); //....
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            //$vdate = func::format($company->get_DeliveryDate(), "DATE", $locale); 
            //$vdate .= "-".$company->get_DeliveryTime();
            $printdetail->set_bookmark("DELIVERYDATE2"); $printdetail->set_ptext($deliverydatetime); //....
            $printdetail->Savedata();
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $deliverynote = $company->get_DeliveryNotes();
            $printdetail->set_bookmark("DELIVERYNOTE2"); $printdetail->set_ptext($deliverynote);
            $printdetail->Savedata();
            
            //VOUCHER-VRESNET ID
//            $lastvoucherid = func::vlookup("keyvalue", "SETTINGS", "keycode LIKE 'VOUCHER-VRESNET'", $db1);
//            $newvoucherid = $lastvoucherid + 1;
//            $sql = "UPDATE SETTINGS SET keyvalue = ? WHERE keycode LIKE ?";
//            $db1->execSQL($sql, array($newvoucherid, "VOUCHER-VRESNET"));
//            $vouchercode = "EP-".$newvoucherid;
            
            $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
            $printdetail->set_bookmark("VOUCHERID2"); $printdetail->set_ptext($vouchercode);
            $printdetail->Savedata();
            
            
            
            
            //.....
            $msg .= "VOUCHER PRINT OK / ";
            
        }
        else {
            $msg .= "VOUCHER PRINT ERROR / ";
        }
        
    }
    
    
    if (isset($_POST['chkInvoice']) && $_POST['chkInvoice']==1) {
        $print = new PRINTJOB($db1,0);
        
        $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-invoice'");
        $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);
        //$printsetting = new PRINTSETTINGS($db1,0,NULL,"SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-invoice'");
        
        $print->set_ptemplate($printsetting->get_template());
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
            $printdetail->set_bookmark("PHONE"); $printdetail->set_ptext($company->get_phone1());
            $printdetail->Savedata();
            
            //.....
            $msg .= "INVOICE PRINT OK / ";
            
        }
        else {
            $msg .= "INVOICE PRINT ERROR / ";
        }
        
    }
    
    
    //action + company
    $userid = $_SESSION['user_id'];
    $action = new ACTIONS($db1, 0);
    $action->set_company($company->get_id());
    $status2 = 6; //ektipothike...
    $action->set_status1($company->get_status());
    $action->set_status2($status2);     
    $action->set_user($userid);
    
    if ($action->Savedata()) {        
        $company->set_status($status2);
        $company->set_user($userid);        
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
    
    <body class="form">
        
        <div class="form-container">
            
            <form action="companyPrint.php?id=<?php echo $companyid; ?>&confirm=1" method="POST">
                
                <?php if ($msg!="") { echo $msg . "<br/><br/>";} ?>
                
                <div class="col-3"><input type="checkbox" name="chkLetter" value="1" checked="checked" /></div>
                <div class="col-9"><?php echo $l->l("letter"); ?></div>
                <div class="clear"></div>
                
                <div class="col-3"><input type="checkbox" name="chkVoucher" value="1" checked="checked" /></div>
                <div class="col-9"><?php echo $l->l("voucher"); ?></div>
                <div class="clear"></div>
                
                <div class="col-3"><input type="checkbox" name="chkLabel" value="1" checked="checked" /></div>
                <div class="col-9"><?php echo $l->l("label"); ?></div>
                <div class="clear"></div>
                
                <div class="col-3"><input type="checkbox" name="chkInvoice" value="1" /></div>
                <div class="col-9"><?php echo $l->l("invoice"); ?></div>
                <div class="clear"></div>
                
                <?php                 
                $btnOK = new button("BtnOk", $lg->l('confirm'));
                $btnOK->get_button();                
                ?>
                
                <div class="clear"></div>
            
            </form>
            
            
        </div>
        
    </body>
    
</html>

