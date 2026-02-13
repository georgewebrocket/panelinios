<?php

$sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? ORDER BY recalldate, recalltime";
$rs2 = $db1->getRS($sql, array($company->get_id()));
$strComm = "";
$myRecallDate = ""; 
$myRecallTime = 0;
for ($k = 0; $k < count($rs2); $k++) {
    $strComm .= $rs2[$k]['id']."|";
    $strComm .= $rs2[$k]['productcategory']."|";
    $strComm .= $rs2[$k]['status']."|";
    $strComm .= $rs2[$k]['userid']."|";
    $strComm .= $rs2[$k]['recalldate']."|";
    $strComm .= $rs2[$k]['recalltime'];
    if ($k<count($rs2)-1) {
        $strComm .= "/";
    }                    
    switch ($rs2[$k]['productcategory']) {
        case 1:
            $company->set_lastactiondate1(date("YmdHis"));
            break;
        case 2:
            $company->set_lastactiondate2(date("YmdHis"));
            break;
        case 3:
            $company->set_lastactiondate3(date("YmdHis"));
            break;
        default:
    }

    if ($myRecallDate=="" && $rs2[$k]['recalldate']!="") {
        $myRecallDate = $rs2[$k]['recalldate'];
        $myRecallTime = $rs2[$k]['recalltime'];
    }


}
$company->set_commstatus($strComm);
$company->set_recalldate($myRecallDate);
$company->set_recalltime($myRecallTime);
$company->Savedata();

// include 'updatePanelCompany.php';