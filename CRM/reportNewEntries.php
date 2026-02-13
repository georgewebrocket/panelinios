<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTNEWENTRIES",$lang,$db1);

$sql="";
$criteria = "";
$msg = "";

$post_txtDateFrom = "";
$post_txtDateTo = "";
$post_cUser = 0;

if (isset($_GET['search']) && $_GET['search']==1) {
    $post_txtDateFrom = $_POST['txtDateFrom'];
    $post_txtDateTo = $_POST['txtDateTo'];
    $post_cUser = $_POST['cUser'];
    
    if ($_POST['txtDateFrom']!="") {
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        $yy = substr($dateFrom, 0, 4);
        $mm = substr($dateFrom, 4, 2);
        $dd = substr($dateFrom, 6, 2);
        $dateFrom = $yy."-".$mm."-".$dd." 00:00:00";
        
        if ($_POST['txtDateTo']!="") {
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
            $yy = substr($dateTo, 0, 4);
            $mm = substr($dateTo, 4, 2);
            $dd = substr($dateTo, 6, 2);
        }        
        $dateTo = $yy."-".$mm."-".$dd." 23:59:59";
        
        // $sql = "SELECT * FROM COMPANIES WHERE dataentrydatetime>='".$dateFrom."' AND dataentrydatetime<='".$dateTo."'";

        $sql = "SELECT id, SUBSTRING(companyname, 1, 60) AS companyname, status, userdataentry, dataentrydatetime FROM COMPANIES WHERE dataentrydatetime>='".$dateFrom."' AND dataentrydatetime<='".$dateTo."'";
            
        $criteria = $lg->l("DATES").":".$_POST['txtDateFrom']."-".$_POST['txtDateTo'];
        
        if ($_POST['cUser']<>0) {
            $username = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
            $sql .= " AND userdataentry=".$_POST['cUser'];            
            $criteria = $lg->l("USER").":".$username." / ".$criteria;            
        } 
        $sql .= " ORDER BY id";
    }
    else{
        $msg = "Παρακαλώ επιλέξτε ημερομηνίες";
        $sql="";
    }
}

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
        max-width: 1200px;
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

    <div class="col-12"><h2 style="margin-left:1em">Νέες Καταχωρήσεις</h2></div>
    <div style="clear: both"></div>        
    <div class="col-8 col-sm-12">

        <div class="form-container">

            <form action="reportNewEntries.php?search=1" method="POST">
                <div class="col-8 col-md-12 col-sm-12">
                    <?php
                    $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), $post_txtDateFrom);
                    $txtDateFrom->set_format("DATE");
                    $txtDateFrom->set_locale($locale);                        
                    $txtDateFrom->get_Textbox();

                    $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), $post_txtDateTo);
                    $txtDateTo->set_format("DATE");
                    $txtDateTo->set_locale($locale);                        
                    $txtDateTo->get_Textbox();

                    $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                            "id","fullname",$post_cUser,$lg->l("user"));
                    $cUser->get_comboBox(); 


                    ?>

                </div>
                <div style="clear: both"></div>

                <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

            </form>
        </div>

        <?php if ($sql!="") { ?>
        <!-- <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2> -->

            <?php
                $gridReportNE = new datagrid("gridReportUC", $db1, 
                        $sql, 
                        array("id","companyname","status","userdataentry", "dataentrydatetime"), 
                        array("ID","Επωνυμία","Status","Χρήστης data-entry", "Ημερ. data-entry"),
                        $ltoken, 0,
                        TRUE,"editcompany.php",$lg->l("open")
                        );

                $gridReportNE->set_colWidths(array("50","350","150","150","150","50"));
                //$gridReportNE->set_colsFormat(array("","","","","CURRENCY"));
                $gridReportNE->col_vlookup("status","status","STATUS","description", $db1); 
                $gridReportNE->col_vlookup("userdataentry","userdataentry","USERS","fullname", $db1);
                $gridReportNE->set_popup(FALSE);
                $gridReportNE->get_datagrid();
                
                echo "<br/><h3 style=\"margin-left:20px\">ΣΥΝΟΛΟ: ".count($gridReportNE->get_rs())."</h3>";

        }
        else{
            echo '<h2 class="search-results" style="margin-left:20px">'.$msg.'</h2>';
        }
        ?>
    </div>       

    <div style="clear: both"></div>        

</div>

<div style="clear: both"></div>    

<?php include "blocks/footer.php"; ?>       

</body>
</html>