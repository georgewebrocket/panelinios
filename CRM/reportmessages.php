<?php

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("MESSAGES",$lang,$db1);

$sql="";
$criteria = "";
$msg = "";

if (isset($_GET['search']) && $_GET['search']==1) {
    $sql = "SELECT * FROM MESSAGES WHERE id>0 ";
    if ($_POST['txtSDate']!="") {
        $criteria = $lg->l("s-date")."=".$_POST['txtSDate'];
        $sDate = $_POST['txtSDate'];
        $sDate = func::dateTo14str($sDate) ;
        $sDate = func::str14toDate($sDate, "-","EN");
        $sql .= "date_format(mdatetime,'%Y-%m-%d')='".$sDate."'";
//        echo $sql;
    }    
    if ($_POST['cSender']<>0) {
        $sender = func::vlookup("fullname", "USERS", "id=".$_POST['cSender'], $db1);
        $criteria .= ", ".$l->l("form_sender")."=".$sender;        
        $sql .= " AND sender =".$_POST['cSender'];
//            echo $sql;
    }
    if ($_POST['cReceiver']<>0) {
        $receiver = func::vlookup("fullname", "USERS", "id=".$_POST['cReceiver'], $db1);
        $criteria .= ", ". $l->l("form_receiver")."=".$receiver;        
        $sql .= " AND receiver =".$_POST['cReceiver'];
//            echo $sql;
    }   
    /*}
    else{
        $msg = $lg->l("select-date");
        $sql="";
    }*/
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
    
    #gridReportMessages {
        max-width: 1000px;
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
        
        <div class="col-3"><h2 style="margin-left:1em"><?php echo $l->l("search-messages") ?></h2></div>
        <div style="clear: both"></div>        
        <div class="col-8 col-sm-12">
            
            <div class="form-container">

                <form action="reportmessages.php?search=1" method="POST">
                    <div class="col-6 col-md-12">
                        <?php
                        $txtSDate = new textbox("txtSDate", $l->l("s-date"), "");
                        $txtSDate->set_format("DATE");
                        $txtSDate->set_locale($locale);                        
                        $txtSDate->get_Textbox();
                        ?>

                        <?php
                        $cSender = new comboBox("cSender", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname",0,$l->l("form_sender"));
                        $cSender->get_comboBox();                        
                        ?>

                        <?php
                        $cReceiver = new comboBox("cReceiver", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname",0,$l->l("form_receiver"));
                        $cReceiver->get_comboBox();                        
                        ?>
                    </div>
                    <div style="clear: both"></div>

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

                </form>
            </div>

            <?php if ($sql!="") { ?>
            <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>

                <?php
                    $gridReportMessages = new datagrid("gridReportMessages", $db1, 
                            $sql, 
                            array("mdatetime","sender","receiver","message","companyid","read"), 
                            array($l->l("form_mdatetime"),$l->l("form_sender"),$l->l("form_receiver"),$l->l("form_message"),"CUSTOMER-ID",$l->l("form_read")),
                            $ltoken, 0, 
                            TRUE,"openmessage.php",$lg->l("open")
                            );
                    $gridReportMessages->set_colWidths(array("100","150","150","450","100","50","50"));
                    $gridReportMessages->col_vlookup("sender","sender","USERS","fullname", $db1);
                    $gridReportMessages->col_vlookup("receiver","receiver","USERS","fullname", $db1);
                    $gridReportMessages->set_colsFormat(array('DATE','','','','','YESNO',''));
                    $gridReportMessages->col_func("companyid","companyid","<a class=\"fancybox1000\" href=\"editcompany.php?id=??\">??</a>", "??");
                    $gridReportMessages->get_datagrid();
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
