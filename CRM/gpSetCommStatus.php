<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

//$start = $_GET['start']; $stop = $_GET['stop'];
//$sql = "SELECT * FROM COMPANIES WHERE id>=$start AND id<$stop ORDER BY id";
$sql = "SELECT * FROM COMPANIES WHERE id IN (128004,124137,110528,125161,125763,125319,124399,77349,128334,127427,16868,125764,127796,124191,69411,79459,110231,67926,108497,128369,981,125599,127104,124623,128794,110495,127161,128648,128327,57550,127572,128400,127200,34772,10808,132337,82212,127141,125260,109788,2341,124151,28406,127473,126837,109138,79791,80955,126685,82121,128940,127675) ORDER BY id";
$rs = $db1->getRS($sql);

for ($i = 0; $i < count($rs); $i++) {
    $rs2 = $db1->getRS("SELECT * FROM COMPANIES_STATUS WHERE companyid=".$rs[$i]['id']);
    $strComm = "";
    for ($k = 0; $k < count($rs2) && $rs2; $k++) {
        $strComm .= $rs2[$k]['id']."|";
        $strComm .= $rs2[$k]['productcategory']."|";
        $strComm .= $rs2[$k]['status']."|";
        $strComm .= $rs2[$k]['userid']."|";
        $strComm .= $rs2[$k]['recalldate']."|";
        $strComm .= $rs2[$k]['recalltime'];
        if ($k<count($rs2)-1) {
            $strComm .= "/";
        }        
    }
    echo $strComm . "<br/>";
    if ($strComm!="") {
        $company = new COMPANIES($db1, $rs[$i]['id'], $rs);
        $company->set_commstatus($strComm);
        $company->Savedata();
    }
}