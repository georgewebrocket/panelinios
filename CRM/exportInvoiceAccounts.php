<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/utils.php');
require_once('inc.php');

function substr_unicode($str, $s, $l = null) {
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}

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
$sql = "SELECT C.eponimia AS EPONYMIA, C.log_code AS KODIKOS "        
        . "FROM INVOICEHEADERS I INNER JOIN COMPANIES C ON I.company=C.id "
        . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "        
        . "WHERE idate>=$D1 AND iDate<=$D2 ";
if ($series>0) {
    $sql .= "AND series = $series";
}
$rs = $db1->getRS($sql);

download_send_headers("LOGLOGIST.TXT");


for ($i=0;$i<count($rs);$i++) {
    
    
    echo func::fixedLength($rs[$i]['KODIKOS'],30) . "#"; 
    echo func::fixedLength("",13) . "#";
    echo func::fixedLength($rs[$i]['EPONYMIA'],40) . "#";
    echo func::fixedLength("3",1) . "#"; //kat x/p
    echo func::fixedLength("1",1) . "#"; //enim tzirou
    echo func::fixedLength("",40) . "#"; //log fpa
    echo func::fixedLength("",13) . "#"; //entypa fpa
    echo func::fixedLength("1",1) . "#"; //enim myf
            
    echo "\r\n";
}


die();