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
if (isset($_GET['company'])) {
    $companyid = $_GET['company'];
}

$voucher = new VOUCHERS($db1, $id);
if ($id>0) {
    $companyid = $voucher->get_customer();
}

$company = new COMPANIES($db1, $companyid);

if ($id==0) {
    $voucher->set_vdate(date('Ymd')."000000");
    $voucher->set_courier($company->get_courier());
    
    //voucher code
    $courier = new COURIER($db1, $company->get_courier());
    $vouchercode = $courier->get_vouchercount();
    $vouchercode++;
    $courier->set_vouchercount($vouchercode);
    $courier->Savedata();
    $voucher->set_vcode($vouchercode); ///
    $company->set_voucherid($vouchercode); ////
    $company->Savedata();
    
    $finalprice = 0;
    $sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype = 1 AND `status`=1 AND company= " . $company->get_id();
    $rsTrans = $db1->getRS($sql);
    for ($i = 0; $i < count($rsTrans); $i++) {
        $finalprice += $rsTrans[$i]['amount'] + $rsTrans[$i]['vat'];    
    }    
    //$finalprice = func::format($finalprice, "CURRENCY");
    
    $voucher->set_amount($finalprice);
    $voucher->set_customer($companyid);    
    $voucher->set_deliverydate($company->get_DeliveryDate());
    $voucher->set_deliverynotes($company->get_DeliveryNotes());
    $voucher->set_deliverytime($company->get_DeliveryTime());
    $voucher->set_courier_ok(0);
    $voucher->set_courier_notes("");
    $voucher->set_courier_delivery_date("");
    $voucher->set_courier_return(0);
    $voucher->set_courier_status(0);
    
    $voucher->set_export_to_excel(checkbox::getVal2($_POST, "chk_export_to_excel"));
    $voucher->set_exported_to_excel(checkbox::getVal2($_POST, "chk_exported_to_excel"));
    
    $voucher->set_vcode2($_POST['t_vcode2']);
    $voucher->set_deliverynotes($_POST['t_deliverynotes']);
    
    $voucher->set_customername($company->get_companyname());
    
    
    $mySql = "SELECT company, 
        SUBSTRING_INDEX(GROUP_CONCAT(user ORDER BY atimestamp DESC), ',', 1) 
        AS user5 ,    
        GROUP_CONCAT(user ORDER BY atimestamp DESC),
        GROUP_CONCAT(atimestamp ORDER BY atimestamp DESC)
        FROM ACTIONS WHERE status2=5 AND company=? GROUP BY `company`";
    $myRS = $db1->getRS($mySql, array($company->get_id()));
    $voucher->set_userid($myRS[0]['user5']);
    
    
    $voucher->Savedata();
    $id = $voucher->get_id();
    
}

if (isset($_GET['save'])) {
    
    $voucher->set_amount(textbox::getCurrency($_POST['t_amount'], $locale));
    $voucher->set_deliverydate(textbox::getDate($_POST['t_deliverydate'], $locale));
    $voucher->set_deliverytime($_POST['t_deliverytime']);
    ////
    
    $voucher->set_export_to_excel(checkbox::getVal2($_POST, "chk_export_to_excel"));
    $voucher->set_exported_to_excel(checkbox::getVal2($_POST, "chk_exported_to_excel"));
    
    $voucher->set_vcode2($_POST['t_vcode2']);
    $voucher->set_deliverynotes($_POST['t_deliverynotes']);
    
    $voucher->set_courier_status($_POST['c_courier_status']);
    //c_courier_status
    
    if ($voucher->Savedata()) {
        $msg = "OK";
    }
    else {
        $msg = "ERROR";
    }    
    $id = $voucher->get_id();
    
    
    
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
            
            <h1>VOUCHER</h1>
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <form action="editVoucher.php?id=<?php echo $id; ?>&save=1&company=<?php echo $companyid; ?>
                  &<?php echo $ltoken; ?>" method="POST">
                
                <?php
                
                echo "<h2 style=\"padding-left:0px\">ΠΕΛΑΤΗΣ: ".$company->get_companyname()."</h2>";
                
                $t_vdate = new textbox("t_vdate", "ΗΜΕΡ/ΝΙΑ", 
                        $voucher->get_vdate(), "DD/MM/YYYY");
                $t_vdate->set_format("DATE");
                $t_vdate->set_locale($locale);
                $t_vdate->get_Textbox();
                
                $t_vcode = new textbox("t_vcode", "ΚΩΔ.", $voucher->get_vcode(), "");
                $t_vcode->set_disabled();
                $t_vcode->get_Textbox();
                
                $t_amount = new textbox("t_amount", "ΠΟΣΟ ΑΝΤΙΚ.", $voucher->get_amount(), "");
                $t_amount->set_format("CURRENCY");
                $t_amount->set_locale($locale);
                $t_amount->get_Textbox();
                
                $c_courier = new comboBox("c_courier", $db1, 
                        "SELECT * FROM COURIER", "id", "description", 
                        $voucher->get_courier(), "COURIER");
                $c_courier->set_disabled();
                $c_courier->get_comboBox();
                
                $c_courier_status = new comboBox("c_courier_status", $db1, 
                        "SELECT * FROM COURIERSTATUS", "id", "description", 
                        $voucher->get_courier_status(), "COURIER STATUS");
                $c_courier_status->get_comboBox();
                
                $t_deliverydate = new textbox("t_deliverydate", "ΗΜΕΡ. ΠΑΡΑΔ.", 
                        $voucher->get_deliverydate(), "DD/MM/YYYY");
                $t_deliverydate->set_format("DATE");
                $t_deliverydate->set_locale($locale);
                $t_deliverydate->get_Textbox();
                
                $t_deliverytime = new textbox("t_deliverytime", "ΩΡΑ ΠΑΡΑΔ.", $voucher->get_deliverytime(), "");
                $t_deliverytime->get_Textbox();
                
                $chk_export_to_excel = new checkbox("chk_export_to_excel", "Export to EXCEL", $voucher->get_export_to_excel());
                $chk_export_to_excel->get_Checkbox();
                
                $chk_exported_to_excel = new checkbox("chk_exported_to_excel", "Exported to EXCEL", $voucher->get_exported_to_excel());
                $chk_exported_to_excel->get_Checkbox();
                
                $t_vcode2 = new textbox("t_vcode2", "ΚΩΔ. #2", $voucher->get_vcode2(), "");
                $t_vcode2->get_Textbox();
                
                $t_deliverynotes = new textbox("t_deliverynotes", "ΣΧΟΛΙΑ ΠΑΡΑΔΟΣΗΣ", $voucher->get_deliverynotes(), "");
                $t_deliverynotes->set_multiline();
                $t_deliverynotes->get_Textbox();
                
                echo "<div style=\"clear:both;height:1em\"></div>";
                $btnOK = new button("btnOK", "ΑΠΟΘΗΚΕΥΣΗ");
                echo $btnOK->get_button_simple();
                
                ?>
                
                &nbsp; 
                <input onclick="window.parent.location.href = 'editcompany.php?id=<?php echo $companyid ?>#invoices';" type="button"                      
               value="CLOSE &amp; UPDATE" />                
                &nbsp; 
                <a href="printVoucher.php?id=<?php echo $id; ?>" class="button" style="background-color: gray">Εκτύπωση</a>
                &nbsp; 
                
                <div style="clear: both"></div>
            
            
            </form>
            
        </div>
        
    </body>
    
</html>
                