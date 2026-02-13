<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/utils.php');
require_once('inc.php');

function array2csv(array &$array, $skipfirst = FALSE)
{
    if (count($array) == 0) {
      return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($array)), ";");
    $i=0;
    foreach ($array as $row) {
       if ($i>0 || !$skipfirst) {
         fputcsv($df, $row);
       }
       $i++;
    }
    fclose($df);
    return ob_get_clean();
}

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

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

$D1 = $_GET['D1'];
$D2 = $_GET['D2'];
$series = $_GET['series'];
$sql = "SELECT C.eponimia AS EPONYMIA, C.log_code AS KODIKOS, "
        . "C.afm AS AFM, C.address, "
        . "PROFESSIONS.description AS EPAGGELMA, C.doy AS DOY, "
        . "CITIES.description AS POLH, "
        . "C.zipcode AS TK, C.phone1 AS TEL, C.phone2 AS TEL2,  "
        . "C.fax "
        . "FROM INVOICEHEADERS I INNER JOIN COMPANIES C ON I.company=C.id "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
        . "LEFT OUTER JOIN PROFESSIONS ON C.profession = PROFESSIONS.id "
        . "LEFT OUTER JOIN EP_CITIES CITIES ON C.city_id = CITIES.id "
        . "WHERE idate>=$D1 AND iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND series = $series";
}
$rs = $db1->getRS($sql);

//download_send_headers("CUSTOMERS_" . date("Y-m-d") . ".N");
download_send_headers("PELATES.TXT");
//echo array2csv($rs, TRUE);

function substr_unicode($str, $s, $l = null) {
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}

for ($i=0;$i<count($rs);$i++) {
    
    echo func::fixedLength($rs[$i]['EPONYMIA'],40) . "#";
    echo func::fixedLength($rs[$i]['KODIKOS'],15) . "#"; 
    echo func::fixedLength($rs[$i]['KODIKOS'],15) . "#"; //log. logistikis
    echo func::fixedLength(" ",5) . "#";
    $myAFM = preg_replace("/[^0-9]/","",$rs[$i]['AFM']);
    echo func::fixedLength($myAFM,13) . "#";
    echo func::fixedLength(" ",4) . "#"; //doy
    echo func::fixedLength(" ",40) . "#"; //doy address
    echo func::fixedLength($rs[$i]['address'],40) . "#";    
    echo func::fixedLength($rs[$i]['EPAGGELMA'],40)."#";
    echo func::fixedLength("1",1) . "#"; //kathestos fpa
    echo func::fixedLength($rs[$i]['DOY'],40)."#";
    echo func::fixedLength($rs[$i]['EPAGGELMA'],40)."#";
    echo func::fixedLength($rs[$i]['POLH'],30)."#";
    $myZipcode = preg_replace("/[^0-9]/","",$rs[$i]['TK']);
    echo func::fixedLength($myZipcode, 5) . "#";
    echo func::fixedLength(" ",15) . "#"; //telex
    echo func::fixedLength($rs[$i]['TEL'],15)."#";
    echo func::fixedLength($rs[$i]['TEL2'],15)."#";
    echo func::fixedLength("1",1) . "#"; //ypovoli myf
    echo func::fixedLength($rs[$i]['fax'],15) . "#";
        
    echo "\r\n";
}


die();