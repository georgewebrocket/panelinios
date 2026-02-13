<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


$id = $_GET['id'];
$invoice = new INVOICES($db1,$id);
$invoiceheaderid = $invoice->get_headerid();

$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    if ($invoice->Delete()) {
        $msg = "Η γραμμή τιμολογίου διαγράφτηκε<br/>";
        
        $myAmount = func::vlookup("SUM(amount)", "INVOICES", "headerid=$invoiceheaderid", $db1);
        $myVat = func::vlookup("SUM(vat)", "INVOICES", "headerid=$invoiceheaderid", $db1);

        $invoiceheader = new INVOICEHEADERS($db1, $invoiceheaderid);
        $invoiceheader->set_amount($myAmount);
        $invoiceheader->set_vat($myVat);
        $invoiceheader->Savedata();
        
    }
    else {
        $msg = "Παρουσιάσθηκε σφάλμα<br/>";
    }    
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="form-container">
            
        <?php if (isset($_GET['confirm']) && $_GET['confirm']==1) { ?>            

        <form action="" method="POST">
        <h1><?php echo $msg; ?></h1>        
        <div class="col-4"></div>
        <div class="col-8">
        <input onclick="window.parent.location.reload(false);" type="button" 
               value="CLOSE &amp; UPDATE" />
        </div>
        <div style="clear: both"></div>
        </form>

        <?php } else { ?>

        <form action="delInvoiceLine.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">
            <h1>
                Διαγραφή γραμμής τιμολογίου #<?php echo $invoice->get_id(); ?>
            </h1>
        <div class="col-4"></div>
        <div class="col-8">
        <input type="submit" value="ΟΚ" /></div>
        <div style="clear: both"></div>
        </form>

        <?php } ?>
    </div>
</body>
</html>