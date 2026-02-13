<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

header('Content-Type: text/html; charset=utf-8');

$token = "75085710-6977-11e8-8f5e-448a5b2c3bfc"; // epag
//$voucherCodes = "710493, 698729, 682124, 714550";
$voucherCodes = "710493";
$data = array( 'Vouchers' => $voucherCodes );

echo "<pre>";
var_dump($data);
echo "</pre>";

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);

$url = "https://tracking.redcourier.gr/api/GetLastStatus.php?TokenAPI=$token";

$result = file_get_contents($url, false, $context);

$res = json_decode($result);

echo "<pre>";
var_dump($res);
echo "</pre>";