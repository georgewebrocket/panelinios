<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
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
$sql = "SELECT C.afm AS AFM, C.eponimia AS EPONYMIA, CONCAT('30.00.',C.id) AS KODIKOS, "
        . "CAT.description AS EPAGGELMA, C.address, C.doy AS DOY, "
        . "'0' AS YPOLOIPO, A.description AS PERIOXH, CITIES.description AS POLH, "
        . "C.zipcode AS TK, C.phone1 AS TEL, '0' AS FPA, '1' AS KODPOL "
        . "FROM INVOICEHEADERS I INNER JOIN COMPANIES C ON I.company=C.id "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
        . "LEFT OUTER JOIN CATEGORIES CAT ON C.basiccategory = CAT.id "
        . "LEFT OUTER JOIN AREAS A ON C.area = A.id "
        . "LEFT OUTER JOIN EP_CITIES CITIES ON C.city_id = CITIES.id "
        . "WHERE idate>=$D1 AND iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND series = $series";
}
$rs = $db1->getRS($sql);

//download_send_headers("CUSTOMERS_" . date("Y-m-d") . ".N");
download_send_headers("CUSTOMERS.NEW");
//echo array2csv($rs, TRUE);

function substr_unicode($str, $s, $l = null) {
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}

for ($i=0;$i<count($rs);$i++) {
    echo preg_replace("/[^0-9]/","",$rs[$i]['AFM']).";";
    echo substr_unicode($rs[$i]['EPONYMIA'],0,60).";";
    echo $rs[$i]['KODIKOS'].";";
    echo substr_unicode($rs[$i]['EPAGGELMA'],0,40).";";
    echo substr_unicode($rs[$i]['address'],0,60).";";
    echo $rs[$i]['DOY'].";";
    echo $rs[$i]['YPOLOIPO'].";";
    echo substr_unicode($rs[$i]['PERIOXH'],0,60).";";
    echo substr_unicode($rs[$i]['POLH'],0,60).";";
    echo preg_replace("/[^0-9]/","",$rs[$i]['TK']).";";
    echo $rs[$i]['TEL'].";";
    echo $rs[$i]['FPA'].";";
    echo $rs[$i]['KODPOL'];
    echo "\r\n";
}


die();

?>