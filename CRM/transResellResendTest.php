<?php
$tdate = $transaction->get_tdatetime();
$tdateDate = func::str14toDate($tdate, "-", "EN")." 23:59:59";
$tdateLast = ""; $tdateDateLast = "";

$resend = 0; $resell = 0; $prev = 0;

//βρίσκω την τελευταία χρέωση (αν υπάρχει)
$sqlTrans = "SELECT * FROM TRANSACTIONS WHERE company=$companyid AND tdatetime<'$tdate' AND transactiontype=1 ORDER BY tdatetime DESC";
$rsTrans = $db1->getRS($sqlTrans);

if ($rsTrans) {
	//transactiontype
	if ($rsTrans[0]['status'] <>3) { //oxi akyrosi
		$tdateLast = $rsTrans[0]['tdatetime'];
		$tdateDateLast = func::str14toDate($tdateLast, "-", "EN")." 23:59:59";
	}
	else { //akyrosi
		$resend = 1;
	}
	$prev = 1;	
}
else {
	$prev = 0;
}

if ($resend == 0 && $prev == 1) {
	//ψάχνω το ιστορικό από την ημερομηνία της χρέωσης προς τα πίσω μέχρι την προηγούμενη χρέωση - αν υπάρχει.
	$criteriaLastTransaction = $tdateDateLast!=""? " AND atimestamp>'$tdateDateLast' ": "";
	$sqlActions = "SELECT * FROM ACTIONS WHERE company=$companyid AND atimestamp<'$tdateDate' $criteriaLastTransaction ORDER BY atimestamp DESC";
	$rsActions = $db1->getRS($sqlActions);
	$arnitikos = 0; $arnisiananeosis = 0;
	for ($i=0;$i<count($rsActions);$i++) {		
		if ($rsActions[$i]['status2']==4) { // arnitikos
			$arnitikos++;
		}
		if ($rsActions[$i]['status2']==15) { // arnisi ananeosis
			$arnisiananeosis++;
		}			
			
		if ($arnitikos>0 || $arnisiananeosis>0)   {
			break;
		}	
		if ($i==count($rsActions)-1) { //teleftaio record
			$resell = 1;
			break;
		}	
	}
}
	
//save
/*
$transaction->set_resend($resend);
$transaction->set_resell($resell);

if (!$transaction->Savedata()) {
	$msg .= "RESELL/RESEND ERROR";
}
*/

?>