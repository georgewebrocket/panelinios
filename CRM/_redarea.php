<?php

//header('Content-Type: text/html; charset=utf-8');

//$token = "7a433396-2393-11e8-870a-525400c094e4"; // test
$token = "75085710-6977-11e8-8f5e-448a5b2c3bfc"; // epag

//$area = ""; $zipcode = "";

//$area = $_REQUEST['area'];
//$zipcode = $_REQUEST['zipcode'];

//echo $area . "<br/>";
//echo $zipcode . "<br/>";

if (isset($_REQUEST['area'])) {
    $area = $_REQUEST['area'];
}
if (isset($_REQUEST['zipcode'])) {
    $zipcode = $_REQUEST['zipcode'];
}


if ($area=="" && $zipcode=="") {
    echo "Παρακαλώ συμπηρώστε πόλη και ΤΚ";
}
else {
if ($zipcode=="") {$zipcode="11111";}
if ($area=="") {$zipcode="ΑΑΑ";}

$url = "https://tracking.redcourier.gr/api/GetAreaZipCode.php?TokenAPI=$token";

$data = array("ReceiverPostal"  => $zipcode, 
    "ReceiverCity"  => $area);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$res = json_decode($result);

echo "<pre>";
var_dump($res);
echo "</pre>";

if (!is_null($res->error)) {
    echo $res->error . "<br/>";
}
if (!is_null($res->City)) {
    //1 epilogi
    if ($area . $zipcode != $res->City . $res->Postal) {
        echo $res->City . " " . $res->Postal;
    }    
}
else {
    //polles epiloges
    $areas = $res->AreasToChoose;
    foreach($areas as $obj){
        if (is_object($obj)) {
            echo $obj->area . " " . $obj->zip_code . "<br/>";
        }
    }
}

}