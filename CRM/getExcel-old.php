<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Athens');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

function c($i) {
    switch ($i) {
        case 1: return 'A'; break;
        case 2: return 'B'; break;
        case 3: return 'C'; break;
        case 4: return 'D'; break;
        case 5: return 'E'; break;
        case 6: return 'F'; break;
        case 7: return 'G'; break;
        case 8: return 'H'; break;
        case 9: return 'I'; break;
        case 10: return 'J'; break;
        case 11: return 'K'; break;
        case 12: return 'L'; break;
        case 13: return 'M'; break;
        case 14: return 'N'; break;
        case 15: return 'O'; break;
        case 16: return 'P'; break;
        case 17: return 'Q'; break;
        case 18: return 'R'; break;
        case 19: return 'S'; break;
        case 20: return 'T'; break;
    }
}

/*=============================== get data ===================================*/
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = urldecode($_GET["sql"]);
$filename = $_GET["filename"];
$filename2 = $filename . date("YmdHis");
$sql = str_replace("SLCT", "SELECT", $sql);
$rs = $db1->getRS($sql);

if ($filename=="home") {
    include "_home_rsproc.php";
}

if ($filename=="vouchers") {
    include "_vouchers_rsproc.php";
}


/*============================================================================*/

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Epagelmatias")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Document")
    ->setSubject("Office 2007 XLSX Document")
    ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Result file");

$objPHPExcel->setActiveSheetIndex(0);


/*============================================================================*/
for ($i=0;$i<count($rs);$i++) {
    $record = $rs[$i];
    $keys = array_keys($record);
    if ($i==0) {
        for ($k = 0; $k < count($keys); $k++) {
            //echo $keys[$k]. ";";
            $objPHPExcel->getActiveSheet()->setCellValue(c($k+1).'1', $keys[$k]);
        }
        //echo "\r\n";
    }
    for ($k = 0; $k < count($keys); $k++) {
        //echo $record[$keys[$k]]. ";";
        $row = $i + 2;
        $objPHPExcel->getActiveSheet()->setCellValue(c($k+1).$row, $record[$keys[$k]]);
    }
    //echo "\r\n";
}
/*============================================================================*/

//$objPHPExcel->setCellValue('A1', 'Hello');



$objPHPExcel->getActiveSheet()->setTitle($filename);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename2.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;