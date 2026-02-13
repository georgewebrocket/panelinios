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
        
        $sql = "SELECT * FROM INVOICES WHERE headerid=?";
        $rs = $db1->getRS($sql, array($invoice->get_id()));
        $invoiceLines = "";
        
        $subtotalAxiaLine = 0;
        $subtotalFpaPercentage = 0;
        $subtotalFpa = 0;        
        $totalPosotita = 0;
        $totalAxiaProEkptosi = 0;
        $totalEkptosi = 0;
        $totalAxiaMetaEkptosi = 0;
        $totalFpa = 0;
        $totalTelikiAxia = 0;
        
        for ($i = 0; $i < count($rs) && $rs; $i++) {
            $invoiceLine = new INVOICES($db1, $rs[$i]['id'], $rs);
            
            $invoiceLines .= $invoiceLine->get_description()."<td>";
            $invoiceLines .= $invoiceLine->get_comment()."<td>";
            $invoiceLines .= "ΤΕΜ<td>";
            $invoiceLines .= "1<td>";
            $price = $invoiceLine->get_price();
            $invoiceLines .= func::nrToCurrency($price) . "<td>";
            $discount = $invoiceLine->get_discount();
            $invoiceLines .= func::nrToCurrency($discount) . "<td>";
            $axialine = $price * (1-$discount/100);            
            $invoiceLines .= func::nrToCurrency($axialine) . "<td>";
            $fpaline = $invoiceLine->get_vatpercentage();            
            $invoiceLines .= func::nrToCurrency($fpaline);
            
            
            $subtotalAxiaLine += $axialine;
            $subtotalFpaPercentage = $fpaline;
            $subtotalFpa += $invoiceLine->get_vat();
            
            $totalPosotita++;
            $totalAxiaProEkptosi += $price;
            $ekptosi = $price * $discount / 100;
            $totalEkptosi += $ekptosi;
            $totalAxiaMetaEkptosi += $axialine;
            $totalFpa += $invoiceLine->get_vat();
            $totalTelikiAxia += $axialine + $invoiceLine->get_vat();
            
            if ($i < count($rs) - 1) {
                $invoiceLines .= "<tr>";
            }
        }
        PrintInvoiceDetail($db1,$print,"INVOICE_LINES",$invoiceLines);
        
        PrintInvoiceDetail($db1,$print,"KATH_AXIA_FPA1",func::nrToCurrency($subtotalAxiaLine));
        PrintInvoiceDetail($db1,$print,"POSOSTO_FPA1",func::nrToCurrency($subtotalFpaPercentage));
        PrintInvoiceDetail($db1,$print,"AXIA_FPA1",func::nrToCurrency($subtotalFpa));
        
        PrintInvoiceDetail($db1,$print,"TOTAL_POSOTITA",$totalPosotita);
        PrintInvoiceDetail($db1,$print,"AXIA_PRO_EKPTOSIS",func::nrToCurrency($totalAxiaProEkptosi));
        PrintInvoiceDetail($db1,$print,"TOTAL_EKPTOSI",func::nrToCurrency($totalEkptosi));
        PrintInvoiceDetail($db1,$print,"AXIA_META_EKPTOSI",func::nrToCurrency($totalAxiaMetaEkptosi));
        PrintInvoiceDetail($db1,$print,"TOTAL_FPA",func::nrToCurrency($totalFpa));
        PrintInvoiceDetail($db1,$print,"TELIKI_AXIA",func::nrToCurrency($totalTelikiAxia));

    }


$userid = isset($_SESSION['user_id'])? $_SESSION['user_id']: $_GET['user_id'];

$id = $_GET['id'];

$invoice = new INVOICEHEADERS($db1, $id);
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
        //ektypwnetai mono ena antigrafo
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