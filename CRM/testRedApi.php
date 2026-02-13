<?php

header('Content-Type: text/html; charset=utf-8');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//https://tracking.redcourier.gr/api/NewVoucher.php
//7a433396-2393-11e8-870a-525400c094e4

//TokenAPI
//ReceiverName
//ReceiverAddress
//ReceiverCity
//ReceiverPostal
//ReceiverTelephone
//Notes
//OrderID
//Cod (0.00)

//error
//Voucher

//new //////////////////////////////////////////////////////////////////////////

$url = 'https://tracking.redcourier.gr/api/NewVoucher.php?TokenAPI=7a433396-2393-11e8-870a-525400c094e4';
$data = array(
    'ReceiverName' => 'ΓΕΩΡΓΙΟΣ ΠΑΠΑΓΙΑΝΝΗΣ 3', 
    'ReceiverAddress' => 'ΑΙΓΑΙΟΥ 10',
    'ReceiverCity' => 'ΝΕΑ ΣΜΥΡΝΗ',
    'ReceiverPostal' => '17121',
    'ReceiverTelephone' => '2109320686-6944319607',
    'Notes' => 'ΤΕΣΤ',
    'OrderID' => 'X999999999',
    'Cod' => 333.50
    );

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
 //...
}


var_dump($result);


//print
//https://tracking.redcourier.gr/api/PrintVoucher.php?voucher=724139&TokenAPI=7a433396-2393-11e8-870a-525400c094e4

//edit //////////////////////////////////////////////////////////////////////////
/*
$url = 'https://tracking.redcourier.gr/api/EditVoucher.php?VoucherID=745907&TokenAPI=7a433396-2393-11e8-870a-525400c094e4';
$data = array(
    'ReceiverName' => 'ΓΕΩΡΓΙΟΣ ΠΑΠΑΓΙΑΝΝΗΣ 4', 
    'ReceiverAddress' => 'ΕΥΜΕΝΟΥΣ 39Α',
    'ReceiverCity' => 'ΑΘΗΝΑ',
    'ReceiverPostal' => 11632,
    'ReceiverTelephone' => '2107517649',
    'Notes' => 'ΤΕΣΤ',
    'OrderID' => 'X999999999',
    'Cod' => 10.50
    );

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
 //...724145
}


var_dump($result);
//echo "<br/>";
//echo utf8_decode($result);
*/


//delete ///724148
/*$url = 'https://tracking.redcourier.gr/api/DeleteVoucher.php?VoucherID=745907&TokenAPI=7a433396-2393-11e8-870a-525400c094e4';
$data = array();

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
 //...724145
}


var_dump($result);*/