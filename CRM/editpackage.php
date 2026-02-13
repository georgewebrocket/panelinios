<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("PACKAGES",$lang,$db1);

$id = $_GET['id'];
$package = new PACKAGES($db1,$id);
$val = 0;
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtDescription'] == ""){
        $err = 1;
        $msg .= $l->l('blank_description')."</br>";
    }    
    if(isset($_POST['chkActive'])) {$val = 1;}else{$val = 0;}        
    if ($err==0) {        
        $package->set_description($_POST['txtDescription']);
        $package->set_comment($_POST['txtComment']);
        $package->set_price(textbox::getCurrency($_POST['txtPrice'],$locale));
        $package->set_duration(textbox::getCurrency($_POST['txtDuration'],$locale));          
        $package->set_active(checkbox::getVal2($_POST, "chkActive"));
        $package->set_basic(checkbox::getVal2($_POST, "chk_basic"));
        $package->set_comment2($_POST['t_comment2']);
        
        $package->set_online_package($_POST['c_online_package']);
                
        if ($package->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $package->get_id();
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
                
        <h1><?php echo $l->l("form_package"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editpackage.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //description
            $txtDescription = new textbox("txtDescription", $l->l('list_description'),$package->get_description(), $lg->l('required-field'));
            $txtDescription->get_Textbox();
            
            //comment2
            $t_comment2 = new textbox("t_comment2", "Αναλυτική περιγραφή",$package->get_comment2());
            $t_comment2->set_multiline();
            $t_comment2->get_Textbox();
            
            //comment
            $txtComment = new textbox("txtComment", $l->l('list_comment'),$package->get_comment());
            $txtComment->set_multiline();
            $txtComment->get_Textbox();
            
            //price
            $txtPrice = new textbox("txtPrice", $l->l('list_price'),$package->get_price());
            $txtPrice->set_locale($locale);            
            $txtPrice->set_format("CURRENCY");
            $txtPrice->get_Textbox();
            //Duration
            $txtDuration = new textbox("txtDuration", "Διάρκεια (Ημέρες)",$package->get_duration());
            $txtDuration->set_format("CURRENCY");
            $txtDuration->set_locale($locale);
            $txtDuration->get_Textbox();
            
            //active
            $chkActive = new checkbox("chkActive", $l->l('list_active'), $package->get_active());
            $chkActive->get_Checkbox();
            
            //basic
            $chk_basic = new checkbox("chk_basic", "Βασικό", $package->get_basic());
            $chk_basic->get_Checkbox();
            
            //online-package
            $c_online_package = new comboBox("c_online_package", $db1, "", 
                    "id", "description", 
                    $package->get_online_package(), "Online package");
            $html = file_get_contents("http://www.epagelmatias.gr/if/getOnlinePackages.php");
            $arOnlinePackages = explode("||", $html);
            $rs = array();
            for ($i=0;$i<count($arOnlinePackages);$i++) {
                $arPackageDetails = explode("|", $arOnlinePackages[$i]);
                $rs[$i]['id'] = $arPackageDetails[0];
                $rs[$i]['description'] = $arPackageDetails[1];
            }
            $c_online_package->set_rs($rs);
            $c_online_package->get_comboBox();
            
                        
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