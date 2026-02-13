<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("PRINTERS",$lang,$db1);

$id = $_GET['id'];
$printer = new PRINTERS($db1,$id);
$val = 0;
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtPrinterName'] == ""){
        $err = 1;
        $msg .= $l->l('blank_printername')."</br>";
    }
           
    if ($err==0) {        
        $printer->set_printername($_POST['txtPrinterName']);
        $printer->set_comment($_POST['txtComment']);
        
        if ($printer->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $printer->get_id();
        }
        else {
            $msg .= $lg->l('error')."<br/>".$lg->l('try-again')."<br/>"; //...........
        }
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
                
        <h1><?php echo $l->l("form_printer"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editprinter.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //id
            $txtId = new textbox("txtId", $l->l('form_id'),$printer->get_id());
            $txtId->set_disabled();
            $txtId->get_Textbox();            
            //printername
            $txtPrinterName = new textbox("txtPrinterName", $l->l('list_printername'),$printer->get_printername(), $lg->l('required-field'));
            $txtPrinterName->get_Textbox();
            //comment
            $txtComment = new textbox("txtComment", $l->l('form_comment'),$printer->get_comment());
            $txtComment->set_multiline();
            $txtComment->get_Textbox();
            
            //submit
            $btnOK = new button("BtnOk", $lg->l('save'));            
            
            echo "<div class=\"col-4\"></div><div class=\"col-8\">";
            $btnOK->get_button_simple();
            $btnCloseUpdate = new button("button", $lg->l("close-update"), "close-update");
            echo "&nbsp;";
            $btnCloseUpdate->get_button_simple();
            echo "</div>";
            
            ?> 
            
            <div style="clear: both;"></div>
            
        </form>
        
    </div>    
</body>
</html>