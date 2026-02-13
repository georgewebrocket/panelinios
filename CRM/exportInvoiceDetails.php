<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
	header('Content-Type: charset=utf-8');

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
	
}

$D1 = $_GET['D1'];
$D2 = $_GET['D2'];
$series = $_GET['series'];
$sql = "SELECT I.id AS ID, I.price AS PRICE,  " 
		. "I.discount AS DISCOUNT "
        . "FROM INVOICES I INNER JOIN INVOICEHEADERS IH ON I.headerid = IH.id "
        . "WHERE IH.idate>=$D1 AND IH.iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND series = $series";
}
$rs = $db1->getRS($sql);

//download_send_headers("DETAILS_" . date("Y-m-d") . ".csv");
download_send_headers("DETAILS.TXT");

for ($i=0;$i<count($rs);$i++) {
    echo $rs[$i]['ID'].";";
	echo "001;";
    echo $rs[$i]['PRICE'].";";
    echo "1;";
	echo $rs[$i]['DISCOUNT'].";";
	echo "001;";
    echo "NO COMMENTS;";
    echo "\r\n";
}


die();

?>