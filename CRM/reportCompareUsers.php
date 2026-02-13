<?php

/*

 *  */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$date1 = "";
$date2 = "";
$status = 0;
$rs = array();

if ($_POST) {
    
    $date1 = $_POST['t_date1'];
    $date1d = func::dateTo14str($date1) ;
    $date1d = func::str14toDate($date1d, "-","EN");
    
    $date2 = $_POST['t_date2'];
    if ($date2=="") { $date2 = $date1; }
    $date2d = substr(func::dateTo14str($date2), 0, 8)."235959" ;
    $date2d = func::str14toDateTime($date2d, "-","EN");
    
    $status = $_POST['t_status'];
    
    $sql = "SELECT user, count(id) AS MYCOUNT, date_format( atimestamp, \"%H\" ) AS MYHOUR FROM `ACTIONS` WHERE `status2`=? AND atimestamp >= ? AND atimestamp<= ? GROUP BY user, date_format( atimestamp, \"%H\" )";
    
    $rsActions = $db1->getRS($sql, array($status, $date1d, $date2d));
    //var_dump($rsActions);
    
    $rsUsers = $db1->getRS("SELECT id, fullname FROM USERS WHERE active=1 AND is_agent=1 ORDER BY fullname");
    
    $rsHours = array("08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21");
    
    
    //$rs = array();
        
    for ($i = 0; $i < count($rsUsers); $i++) {
        for ($k = 0; $k < count($rsHours); $k++) {
            $rs[$i]['id'] = $rsUsers[$i]['id'];
            $rs[$i]['user'] = $rsUsers[$i]['fullname'];
                        
            $action = findInArray($rsActions, 
                    array("user","MYHOUR"), 
                    array($rsUsers[$i]['id'], $rsHours[$k]));
                        
            if ($action) {
                $rs[$i][$rsHours[$k]] = $action['MYCOUNT'];
            }
            else {
                $rs[$i][$rsHours[$k]] = 0;
            }
            
            
        
        }
    
    }
    
    
    
    
    
}


function findInArray($rs, $fields, $vals) {
    
    for ($i = 0; $i < count($rs); $i++) {
        $condition = true;
        for ($k = 0; $k < count($fields); $k++) {
            $myCondition = $rs[$i][$fields[$k]] == $vals[$k];
            $condition = $condition && $myCondition;      
        }
        if ($condition) {
            return $rs[$i];
        }    
    }
    return false;
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
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 550 });	
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
        max-width: 600px;
        min-height: 0px;
    }
    
    #gridReportComparisonUsers {
        max-width: 1020px;
        margin-left: 20px;
    }
    
    h2.search-results {
        margin-left: 20px;
    }
    
    #grid {
        max-width: 1000px;
        margin-left: 20px;
    }
    
</style>


</head>
    
    
<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-12"><h2 style="margin-left:1em">Σύγκριση δραστηριότητας χρηστών</h2></div>
            <div style="clear: both"></div>   
        
            <div class="form-container">
            
                <form action="reportCompareUsers.php" method="POST">
                    
                    <?php
                    $t_date1 = new textbox("t_date1", 
                            "Ημερ. από", $date1);
                    $t_date1->set_format("DATE");
                    $t_date1->set_locale($locale);                        
                    $t_date1->get_Textbox();
    
                    $t_date2 = new textbox("t_date2", 
                            "Ημερ. έως", $date2);
                    $t_date2->set_format("DATE");
                    $t_date2->set_locale($locale);                        
                    $t_date2->get_Textbox();
    
                    $t_status = new comboBox("t_status", $db1, 
                            "SELECT id, description FROM STATUS", 
                            "id","description",
                            $status, "Status");
                    $t_status->get_comboBox();                        
                    ?>
                            
                        
                    <div style="clear: both"></div>
    
                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
    
                    <div style="clear: both"></div>
                    
                </form>
            
            <div style="clear: both"></div>
            
        </div>
        
        <div style="clear: both"></div>
        
        
        <div>
            
            <?php
            
            if ($rs) {
                
                $grid = new datagrid("grid", $db1, "", 
                        array("user", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21"), 
                        array("USER", "08.00", "09.00", "10.00", "11.00", "12.00", "13.00", "14.00", "15.00", "16.00", "17.00", "18.00", "19.00", "20.00", "21.00"));
                
                $grid->set_rs($rs);
                
                for ($i = 0; $i < count($rsHours); $i++) {
                    $grid->col_sum($rsHours[$i], '');                
                }
                                
                $grid->get_datagrid();
                
                
                
            }
            
            
            ?>
            
            
        </div>
        
        
        
        
    </div>
    
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>