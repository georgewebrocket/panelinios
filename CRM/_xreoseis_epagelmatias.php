<?php


$strSql = "SELECT * FROM PACKAGES";
$rsPackagesEpag = $dboEpag->getRS($strSql);

$sql = "SELECT *, amount + vat AS total FROM TRANSACTIONS WHERE company=? AND transactiontype=1";
$rs = $dboEpag->getRS($sql, array($epagId));
for ($i=0;$i<count($rs);$i++) {
    if ($rs[$i]['package']!=0 && $rs[$i]['package']!="") {
        $rs[$i]['description'] = func::vlookupRS("description", 
            $rsPackagesEpag, $rs[$i]['package']);
    }
}

$strSql = "SELECT * FROM TRANSACTION_STATUS";
$rsTransStatusEpag = $dboEpag->getRS($strSql);

$strSql = "SELECT * FROM USERS";
$rsUsersEpag = $dboEpag->getRS($strSql);

$dg = new datagrid("dg_xrewseis", $dboEpag, "", 
        array("id","tdatetime", "description","amount","vat","total", "seller","status"), 
        array("ID","ΗΜΕΡ.","ΠΕΡΙΓΡΑΦΗ","ΠΟΣΟ","ΦΠΑ","ΣΥΝΟΛΟ", "ΠΩΛΗΤΗΣ","STATUS"), 
        $ltoken);
$dg->set_rs($rs);
$dg->col_vlookupRS("seller", "seller", $rsUsersEpag, "fullname");
$dg->col_vlookupRS("status", "status", $rsTransStatusEpag, "description");
$dg->set_colsFormat(array("","DATE","","CURRENCY","CURRENCY","CURRENCY","","",""));
if (($_SESSION['user_profile']>1)) {
    //$dg->set_edit("editTransaction.php", "EDIT");
    //$dg->set_del("delTransaction.php", "DEL");
}

echo "<div id=\"xreoseis-epagelmatias\" style=\"display:none\">";
$dg->get_datagrid();
echo "</div>";