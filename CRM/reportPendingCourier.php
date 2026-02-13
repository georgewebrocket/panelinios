<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTPENDINGCOURIER",$lang,$db1);

$criteria = "";
if (isset($_POST['c_courier'])) {
    $courier = $_POST['c_courier'];
    $criteria = " AND (courier=$courier)";
}

$sql="SELECT COMPANIES.id, companyname, phone1, DeliveryDate, MAX(ACTIONS.atimestamp) as PrintDate, courier FROM `COMPANIES` INNER JOIN ACTIONS ON COMPANIES.id = ACTIONS.company 
WHERE status2 = 6 AND status IN (6,7) $criteria
GROUP BY COMPANIES.id, companyname, phone1, DeliveryDate";






?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
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
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReportUC {
        max-width: 1100px;
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
        
        <div class="col-3">
            
            <h2 style="margin-left:0px">ΕΚΚΡΕΜΟΤΗΤΕΣ COURIER</h2>
        
        
            <form style="margin-left:0px" name="form-search" action="reportPendingCourier.php" method="POST">

                <?php            

                $c_courier = new comboBox("c_courier", $db1, 
                        "SELECT id, description FROM COURIER WHERE active=1", 
                        "id","description",0,"Courier");                                   
                $c_courier->get_comboBox();


                ?>

                <div style="clear: both"></div>
                <input name="BtnSearch" type="submit" value="OK" />

            </form>
            
        </div>
        
        
        <div style="clear: both"></div>        
        <div class="col-8">
            
            

            <?php if ($sql!="") { 
                    $gridReportPC = new datagrid("gridReportPC", $db1, 
                            $sql, 
                            array("id","companyname","phone1","DeliveryDate","PrintDate","courier"), 
                            array("ID","Επωνυμία","Τηλέφωνο","Ημερ. παραδ.","Ημερ. εκτύπ.","COURIER"),
                            $ltoken, 0,
                            TRUE,"editcompany.php",$lg->l("open")
                            );
                                                           
                    $gridReportPC->set_colWidths(array("50","350","150","150","150","150","50"));
                    $gridReportPC->set_colsFormat(array("","","","DATE","DATE",""));
                    $gridReportPC->col_vlookup("courier","courier","COURIER","description", $db1); 
                    //$gridReportPC->col_vlookup("user","user","USERS","fullname", $db1);
                    $gridReportPC->get_datagrid();
                    
                    echo "<br/><h3>ΣΥΝΟΛΟ: ".count($gridReportPC->get_rs())."</h3>";
                                        
            }
            else{
                echo '<h2 class="search-results">'.$l->l("no-data").'</h2>';
            }
            ?>
        </div>       
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>




