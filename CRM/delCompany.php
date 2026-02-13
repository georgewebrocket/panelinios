<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);
require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$id = $_GET['id'];

$msg = $l->l('delete-company?');
$del = 0;
if (isset($_REQUEST['del'])){$del = 1;}
//var_dump($del);
//var_dump($id);
if ($del==1) {
    
    //delete from table `MESSAGES`
    $result = $db1->execSQL("SELECT * FROM `MESSAGES` WHERE `companyid`=?", 
		array($id));
    
    //delete from table `ACTIONS`
    $result = $db1->execSQL("DELETE FROM `ACTIONS` WHERE `company`=?", 
		array($id));
    
    //delete COMPANIES
    $company = new COMPANIES($db1,$id);
    if($company->Delete()){
        $msg = $l->l('company-deleted');
        $del = 1;
    }
    else{
        $msg = $l->l('error');
        $del = 0;
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
    
    </head>

    <body class="form">
        <div class="form-container">
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <?php if($del == 0){ ?>
                <form action="delCompany.php?del=1&id=<?php echo $id; ?>" method="POST">

                   <div class="col-4">
                        <?php
                        $btnOK = new button("btnOK", $l->l("delete"));
                        $btnOK->get_button_simple();

                        ?>

                    </div>
                    <div style="clear: both"></div>
                </form>
            <?php } ?>
        </div>
    </body>
</html>