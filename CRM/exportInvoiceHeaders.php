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
$sql = "SELECT I.id AS ID, '010' AS KODPAR, I.icode AS ARPAR, I.idate AS DATEPAR, " 
		. "CONCAT('30.00.',I.company) AS KODIKOSPEL, I.series AS SERIES, " 
		. "IC.code AS SERIESCODE "
        . "FROM INVOICEHEADERS I "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
        . "WHERE idate>=$D1 AND iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND series = $series";
}
$rs = $db1->getRS($sql);

//download_send_headers("HEADERS_" . date("Y-m-d") . ".csv");
download_send_headers("HEADERS.TXT");

for ($i=0;$i<count($rs);$i++) {
    echo $rs[$i]['ID'].";";
	echo $rs[$i]['KODPAR'].";";
    echo $rs[$i]['ARPAR'].";";
    echo func::str14toDate($rs[$i]['DATEPAR'], "/").";";
    echo $rs[$i]['KODIKOSPEL'].";";
	switch ($rs[$i]['SERIES']) {
		case 1:
			echo "Β;";
			break;
		case 2:
			echo "Γ;";
			break;
		default:
			break;
	}
    
    echo "ΠΑΡΟΧΗ;";
    echo ";";
    echo "\r\n";
}


die();

?>