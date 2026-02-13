<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$d1 = ""; $d2 = ""; $series = 0;

if (isset($_GET['search']) && $_GET['search']==1) {
    $d1 = $_POST['t_datestart'];
    $D1 = textbox::getDate($d1, $locale);
    $d2 = $_POST['t_dateend'];
    $D2 = textbox::getDate($d2, $locale);
    $series = $_POST['c_series'];
    $sql = "SELECT C.id, I.id AS iid, I.idate, I.amount, I.vat, I.icode, "
            . "IF(C.eponimia = '' OR C.eponimia IS NULL, C.companyname, "
            . "C.eponimia) AS cname, C.afm, C.doy, C.address, IC.code AS scode  "
            . "FROM INVOICES I INNER JOIN COMPANIES C ON I.company=C.id "
            . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
            . "WHERE idate>=$D1 AND iDate<=$D2 ";
    if ($series>0) {
        $sql .= "AND series = $series";
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
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 550 });
        $("a.fancybox1000").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 800 });
});
</script>

<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
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
        <div class="dontprint">
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?>
        </div>

            <div class="main">
                
                <h2 style="margin-left:1em">ΤΙΜΟΛΟΓΙΑ</h2>
                
                <div class="form-container">

                    <form action="reportInvoices.php?search=1" method="POST">
                        
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
                        <div style="clear: both"></div>
                        
                    </form>
                    
                </div>
                
                <?php
                if ($sql!="") {
                    $dg = new datagrid("dg", $db1, $sql, 
                        array("iid","idate", "scode", "icode", "cname", "afm", "doy", "address", "amount", "vat"), 
                        array("ID","ΗΜΕΡ.", "ΣΕΙΡΑ", "ΚΩΔ", "ΕΠΩΝΥΜΙΑ", "ΑΦΜ", "ΔΟΥ", "ΔΝΣΗ", "ΚΑΘ.ΑΞΙΑ", "ΦΠΑ"), 
                        $ltoken);
                    $dg->set_colsFormat(array("","DATE", "", "", "", "", "", "", "CURRENCY", "CURRENCY", ""));
                    $dg->set_edit("editcompany.php", "COMPANY");
                    $dg->col_sum("amount");
                    $dg->col_sum("vat");
                    $dg->get_datagrid();
                    
                    echo "<br/><h2 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ". count($dg->get_rs()). "</h2>";
                    
                    
                }
                
                
                ?>
                
                <a class="button" onclick="window.print();" style="margin-left: 1em">ΕΚΤΥΠΩΣΗ</a>


            </div>

            <div style="clear: both"></div>    
            
            <div class="dontprint">
            <?php include "blocks/footer.php"; ?>       
            </div>
            
    </body>
    
</html>