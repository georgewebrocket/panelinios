<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = (isset($_GET['id'])? $_GET['id']: 0);


$sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype=1 AND id>$id ORDER BY id";
$rs = $db1->getRS($sql);

for ($k=0;$k<count($rs);$k++) {
    $transaction = new TRANSACTIONS($db1, $rs[$k]['id'], $rs);
    $companyid = $transaction->get_company();
    
    $tdate = $transaction->get_tdatetime();
    $tdateDate = func::str14toDate($tdate, "-", "EN")." 23:59:59";
    $prev = func::vlookup("COUNT(id)", "TRANSACTIONS", 
            "company=$companyid AND tdatetime<'$tdate' AND status<>3", $db1);
	/*
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
    */
    $msg = " ... ";
    if ($prev>0 /*|| $resend==1*/) {
        //if ($prev>0) {
            $transaction->set_resell(1);
        //}
        //$transaction->set_resend($resend);
        if (!$transaction->Savedata()) {
            $msg = "ERROR";
        }
        else {
            $msg = "SAVED";
        }
    }
    
    echo $transaction->get_id() . " - " . $msg . "<br/>";
    
}