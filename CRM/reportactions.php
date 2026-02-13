<?php

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTACTIONS",$lang,$db1);

$sql="";
$criteria = "";
$msg = "";

for($i=0;$i<15;$i++){
    $arrActions[$i]['id'] = $i ;
    $arrActions[$i]['actiontime'] = $i + 8 ;
    $arrActions[$i]['cactions'] = 0 ;
    $arrActions[$i]['simfonise'] = 0 ;
    $arrActions[$i]['recall'] = 0 ;
}

$post_txtSDate = "";
$post_cUser = 0;

if (isset($_GET['search']) && $_GET['search']==1) {
    $post_txtSDate = $_POST['txtSDate'];
    $post_cUser = $_POST['cUser'];
    
    if ($_POST['txtSDate']!="") {
        $criteria = $lg->l("date")."=".$_POST['txtSDate'];
        $sDate = $_POST['txtSDate'];
        $sDate = func::dateTo14str($sDate) ;
        $sDate = func::str14toDate($sDate, "-","EN");
        $criteriaSDate = " date_format(atimestamp,'%Y-%m-%d')='".$sDate."' ";
//        echo $sql;
        
        ///if ($_POST['cUser']<>0) {
            $user = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
            $criteriaUser = "";
            if ($_POST['cUser']<>0) {
                $criteria .= ", ".$lg->l("user")."=".$user;        
                $criteriaUser = " AND user =".$_POST['cUser'];
            }
            
            $sql .= 'SELECT date_format( atimestamp, "%H" ) AS actiontime, count( id ) AS cactions, 
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE '.$criteriaSDate.$criteriaUser.' AND status2 =5 AND date_format( atimestamp, "%H" ) =actiontime
                ) AS simfonise, 
                (SELECT count( id ) AS cactions FROM ACTIONS WHERE '.$criteriaSDate.$criteriaUser.' AND status2 =3 AND date_format( atimestamp, "%H" ) =actiontime
                ) AS recall
                FROM ACTIONS WHERE '.$criteriaSDate.$criteriaUser.'  GROUP BY date_format(atimestamp,"%H")';
//            echo $sql;
//        } 
//        else{
//            $msg = $l->l("select-user");
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
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReportActions, #form-actions {
        max-width: 600px;
        margin-left: 20px;
    }
    
    h2.search-results {
        margin-left: 20px;
    }
    
</style>


</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-12"><h2 style="margin-left:1em"><?php echo $l->l("search-actions") ?></h2></div>
        <div style="clear: both"></div>        
        <div class="col-12">
            
            <div class="form-container">

                <form id="form-actions" action="reportactions.php?search=1" method="POST">
                    <div class="col-6 col-md-12">
                        <?php
                        $txtSDate = new textbox("txtSDate", $lg->l("date"), $post_txtSDate);
                        $txtSDate->set_format("DATE");
                        $txtSDate->set_locale($locale);                        
                        $txtSDate->get_Textbox();
                        ?>

                        <?php
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
                    $gridReportActions = new datagrid("gridReportActions", $db1, 
                            $sql, 
                            array("actiontime","cactions","simfonise","recall"), 
                            array($l->l("actiontime"),$l->l("cactions"),$l->l("simfonise"),$l->l("recall")),
                            $ltoken, 0
                            );
                    $arrGridActions = $gridReportActions->get_rs();
                    //αλλαγή στο "$gridReportActions->get_rs()" προσθήκη ωρές που δεν υπάρχούν
                    for($i=0;$i<15;$i++){
                        for($k=0;$k<count($arrGridActions);$k++){
                            if($arrActions[$i]['actiontime'] == $arrGridActions[$k]['actiontime']){
                                $arrActions[$i]['id'] = $i ;
                                $arrActions[$i]['actiontime'] = $arrGridActions[$k]['actiontime'] ;
                                $arrActions[$i]['cactions'] = $arrGridActions[$k]['cactions'] ;
                                $arrActions[$i]['simfonise'] = $arrGridActions[$k]['simfonise'] ;
                                $arrActions[$i]['recall'] = $arrGridActions[$k]['recall'] ;
                            }                            
                        }
                        $arrActions[$i]['actiontime'] .= ':00';
                    }
                    //Count actions
                    $countActions = 0;
                    $countSimfonise = 0;
                    $countRecall = 0;
                    foreach ($arrActions as $values) {
                        $countActions = $countActions + $values['cactions'];
                        $countSimfonise = $countSimfonise + $values['simfonise'];
                        $countRecall = $countRecall + $values['recall'];
                    }
                    array_push($arrActions, array('id' => count($arrActions),'actiontime' => "ΣΥΝΟΛΟ",
                        'cactions' => $countActions,'simfonise' => $countSimfonise,'recall' => $countRecall));
//                    var_dump($arrActions);
                    $gridReportActions->set_rs($arrActions);
                    $gridReportActions->set_colWidths(array("50","50","50","50"));
                    $gridReportActions->set_colsFormat(array('','','',''));
                    $gridReportActions->get_datagrid();
                                        
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
