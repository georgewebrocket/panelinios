<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$id = $_GET['company'];

$userid = $_SESSION['user_id'];

$company = new COMPANIES($db1,$id);

$status1 = $_GET['status1'];
$status2 = $_GET['status2'];

$msg = "";
$err=0;

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
    
    //if ($status1==$status2) { $err=1;}
    if ($status2==3){
        if ($_POST['txtRecalldate']=="") {$err=1;}
        if ($_POST['cRecalltime']==0) {$err=1;}        
    }
    
    if ($err==0) {    
        $action = new ACTIONS($db1, 0);
        $action->set_company($company->get_id());
        $action->set_status1($status1);
        $action->set_status2($status2);
        $action->set_comment($_POST['t_comment']);

        $action->set_user($userid);

        if ($action->Savedata()) {        
            if ($status2!=14) {
                $company->set_status($status2);
                $company->set_user($userid);
            }
            //$company->set_comment($_POST['txtComment']);
            
            if ($status2==3){ //RECALL
                $company->set_recalldate(textbox::getDate($_POST['txtRecalldate'], $locale));
                $company->set_recalltime($_POST['cRecalltime']);
            }
            else {
                $company->set_recalldate("");
                $company->set_recalltime(0);            
            }
			
			$lastactiondate = func::vlookup("DATE_FORMAT(MAX(atimestamp),\"%Y%m%d000000\")", 
                "ACTIONS", "company=".$company->get_id(), $db1);
            $company->set_lastactiondate($lastactiondate);
			
            if ($company->Savedata()) {
                $msg = $lg->l("save-ok");
                
                if ($status2 == 5) {
                    //create xreosi
                    $transaction = new TRANSACTIONS($db1, 0);
                    $transaction->set_tdatetime(date('Ymd')."000000");
                    $transaction->set_transactiontype(1);
                    $transaction->set_status(1);
                    $transaction->set_seller($userid);
                    $transaction->set_company($company->get_id());                    
                    $transaction->set_package($company->get_package());
                    $package = new PACKAGES($db1, $company->get_package());
                    $transaction->set_price($package->get_price());
                    $discount = new DISCOUNTS($db1, $company->get_discount());
                    $transaction->set_discount($discount->get_discount());
                    $amount = $package->get_price() * (100-$discount->get_discount())/100;
                    $transaction->set_amount($amount);
                    $vat = $amount * $theVatPercentage / 100;
                    $transaction->set_vat($vat);
                    $transaction->set_vatpercentage($theVatPercentage);
                    $transaction->set_payedamount(0);
                    $transaction->set_comment("");
                    $newsales = 0;
                    $salesCount = func::vlookup("count(id) AS MyCount", "TRANSACTIONS", 
                            "company=".$company->get_id(), $db1);
                    if ($salesCount>0) {
                        $newsales = 1;
                    }
                    $transaction->set_newsales($newsales);
                    
                    $transaction->set_resell(0);
                    $transaction->set_resend(0);
                    
                    if ($transaction->Savedata()) {
                        $msg .= " / Transaction OK ";
                        $companyid = $id;
                        include('transResellResend.php');
                    }
                    else {
                        $msg .= " / Transaction ERROR ";
                    }
                    
                    
                    
                    
                }
                
                if ($status2 == 9) {
                    //create eispraksi
                    
                    $sql = "SELECT * FROM TRANSACTIONS WHERE status=1 "
                            . "AND transactiontype=1 AND company=".$company->get_id();
                    $rsTrans = $db1->getRS($sql);
                    $amount = 0;
                    if (count($rsTrans)==1) {
                        $cTrans = new TRANSACTIONS($db1, $rsTrans[0]['id'], $rsTrans);
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
                }
                
                if ($status2 == 8) {
                    $sql = "SELECT * FROM TRANSACTIONS WHERE status=1 "
                            . "AND transactiontype=1 AND company=".$company->get_id();
                    $rsTrans = $db1->getRS($sql);
                    $amount = 0;
                    if (count($rsTrans)==1) {
                        $cTrans = new TRANSACTIONS($db1, $rsTrans[0]['id'], $rsTrans);
                        $cTrans->set_status(3);
                        if ($cTrans->Savedata()) {
                            $msg .= " / Transaction OK ";
                        }
                        else {
                            $msg .= " / Transaction ERROR ";
                        }
                        
                    }
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
    else {
        $msg = $lg->l("error"); 
    }
    
}

$userid = $_SESSION['user_id'];
$sql = "SELECT * FROM MESSAGES WHERE companyid=$id AND "
        . "((`sender`=$userid) OR (`receiver`=$userid) OR (`receiver`=43)) "
        . "ORDER BY id DESC";
$messages = $db1->getRS($sql);


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
            });
            
        function UnlockCompany(id) {
            var myURL = 'unlockcompany.php?id=' + id;
                $.ajax({
                   url: myURL,
                   success: function(data) {
                        window.parent.location.reload(false);                      
                   }
                   });            
        }

    </script>
    
    </head>
    
    
    <body class="form">
        
        <div class="form-container">
        
            <h1><?php echo $l->l("change-status"); ?></h1>
            
            <?php 
            if ($msg!="") { 
                
                echo "<h2 class=\"msg\">".$msg."</h2><form>";
                
                
                if ($company->get_lockedbyuser()==1 && $company->get_lockuser()==$_SESSION['user_id']) {
                        $btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "");
                        $btnCloseUpdate->set_method("UnlockCompany($id)");
                    }
                    else {
                        $btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "close-update");
                    }
                    $btnCloseUpdate->get_button_simple();
                
                
                echo "</form>";
            } 
            else {
            ?>                
                
                <form action="companyChangeStatus.php?company=<?php echo $company->get_id(); ?>&confirm=1&status1=<?php echo $status1; ?>&status2=<?php echo $status2; ?>" method="POST">

                    <h3><?php echo $l->l("company").": ".$company->get_companyname(); ?></h3>

                    <?php 
                    $status2description = func::vlookup("altdescription", "STATUS", "id=".$status2, $db1);
                    ?>

                    <h3><?php echo $l->l("new-status").": ".$status2description; ?></h3>

                    <?php
                    
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
                    $btnOK = new button("BtnOk", $lg->l('confirm'));
                    $btnOK->get_button();
                    
                    
                    ?>
                    
                    
                    
                    <div class="clear"></div>

                </form>
            
            <?php } ?>
            
            <div style="padding-left:15px">
            <a class="button" href="editcompany.php?id=<?php echo $id; ?>"><?php echo $l->l("Go back to company") ?></a>
            
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
        
    </body>
    
</html>

