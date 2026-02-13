<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/utils.php');
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
$sql = "SELECT I.icode AS ARPAR, I.idate AS DATEPAR, " 
		. "I.company AS KODIKOSPEL, " 
		. "IC.code AS SERIESCODE, SUM(INVDETAILS.price) AS TOTALPRICE "
        . "FROM INVOICEHEADERS I "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
        . "INNER JOIN INVOICES INVDETAILS ON INVDETAILS.headerid = I.Id "
        . "WHERE I.idate>=$D1 AND I.iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND I.series = $series ";
}
$sql .= "GROUP BY I.id, I.icode, I.idate, I.company, IC.code";

$rs = $db1->getRS($sql);

download_send_headers("KINISEIS.TXT");

for ($i=0;$i<count($rs);$i++) {
    echo func::fixedLength("5",2) . "#";  //
    echo func::fixedLength("",9) . "#";  //a/a kin
    echo func::fixedLength("",8) . "#"; //imer enimerosis
    echo func::fixedLength("002",3) . "#"; //kod kinisis
    echo func::fixedLength("73.00.0024",30) . "#"; //kod log
    echo func::fixedLength("024",3) . "#"; //parastatiko
    echo func::fixedLength("ΠΩΛΗΣΕΙΣ ΥΠΗΡΕΣΙΩΝ ΧΟΝΔΡΙΚΑ 24%",40) . "#"; //ΑΙΤΙΟΛΟΓΙΑ ΛΟΓΑΡΙΑΣΜΟΥ
    echo func::fixedLength("ΤΠΥ Α",13) . "#"; //ΑΙΤΙΟΛΟΓΙΑ ΠΑΡΑΣΤΑΤΙΚΟΥ
    echo func::fixedLength("",14) . "#"; //ΜΥΦ ΑΡΝΗΤΙΚΗ
    echo func::fixedLength($rs[$i]['TOTALPRICE'],14) . "#"; //ΜΥΦ ΘΕΤΙΚΗ
    echo func::fixedLength($rs[$i]['ARPAR'],7) . "#"; //ΑΡ ΠΑΡΑΣΤΑΤΙΚΟΥ
    echo func::fixedLength("30.00.".$rs[$i]['KODIKOSPEL'],15) . "#"; //ΚΩΔ. ΠΕΛΑΤΗ
    echo func::fixedLength($rs[$i]['TOTALPRICE'],14) . "#"; //ΠΙΣΤΩΣΗ
    echo func::fixedLength("0",14) . "#"; //ΧΡΕΩΣΗ
    echo func::fixedLength("1",16) . "#"; //ΠΟΣΟΤΗΤΑ ΜΥΦ
    echo func::fixedLength($rs[$i]['SERIESCODE'],2) . "#"; //ΣΕΙΡΑ ΠΑΡΑΣΤΑΤΙΚΟΥ
        
    echo "\r\n";
}


die();
