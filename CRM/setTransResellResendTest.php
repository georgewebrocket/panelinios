<?php
/*
ini_set('display_errors',1); 
error_reporting(E_ALL);
*/
require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = (isset($_GET['id'])? $_GET['id']: 0);


//$sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype=1 AND id>$id ORDER BY id";
$sql = "SELECT * FROM TRANSACTIONS WHERE id=$id";
$rs = $db1->getRS($sql);

if ($rs) {
for ($k=0;$k<count($rs);$k++) {
    $transaction = new TRANSACTIONS($db1, $rs[$k]['id'], $rs);
    $companyid = $transaction->get_company();
    
    include "transResellResendTest.php";
	
	echo $id;
	//$resend=1;
	if ($resend==1) {
		echo " - EPANADROMOLOGHSH";
	}
	if ($resell==1) {
		echo " - ANANEOSI";
	}
    
    //echo $transaction->get_id() . " - " . $msg . "<br/>";
    
}
}
else {
	echo "....";
}