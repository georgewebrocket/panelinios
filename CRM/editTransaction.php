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
$companyid = 0;
$transactionType = 0;
if (isset($_GET['company'])) {
    $companyid = $_GET['company'];
}
if (isset($_GET['type'])) {
    $transactionType = $_GET['type'];
}

$transaction = new TRANSACTIONS($db1, $id);

if ($id>0) {
    $companyid = $transaction->get_company();
    $transactionType = $transaction->get_transactiontype();
}
if ($id==0) {
    $transaction->set_tdatetime(date("Ymd")."000000");
    $transaction->set_transactiontype($transactionType);
    if ($transaction->get_transactiontype()==1) {
        $transaction->set_status(1);
    }
    else {
        $transaction->set_status(2);
    }
    $transaction->set_seller($userid);
    
    
    
}


if (isset($_GET['save'])) {
    if ($id==0) {
        $transaction->set_company($companyid);
        $transaction->set_transactiontype($transactionType); 
        $transaction->set_resell(0);
        $transaction->set_resend(0);
        $transaction->set_returned(0);
    }
    $transaction->set_tdatetime(textbox::getDate($_POST['t_tdatetime'], $locale));
    
    $theVatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);
    
    $theDiscount = textbox::getCurrency($_POST['t_discount'], $locale);
    
    ///if (isset($_POST['c_package']) && $transaction->get_package()>0) {
    if (isset($_POST['c_package']) && $_POST['c_package']>0) {
        $thePackage = $_POST['c_package'];
        $transaction->set_package($thePackage); 
        $thePrice = func::vlookup("price", "PACKAGES", "id=$thePackage", $db1);
        $transaction->set_price($thePrice);
        //$theDiscountId = $_POST['c_discount'];
        //$theDiscount = func::vlookup("discount", "DISCOUNTS", "id=$theDiscountId", $db1);        
        //$theDiscount = $_POST['t_discount'];
        $transaction->set_discount($theDiscount);
        if ($thePrice>0) {
            $theAmount = $thePrice * (100-$theDiscount)/100;
        }
        else {
            $theAmount = textbox::getCurrency($_POST['t_amount'], $locale);
        }
        $transaction->set_amount($theAmount);
        $theVat = $theAmount * $theVatPercentage / 100;
        $transaction->set_vat($theVat);
        $transaction->set_vatpercentage($theVatPercentage);
    }
    elseif (isset($_POST['t_price']) && $_POST['t_price']>0) {
        $transaction->set_package(0); 
        $thePrice = textbox::getCurrency($_POST['t_price'], $locale);
        $transaction->set_price($thePrice);
        //$theDiscountId = $_POST['c_discount'];
        //$theDiscount = func::vlookup("discount", "DISCOUNTS", "id=$theDiscountId", $db1);
        //$transaction->set_discount($theDiscount);        
        //$theDiscount = $_POST['t_discount'];
        $transaction->set_discount($theDiscount);
        
        $theAmount = $thePrice * (100-$theDiscount)/100;
        $transaction->set_amount($theAmount);
        $theVat = $theAmount * $theVatPercentage / 100;
        $transaction->set_vat($theVat);
        $transaction->set_vatpercentage($theVatPercentage);
    }
    else {
        $transaction->set_package(0);
        $transaction->set_price(0);
        $transaction->set_discount(0);
        $transaction->set_amount(textbox::getCurrency($_POST['t_amount'], $locale));
        $theVat = $theAmount * $theVatPercentage / 100;
        $transaction->set_vat($theVat);
        $transaction->set_vatpercentage($theVatPercentage);
    }
        
//    if (isset($_POST['t_vat'])) {
//        $transaction->set_vat(textbox::getCurrency($_POST['t_vat'], $locale));
//    }
//    else {
//        $transaction->set_vat(0);
//    }
    
//    if (isset($_POST['c_discount'])) {
//        $theDiscount = $_POST['c_discount'];
//        $transaction->set_discount($theDiscount);
//    }
//    else {
//        $transaction->set_discount(0);
//    }
    
//    if (isset($_POST['t_price']) && $transaction->get_package()==0) {
//        $transaction->set_price(textbox::getCurrency($_POST['t_price'], $locale));
//    }
//    else {
//        $transaction->set_price(0);
//    }
    
    if (isset($_POST['t_description'])) {
    $transaction->set_description($_POST['t_description']);
    }
    
    
    if (isset($_POST['c_seller'])) {
        $transaction->set_seller($_POST['c_seller']);
    }
    else {
        $transaction->set_seller(0);
    }
    if (isset($_POST['t_payedamount'])) {
        $transaction->set_payedamount(textbox::getCurrency($_POST['t_payedamount'], $locale));
    }
    else {
        $transaction->set_payedamount(0);
    }
    
    $transaction->set_comment($_POST['t_comment']);
    
    if (isset($_POST['c_status'])) {
        $transaction->set_status($_POST['c_status']);
    }
    else {
        $transaction->set_status(2); // finished eispraksi
    }
    
    $transaction->set_invoiced(checkbox::getVal2($_POST, "chk_invoiced"));       
    
    if ($transaction->Savedata()) {
        $msg = "OK";
    }
    else {
        $msg = "ERROR";
    }
    
    $id = $transaction->get_id();
    
    //ananeosi
    if ($transaction->get_transactiontype()==1) {
        include('transResellResend.php');
        
        /*$tdate = $transaction->get_tdatetime();
        $tdateDate = func::str14toDate($tdate, "-", "EN")." 23:59:59";
        $prev = func::vlookup("COUNT(id)", "TRANSACTIONS", 
                "company=$companyid AND tdatetime<'$tdate'", $db1);
        
        $sqlResend = "SELECT * FROM ACTIONS WHERE company=$companyid AND atimestamp<'$tdateDate' ORDER BY atimestamp DESC";
        $rsResend = $db1->getRS($sqlResend);
        $actionOK = 0; $actionCancel = 0; $resend = 0;
        for ($i=0;$i<count($rsResend);$i++) {
            if ($rsResend[$i]['status2']==5) {
                $actionOK++;
            }
            if ($rsResend[$i]['status2']==8) {
                $actionCancel++;
            }
            if ($actionOK==1 && $actionCancel==1) {
                $resend = 1;
                break;
            }            
        }        
        
        if ($prev>0 || $resend==1) {
            if ($prev>0) {
                $transaction->set_resell(1);
            }
            $transaction->set_resend($resend);
            if (!$transaction->Savedata()) {
                $msg = "ERROR";
            }
        }*/
    }


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
            <?php if ($transactionType==1) { ?>
            <h1>ΧΡΕΩΣΗ ΠΕΛΑΤΗ</h1> 
            <?php } ?>
            <?php if ($transactionType==2) { ?>
            <h1>ΕΙΣΠΡΑΞΗ</h1>
            <?php } ?>
            <?php if ($transactionType==3) { ?>
            <h1>ΠΙΣΤΩΣΗ ΠΕΛΑΤΗ</h1>
            <?php } ?>
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <form action="editTransaction.php?id=<?php echo $id; ?>&save=1&company=<?php echo $companyid; ?>&type=<?php echo $transactionType; ?>&<?php echo $ltoken; ?>" method="POST">
            
                <?php
                
                $t_tdatetime = new textbox("t_tdatetime", "ΗΜΕΡ/ΝΙΑ", 
                        $transaction->get_tdatetime(), "DD/MM/YYYY");
                $t_tdatetime->set_format("DATE");
                $t_tdatetime->set_locale($locale);
                $t_tdatetime->get_Textbox();
                
                if ($transactionType==1) {
                    $c_package = new comboBox("c_package", $db1, 
                        "SELECT id, CONCAT(description,'-',price,' €') as descprice FROM PACKAGES WHERE active=1",   
                        "id","descprice",
                        $transaction->get_package(),"PACKAGE");                    
                    $c_package->get_comboBox();

                    $t_description = new textbox("t_description", "ΠΕΡΙΓΡΑΦΗ",
                            $transaction->get_description(), "");
                    $t_description->get_Textbox();                

                    $t_price = new textbox("t_price", "ΤΙΜΗ",
                            $transaction->get_price(), "");
                    $t_price->set_format("CURRENCY");
                    $t_price->set_locale($locale);
                    $t_price->get_Textbox();
                    
                    $t_discount = new textbox("t_discount", "ΕΚΠΤΩΣΗ %",
                            $transaction->get_discount(), "");
                    $t_discount->set_format("CURRENCY");
                    $t_discount->set_locale($locale);
                    $t_discount->get_Textbox(); 
                    
                    /*
                    $c_discount = new comboBox("c_discount", $db1, 
                            "SELECT id, CONCAT(discount,' %') as MyDiscount FROM DISCOUNTS", 
                            "id","MyDiscount",
                            $transaction->get_discount(),"ΕΚΠΤΩΣΗ");
                    $c_discount->get_comboBox();*/
                    
                }            
                
                
                $t_amount = new textbox("t_amount", "ΠΟΣΟ",
                        $transaction->get_amount(), "");
                $t_amount->set_format("CURRENCY");
                $t_amount->set_locale($locale);
                $t_amount->get_Textbox();
                
                if ($transactionType==1) {
                    $t_vat = new textbox("t_vat", "ΦΠΑ",
                            $transaction->get_vat(), "");
                    $t_vat->set_format("CURRENCY");
                    $t_vat->set_locale($locale);
                    $t_vat->get_Textbox();

                    $t_payedamount = new textbox("t_payedamount", "ΕΞΩΦΛ.",
                            $transaction->get_payedamount(), "");
                    $t_payedamount->set_format("CURRENCY");
                    $t_payedamount->set_locale($locale);
                    $t_payedamount->get_Textbox();                
                
                    $c_status = new comboBox("c_status", $db1, 
                            "SELECT id, description FROM TRANSACTION_STATUS", 
                            "id","description",
                            $transaction->get_status(),"STATUS");
                    $c_status->get_comboBox();

                    $c_seller = new comboBox("c_seller", $db1, 
                            "SELECT id, fullname FROM USERS", 
                            "id","fullname",
                            $transaction->get_seller(),"ΠΩΛΗΤΗΣ");
                    $c_seller->get_comboBox();
                }
                
                $chk_invoiced = new checkbox("chk_invoiced", "ΕΧΕΙ ΕΚΔΟΘΕΙ ΤΙΜ.", $transaction->get_invoiced());
                $chk_invoiced->get_Checkbox();
                
                $t_comment = new textbox("t_comment", "ΣΧΟΛΙΑ",
                        $transaction->get_comment(), "");
                $t_comment->set_multiline();
                $t_comment->get_Textbox();
                
                $btnOK = new button("btnOK", "ΑΠΟΘΗΚΕΥΣΗ");
                echo $btnOK->get_button_simple();
                
                echo "&nbsp;";
                
                $btnCloseUpdate = new button("btnCloseUpdate", "CLOSE &amp; UPDATE", "close-update");
                echo $btnCloseUpdate->get_button_simple();
                
                ?>
                
                <div style="clear:both"></div>
            
            </form>
            
        </div>