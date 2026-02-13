<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$voucher = new VOUCHERS($db1, $id);
$company = new COMPANIES($db1, $voucher->get_customer());


$userid = $_SESSION['user_id'];
$sql = "SELECT * FROM MESSAGES WHERE companyid=$id AND ((`sender`=$userid) OR (`receiver`=$userid)) ORDER BY id DESC";
$messages = $db1->getRS($sql);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
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
        
        <h1 style="margin:10px 0px 0px 1em">Καρτέλα εταιρείας - courier</h1>   
	<?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <div class="col-8">
            <div class="form-container">
                <form action="editVoucherCourier.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
                    <?php
                    
                    $t_vcode = new textbox("t_vcode", "Voucher code", $voucher->get_vcode());
                    $t_vcode->set_disabled();
                    $t_vcode->get_Textbox();
                    
                    $t_customer = new textbox("t_customer", "Πελάτης", $company->get_companyname());
                    $t_customer->set_disabled();
                    $t_customer->get_Textbox();
                    
                    $t_amount = new textbox("t_amount", "Ποσό", $voucher->get_amount());
                    $t_amount->set_disabled();
                    $t_amount->set_format("CURRENCY");
                    $t_amount->set_locale("GR");
                    $t_amount->get_Textbox();
                    
                    $c_courier_status = new comboBox("c_courier_status", $db1, "SELECT id, description FROM COURIERSTATUS", "id","description", $voucher->get_courier_status(), "Status");
                    $c_courier_status->get_comboBox();
                    
                    $t_courier_delivery_date = new textbox("t_courier_delivery_date", 
                            "Ημ. Παραδ. Cour.", $voucher->get_courier_delivery_date());
                    $t_courier_delivery_date->set_format("DATE");
                    $t_courier_delivery_date->set_locale($locale);
                    $t_courier_delivery_date->get_Textbox();
                    
                    
                    $btnOK = new button("BtnOk", $lg->l('save'));
                    $btnOK->get_button();
                    
                    ?>
                    
                    <div class="clear"></div>
                
                </form>
                
                <div style="margin:1em">
                    
                    <?php	
                        for ($i=0;$i<count($messages);$i++) {
                        $curmessage = new MESSAGES($db1,$messages[$i]['id'],$messages);
                        if ($curmessage->get_receiver()==$userid) { 
                            $msgheader = "<strong>FROM: " . func::vlookup("fullname", "USERS", "id=".$curmessage->get_sender(), $db1) . " " . $curmessage->get_mdatetime() ."</strong>";
                        }
                        else {
                            $msgheader = "<strong>TO: " . func::vlookup("fullname", "USERS", "id=".$curmessage->get_receiver(), $db1) . " " . $curmessage->get_mdatetime() ."</strong>";
                        }
                    ?>
                    
                    <div class="message">
                        <div style="margin-bottom:5px;"></div>
                        <?php 
                        echo $msgheader . "<br/>";
                        echo $curmessage->get_message()." -- ".$curmessage->get_companyid();
                        ?>
                    </div>                    
                    <?php } ?>
                    
                </div>
                
                
            </div>
        </div>
        
        <div class="col-4">
            
            <iframe src="messages.php?showlist=0&companyid=<?php echo $voucher->get_customer(); ?>" width="100%" height="450" frameborder="0"></iframe>
            
        </div>
        
        <div class="clear"></div>
        
    </body>
    
</html>