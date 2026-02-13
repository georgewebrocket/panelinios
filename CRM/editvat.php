<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("VAT",$lang,$db1);

$id = $_GET['id'];
$vat = new VAT($db1,$id);

$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtZone'] == ""){
        $err = 1;
        $msg .= $l->l('blank_zone')."</br>";
    }
    if ($_POST['txtValue'] == ""){
        $err = 1;
        $msg .= $l->l('blank_value')."</br>";
    }
    if ($err==0) {        
        $vat->set_zone($_POST['txtZone']);
        $vat->set_value(textbox::getCurrency($_POST['txtValue'],$locale));
        if ($vat->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $vat->get_id();
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
                
        <h1><?php echo $l->l("list_vat"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editvat.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //zone
            $txtZone = new textbox("txtZone", $l->l('list_zone'),$vat->get_zone(), $lg->l('required-field'));
            $txtZone->get_Textbox();
            //value
            $txtValue = new textbox("txtValue", $l->l('list_value'),$vat->get_value(), $lg->l('required-field'));
            $txtValue->get_Textbox();
            
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