<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
//$l = new mlng("PACKAGES",$lang,$db1);

$id = $_GET['id'];
$courier = new COURIER($db1,$id);
$val = 0;
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['t_description'] == ""){
        $err = 1;
        $msg .= "ΔΕΝ ΥΠΑΡΧΕΙ ΠΕΡΙΓΡΑΦΗ</br>";
    }    
            
    if ($err==0) {        
        $courier->set_description($_POST['t_description']);
        $courier->set_vouchertemplate($_POST['t_vouchertemplate']); 
        $courier->set_copies($_POST['t_copies']);
        if (isset($_POST['chk_resetVoucherCount'])) {
            $courier->set_vouchercount($_POST['t_vouchercount']); 
        }
        if(isset($_POST['chk_active'])) {$active = 1;}else{$active = 0;}
        $courier->set_active($active);
        $courier->set_user($_POST['c_user']);
        $courier->set_comment($_POST['t_comment']);
        
        if ($courier->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $courier->get_id();
        }
        else {
            $msg .= "An error occured<br/>Please try again<br/>"; //...........
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="form-container">
                
        <h1>EDIT COURIER</h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editCourier.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //description
            $t_description = new textbox("t_description", "ΠΕΡΙΓΡΑΦΗ", 
                    $courier->get_description(), "* Required field");
            $t_description->get_Textbox();
            
            $t_vouchertemplate = new textbox("t_vouchertemplate", "VOUCHER TEMPLATE",
                    $courier->get_vouchertemplate(), "");
            $t_vouchertemplate->get_Textbox();
            
            $t_copies = new textbox("t_copies", "NR OF COPIES",
                    $courier->get_copies(), "");
            $t_copies->get_Textbox();
            
            $t_vouchercount = new textbox("t_vouchercount", "VOUCHER COUNT",
                    $courier->get_vouchercount(), "");
            $t_vouchercount->get_Textbox();
            
            $chk_resetVoucherCount = new checkbox("chk_resetVoucherCount", "RESET COUNTER", 0);
            $chk_resetVoucherCount->get_Checkbox();
            
            $c_user = new comboBox("c_user", $db1, "SELECT id, fullname FROM USERS", 
                    "id","fullname", $courier->get_user(), "USER");
            $c_user->get_comboBox();
            
            $t_comment = new textbox("t_comment", "COMMENT",
                    $courier->get_comment(), "");
            $t_comment->set_multiline();
            $t_comment->get_Textbox();
            
            
            //active
            $chk_active = new checkbox("chk_active", "ACTIVE", $courier->get_active());
            $chk_active->get_Checkbox();
                        
            //submit
            $btnOK = new button("BtnOk", $lg->l('save'));            
            
            echo "<div class=\"col-4\"></div><div class=\"col-8\">";
            $btnOK->get_button_simple();
            $btnCloseUpdate = new button("button", "Close & Update", "close-update");
            echo "&nbsp;";
            $btnCloseUpdate->get_button_simple();
            echo "</div>";
            
            ?> 
            
            <div style="clear: both;"></div>
            
        </form>
        
    </div>    
</body>
</html>