<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$url = "https://web.redcourier.gr/api/v5.0/CreateVoucher";

$tokenEE = "gjbbKBlYUcyRYhfZJ6Ym1X3uouym8eDe9j9kBeIree01e532"; //kzigogiannis ee

$tokenPan = "gjbbKBlYUcyRYhfZJ6Ym1X3uouym8eDe9j9kBeIree01e532"; //26/2/2022 sarris - na valoume to idio

$sql = "SELECT V.id, CONCAT(C.companyname, ' (', PROFESSIONS.description, ')') AS  companyname, IF(C.courier_address<>'', C.courier_address, C.address )  AS address, IF(C.courier_zipcode<>'', C.courier_zipcode, C.zipcode) AS zipcode, CITIES.description AS city, IF(C.courier_phone<>'', C.courier_phone, CONCAT(C.mobilephone, ' ', C.phone1, ' ', C.phone2)) AS phones, V.deliverydate, V.deliverytime, V.deliverynotes, N'' AS comments, V.vcode, V.amount, C.courier_notes, V.customer, V.publisher FROM VOUCHERS V INNER JOIN COMPANIES C ON V.customer = C.id INNER JOIN EP_CITIES CITIES ON IF(C.courier_city<>0, C.courier_city, C.city_id) = CITIES.id INNER JOIN PROFESSIONS  ON C.profession = PROFESSIONS.id WHERE V.export_to_excel = 1 AND V.exported_to_excel = 0 AND V.courier = 1";

$rs = $db1->getRS($sql);

for ($i = 0; $i < count($rs); $i++) {
    switch ($rs[$i]['publisher']) {
        case 0:
        case 1:
            $token = $tokenPan;
            break;
        case 2:
            $token = $tokenEE;
            break;
        default:
            $token = $tokenPan;
            break;
    }
    
    // $url = "https://tracking.redcourier.gr/api/NewVoucher.php?TokenAPI=$token";
    
    $voucher = new VOUCHERS($db1, $rs[$i]['id']); 
    $company = new COMPANIES($db1, $rs[$i]['customer']);
    
    if ($company->get_courier_region()!="") {
        $area = $company->get_courier_region();
    }
    elseif ($company->get_courier_city_descr()!="") {
        $area = $company->get_courier_city_descr();
    }
    elseif ($company->get_region()!="") {
        $area = $company->get_region();
    }
    else {
        $area = $company->get_city();
    }
    $area = trim($area);
    
    if ($company->get_courier_zipcode()!="") {
        $zipcode = $company->get_courier_zipcode();
    }    
    else {
        $zipcode = $company->get_zipcode();
    }
    $zipcode = str_replace(" ", "", $zipcode);
    $zipcode = trim($zipcode);
    
    $data = array(
        'ReceiverName' => $rs[$i]['companyname'], 
        'ReceiverAddress' => $rs[$i]['address'],
        'ReceiverCity' => $area,
        'ReceiverPostal' => $zipcode,
        'ReceiverTelephone' => $rs[$i]['phones'],
        'Notes' => func::str14toDate($rs[$i]['deliverydate']) . " " . $rs[$i]['deliverytime'] . " " . $rs[$i]['deliverynotes'],
        'OrderID' => $rs[$i]['id'],
        'Cod' => $rs[$i]['amount']
        );
    
    /*echo "<pre>";
    var_dump($data);
    echo "</pre>";*/
    
    
    // use key 'http' even if you send the request to https://...
    /*$options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);*/

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];
    $responseJSON = func::curl_post($url, $data, $headers, "POST");
    
    $res = json_decode($responseJSON);
        
    if (/*!is_null($res->error)*/ $res->error != false) { 
        echo $rs[$i]['id'] . " - ERROR - <br/>  " .  $res->error . "<br/>";
                
        echo "<pre>";
        var_dump($res);
        echo "</pre>";
        
    }
    else {
        $voucher->set_vcode2($res->voucher);
        $voucher->set_exported_to_excel(1);
        
        $dayOfWeek = date("N");
        $daysPlus = 2; //+2 hmeres
        if ($dayOfWeek==4 || $dayOfWeek==5) { //For Thursday, Friday + 4 days=>Monday, Tuesday 
            $daysPlus = 4;
        }
        
        $date2 = strtotime(date("Y-m-d") . " + $daysPlus days");
        
        $date2str = date("d/m/Y", $date2);
        $date2str14 = func::dateTo14str($date2str);
        $voucher->set_followup_date($date2str14);
        $voucher->set_followup_time(1);
        
        $voucher->Savedata();
        echo $rs[$i]['id'] . " - OK - " . $res->voucher .  " <br/>";
        
        
    }
    
    echo "<hr/>";
}