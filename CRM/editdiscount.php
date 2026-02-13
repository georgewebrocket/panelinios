<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("DISCOUNTS",$lang,$db1);

$id = $_GET['id'];
$discount = new DISCOUNTS($db1,$id);
$val = 0;
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['cPackage'] == "0"){
        $err = 1;
        $msg .= $l->l('blank_package')."</br>";
    }
    if ($_POST['txtDiscount'] == ""){
        $err = 1;
        $msg .= $l->l('blank_discount')."</br>";
    }
    if ($_POST['txtDatestart'] == ""){
        $err = 1;
        $msg .= $l->l('blank_datestart')."</br>";
    }
//    if (func::validateDate($_POST['txtDatestart']) == FALSE){
//        $err = 1;
//        $msg .= $l->l('error_datestart')."</br>";
//    }
    if ($_POST['txtDatestop'] == ""){
        $err = 1;
        $msg .= $l->l('blank_datestop')."</br>";
    }
//    if (func::validateDate($_POST['txtDatestop']) == FALSE){
//        $err = 1;
//        $msg .= $l->l('error_datestop')."</br>";
//    }
    if(isset($_POST['chkActive'])) {$val = 1;}else{$val = 0;}        
    if ($err==0) {        
        $discount->set_package($_POST['cPackage']);
        $discount->set_discount(textbox::getCurrency($_POST['txtDiscount'],$locale));
        $discount->set_datestart(textbox::getDate($_POST['txtDatestart'],$locale));
        $discount->set_datestop(textbox::getDate($_POST['txtDatestop'],$locale));          
        $discount->set_active($val);
                
        if ($discount->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $discount->get_id();
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

<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>

<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script>        
    $(function() {
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

</script>
</head>

<body class="form">
    <div class="form-container">
        
        <h1><?php echo $l->l("form_discount"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editdiscount.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //package
            $cPackage = new comboBox("cPackage", $db1, "SELECT id, description FROM PACKAGES WHERE active = 1", 
                    "id","description",
                    $discount->get_package(),
                    $l->l('list_package'));
            $cPackage->get_comboBox();
            //discount
            $txtDiscount = new textbox("txtDiscount", $l->l('list_discount'),$discount->get_discount(),$lg->l('required-field'));
            $txtDiscount->set_type("CURRENCY");
            $txtDiscount->get_Textbox();
            //datestart
            $txtDatestart = new textbox("txtDatestart", $l->l('list_datestart'), $discount->get_datestart());
            $txtDatestart->set_format("DATE");
            $txtDatestart->set_locale($locale);
            $txtDatestart->get_Textbox();
            
            //datestop
            $txtDatestop = new textbox("txtDatestop", $l->l('list_datestop'), $discount->get_datestop());
            $txtDatestop->set_format("DATE");
            $txtDatestop->set_locale($locale);
            $txtDatestop->get_Textbox();
            //active
            $chkActive = new checkbox("chkActive", $l->l('list_active'), $discount->get_active());
            $chkActive->get_Checkbox();
            
            
            //submit
            $btnOK = new button("BtnOk", $lg->l('save'));            
            
            echo "<div class=\"col-4\"></div><div class=\"col-8\">";
            $btnOK->get_button_simple();
            $btnCloseUpdate = new button("button", $lg->l('close-update'), "close-update");
            echo "&nbsp;";
            $btnCloseUpdate->get_button_simple();
            echo "</div>";
            
            ?> 
            
            <div style="clear: both;"></div>
            
        </form>
        
    </div>    
</body>
</html>