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

$sql = urldecode($_GET["sql"]);
$filename = $_GET["filename"];

$sql = str_replace("SLCT", "SELECT", $sql);
//echo $sql;
//echo "\r\n";

$rs = $db1->getRS($sql);
download_send_headers($filename . date("YmdHis") . ".xls");
//echo count($rs);
//echo "\r\n";
for ($i=0;$i<count($rs);$i++) {
    $record = $rs[$i];
    $keys = array_keys($record);
    if ($i==0) {
        for ($k = 0; $k < count($keys); $k++) {
            echo $keys[$k]. ";";
        }
        echo "\r\n";
    }
    for ($k = 0; $k < count($keys); $k++) {
        echo $record[$keys[$k]]. ";";
    }
    echo "\r\n";
}

die();

?>