<?php

//$token = "7a433396-2393-11e8-870a-525400c094e4"; // test
$token = "gjbbKBlYUcyRYhfZJ6Ym1X3uouym8eDe9j9kBeIree01e532"; 

$myMsg = "";

if (isset($_REQUEST['area'])) {
    $area = $_REQUEST['area'];
}
if (isset($_REQUEST['zipcode'])) {
    $zipcode = $_REQUEST['zipcode'];
}

$zipcode = str_replace(" ", "", $zipcode);

if ($zipcode=="") {
    $myMsg .= "Παρακαλώ συμπηρώστε ΤΚ";
}
else {
    //if ($zipcode=="") {$zipcode="11111";}
    //if ($area=="") {$area="ΑΑΑ";}

    // $url = "https://tracking.redcourier.gr/api/GetAreaZipCode.php?TokenAPI=$token";

    //echo "zipcode=$zipcode - area=$area <br/>";

    $url = "https://web.redcourier.gr/api/v5.0/SearchAreas?postal=$zipcode&area=";

    /*$data = array("ReceiverPostal"  => $zipcode, 
        "ReceiverCity"  => $area);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);*/

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    $result = curl_exec($ch); 
    curl_close($ch); 

    $res = json_decode($result);

    //echo $result;



    /*if (!is_null($res->error)) {
        $myMsg .= $res->error . "<br/><br/>";
    }
    if (!is_null($res->City)) {
        //1 epilogi
        if ($area . $zipcode != $res->City . $res->Postal) {
            $area = $res->City;
            $zipcode = $res->Postal;
            $myRedBtn = "<span style=\"padding:3px 5px; background-color:#fcc; cursor:pointer\" class=\"$checkareaClass\" data-area=\"$area\" data-zipcode=\"$zipcode\">OK</span>";
            $myMsg .= "<div style=\"margin-bottom:10px\">". $area . " " . $zipcode . " " . $myRedBtn . "</div>";
        }  
    }
    else {
        //polles epiloges
        $areas = $res->AreasToChoose;
        $myMsg .= "<div style=\"column-count: 2; column-gap:30px\">";
        foreach($areas as $obj){
            if (is_object($obj)) {
                $area = $obj->area;
                $zipcode = $obj->zip_code;
                $myRedBtn = "<span style=\"padding:3px 5px; background-color:#fcc; cursor:pointer\" class=\"$checkareaClass\" data-area=\"$area\" data-zipcode=\"$zipcode\">OK</span>";
                $myMsg .= "<div style=\"margin-bottom:10px\">". $obj->area . " " . $obj->zip_code . " " . $myRedBtn . "</div>";
            }
        }
        $myMsg .= "</div>";
    }*/

    if ($res->error) {
        $myMsg .= $res->message . "<br/><br/>";
    }
    else {
        //var_dump($res->data);
        $areas = $res->data;
        $myMsg .= "<div style=\"column-count: 2; column-gap:30px\">";
        foreach($areas as $obj){
            if (is_object($obj)) {
                $area = $obj->area;
                $zipcode = $obj->postal;
                $myRedBtn = "<span style=\"padding:3px 5px; background-color:#fcc; cursor:pointer\" class=\"$checkareaClass\" data-area=\"$area\" data-zipcode=\"$zipcode\">OK</span>";
                $myMsg .= "<div style=\"margin-bottom:10px\">". $obj->area . " " . $obj->postal . " " . $myRedBtn . "</div>";
            }
        }
        $myMsg .= "</div>";
    }



}

if ($myMsg != "") {
    echo "<div style=\"padding:20px; background-color:#ffc\">";
    echo $myMsg;
    echo "</div>";
}