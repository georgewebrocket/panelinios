<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTCOMPARISONUSERS",$lang,$db1);

$sql="";
$criteria = "";
$msg = "";

if (isset($_GET['search']) && $_GET['search']==1) {
    if ($_POST['txtSDate']!="") {
        $criteria = "Ημερ. ".$_POST['txtSDate'];
        $sDate = $_POST['txtSDate'];
        $sDate = func::dateTo14str($sDate) ;
        $sDate = func::str14toDate($sDate, "-","EN");
        
        $sDate2 = $_POST['txtSDate2'];
        if ($sDate2=="") {
           $sDate2 = $_POST['txtSDate']; 
        }
        else {
            $criteria .= " - ".$_POST['txtSDate2'];
        }
        $sDate2 = substr(func::dateTo14str($sDate2), 0, 8)."235959" ;
        $sDate2 = func::str14toDate($sDate2, "-","EN");
        
        
        //$criteriaSDate = " date_format(atimestamp,'%Y-%m-%d')='".$sDate."' ";
        
        $criteriaSDate = " atimestamp >= '$sDate 00:00' AND atimestamp<= '$sDate2 23:59' ";
        
//        echo $sql;
        
        //if ($_POST['cStatus']<>0) {
            $criteriaStatus=""; //....
            if ($_POST['cStatus']<>0) {
                $status = func::vlookup("description", "STATUS", "id=".$_POST['cStatus'], $db1);
                $criteria .= ", ".$l->l("status")."=".$status;        
                $criteriaStatus = " AND Status2 =".$_POST['cStatus'];
            }
            //SELECT U.fullname, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 8 ) AS H8, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 9 ) AS H9, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 10 ) AS H10, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 11 ) AS H11, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 12 ) AS H12, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 13 ) AS H13, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 14 ) AS H14, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 15 ) AS H15, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 16 ) AS H16, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 17 ) AS H17, (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 AND date_format( atimestamp, "%H" ) = 18 ) AS H18 FROM ACTIONS AS A RIGHT OUTER JOIN USERS AS U ON A.user = U.id WHERE date_format(atimestamp,'%Y-%m-%d')='2014-05-26' AND Status2 =3 GROUP BY U.fullname 
            
            $sql .= 'SELECT U.fullname, COALESCE(T.H8,0) AS H8,COALESCE(T.H9,0) AS H9,COALESCE(T.H10,0) AS H10,COALESCE(T.H11,0) AS H11, COALESCE(T.H12,0) AS H12,COALESCE(T.H13,0) AS H13,COALESCE(T.H14,0) AS H14,COALESCE(T.H15,0) AS H15,COALESCE(T.H16,0) AS H16, COALESCE(T.H17,0) AS H17, COALESCE(T.H18,0) AS H18, COALESCE(T.H19,0) AS H19, COALESCE(T.H20,0) AS H20, COALESCE(T.H21,0) AS H21 FROM USERS U LEFT JOIN (SELECT A.user,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 8 ) AS H8,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 9 ) AS H9,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 10 ) AS H10,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 11 ) AS H11,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 12 ) AS H12,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 13 ) AS H13,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 14 ) AS H14,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 15 ) AS H15,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 16 ) AS H16,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 17 ) AS H17,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 18 ) AS H18,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 19 ) AS H19,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 20 ) AS H20,
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE user = A.user AND '.$criteriaSDate.$criteriaStatus.' AND date_format( atimestamp, "%H" ) = 21 ) AS H21
                FROM ACTIONS AS A  
                WHERE '.$criteriaSDate.$criteriaStatus.' GROUP BY A.user) T ON U.id = T.user';
            //echo $sql;
//        } 
//        else{
//            $msg = $l->l("select-status");
//            $sql="";
//        }
        
    }
    else{
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
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReportComparisonUsers {
        max-width: 1020px;
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
        
        <div class="col-3"><h2 style="margin-left:1em">Σύγκριση δραστηριότητας χρηστών</h2></div>
        <div style="clear: both"></div>        
        <div class="col-6">
            
            <div class="form-container">

                <form action="reportcomparisonusers.php?search=1" method="POST">
                    <div class="col-6 col-md-12">
                        <?php
                        $txtSDate = new textbox("txtSDate", "Ημερ. από", "");
                        $txtSDate->set_format("DATE");
                        $txtSDate->set_locale($locale);                        
                        $txtSDate->get_Textbox();
                        
                        $txtSDate2 = new textbox("txtSDate2", "Ημερ. έως", "");
                        $txtSDate2->set_format("DATE");
                        $txtSDate2->set_locale($locale);                        
                        $txtSDate2->get_Textbox();
                        
                        
                        ?>

                        <?php
                        $cStatus = new comboBox("cStatus", $db1, "SELECT id, description FROM STATUS", 
                                "id","description",0,$l->l("status"));
                        $cStatus->get_comboBox();                        
                        ?>
                        
                    </div>
                    <div style="clear: both"></div>

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

                </form>
            </div>
        </div>
        
        <div class="col-8">
            <?php if ($sql!="") { ?>
            <h2 class="search-results"><?php echo "Αποτελέσματα αναζήτησης [Κριτήρια :: ".$criteria."]"; ?></h2>

                <?php
                    $gridReportComparisonUsers = new datagrid("gridReportComparisonUsers", $db1, 
                            $sql, 
                            array("fullname","H8","H9","H10","H11","H12","H13","H14","H15","H16","H17","H18","H19","H20","H21"), 
                            array($lg->l("user"),$l->l("8.00"),$l->l("9.00"),$l->l("10.00"),$l->l("11.00"),$l->l("12.00"),
                                $l->l("13.00"),$l->l("14.00"),$l->l("15.00"),$l->l("16.00"),$l->l("17.00"),$l->l("18.00"),"19.00","20.00","21.00"),
                            $ltoken, 0
                            );
                    $arrgridReportComparisonUsers = $gridReportComparisonUsers->get_rs();
                    //αλλαγή στο "$gridReportActions->get_rs()" προσθήκη id που δεν υπάρχούν
                    for($i=0;$i<count($arrgridReportComparisonUsers);$i++){
                        $arrgridReportComparisonUsers[$i]['id'] = $i+1;
                    }
                    //Count actions
                    $countH8 = 0;$countH9 = 0;$countH10 = 0;$countH11 = 0;$countH12 = 0;$countH13 = 0;$countH14 = 0;
                    $countH15 = 0;$countH16 = 0;$countH17 = 0;$countH18 = 0;
                    $countH19 = 0;$countH20 = 0;$countH21 = 0;
                    foreach ($arrgridReportComparisonUsers as $values) {
                        $countH8 = $countH8 + $values['H8'];
                        $countH9 = $countH9 + $values['H9'];
                        $countH10 = $countH10 + $values['H10'];
                        $countH11 = $countH11 + $values['H11'];
                        $countH12 = $countH12 + $values['H12'];
                        $countH13 = $countH13 + $values['H13'];
                        $countH14 = $countH14 + $values['H14'];
                        $countH15 = $countH15 + $values['H15'];
                        $countH16 = $countH16 + $values['H16'];
                        $countH17 = $countH17 + $values['H17'];
                        $countH18 = $countH18 + $values['H18'];
                        $countH19 = $countH19 + $values['H19'];
                        $countH20 = $countH20 + $values['H20'];
                        $countH21 = $countH21 + $values['H21'];
                    }
                    array_push($arrgridReportComparisonUsers, array('id' => count($arrgridReportComparisonUsers),
                        'fullname' => $lg->l("total"),'H8' => $countH8,'H9' => $countH9,'H10' => $countH10,
                        'H11' => $countH11,'H12' => $countH12,'H13' => $countH13,'H14' => $countH14,
                        'H15' => $countH15,'H16' => $countH16,'H17' => $countH17,'H18' => $countH18,'H19' => $countH19,'H20' => $countH20,'H21' => $countH21));
//                    var_dump($arrgridReportComparisonUsers);
                    $gridReportComparisonUsers->set_rs($arrgridReportComparisonUsers);
                    $gridReportComparisonUsers->set_colWidths(array("250","70","70","70","70",
                        "70","70","70","70","70","70","70","70","70","70"));
                    $gridReportComparisonUsers->set_colsFormat(array('','NR','NR','NR','NR','NR','NR','NR','NR','NR','NR','NR','NR','NR','NR'));
                    $gridReportComparisonUsers->get_datagrid();
                                        
            }
            else{
                echo '<h2 class="search-results">'.$msg.'</h2>';
            }
            ?>
               
        </div>
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>
