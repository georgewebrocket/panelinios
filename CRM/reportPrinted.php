<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTPENDINGCOURIER",$lang,$db1);

$sql="";

$post_txtDate = "";

if (isset($_GET['search']) && $_GET['search']==1) {
    
    $post_txtDate = $_POST['txtDate'];


    if ($_POST['txtDate']!="") {
        $dateFrom = textbox::getDate($_POST['txtDate'], $locale); //str14
        $yy = substr($dateFrom, 0, 4);
        $mm = substr($dateFrom, 4, 2);
        $dd = substr($dateFrom, 6, 2);
        $dateFrom = $yy."-".$mm."-".$dd." 00:00:00";
        $dateTo = $yy."-".$mm."-".$dd." 59:59:59";
        
        $sql="SELECT COMPANIES.id, companyname, phone1, DeliveryDate, MAX(ACTIONS.atimestamp) as PrintDate FROM `COMPANIES` INNER JOIN ACTIONS ON
            COMPANIES.id = ACTIONS.company      
            WHERE status2 = 6 
            AND atimestamp>='".$dateFrom."' AND atimestamp<='".$dateTo."' 
            GROUP BY COMPANIES.id, companyname, phone1, DeliveryDate"; 
        
        $criteria = $lg->l("DATE").":".$_POST['txtDate'];
        
    }
    else {
        $msg = $lg->l("select-date");
        $sql="";
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
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 550 });	
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
        max-width: 500px;
        min-height: 0px;
    }
    
    #gridReport {
        max-width: 900px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    form {
/*        margin: 0px;
        margin-bottom: 1em;*/
    }
    
</style>
</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-3"><h2 style="margin-left:1em">Εκτυπώσεις (Printed Companies)</h2></div>
        <div style="clear: both"></div>        
        
        <div class="form-container">
            <form action="reportPrinted.php?search=1" method="POST">
                <div class="col-8 col-md-12 col-sm-12">
                    <?php
                    $txtDate = new textbox("txtDate", $lg->l("date"), $post_txtDate);
                    $txtDate->set_format("DATE");
                    $txtDate->set_locale($locale);                        
                    $txtDate->get_Textbox();

                    ?>

                </div>
                <div style="clear: both"></div>

                <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

            </form>
        </div>
        
        
        
        <div class="col-8">
            
            <?php if ($sql!="") { ?>
            <!-- <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2> -->

            <?php if ($sql!="") { 
                    $gridReport = new datagrid("gridReport", $db1, 
                            $sql, 
                            array("id","companyname","DeliveryDate","PrintDate"), 
                            array("ID","Επωνυμία","Ημερ. παράδοσης","Ημερ. εκτύπωσης"),
                            $ltoken, 0,
                            TRUE,"editcompany.php",$lg->l("open")
                            );
                                                           
                    $gridReport->set_colWidths(array("50","350","150","150","50"));
                    $gridReport->set_colsFormat(array("","","DATE","DATE"));
                    $gridReport->get_datagrid();
                    
                    echo "<br/><h3 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ".count($gridReport->get_rs())."</h3>";
                                        
            }
            else{
                echo '<h2 class="search-results" style=\"margin-left:1em\">'.$l->l("no-data").'</h2>';
            }
            }
            ?>
        </div>       
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>

