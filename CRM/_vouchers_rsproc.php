<?php
$voucherIds = "";
for ($i = 0; $i < count($rs); $i++) {
    //eponimia,address,zipcode,city,phones,comments,vcode,amount
    $rs[$i]['companyname'] = mb_substr(trim($rs[$i]['companyname']), 0, 64, 'utf-8');
    //$rs[$i]['eponimia'] = $rs[$i]['eponimia'];
    $rs[$i]['address'] = mb_substr(trim($rs[$i]['address']), 0, 64, 'utf-8');
    $rs[$i]['city'] = mb_substr(trim($rs[$i]['city']), 0, 64, 'utf-8');
    $rs[$i]['zipcode'] = str_replace(" ", "", $rs[$i]['zipcode']);
    $rs[$i]['phones'] = mb_substr(trim($rs[$i]['phones']), 0, 32, 'utf-8');
    $rs[$i]['vcode'] = mb_substr($rs[$i]['vcode'], 0, 36, 'utf-8');
    //$rs[$i]['amount'] = str_replace(",", ".", (string)$rs[$i]['amount']);
    $rs[$i]['amount'] = $rs[$i]['amount'];
    $rs[$i]['comments'] = mb_substr ("Ώρα παράδοσης " .  func::str14toDate($rs[$i]['deliverydate']) . " " .
            $rs[$i]['deliverytime'] . " / " . str_replace("\r\n", " ", $rs[$i]['deliverynotes']), 0, 128, 'utf-8');
    //$rs[$i]['comments'] = "Ώρα παράδοσης " .  func::str14toDate($rs[$i]['deliverydate']) . " " .
            //$rs[$i]['deliverytime'] . " / " . str_replace("\r\n", " ", $rs[$i]['deliverynotes']);
    
    $voucherIds .= $rs[$i]['id'] . ",";    
}
$voucherIds = substr($voucherIds, 0, strlen($voucherIds)-1);

$sqlVouchers = "UPDATE VOUCHERS SET exported_to_excel = 1 WHERE id IN ($voucherIds)";
//die($sqlVouchers);
$ret = $db1->execSQL($sqlVouchers);
