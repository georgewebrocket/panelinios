<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$userid = $_SESSION['user_id'];

$msg = "";

$id = $_GET['id'];
$createLines = FALSE;

$companyid = 0;
if (isset($_GET['company'])) {
    $companyid = $_GET['company'];
}

$sql = "SELECT * FROM TRANSACTIONS WHERE company=? AND transactiontype=1 AND `status`=2 AND (invoiced=0 OR invoiced IS NULL)";
$rs = $db1->getRS($sql, array($companyid));

$vatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);

$invoice = new INVOICEHEADERS($db1, $id);
if ($id>0) {
    $companyid = $invoice->get_company();
}

if ($id==0) {
    $invoice->set_idate(date('Ymd').'000000');
    
    $invoice->set_company($companyid); 
    $company = new COMPANIES($db1, $companyid);
    
    $invoice->set_status(1);
    
    $invoice->set_companyname($company->get_eponimia());
    $profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
    $invoice->set_profession($profession);
    $invoice->set_address($company->get_address());
    $invoice->set_zipcode($company->get_zipcode());
    $city = func::vlookup("description", "EP_CITIES", "id=".$company->get_city_id(), $db1);
    $invoice->set_city($city);
    $area = func::vlookup("description", "AREAS", "id=".$company->get_area(), $db1);
    $invoice->set_area($area);
    $invoice->set_phone($company->get_phone1());
    $invoice->set_afm($company->get_afm());
    $invoice->set_doy($company->get_doy());
    $invoice->set_series(1); ///...
    
    
    
}

if (isset($_GET['save'])) {    
    
    if ($id==0) {
        $invoice->set_series($_POST['c_series']);
        $invoiceseries = new INVOICESERIES($db1, $invoice->get_series());
        $invoiceseries->set_counter($invoiceseries->get_counter()+1); 
        $invoiceseries->Savedata();
        $invoice->set_icode($invoiceseries->get_counter());
        $accesstoken = func::generateRandomString();
        $invoice->set_accesstoken($accesstoken);
        $createLines = TRUE;
    }
    $invoice->set_idate(textbox::getDate($_POST['t_idate'], $locale));    
    $invoice->set_comment($_POST['t_comment']);
    $invoice->set_status($_POST['c_status']);    
    $invoice->set_companyname($_POST['t_companyname']);
    $invoice->set_profession($_POST['t_profession']);
    $invoice->set_address($_POST['t_address']);
    $invoice->set_zipcode($_POST['t_zipcode']);
    $invoice->set_city($_POST['t_city']);
    $invoice->set_area($_POST['t_area']);
    $invoice->set_phone($_POST['t_phone']);
    $invoice->set_afm($_POST['t_afm']);
    $invoice->set_doy($_POST['t_doy']);
    
    /*
    $invoice->set_price(textbox::getCurrency($_POST['t_price'], $locale));
    $invoice->set_discount(textbox::getCurrency($_POST['t_discount'], $locale));
    $invoice->set_amount(textbox::getCurrency($_POST['t_amount'], $locale));
    $invoice->set_vatpercentage(textbox::getCurrency($_POST['t_vatpercentage'], $locale)); 
	
    $amount = textbox::getCurrency($_POST['t_amount'], $locale);
    $vatPercentage = textbox::getCurrency($_POST['t_vatpercentage'], $locale);
    $vat = $amount * $vatPercentage / 100;
    $invoice->set_vat($vat); 
    */    
    //$invoice->set_description($_POST['t_description']);
    
    if ($invoice->Savedata()) {
        $msg = "OK";
    }
    else {
        $msg = "ERROR";
    }    
    $id = $invoice->get_id();
       
    if ($createLines) {        
                
        for ($i = 0; $i < count($rs) && $rs; $i++) {
            $transaction = new TRANSACTIONS($db1, $rs[$i]['id'], $rs);
            
            $invoiceLine = new INVOICES($db1, 0);
            $invoiceLine->set_headerid($id);

            $package = new PACKAGES($db1, $transaction->get_package());
            $invoiceLine->set_description($package->get_description());
            $invoiceLine->set_comment($package->get_comment2());

            $price = $package->get_price();
            $invoiceLine->set_price($price);
            
            $discountAmount = $transaction->get_discount();
            $invoiceLine->set_discount($discountAmount);

            $amount = $price * (100 - $discountAmount)/100;
            $invoiceLine->set_amount($amount);

            $invoiceLine->set_vatpercentage($vatPercentage);

            $vat = $amount * $vatPercentage / 100;
            $invoiceLine->set_vat($vat);

            $invoiceLine->Savedata();
            
            $transaction->set_invoiced(1);
            $transaction->Savedata();
        
        }
        
    }
    
    $myAmount = func::vlookup("SUM(amount)", "INVOICES", "headerid=$id", $db1);
    $myVat = func::vlookup("SUM(vat)", "INVOICES", "headerid=$id", $db1);
    
    $invoice->set_amount($myAmount);
    $invoice->set_vat($myVat);
    $invoice->Savedata();
    
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

    <script>        
        $(function() {
            
            $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 450 });
            
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]); 
            
            $('#closeUpdate').click(function() {
                //console.log('editcompany.php?id=<?php echo $companyid ?>#invoices');
                //window.parent.location.href = 'editcompany.php?id=<?php echo $companyid ?>#invoices';
                //window.parent.location.href = "editcompany.php?id=<?php echo $companyid ?>#invoices";
                //console.log('editcompany.php?id=<?php echo $companyid ?>#invoices');
                window.parent.location.reload(false);
            });
            
            
        });
            
        $("form input").keypress(function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });
            
    </script>
    
    </head>
    
    <body class="form">
        <div class="form-container">
            
            <h1>ΤΙΜΟΛΟΓΙΟ</h1>
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <form action="editInvoice2.php?id=<?php echo $id; ?>&save=1&company=<?php echo $companyid; ?>&<?php echo $ltoken; ?>" method="POST">
                
                <?php
                
                $company = new COMPANIES($db1, $companyid);
                echo "<h2 style=\"padding-left:0px\">ΠΕΛΑΤΗΣ: ".$company->get_companyname()."</h2>";
                
                echo "<div class=\"col-6 col-sm-12\">";
                
                $t_idate = new textbox("t_idate", "ΗΜΕΡ/ΝΙΑ", 
                        $invoice->get_idate(), "DD/MM/YYYY");
                $t_idate->set_format("DATE");
                $t_idate->set_locale($locale);
                $t_idate->get_Textbox();
                
                $c_series = new comboBox("c_series", $db1, "SELECT * FROM INVOICESERIES", 
                        "id", "code", $invoice->get_series(), "ΣΕΙΡΑ");
                $c_series->set_enableNoChoice(FALSE);
                if ($id!=0) {
                    $c_series->set_disabled();
                }
                $c_series->get_comboBox();
                
                $t_icode = new textbox("t_icode", "ΚΩΔ.", $invoice->get_icode(), "");
                $t_icode->set_disabled();
                $t_icode->get_Textbox();
                
                
                $t_comment = new textbox("t_comment", "ΣΧΟΛΙΑ", $invoice->get_comment(), "");
                $t_comment->set_multiline();
                $t_comment->get_Textbox();
                
                $c_status = new comboBox("c_status", $db1, 
                        "SELECT * FROM INVOICE_STATUS", 
                        "id", "description", 
                        $invoice->get_status(), "STATUS");
                $c_status->get_comboBox();
                
                /*                
                $t_description = new textbox("t_description", "ΠΕΡΙΓΡΑΦΗ", $invoice->get_description(), "");
                $t_description->set_multiline();
                $t_description->get_Textbox();
                
                $t_price = new textbox("t_price", "ΤΙΜΗ", $invoice->get_price(), "");
                $t_price->set_format("CURRENCY");
                $t_price->set_locale($locale);
                $t_price->get_Textbox();
                
                $t_discount = new textbox("t_discount", "ΕΚΠΤΩΣΗ", $invoice->get_discount(), "");
                $t_discount->set_format("CURRENCY");
                $t_discount->set_locale($locale);
                $t_discount->get_Textbox();
                
                $t_amount = new textbox("t_amount", "ΑΞΙΑ", $invoice->get_amount(), "");
                $t_amount->set_format("CURRENCY");
                $t_amount->set_locale($locale);
                $t_amount->get_Textbox();
                
                $t_vatpercentage = new textbox("t_vatpercentage", "ΦΠΑ %", $invoice->get_vatpercentage(), "");
                $t_vatpercentage->set_format("CURRENCY");
                $t_vatpercentage->set_locale($locale);
                $t_vatpercentage->get_Textbox();
                
                $t_vat = new textbox("t_vat", "ΦΠΑ", $invoice->get_vat(), "");
                $t_vat->set_format("CURRENCY");
                $t_vat->set_locale($locale);
                $t_vat->get_Textbox();       
                
                $total = round(($invoice->get_amount() + $invoice->get_vat()),2);
                echo "<h2 style=\"padding-left:0px\">ΣΥΝΟΛΟ: $total</h2>";
                */
                echo "</div>";
                
                echo "<div class=\"col-6 col-sm-12\">";
                
                $t_companyname = new textbox("t_companyname", "ΕΠΩΝΥΜΙΑ", $invoice->get_companyname(), "");
                $t_companyname->get_Textbox();
                
                $t_profession = new textbox("t_profession", "ΕΠΑΓΓΕΛΜΑ", $invoice->get_profession(), "");
                $t_profession->get_Textbox();
                
                $t_address = new textbox("t_address", "ΔΙΕΥΘΥΝΣΗ", $invoice->get_address(), "");
                $t_address->get_Textbox();
                
                $t_zipcode = new textbox("t_zipcode", "ΤΚ", $invoice->get_zipcode(), "");
                $t_zipcode->get_Textbox();
                
                $t_city = new textbox("t_city", "ΠΟΛΗ", $invoice->get_city(), "");
                $t_city->get_Textbox();
                
                $t_area = new textbox("t_area", "ΠΡΟΟΡΙΣΜΟΣ", $invoice->get_area(), "");
                $t_area->get_Textbox();
                
                $t_phone = new textbox("t_phone", "ΤΗΛΕΦΩΝΟ", $invoice->get_phone(), "");
                $t_phone->get_Textbox();
                
                $t_afm = new textbox("t_afm", "ΑΦΜ", $invoice->get_afm(), "");
                $t_afm->get_Textbox();
                
                $t_doy = new textbox("t_doy", "ΔΟΥ", $invoice->get_doy(), "");
                $t_doy->get_Textbox();
                
                echo "</div>";
                
                echo '<div style="clear: both"></div>';
                
                
                
                echo '<div style="clear: both;height:10px"></div>';
                ?>
                
                
                <?php
                if ($id>0) {
                    $datagrid = new datagrid("datagrid", $db1, "SELECT * FROM INVOICES WHERE headerid=$id", 
                            array("id","description","amount", "vat" ), 
                            array("ID", "ΑΙΤΙΟΛΟΓΙΑ","ΠΟΣΟ", "ΦΠΑ"), $ltoken);
                    $datagrid->set_edit("editInvoiceLine.php");
                    $datagrid->set_del("delInvoiceLine.php");
                    $datagrid->set_colsFormat(array("","", "CURRENCY","CURRENCY", ""));                
                    $datagrid->get_datagrid();
                
                ?>
                
                
                <br/><br/>
                <a class="fancybox button" href="editInvoiceLine.php?id=0&invoice=<?php echo $id; ?>">Προσθήκη γραμμής</a>
                <br/><br/>
                
                <?php } ?>
                
                <br/><br/>
                
                <h3>ΑΞΙΑ <?php echo $invoice->get_amount(); ?> | ΦΠΑ <?php echo $invoice->get_vat(); ?>
                <?php
                $total = round(($invoice->get_amount() + $invoice->get_vat()),2);
                ?>
                | ΣΥΝΟΛΟ <?php echo $total; ?></h3>
                
                <br/><br/>
                
                <?php
                
                if ($id==0) {
                    if (!$rs) {
                        echo "<h3>Δεν υπάρχουν χρεώσεις που δεν έχουν τιμολογηθεί.</h3>";
                    }
                    else {
                        echo "<h3>Θα τιμολογηθούν οι ακόλουθες χρεώσεις.</h3><ul>";
                        for ($i = 0; $i < count($rs) && $rs; $i++) {
                            $transaction = new TRANSACTIONS($db1, $rs[$i]['id'], $rs);
                            $package = new PACKAGES($db1, $transaction->get_package());
                            echo "<li>".$package->get_description(). " : ";
                            echo func::nrToCurrency($transaction->get_amount()) . " € </li>";
                        }
                        echo "</ul>";
                    }
                    
                    echo "<br/>";
                }
                
                $saveCaption = $id==0? "ΕΠΟΜΕΝΟ": "ΑΠΟΘΗΚΕΥΣΗ";
                $btnOK = new button("btnOK", $saveCaption);
                echo $btnOK->get_button_simple();
                
                ?>
                
                <?php if ($id>0) { ?>
                &nbsp; 
                <div class="button" style="display:inline-block" id="closeUpdate" >CLOSE &amp; UPDATE</div>                
                &nbsp;
                
                    <?php
                $invoicelink = "http://www.epagelmatias.gr/crm/tcpdf/examples/invoicepdf2.php?id=".
                    $invoice->get_id()."&accesstoken=".$invoice->get_accesstoken();
                ?>
                <a target="_blank" href="<?php echo $invoicelink; ?>" class="button" >Preview</a>
                &nbsp; 
                <a href="printInvoice2.php?id=<?php echo $id; ?>" class="button" >Εκτύπωση</a>
                &nbsp; 
                <a href="sendInvoice2.php?id=<?php echo $id; ?>" class="button" >Αποστολή με email</a>
                <?php } ?>
                
                <div style="clear: both"></div>
                
                
                
                
            
            </form>
            
            
        </div>
        
    </body>
    
</html>