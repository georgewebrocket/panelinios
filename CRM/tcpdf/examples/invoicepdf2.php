<?php

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('../../php/config.php');
//require_once('../../php/session.php');
require_once('../../php/dataobjects.php');
require_once('../../php/controls.php');
require_once('../../inc.php');

$companyIPaddress = "185.70.76.16";

$id = $_GET['id'];
$accesstoken = "";
if (isset($_GET['accesstoken'])) {
    $accesstoken = $_GET['accesstoken'];
}
$invoice = new INVOICEHEADERS($db1, $id);

if ($invoice->get_accesstoken()!=$accesstoken) {
    echo "Invalid accestoken";
    exit();
}
else {
    if ($_SERVER['REMOTE_ADDR'] != $companyIPaddress) {
        $timesRead = $invoice->get_timesread();
        $timesRead = $timesRead==""? 0: $timesRead;
        $timesRead++;
        $invoice->set_timesread($timesRead);
        $invoice->Savedata();
    }
}

//$companyid = $invoice->get_company();
//$company = new COMPANIES($db1, $companyid);

$idate = $invoice->get_idate();
//$eponimia = $company->get_eponimia();
$eponimia = $invoice->get_companyname();


// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintFooter(false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Panelinios');
$pdf->SetTitle('INVOICE');
$pdf->SetSubject('');
$pdf->SetKeywords('PDF, invoice');

// set default header data

if ($invoice->get_publisher()==1) {
    $headerImage = "logo.jpg";
}
elseif ($invoice->get_publisher()==2) {
    // $headerImage = "kzigogiannis-header.jpg";
    if ($invoice->get_idate()<'20260624000000') {
        $headerImage = "kzigogiannis-header.jpg";
    }
    else {
        $headerImage = "kzigogiannis-header-20260624.png";
    }
}

//PDF_HEADER_LOGO
$pdf->SetHeaderData($headerImage , PDF_HEADER_LOGO_WIDTH, '', '',array(0,0,0),array(255,255,255));
//echo PDF_HEADER_LOGO;

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}


// ---------------------------------------------------------

// set font
//$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetFont('freeserif', '', 20);

// add a page
$pdf->AddPage();

//$pdf->SetFont('freemono', '', 10);
$pdf->SetFont('freeserif', '', 11);


// -----------------------------------------------------------------------------

/*=================*/
$tbl = "";

$series = $invoice->get_seriesCode();
$inr = $invoice->get_icode();
$idate = $invoice->get_idate();
$idate = func::str14toDate($idate);
$tbl .= "<h1>ΤΙΜΟΛΟΓΙΟ $series-$inr &nbsp;&nbsp; $idate</h1>";
$tbl .= "<hr></hr>";

$style3 = array('width' => 1, 'cap' => 'square', 'join' => 'mitter', 'dash' => '2,10', 'color' => array(200, 200, 200));
$pdf->Line(50, 300, 800, 300, $style3);

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

//stoixeia pelati
$tbl = '<table width="800" cellspacing="0" cellpadding="1" border="0">';

//$companycode = $company->get_id();
$companyid = $invoice->get_company();
$company = new companies($db1, $companyid);
//$companycode = $company->get_catalogueid() * 2 + 7128;

//...............................
$tbl .= '<tr>';
$tbl .= '<td style="width:150px">ΕΠΩΝΥΜΙΑ</td>';
$tbl .= "<td style=\"width:500px\">$eponimia</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΕΠΑΓΓΕΛΜΑ</td>';
//$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
$profession = $invoice->get_profession();
$tbl .= "<td>$profession</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
//$area = func::vlookup("description", "AREAS", "id=".$company->get_area(), $db1);
$area = $invoice->get_area();
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΔΙΕΥΘΥΝΣΗ</td>';
//$address = $company->get_address();
$address = $invoice->get_address();
$tk = $invoice->get_zipcode();
$city = $invoice->get_city();
$tbl .= "<td>$address , $city ΤΚ $tk</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΤΗΛ</td>';
//$til = $company->get_phone1();
$til = $invoice->get_phone();
$tbl .= "<td>$til</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
/*$tbl .= '<tr>';
$tbl .= '<td>ΠΟΛΗ/ΤΚ</td>';
//$tk = $company->get_zipcode();
$tk = $invoice->get_zipcode();
$city = $invoice->get_city();
$tbl .= "<td>$city / $tk </td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';*/

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΑΦΜ</td>';
//$afm = $company->get_afm();
$afm = $invoice->get_afm();
$tbl .= "<td>$afm</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΔΟΥ</td>';
//$doy = $company->get_doy();
$doy = $invoice->get_doy();
$tbl .= "<td>$doy</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td></td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

//$pdf->writeHTML('<u>ΣΤΟΙΧΕΙΑ ΕΙΔΩΝ</u><br/>', true, false, true, false, '');

$pdf->writeHTML('<br/>', true, false, true, false, '');

//stoixeia eidwn
$tbl = '<table cellspacing="0" cellpadding="3" border="1">';

//...............................
$tbl .= '<tr style="background-color: #eee;">';
//$tbl .= '<td>ΚΩΔΙΚΟΣ</td>';
$tbl .= '<td width="170">ΠΕΡΙΓΡΑΦΗ</td>';
//$tbl .= "<td width=\"50\">Μ.Μ.</td>";
$tbl .= "<td width=\"60\">ΠΟΣΟΤ.</td>";
$tbl .= "<td>ΤΙΜΗ ΜΟΝ.</td>";
$tbl .= "<td>% ΕΚΠΤΩΣΗ</td>";
$tbl .= "<td>ΑΞΙΑ ΜΕ ΕΚΠΤ.</td>";
$tbl .= "<td width=\"80\">ΦΠΑ%</td>";
$tbl .= '</tr>';

$sql = "SELECT * FROM INVOICES WHERE headerid=?";
$rs = $db1->getRS($sql, array($id));

$totalAxiaLine = 0;
for ($i = 0; $i < count($rs); $i++) {
    $invoiceLine = new INVOICES($db1, $rs[$i]['id'], $rs);
    $price = $invoiceLine->get_price(); 
    $tbl .= '<tr>';
    //$tbl .= '<td>'.$invoiceLine->get_description().'</td>';
    $tbl .= '<td>'.$invoiceLine->get_comment().'</td>';
    //$tbl .= "<td>ΤΕΜ</td>";
    $tbl .= "<td>1</td>";
    $tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($price)." €</td>";
    $discount = $invoiceLine->get_discount();
    $tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($discount)."</td>";
    $axialine = $price * (1-$discount/100);
    $totalAxiaLine += $axialine;
    $tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($axialine)." €</td>";
    $fpapercentage = $invoiceLine->get_vatpercentage();
    $tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($fpapercentage)."</td>";
    $tbl .= "</tr>";
}

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->writeHTML('<br/>', true, false, true, false, '');

//synola
$tbl = "<table border=\"0\"><tr><td width=\"380px\"></td><td>";
$tbl .= '<table align="left" width="250" cellspacing="0" cellpadding="3" border="1">';

$tbl .= '<tr>';
$tbl .= '<td style="background-color: #eee;">ΚΑΘ. ΑΞΙΑ</td>';
$tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($totalAxiaLine)." €</td>";
$tbl .= '</tr>';
$tbl .= '<tr>';
$fpa = $totalAxiaLine * $fpapercentage / 100 ;
$tbl .= '<td style="background-color: #eee;">ΦΠΑ</td>';
$tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($fpa).' €</td>';
$tbl .= '</tr>';
$tbl .= '<tr>';
$telikiaxia = $totalAxiaLine + $fpa;
$tbl .= '<td style="background-color: #eee;">ΣΥΝΟΛΟ</td>';
$tbl .= "<td style=\"text-align:right\">".func::nrToCurrency($telikiaxia).' €</td>';
$tbl .= '</tr>';

$tbl .= '</table>';
$tbl .= '</td></tr></table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->writeHTML("<br/><br/><br/><br/><br/><br/>", true, false, false, false, '');
$paymethodId = $invoice->get_paymethod();
if ($paymethodId>0) {
    $paymethodDescr = func::vlookup("description", "INV_PAYMETHODS", "id=$paymethodId", $db1);
    $pdf->writeHTML("Τρόπος πληρωμής: $paymethodDescr", 
        true, false, false, false, '');
}

$pdf->writeHTML("<br/><br/>Mydata Mark: " . $invoice->get_myDataMark(), true, false, false, false, '');

$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$pdf->write2DBarcode($invoice->get_myDataQrCode(), 'QRCODE,M', 160, 230, 40, 40, $style, 'N');




//Close and output PDF document
$pdf->Output('invoice.pdf', 'I');

//============================================================+
// END OF FILE
//=

?>

