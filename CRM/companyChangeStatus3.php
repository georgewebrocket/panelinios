<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

//require_once('php/session.php');
session_start();
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$suggestedProductCategories = "";

$id = $_GET['company'];
$userid = $_SESSION['user_id'];

if ($userid=="") {
    die("You must login to the application");
}

$company = new COMPANIES($db1,$id);

$status1 = $_GET['status1']!=""? $_GET['status1']: 0;
$status2 = $_GET['status2'];

$comment = $status2==9 || $status2==8 || $status2==16 || $status2==17? "Θα ενημερωθούν όλες οι χρεώσεις που είναι σε εκκρεμότητα": "";

//SUGGESTED PRODUCT CATEGORIES
if ($status2 == 5 || $status2 == 3 || $status2 == 4 || $status2 == 15) { //symfonise // recall // arnitikos
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status = 3"; // get recalls
    $rs = $db1->getRS($sql);
    for ($i = 0; $i < count($rs); $i++) {
        $suggestedProductCategories = func::ConcatSpecial($suggestedProductCategories, 
                "[".$rs[$i]['productcategory']."]", ",");    
    }
}
if ($status2==9) { //plirose
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status = 6"; // get ektyposi
    $rs = $db1->getRS($sql);
    for ($i = 0; $i < count($rs); $i++) {
        $suggestedProductCategories = func::ConcatSpecial($suggestedProductCategories, 
                "[".$rs[$i]['productcategory']."]", ",");    
    }
}
if ($status2==16) { //akyrosi
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status IN (5,6)"; // ektyposi / symfonise
    $rs = $db1->getRS($sql);
    for ($i = 0; $i < count($rs); $i++) {
        $suggestedProductCategories = func::ConcatSpecial($suggestedProductCategories, 
                "[".$rs[$i]['productcategory']."]", ",");    
    }
}
if ($status2==8  || $status2==17) { //epistrofi
    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status = 6"; // ektyposi
    $rs = $db1->getRS($sql);
    for ($i = 0; $i < count($rs); $i++) {
        $suggestedProductCategories = func::ConcatSpecial($suggestedProductCategories, 
                "[".$rs[$i]['productcategory']."]", ",");    
    }
}

$msg = "";
$err=0;
$productCategories = "";

$theVatPercentage = func::vlookup("keyvalue", "SETTINGS", "keycode='VAT'", $db1);

if ($id==0) {
   $err=1;
   $msg = "ERROR";
}

if ($status1==5 && $status2==5) {
   $err=1;
   $msg = "ERROR";
}

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    //session_start();
    $userid = $_SESSION['user_id'];
    
    $productCategories = selectList::getVal("l_productcategories", $_POST);
    if ($productCategories=="") {
       $err=1; 
    }
    
    //if ($status1==$status2) { $err=1;}
    /*if ($status2==3){ //recall
        if ($_POST['txtRecalldate']=="") {$err=1;}
        if ($_POST['cRecalltime']==0) {$err=1;} 
        
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND status=3";
        $myRS = $db1->getRS($sql);
        if ($myRS) {
            for ($i = 0; $i < count($myRS); $i++) {
                if ($myRS[$i]['userid']!=$userid) {
                    echo "Η καταχώρηση έχει δεσμευθεί από άλλον χρήστη<br/>";
                    $err=1;
                }
            }
        }        
    }*/
    //9/1/2024
    
    
    
    if ($err==0) {    
        $action = new ACTIONS($db1, 0);
        $action->set_company($company->get_id());
        $action->set_status1($status1);
        $action->set_status2($status2);
        $action->set_comment($_POST['t_comment']);
        $action->set_user($userid);
        
        $action->set_voucherid(0);
        
        $action->set_product_categories($productCategories);
        
        //βαζω την πρώτη κατηγορία στο πεδιο product_cat
        $arProdCat = explode(",", $productCategories);
        $productCat = str_replace(array("[", "]"), "", $arProdCat[0]);
        $action->set_product_cat($productCat);
        
        $productCategoriesIds = str_replace(array("[","]"), "", $productCategories);
        $productCats = explode(",", $productCategoriesIds);
        //echo $productCategoriesIds;
        $criteriaTransProduct = " package IN (SELECT id FROM PACKAGES WHERE product_category IN ($productCategoriesIds)) ";
        
        if ($action->Savedata()) {        
            if ($status2!=14) { // epikoinwnia(14) den allazei to status
                for ($i = 0; $i < count($productCats); $i++) {
                    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id AND productcategory =" . $productCats[$i];
                    $rs = $db1->getRS($sql);
                    if ($rs) {                    
                        $companyStatus = new COMPANIES_STATUS($db1, $rs[0]['id'], $rs);
                    }
                    else {
                        $companyStatus = new COMPANIES_STATUS($db1, 0);
                        $companyStatus->set_companyid($id);
                        $companyStatus->set_productcategory($productCats[$i]);
                    }
                    $companyStatus->set_status($status2);
                    $companyStatus->set_userid($userid);
                    
                    if ($status2==3){ //RECALL
                        $companyStatus->set_recalldate(textbox::getDate($_POST['txtRecalldate'], $locale));
                        $companyStatus->set_recalltime($_POST['cRecalltime']);
                    }
                    else {
                        $companyStatus->set_recalldate("");
                        $companyStatus->set_recalltime(0);            
                    } 
                    
                    if ($status2==5){ //SYMFONISE
                        $companyStatus->set_last_user5($userid);
                    }
                    
                    $companyStatus->set_csdatetime(date("YmdHis"));
                    
                    $companyStatus->Savedata();
                }
                
                
            }            
            
            if ($company->get_status() == 1) {
                $company->set_status(2);
            }
			
            $lastactiondate = func::vlookup("DATE_FORMAT(MAX(atimestamp),\"%Y%m%d000000\")", 
                "ACTIONS", "company=".$company->get_id(), $db1);
            $company->set_lastactiondate($lastactiondate);
            //???
			
            if ($company->Savedata()) {
                $msg = "Τα δεδομένα αποθηκεύτηκαν με επιτυχία.";
                
                if ($status2 == 4 || $status2 == 15) { //arnitikos
                    $company->set_DeliveryDate("");
                    $company->set_DeliveryTime("");
                    $company->Savedata();
                }
                
                if ($status2 == 5) { // symfwnhse /////////////////                   
                   
                    
                    //create xreosi gia basiko paketo
                    if (strpos($productCategories, "[1]")!==FALSE) {                        
                        //echo "....";
                        //$res = file_get_contents(APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_package()."&user=$userid");

                        // $url = APP_HOST."/createChargeTransaction3.php";
                        // $data = [
                        //     'company' => $id,
                        //     'product' => $company->get_package(),
                        //     'user' => $userid
                        // ];
                        // $res = func::postDataWithCurl($url, $data);
                        // echo $res;
                        header("Location: " . APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_package()."&user=$userid");
                        
                    }
                    
                    //dimioyrgia xreosis gia domain
                    if (strpos($productCategories, "[2]")!==FALSE) {
                        // $res = file_get_contents(APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_package2()."&user=$userid");
                        header("Location: " . APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_package2()."&user=$userid");
                    }                    
                    
                    //dimioyrgia xreosis gia facebook
                    if (strpos($productCategories, "[4]")!==FALSE) {
                        // $res = file_get_contents(APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_fb_package()."&user=$userid");  
                        header("Location: " . APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_fb_package()."&user=$userid");                      
                    }
                    
                    //dimioyrgia xreosis gia google
                    if (strpos($productCategories, "[5]")!==FALSE) {
                        // $res = file_get_contents(APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_ga_package()."&user=$userid"); 
                        header("Location: " . APP_HOST."/createChargeTransaction3.php?company=$id&product=".$company->get_ga_package()."&user=$userid");                       
                    }
                    
                    //update last user status 5 in COMPANIES_STATUS               
                    
                    
                    
                }
                
                if ($status2 == 9) { // plirwse // oti einai se ekkremotita
                    //create eispraksi                    
                    $sql = "SELECT * FROM TRANSACTIONS WHERE status=1 "
                            . "AND transactiontype=1 AND $criteriaTransProduct AND company=".$company->get_id();
                    $rsTrans = $db1->getRS($sql);
                    $amount = 0;
                    //if (count($rsTrans)==1) {
                    for ($i = 0; $i<count($rsTrans) && $rsTrans; $i++) {
                        $cTrans = new TRANSACTIONS($db1, $rsTrans[$i]['id'], $rsTrans);
                        $amount = $cTrans->get_amount() + $cTrans->get_vat();                        
                        $transaction = new TRANSACTIONS($db1, 0);
                        $transaction->set_tdatetime(date('Ymd')."000000");
                        $transaction->set_transactiontype(2);
                        $transaction->set_status(2);
                        $transaction->set_seller(0);
                        $transaction->set_company($company->get_id());                    
                        $transaction->set_package(0);
                        $transaction->set_price(0);
                        $transaction->set_discount(0);
                        $transaction->set_amount($amount);
                        $transaction->set_vat(0);
                        $transaction->set_vatpercentage(0);
                        $transaction->set_payedamount(0);
                        $transaction->set_comment("");
                        $transaction->set_newsales(0);
                        
                        $transaction->set_resell(0);
                        $transaction->set_resend(0);
                        $transaction->set_returned(0);
                        
                        if ($transaction->Savedata()) {
                            $msg .= " / Transaction OK ";
                            //update xreosi
                            $cTrans->set_status(2);
                            $cTrans->set_payedamount($amount);
                            if ($cTrans->Savedata()) {
                                $msg .= " / Transaction OK ";
                            }
                            else {
                                $msg .= " / Transaction ERROR ";
                            }
                        }
                        else {
                            $msg .= " / Transaction ERROR ";
                        }                        
                    }
                    
                    
                    //enimerosi vouchers // status=2
                    $sql = "SELECT * FROM VOUCHERS WHERE (courier_status = 1 OR courier_status = 0 OR courier_status IS NULL) AND customer=".$company->get_id();
                    $rsVouchers = $db1->getRS($sql);
                    for ($i = 0; $i < count($rsVouchers); $i++) {
                        $voucher = new VOUCHERS($db1, $rsVouchers[$i]['id'], $rsVouchers);
                        $voucher->set_courier_status(2);
                        $voucher->Savedata();
                    }
                    
                    
                    $company->set_DeliveryDate("");
                    $company->set_DeliveryTime("");
                    $company->Savedata();
                    
                }
                
                if ($status2 == 8  || $status2==17) { // epistrofi
                    $sql = "SELECT * FROM TRANSACTIONS WHERE status=1 "
                            . "AND transactiontype=1 AND $criteriaTransProduct AND company=".$company->get_id();
                    $rsTrans = $db1->getRS($sql);
                    $amount = 0;
                    for ($i = 0; $i<count($rsTrans) && $rsTrans; $i++) {
                        $cTrans = new TRANSACTIONS($db1, $rsTrans[$i]['id'], $rsTrans);
                        $cTrans->set_status(3);
                        $cTrans->set_returned(1);
                        if ($cTrans->Savedata()) {
                            $msg .= " / Transaction OK ";
                        }
                        else {
                            $msg .= " / Transaction ERROR ";
                        }                        
                    }
                    
                    //enimerosi vouchers // status=3
                    $sql = "SELECT * FROM VOUCHERS WHERE (courier_status = 1 OR courier_status = 0 OR courier_status IS NULL) AND customer=".$company->get_id();
                    $rsVouchers = $db1->getRS($sql);
                    for ($i = 0; $i < count($rsVouchers); $i++) {
                        $voucher = new VOUCHERS($db1, $rsVouchers[$i]['id'], $rsVouchers);
                        $voucher->set_courier_status(3);
                        $voucher->Savedata();
                    }
                    
                    $company->set_DeliveryDate("");
                    $company->set_DeliveryTime("");
                    $company->Savedata();
                    
                }
                
                if ($status2 == 16) { // ΑΚΥΡΩΣΗ
                    $sql = "SELECT * FROM TRANSACTIONS WHERE status=1 "
                            . "AND transactiontype=1 AND $criteriaTransProduct AND company=".$company->get_id();
                    $rsTrans = $db1->getRS($sql);
                    $amount = 0;
                    for ($i = 0; $i<count($rsTrans) && $rsTrans; $i++) {
                        $cTrans = new TRANSACTIONS($db1, $rsTrans[$i]['id'], $rsTrans);
                        $cTrans->set_status(3);
                        $cTrans->set_returned(0);
                        if ($cTrans->Savedata()) {
                            $msg .= " / Transaction OK ";
                        }
                        else {
                            $msg .= " / Transaction ERROR ";
                        }                        
                    }
                    
                    $company->set_DeliveryDate("");
                    $company->set_DeliveryTime("");
                    $company->Savedata();
                    
                }
                
                if ($status2!=3 && $status2!=14) { //allagi status, oxi recall or epikoinonia
                    $company->set_for_renewal(0);
                    $company->Savedata();
                }
                
                include "updateCompanyData.php";
                
            }
            else {
                $msg = $lg->l("error");
            }
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
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />

    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);

            $('#l_productcategories input[type="checkbox"]').click(function() {
                $('#l_productcategories input[type="checkbox"]').not(this).prop('checked', false);
            });

        });
            
        function UnlockCompany(id) {
            var myURL = 'unlockcompany.php?id=' + id;
                $.ajax({
                   url: myURL,
                   success: function(data) {
                        //window.parent.location.reload(false); 
                        window.location.href = 'editcompany.php?id=' + id;
                   }
                   });            
        }

    </script>
    
    <style>
            
        #l_productcategories {
            margin-top: 10px;
            margin-bottom: 10px;
            width: 60%;

        }
        #l_productcategories div.col-10, #l_productcategories div.col-2 {
            border-top: 1px dotted rgb(200,200,200);
            padding-top: 5px;
            padding-bottom: 5px;
        }


    </style>
    
    </head>
    
    
    <body class="form" style="background-color: #ddd;">
        
        <div class="form-container" style="max-width:1000px; margin: auto; padding: 20px;">
        
            <h1><?php echo $l->l("change-status"); ?></h1>
            
            <?php
            
            if ($status2==5) {
                //check delivery date
                $deliveryDate = $company->get_DeliveryDate();
                if ($deliveryDate<date("YmdHis")) {
                    echo "<h2 style=\"color:red\">Η ημερομηνία παράδοσης δεν είναι σωστή</h2>";
                }
                
                if ($company->get_courier_address()!='' 
                        || $company->get_courier_zipcode()!=''
                        || $company->get_courier_city()!=0
                        || $company->get_courier_region()!='') {
                    echo "<h2 style=\"color:red\">Παράδοση σε εναλλακτική διεύθυνση</h2>";
                }
            }
            
            
            if ($msg!="") { 
                
                echo "<h2 class=\"msg\">".$msg."</h2><div style=\"padding:20px\">";
                
                
                if ($company->get_lockedbyuser()==1 && $company->get_lockuser()==$_SESSION['user_id']) {
                    $btnCloseUpdate = new button("btnCloseUpdate", "Κλείσιμο και ενημέρωση+", "");
                    $btnCloseUpdate->set_method("UnlockCompany($id)");
                    $btnCloseUpdate->get_button_simple();
                }
                else {
                    //$btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "close-update");
                    echo "<a class=\"button\" href=\"editcompany.php?id=$id\">Κλείσιμο και ενημέρωση</a>";
                }
                //$btnCloseUpdate->get_button_simple();
                
                
                echo "</div>";
            } 
            else {
            ?>                
                
                <form id="form1" action="companyChangeStatus3.php?company=<?php echo $company->get_id(); ?>&confirm=1&status1=<?php echo $status1; ?>&status2=<?php echo $status2; ?>" method="POST">

                    <h3><?php echo "Εταιρεία: ".$company->get_companyname(); ?></h3>

                    <?php 
                    $status2description = func::vlookup("altdescription", "STATUS", "id=".$status2, $db1);
                    ?>

                    <h3><?php echo "Νέο status: <span style=\"background-color: #ff9; padding:3px 10px\">". $status2description . "</span>"; ?></h3>

                    <?php                    
                    
                    $l_productcategories = new selectList("l_productcategories", 
                            "PRODUCT_CATEGORIES", $suggestedProductCategories, $db1);
                    $l_productcategories->set_descrField("description");
                    $l_productcategories->set_orderby("id");
                    $l_productcategories->set_label("Κατηγορίες προϊόντων");
                    $l_productcategories->getList();
                    
                    
                    
                    if ($status2==3){ //RECALL
                        $txtRecalldate = new textbox("txtRecalldate", $l->l('recall-date'),"", "ΗΗ/ΜΜ/ΕΕΕΕ");
                        $txtRecalldate->set_format("DATE");
                        $txtRecalldate->set_locale($locale);
                        $txtRecalldate->get_Textbox();

                        $cRecalltime = new comboBox("cRecalltime", $db1, 
                                "SELECT id, description FROM TIMES ORDER BY description", 
                                "id","description",
                                0,$l->l("recall-time"));
                        $cRecalltime->get_comboBox();
                    }
                    
                    $t_comment = new textbox("t_comment", "ΣΧΟΛΙΑ", "", ""); 
                    $t_comment->set_multiline();
                    $t_comment->get_Textbox();
                    
                    
                    
                    ?>
                    
                    
                    
                    
                    <?php
                    //$btnOK = new button("BtnOk", $lg->l('confirm'));
                    //$btnOK->get_button();
                    
                    
                    ?>
                    
                    <div class="col-4"></div>
                    <div class="col-8">
                        <input onclick="this.disabled=true;this.value='Submitting...'; this.form.submit();" type="submit" value="Confirm" name="BtkOK" />
                    </div>
                    
                    
                    
                    <div class="clear"></div>
                    
                    <?php 
                    //echo $comment;
                    ?>
                    
                    <div class="clear"></div>

                </form>
            
            <?php } ?>
            
            <div style="padding-left:20px; padding-top:20px">
            <a class="button" href="editcompany.php?id=<?php echo $id; ?>">Επιστροφή στην καρτέλα εταιρείας</a>
            
            <?php if ($_SESSION['user_profile']>1 && $status2==9) { ?>
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
        
        <div style="clear:both; height: 30px"></div>
        
        <div style="position: fixed; top:20px; right:20px;font-size: 30px;">
            <a href="editcompany.php?id=<?php echo $id; ?>">
                <span class="fa fa-close"></span>
            </a>
        </div>
        
        <script>
            
            /*var btnClicked = false;
            
            $(function() {
                $("#BtnOk").click(function(e) {
                    e.preventDefault();
                    if (!btnClicked) {                        
                        console.log("Please wait...");
                        $("#form1").submit();
                        btnClicked = true;
                    }
                    $(this).hide();
                });          

            });*/
    
            // jQuery plugin to prevent double submission of forms
            /*jQuery.fn.preventDoubleSubmission = function() {
              $(this).on('submit',function(e){
                var $form = $(this);

                if ($form.data('submitted') === true) {
                  // Previously submitted - don't submit again
                  e.preventDefault();
                } else {
                  // Mark it so that the next submit can be ignored
                  $form.data('submitted', true);
                }
              });

              // Keep chainability
              return this;
            };
            
            $(function() {
                $('form').preventDoubleSubmission();
            });*/
    
            /*$(function()
            {
              $('#form1').submit(function(){
                $("input[type='submit']", this)
                  .val("Please Wait...")
                  .attr('disabled', 'disabled');
                return true;
              });
            });*/
    
            $(function()
            {
              $('#BtnOk').on('click',function()
              {
                /*$(this).val('Please wait ...')
                  .attr('disabled','disabled');
                $('#form1').submit();*/
                //this.disabled=true;this.value='Submitting...'; this.form.submit();
              });

            });

            
        
        <script>
        
    </body>
    
</html>