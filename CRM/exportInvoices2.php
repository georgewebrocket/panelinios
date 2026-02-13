<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$d1 = ""; $d2 = ""; $series = 0;
$showlinks = FALSE;

if (isset($_GET['search']) && $_GET['search']==1) {
    $d1 = $_POST['t_datestart'];
    $D1 = textbox::getDate($d1, $locale);
    $d2 = $_POST['t_dateend'];
    $D2 = textbox::getDate($d2, $locale);
    $D2 = substr($D2, 0, 8) . "235959";
    $series = $_POST['c_series'];
    $showlinks = TRUE; 
    $params = "?D1=$D1&D2=$D2&series=$series";
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
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 550 });
        $("a.fancybox1000").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 800 });
});
</script>

<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script>        
    $(function() {
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

</script>



<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #dg {
        max-width: 1200px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
</style>


</head>
    
    <body>
 
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?>
    

            <div class="main">
                
                <h2 style="margin-left:1em">ΤΙΜΟΛΟΓΙΑ EXPORT 2</h2>
                
                <div class="form-container">

                    <form action="exportInvoices2.php?search=1" method="POST">
                        
                        <?php
                        $t_datestart = new textbox("t_datestart", "ΗΜΕΡ. ΑΠΟ", $d1);
                        $t_datestart->set_format("DATE");
                        $t_datestart->set_locale($locale);                        
                        $t_datestart->get_Textbox();
                        
                        $t_dateend = new textbox("t_dateend", "ΗΜΕΡ. ΕΩΣ", $d2);
                        $t_dateend->set_format("DATE");
                        $t_dateend->set_locale($locale);                        
                        $t_dateend->get_Textbox();
                        
                        $c_series = new comboBox("c_series", $db1, 
                                "SELECT * FROM INVOICESERIES", "id", "code", 
                                $series, "ΣΕΙΡΑ");
                        $c_series->set_dontSelect("ΟΛΕΣ");
                        $c_series->get_comboBox();
                        
                        echo '<div class="dontprint">';
                        $btnOK = new button("btnOK", "ΑΝΑΖΗΤΗΣΗ");
                        $btnOK->get_button();
                        echo '</div>';
                        
                        ?>
                        <div style="clear: both; height: 30px"></div>
                        
                        
                        <?php if ($showlinks) {?>
                
                        <a target="_blank" class="button" href="exportInvoiceCustomers2.php<?php echo $params; ?>">ASCII ΠΕΛΑΤΩΝ</a>

                        <a target="_blank" class="button" href="exportInvoiceAccounts.php<?php echo $params; ?>">ASCII ΛΟΓΑΡΙΑΣΜΩΝ</a>

                        <a target="_blank" class="button" href="exportInvoiceTransactions.php<?php echo $params; ?>">ASCII ΚΙΝΗΣΕΩΝ</a>
                        
                        <a target="_blank" class="button" href="exportInvoiceKiniseis.php<?php echo $params; ?>">ΑΡΧΕΙΟ ΚΙΝΗΣΕΩΝ</a>

                        <?php } ?>
                        
                        
                    </form>
                    
                </div>
                
                <div style="clear: both"></div>
                
                

            </div>

            <div style="clear: both"></div>             
           
            <?php include "blocks/footer.php"; ?> 
            

            
    </body>
    
</html>