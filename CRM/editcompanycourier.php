<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$id = $_GET['id'];
$company = new COMPANIES($db1,$id);

$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    //$company->set_courier_ok(checkbox::getVal($_POST['chk_courier_ok']));
    //$company->set_courier_return(checkbox::getVal($_POST['chk_courier_return']));
    $company->set_courier_status($_POST['cCourierStatus']);
    
    $company->set_courier_delivery_date(textbox::getDate($_POST['t_courier_delivery_date'], $locale));
    
    $company->set_courier_notes($_POST['t_courier_notes']);
    
    if ($company->Savedata()) {
        $msg .= $lg->l('save-ok')."<br/>"; //...........
    }
    else {
        $msg .= $lg->l('error')."<br/>".$lg->l('try-again')."<br/>"; //...........
    }
}

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
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    <script>
        $(document).ready(function() {	
                $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 450 });
                $("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });
                $("a.fancybox700").fancybox({'type' : 'iframe', 'width' : 700, 'height' : 450 });
        });
        

    </script>

    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);            
                        
            });
            
           
           
        $(document).ready(function() {
            $("form input").keypress(function (e) {
               var code = e.keyCode || e.which;
               if (code === 13) {
                   e.preventDefault();
                   return false;
               }
               });
               
            $('#close-note').click(function() {                     
                if ($('#t_new_note').val()==="") { 
                    $('#new-note').val("");
                    $('#new-note').hide();
                    return; 
                }
        
                var prevNote = $('#show_courier_notes').html();
                var currentTime = new Date();
                var sMonth = currentTime.getMonth()+ 1;
                var sTime = currentTime.getDate() + "/" +
                        sMonth + "/" +
                        currentTime.getFullYear() + " " +
                        currentTime.getHours() + ":" +
                        currentTime.getMinutes();
                        
                if (prevNote.length!=0) { prevNote += "<br/>===================<br/>"; }
                var newNote = prevNote + $('#t_new_note').val() + "<br/><br/><em><?php echo $_SESSION['user_fullname']; ?>" + " - " + sTime + "</em><br/>";
                //alert(newNote);
                $('#show_courier_notes').html(newNote);
                $('#t_courier_notes').val(newNote);
                $('#new-note').val("");
                $('#new-note').hide();
                
            });               
               
            });

    </script>
    
    
    
    
    
    <style>
        
        #t_courier_notes {
            display: none;
        }
    
		div.message {
			border-bottom: 1px dashed rgb(200,200,200);        
			padding: 10px 0px;
			font-size: 0.9em;
		}
		div.message h3 {
			display:inline; 
			margin-right: 20px;
			
		}
		
		
	</style>



    </head>

    <body class="form">
        
		<h1 style="margin:10px 0px 0px 1em"><?php echo $l->l("Καρτέλα εταιρείας - courier"); ?></h1>   
		<?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
		
		<div class="col-8">
			<div class="form-container">
				
				
				
				<form action="editcompanycourier.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
				
					<?php

					//Id
					$txtId = new textbox("txtId", $l->l('id'),$company->get_id(), $lg->l('auto'));
					$txtId->set_disabled();
					$txtId->get_Textbox();

					//companyname
					$txtCompanyname = new textbox("txtCompanyname", $l->l('companyname'),$company->get_companyname(), "*");
					$txtCompanyname->set_disabled();
					$txtCompanyname->get_Textbox();
					
					$txtPrice = new textbox("txtPrice", $l->l('Price'), $company->get_price(), "*");
					$txtPrice->set_disabled();
					$txtPrice->set_format("CURRENCY");
					$txtPrice->set_locale("GR");
					$txtPrice->get_Textbox();

	//                $chk_courier_ok = new checkbox("chk_courier_ok", $l->l("Παραδόθηκε"), 
	//                        $company->get_courier_ok());
	//                $chk_courier_ok->get_Checkbox();
					
					$cCourierStatus = new comboBox("cCourierStatus", $db1, "SELECT id, description FROM COURIERSTATUS", 
						"id","description",$company->get_courier_status(),$l->l("STATUS"));
					$cCourierStatus->get_comboBox();
					
					$t_courier_delivery_date = new textbox("t_courier_delivery_date", $l->l("Ημ. Παρ. Cour."),
							$company->get_courier_delivery_date());
					$t_courier_delivery_date->set_format("DATE");
					$t_courier_delivery_date->set_locale($locale);
					$t_courier_delivery_date->get_Textbox();
					
	//                $chk_courier_return = new checkbox("chk_courier_return", $l->l("Επιστροφή"), 
	//                        $company->get_courier_return());
	//                $chk_courier_return->get_Checkbox();

					$t_courier_notes = new textbox("t_courier_notes", "",$company->get_courier_notes(), ""); 
					$t_courier_notes->set_multiline();
					//$t_courier_notes->set_disabled();
					$t_courier_notes->get_Textbox();
					
					?>
					<!--
					<div class="col-4"><?php echo $l->l("COURIER_NOTES"); ?></div>
					
					<div class="col-8">
						<div id="show_courier_notes" style="padding:1em; width: 88%; background-color: rgb(230,230,230)"><?php echo $company->get_courier_notes(); ?></div>
						<div style="clear: both; height: 1em"></div>
						<a href="#" id="add-note" onclick="$('#new-note').show()" style="font-weight: bold">Add-Note</a>
						
						<div id="new-note" style="display:none;">
							<textarea id="t_new_note"  name="t_new_note" rows="4" cols="20"></textarea>
							<div style="clear: both; height: 0.5em"></div>
							<a href="#" id="close-note" style="font-weight: bold">OK</a>
							
						</div>
						
						<div style="clear: both; height: 1em"></div>
						
					</div>
					-->
					
					<?php
					$btnOK = new button("BtnOk", $lg->l('save'));
					$btnOK->get_button();
					?> 
					
					<div style="clear: both"></div>
				
				
				</form>
			
				<div style="margin:1em">		
					<?php	
						for ($i=0;$i<count($messages);$i++) {
						$curmessage = new MESSAGES($db1,$messages[$i]['id'],$messages);
					?>
					
					<div class="message">
                                            <div style="margin-bottom:5px;">
                                                <?php 
                                                if ($curmessage->get_receiver()==$userid) { 
                                                        $sendername = func::vlookup("fullname", "USERS", "id=".$curmessage->get_sender(), $db1)
                                                        ?>
                                                <strong>FROM: <?php echo $sendername; ?> <br/>@ <?php echo $curmessage->get_mdatetime(); ?></strong></div>
                                                <?php                 
                                                } 
                                                else { 
                                                        $receivername = func::vlookup("fullname", "USERS", "id=".$curmessage->get_receiver(), $db1)
                                                        ?>
                                                        <strong>TO: <?php echo $receivername; ?> <br/>@ <?php echo $curmessage->get_mdatetime();?></strong></div>
                                                <?php                
                                                } 
                                                ?>
                                            <?php echo $curmessage->get_message()." -- ".$curmessage->get_companyid(); ?>
					</div>
					
					<?php } ?>
				</div>
				
			</div>
			<div style="clear: both"></div>
		</div>
		
		<div class="col-4">
			<iframe src="messages.php?showlist=0&companyid=<?php echo $id; ?>" width="100%" height="450" frameborder="0"></iframe>
		</div>
		
		<div style="clear: both"></div>
        
    </body>
    
</html>