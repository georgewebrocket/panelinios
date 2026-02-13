<?php
$tdate = $transaction->get_tdatetime();
$tdateDate = func::str14toDate($tdate, "-", "EN")." 23:59:59";
$prev = func::vlookup("COUNT(id)", "TRANSACTIONS",
		"company=$companyid AND tdatetime<'$tdate' AND status<>3", $db1);

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
		$msg .= "RESELL/RESEND ERROR";
	}
}

?>