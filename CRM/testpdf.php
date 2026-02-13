<?php
//require('pdf/fpdf.php');
//
////header('Content-Type: text/html; charset=utf-8');
//
//$pdf = new FPDF();
//$pdf->AddPage();
//$pdf->AddFont('Tahoma', '', 'tahoma.php');
//$pdf->SetFont('Tahoma', '', 16);
//$pdf->Cell(40,10,'γεια');
//$pdf->Output();

require('pdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddFont('Tahoma','','tahoma.php');
$pdf->AddPage();
$pdf->SetFont('Tahoma','',35);
$pdf->Write(10,'Enjoy new fonts with FPDF! γεια σου...');
$pdf->Output();
?>