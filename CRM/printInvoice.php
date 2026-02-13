<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$msg = "";


function PrintInvoiceDetail($db1,$print,$bookmark,$content) {
        $printdetail = new PRINTDETAILS($db1,0); $printdetail->set_jobid($print->get_id());
        $printdetail->set_bookmark($bookmark); $printdetail->set_ptext($content);
        $printdetail->Savedata();
    }
    
function PrintInvoiceDetails($db1,$print,$company,$extra,$invoice) {
        //$company = new COMPANIES($db1, $companyid);
        //$invoice = new INVOICES($myconn, $_id);
        
        $ar_extra = explode("|", $extra);
        
        PrintInvoiceDetail($db1,$print,"SEIRA",$ar_extra[0]);
        PrintInvoiceDetail($db1,$print,"ARITHMOS",$ar_extra[1]);
        PrintInvoiceDetail($db1,$print,"DATE",$ar_extra[2]);
        PrintInvoiceDetail($db1,$print,"TIME",$ar_extra[3]);
        PrintInvoiceDetail($db1,$print,"CUSTOMER_CODE",$company->get_id());
         
        PrintInvoiceDetail($db1,$print,"EPONIMIA",$invoice->get_companyname());
        $profession = $invoice->get_profession();
        PrintInvoiceDetail($db1,$print,"EPAGGELMA",$profession);
        PrintInvoiceDetail($db1,$print,"ODOS_AR",$invoice->get_address());
        PrintInvoiceDetail($db1,$print,"TIL",$invoice->get_phone());
        PrintInvoiceDetail($db1,$print, "POLI_TK", $invoice->get_city()." / ".$invoice->get_zipcode());
        PrintInvoiceDetail($db1,$print,"AFM",$invoice->get_afm());
        PrintInvoiceDetail($db1,$print,"DOY",$invoice->get_doy());
        PrintInvoiceDetail($db1,$print,"PROORISMOS",$invoice->get_area());
        PrintInvoiceDetail($db1,$print,"ITEM_CODE",$invoice->get_description()); 
        PrintInvoiceDetail($db1,$print,"ITEM_DESCRIPTION","ΚΑΤΑΧΩΡΗΣΗ ΣΕ ONLINE ΚΑΤΑΛΟΓΟ EPAGELMATIAS.GR");
        PrintInvoiceDetail($db1,$print,"MM","ΤΕΜ");
        PrintInvoiceDetail($db1,$print,"POSOTITA","1");
        $price = $invoice->get_price(); 
        PrintInvoiceDetail($db1,$print,"TIMI_MON",func::nrToCurrency($price)); 
        $discount = $invoice->get_discount(); /////
        PrintInvoiceDetail($db1,$print,"DISCOUNT",func::nrToCurrency($discount));
        $axialine = $price * (1-$discount/100);
        PrintInvoiceDetail($db1,$print,"AXIA_LINE",func::nrToCurrency($axialine));
        //$fpaline = $axialine * config::$fpa / 100; //*******//
        $fpaline = $invoice->get_vat();
        PrintInvoiceDetail($db1,$print,"FPA_LINE",func::nrToCurrency($fpaline));
        PrintInvoiceDetail($db1,$print,"KATH_AXIA_FPA1",func::nrToCurrency($axialine));
        PrintInvoiceDetail($db1,$print,"POSOSTO_FPA1",func::nrToCurrency(config::$fpa));
        PrintInvoiceDetail($db1,$print,"AXIA_FPA1",func::nrToCurrency($fpaline));
        PrintInvoiceDetail($db1,$print,"TOTAL_POSOTITA","1");
        PrintInvoiceDetail($db1,$print,"AXIA_PRO_EKPTOSIS",func::nrToCurrency($price));
        $ekptosi = $price * $discount / 100;
        PrintInvoiceDetail($db1,$print,"TOTAL_EKPTOSI",func::nrToCurrency($ekptosi));
        $axia_meta_ekptosi = $price - $ekptosi;
        PrintInvoiceDetail($db1,$print,"AXIA_META_EKPTOSI",func::nrToCurrency($axia_meta_ekptosi));
        PrintInvoiceDetail($db1,$print,"TOTAL_FPA",func::nrToCurrency($fpaline));
        $telikiaxia = $axia_meta_ekptosi + $fpaline;
        PrintInvoiceDetail($db1,$print,"TELIKI_AXIA",func::nrToCurrency($telikiaxia));        
        PrintInvoiceDetail($db1,$print,"ANTIGRAFO","");
    }


$userid = $_SESSION['user_id'];

$id = $_GET['id'];

$invoice = new INVOICES($db1, $id);
$company = new COMPANIES($db1, $invoice->get_company()) ;



$extra = $invoice->get_seriesCode(); //seira

$extra .= "|".$invoice->get_icode(); //arithos timologiou
        
$datetime = new DateTime; // current time = server time
$otherTZ  = new DateTimeZone('Europe/Athens');
$datetime->setTimezone($otherTZ); // calculates with new TZ now

$myDate = $invoice->get_idate();
$myDate = func::str14toDate($myDate);
$extra .= "|".$myDate; //imerominia
$extra .= "|"; //wra

for ($i=0;$i<1;$i++) {
    $print = new PRINTJOB($db1,0);        
    $printsettings = $db1->getRS("SELECT * FROM PRINTSETTINGS WHERE pfunction LIKE 'company-invoice'");
    $printsetting = new PRINTSETTINGS($db1,$printsettings[0]['id'],$printsettings);        
    $print->set_ptemplate($printsetting->get_template());
    $printername = func::vlookup("printername", "PRINTERS", "id=".$printsetting->get_printer(), $db1);
    $print->set_printername($printername); ///
    $print->set_user($_SESSION['user_id']);

    if ($print->Savedata()) {            
        switch ($i) {
            case 0: $extra2 = $extra."|"."ΓΙΑ ΤΟΝ ΠΕΛΑΤΗ"; //αντιγραφο 1
                break;
            case 1: $extra2 = $extra."|"."ΛΟΓΙΣΤΗΡΙΟ"; //αντιγραφο 2
                break;
            case 2: $extra2 = $extra."|"."ΣΤΕΛΕΧΟΣ"; //αντιγραφο 3
                break;
            default :
                break;
        }
        PrintInvoiceDetails($db1, $print, $company, $extra2, $invoice);
        $msg .= "INVOICE PRINT OK / ";            
    }
    else {
        $msg .= "INVOICE PRINT ERROR / ";
    }
}

echo $msg;