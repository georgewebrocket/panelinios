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
$sql = "SELECT I.id, I.icode AS ARPAR, I.idate AS DATEPAR, " 
        . "I.company AS KODIKOSPEL, " 
        . "IC.code AS SERIESCODE, SUM(INVDETAILS.amount) AS TOTALPRICE, SUM(INVDETAILS.vat) AS TOTALVAT, "
        . "C.eponimia AS EPONYMIA, I.series, C.log_code "
        . "FROM INVOICEHEADERS I "
        . "INNER JOIN COMPANIES C ON I.company=C.id "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
        . "INNER JOIN INVOICES INVDETAILS ON INVDETAILS.headerid = I.Id "
        . "WHERE I.idate>=$D1 AND I.iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND I.series = $series ";
}
$sql .= "GROUP BY I.id, I.icode, I.idate, I.company, IC.code";

$rs = $db1->getRS($sql);

download_send_headers("KINISEIS2.TXT");

for ($i=0;$i<count($rs);$i++) {
    for ($k = 1; $k <= 3; $k++) {
        echo func::fixedLength($rs[$i]['id'],10) . "#";  //
        echo func::fixedLength("5",1) . "#";  //a/a kin
        $myDate = func::str14toDateDMYY($rs[$i]['DATEPAR']);
        echo func::fixedLength($myDate,8) . "#"; //imer enimerosis
        echo func::fixedLength("002",3) . "#"; //kod kinisis

        switch ($rs[$i]['series']) {
            case 1:
                $seriesCode = "058";
                break;
            case 2:
                $seriesCode = "059";
                break;
            default :
                $seriesCode = "000";
                break;
        }
        echo func::fixedLength($seriesCode,3) . "#"; //kodikos seiras parastatikou
        
        echo func::fixedLength($rs[$i]['ARPAR'],7) . "#"; //ΑΡ ΠΑΡΑΣΤΑΤΙΚΟΥ
        echo func::fixedLength($rs[$i]['SERIESCODE'],2) . "#"; //seira ΠΑΡΑΣΤΑΤΙΚΟΥ

        switch ($k) {
            case 1:
                $codeLog = "73.00.0024";
                $price1 = "0";
                $price2 = func::nrToCurrency($rs[$i]['TOTALPRICE']);
                $aitiologia = "ΠΩΛΗΣΕΙΣ ΥΠΗΡΕΣΙΩΝ ΧΟΝΔΡΙΚΑ 24%";
                $codeLog2 = "1";
                $price3 = func::nrToCurrency($rs[$i]['TOTALPRICE']);
                break;
            case 2:
                $codeLog = "54.00.0024";
                $price1 = "0";
                $price2 = func::nrToCurrency($rs[$i]['TOTALVAT']);
                $aitiologia = "ΦΠΑ ΠΩΛΗΣΕΩΝ 24%";
                $codeLog2 = "0";
                $price3 = "0";
                break;
            case 3:
                //$codeLog = "30.00." . $rs[$i]['KODIKOSPEL']; 
                $codeLog = $rs[$i]['log_code']; 
                $price1 = func::nrToCurrency($rs[$i]['TOTALPRICE'] + $rs[$i]['TOTALVAT']);
                $price2 = "0";
                $aitiologia = $rs[$i]['EPONYMIA'];
                $codeLog2 = "0";
                $price3 = "0";
                break;
            default:
                break;
        }
        echo func::fixedLength($codeLog,30) . "#";
        echo func::fixedLength($price1, 14) . "#";
        echo func::fixedLength($price2, 14) . "#";
        echo func::fixedLength($aitiologia, 40) . "#";
        echo func::fixedLength($codeLog2, 1) . "#";
        echo func::fixedLength($price3, 14) . "#";
        
        //echo func::fixedLength("30.00.".$rs[$i]['KODIKOSPEL'],15) . "#"; //ΚΩΔ. ΠΕΛΑΤΗ
        echo func::fixedLength($rs[$i]['log_code'],15) . "#";

        echo "\r\n";
    }
        
}


die();
