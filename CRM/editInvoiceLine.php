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

$vatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);

$invoice = new INVOICES($db1, $id);

$invoiceheaderid = isset($_GET['invoice'])? $_GET['invoice']: $invoice->get_headerid();

if ($id>0) {
    //$companyid = $invoice->get_company();
}

if ($id==0) {
    
    /*$package = new PACKAGES($db1, $company->get_package());
    $invoice->set_description($package->get_description());
    
    $price = $package->get_price();
    $invoice->set_price($price);
    
    $discount = new DISCOUNTS($db1, $company->get_discount());
    $discountAmount = $discount->get_discount();
    $invoice->set_discount($discountAmount);
    
    $amount = $price * (100 - $discountAmount)/100;
    $invoice->set_amount($amount);
    
    $invoice->set_vatpercentage($vatPercentage);
    
    $vat = $amount * $vatPercentage / 100;
    $invoice->set_vat($vat);
    
    $invoice->set_status(1);
    
    */
    
}

if (isset($_GET['save'])) {
    if ($id==0) {
        $invoice->set_headerid($invoiceheaderid);        
    }
    //$invoice->set_idate(textbox::getDate($_POST['t_idate'], $locale));
    $invoice->set_price(textbox::getCurrency($_POST['t_price'], $locale));
    $invoice->set_discount(textbox::getCurrency($_POST['t_discount'], $locale));
    $invoice->set_amount(textbox::getCurrency($_POST['t_amount'], $locale));
    $invoice->set_vatpercentage(textbox::getCurrency($_POST['t_vatpercentage'], $locale)); 	
    $amount = textbox::getCurrency($_POST['t_amount'], $locale);
    $vatPercentage = textbox::getCurrency($_POST['t_vatpercentage'], $locale);
    $vat = $amount * $vatPercentage / 100;
    $invoice->set_vat($vat); 
        
    $invoice->set_description($_POST['t_description']);
    $invoice->set_comment($_POST['t_comment']);
    //$invoice->set_status($_POST['c_status']);
    
    if ($invoice->Savedata()) {
        $msg = "OK";
    }
    else {
        $msg = "ERROR";
    }    
    $id = $invoice->get_id();
    
    $myAmount = func::vlookup("SUM(amount)", "INVOICES", "headerid=$invoiceheaderid", $db1);
    $myVat = func::vlookup("SUM(vat)", "INVOICES", "headerid=$invoiceheaderid", $db1);
    
    $invoiceheader = new INVOICEHEADERS($db1, $invoiceheaderid);
    $invoiceheader->set_amount($myAmount);
    $invoiceheader->set_vat($myVat);
    $invoiceheader->Savedata();
    
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
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);            
            
            
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
            
            <h1>ΓΡΑΜΜΗ ΤΙΜΟΛΟΓΙΟΥ</h1>
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <form action="editInvoiceLine.php?id=<?php echo $id; ?>&save=1&invoice=<?php echo $invoiceheaderid; ?>&<?php echo $ltoken; ?>" method="POST">
                
                <?php
                
                echo "<div class=\"col-12\">";
                
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
                
                echo "</div>";                
                                
                echo '<div style="clear: both"></div>';
                
                $t_comment = new textbox("t_comment", "ΣΧΟΛΙΑ", $invoice->get_comment(), "");
                $t_comment->set_multiline();
                $t_comment->get_Textbox();
                                
                echo '<div style="clear: both;height:10px"></div>';
                
                $btnOK = new button("btnOK", "ΑΠΟΘΗΚΕΥΣΗ");
                echo $btnOK->get_button_simple();
                
                
                ?>
                
                &nbsp; 

                <input onclick="window.parent.location.href = 'editInvoice3.php?id=<?php echo $invoiceheaderid; ?>';" type="button"                      
               value="CLOSE &amp; UPDATE" />
                              
                
                <div style="clear: both"></div>
            
            </form>
            
            
        </div>
        
    </body>
    
</html>