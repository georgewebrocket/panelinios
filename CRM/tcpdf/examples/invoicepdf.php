<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../../php/config.php');
require_once('../../php/session.php');
require_once('../../php/dataobjects.php');
require_once('../../php/controls.php');
require_once('../../inc.php');

$id = $_GET['id'];
$accesstoken = "";
if (isset($_GET['accesstoken'])) {
    $accesstoken = $_GET['accesstoken'];
}
$invoice = new INVOICES($db1, $id);

if ($invoice->get_accesstoken()!=$accesstoken) {
    echo "Invalid accestoken";
    exit();
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
$pdf->SetAuthor('Epagelmatias');
$pdf->SetTitle('INVOICE');
$pdf->SetSubject('');
$pdf->SetKeywords('PDF, invoice');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', '',array(0,0,0),array(255,255,255));

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

$pdf->SetFont('freemono', '', 10);


// -----------------------------------------------------------------------------

/*=================*/
$tbl = '<table cellspacing="0" cellpadding="1" border="0">';

$tbl .= '<tr>';
$tbl .= '<td>ΕΙΔΟΣ ΠΑΡΑΣΤΑΤΙΚΟΥ</td>';
$tbl .= '<td>ΣΕΙΡΑ</td>';
$tbl .= '<td>ΑΡΙΘΜΟΣ</td>';
$tbl .= '<td>ΗΜΕΡΟΜΗΝΙΑ</td>';
$tbl .= '<td>ΩΡΑ ΕΝΑΡΞ. ΑΠΟΣΤΟΛΗΣ ή ΠΑΡΑΔΟΣΗΣ</td>';
$tbl .= '</tr>';

$tbl .= '<tr>';
$tbl .= '<td>ΤΙΜΟΛΟΓΙΟ</td>';
$series = $invoice->get_seriesCode();
$tbl .= "<td>$series</td>";
$inr = $invoice->get_icode();
$tbl .= "<td>$inr</td>";
$idate = $invoice->get_idate();
$idate = func::str14toDate($idate);
$tbl .= "<td>$idate</td>";
$tbl .= "<td></td>";
$tbl .= '</tr>';

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

//$pdf->Write(0, 'ΣΧΕΤΙΚΑ ΠΑΡΑΣΤΑΤΙΚΑ', '', 0, 'R', true, 0, false, false, 0);
//$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

$pdf->writeHTML('<u>ΣΤΟΙΧΕΙΑ ΠΕΛΑΤΗ</u>', true, false, true, false, '');
$pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

$tbl = '<table width="800" cellspacing="0" cellpadding="1" border="0">';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΚΩΔΙΚΟΣ</td>';
//$companycode = $company->get_id();
$companycode = $invoice->get_company();
$tbl .= "<td>$companycode</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td>ΣΚΟΠΟΣ ΔΙΑΚΙΝΗΣΗΣ</td>';
$tbl .= "<td>ΠΑΡΟΧΗ</td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΕΠΩΝΥΜΙΑ</td>';
$tbl .= "<td>$eponimia</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td>ΤΟΠΟΣ ΦΟΡΤΩΣΗΣ</td>';
$tbl .= "<td>ΕΔΡΑ ΜΑΣ</td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΕΠΑΓΓΕΛΜΑ</td>';
//$profession = func::vlookup("description", "CATEGORIES", "id=".$company->get_basiccategory(), $db1);
$profession = $invoice->get_profession();
$tbl .= "<td>$profession</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td>ΤΟΠΟΣ ΠΡΟΟΡΙΣΜΟΥ</td>';
//$area = func::vlookup("description", "AREAS", "id=".$company->get_area(), $db1);
$area = $invoice->get_area();
$tbl .= "<td>$area</td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΟΔΟΣ / ΑΡΙΘ.</td>';
//$address = $company->get_address();
$address = $invoice->get_address();
$tbl .= "<td>$address</td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td>ΤΡΟΠΟΣ ΑΠΟΣΤΟΛΗΣ</td>';
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
$tbl .= '<td>ΑΡ. ΑΥΤΟΚ.</td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΠΟΛΗ/ΤΚ</td>';
//$tk = $company->get_zipcode();
$tk = $invoice->get_zipcode();
$city = $invoice->get_city();
$tbl .= "<td>$city / $tk </td>";
//...
$tbl .= '<td width="30">&nbsp;</td>';
$tbl .= '<td>ΤΡΟΠΟΣ ΠΛΗΡΩΜΗΣ</td>';
$tbl .= "<td></td>";
$tbl .= '</tr>';

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

$pdf->writeHTML('<u>ΣΤΟΙΧΕΙΑ ΕΙΔΩΝ</u><br/>', true, false, true, false, '');

$tbl = '<table cellspacing="0" cellpadding="1" border="1">';

//...............................
$tbl .= '<tr>';
$tbl .= '<td>ΚΩΔΙΚΟΣ</td>';
$tbl .= '<td width="130">ΠΕΡΙΓΡΑΦΗ</td>';
$tbl .= "<td width=\"50\">Μ.Μ.</td>";
$tbl .= "<td width=\"60\">ΠΟΣΟΤ.</td>";
$tbl .= "<td>ΤΙΜΗ ΜΟΝ.</td>";
$tbl .= "<td>% ΕΚΠΤΩΣΗ</td>";
$tbl .= "<td>ΑΞΙΑ ΜΕ ΕΚΠΤ.</td>";
$tbl .= "<td width=\"80\">ΦΠΑ%</td>";
$tbl .= '</tr>';

$price = $invoice->get_price(); 
$tbl .= '<tr>';
$tbl .= '<td>'.$invoice->get_description().'</td>';
$tbl .= '<td>ΚΑΤΑΧΩΡΗΣΗ ΣΕ ONLINE ΚΑΤΑΛΟΓΟ EPAGELMATIAS.GR</td>';
$tbl .= "<td>ΤΕΜ</td>";
$tbl .= "<td>1</td>";
$tbl .= "<td>".func::nrToCurrency($price)."</td>";
$discount = $invoice->get_discount();
$tbl .= "<td>".func::nrToCurrency($discount)."</td>";
$axialine = $price * (1-$discount/100);
$tbl .= "<td>".func::nrToCurrency($axialine)."</td>";
$fpapercentage = $invoice->get_vatpercentage();
$tbl .= "<td>".func::nrToCurrency($fpapercentage)."</td>";
$tbl .= "</tr>";

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->writeHTML('<br/>', true, false, true, false, '');


$tbl = '<table align="left" width="250" cellspacing="0" cellpadding="1" border="1">';

$tbl .= '<tr>';
$tbl .= '<td>ΚΑΘ. ΑΞΙΑ</td>';
$tbl .= "<td>".func::nrToCurrency($axialine)."</td>";
$tbl .= '</tr>';
$tbl .= '<tr>';
$fpa = $axialine * $fpapercentage / 100 ;
$tbl .= '<td>ΦΠΑ</td>';
$tbl .= '<td>'.func::nrToCurrency($fpa).'</td>';
$tbl .= '</tr>';
$tbl .= '<tr>';
$telikiaxia = $axialine + $fpa;
$tbl .= '<td>ΣΥΝΟΛΟ</td>';
$tbl .= '<td>'.func::nrToCurrency($telikiaxia).'</td>';
$tbl .= '</tr>';

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->writeHTML("<br/><br/><br/><br/><br/><br/>", true, false, false, false, '');
$pdf->writeHTML("ΛΟΓ. ALPHABANK 484002002004388 IBAN GR03 0140 4840 4840 0200 2004 388", 
        true, false, false, false, '');



//Close and output PDF document
$pdf->Output('invoice.pdf', 'I');

//============================================================+
// END OF FILE
//=

?>

