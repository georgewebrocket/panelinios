<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

include "php/configPanel.php";
include "php/dataobjectsPanel.php";
$dboPanel = new DB(conn_panel::$connstr, conn_panel::$username, conn_panel::$password);

$ids = $_GET['ids'];
$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    $newstatus = $_POST['cStatus'];
    
    $newDate = date("YmdHis");
    
    $otherupdates = ", csdatetime = '$newDate' ";
    if ($_REQUEST['cUser']!='') {
    $otherupdates .= ", userid= ".$_REQUEST['cUser'];
    }
    if ($_REQUEST['txtRecalldate']!='') {
    	$myRecallDate = textbox::getDate($_REQUEST['txtRecalldate'], $locale);
    	$otherupdates .= ", recalldate= '".$myRecallDate."'";
    }
    
    $productCategories = selectList::getVal("l_productcategories", $_POST);
    $productCategories = str_replace(array("[","]"), "", $productCategories);
    
    $sql = "UPDATE COMPANIES_STATUS SET status=".$newstatus." $otherupdates WHERE companyid IN (".$ids.") AND productcategory IN ($productCategories)";
    //echo $sql;
    if ($newstatus!=0) {
        $res = $db1->execSQL($sql);
        if ($res!==FALSE) {
            $msg = "Οι αλλαγές έγιναν κανονικά";
        }
        else {
            $msg = "Παρουσιάσθηκε σφάλμα";
        }
    }
    else {
        $msg = $lg->l("error");
    }
    
    if ($newstatus == 8) { // epistrofi-akyrosi
        $sql = "UPDATE TRANSACTIONS SET status=3 WHERE company IN (".$ids.") AND transactiontype=1 AND status=1";
        $res = $db1->execSQL($sql);
        if ($res!==FALSE) {
            $msg .= " / Transactions cancelled ";
        }
        else {
            $msg .= " / Transactions error ";
        }
    }
    
    if ($newstatus==9 || $newstatus==8 || $newstatus==16 || $newstatus==4) {
        $sql = "UPDATE COMPANIES SET DeliveryDate='', DeliveryTime='' WHERE id IN (".$ids.") ";
        $res = $db1->execSQL($sql);
    }
    
    $sql = "SELECT * FROM COMPANIES WHERE id IN ($ids)";
    $rs = $db1->getRS($sql);
    for ($i = 0; $i < count($rs); $i++) {
        $company = new COMPANIES($db1, $rs[$i]['id'], $rs);
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=?";
        $rs2 = $db1->getRS($sql, array($company->get_id()));
        $strComm = "";
        
        $myRecallDate = ""; 
        $myRecallTime = 0;
        
        for ($k = 0; $k < count($rs2) && $rs2; $k++) {
            $strComm .= $rs2[$k]['id']."|";
            $strComm .= $rs2[$k]['productcategory']."|";
            $strComm .= $rs2[$k]['status']."|";
            $strComm .= $rs2[$k]['userid']."|";
            $strComm .= $rs2[$k]['recalldate']."|";
            $strComm .= $rs2[$k]['recalltime'];
            if ($k<count($rs2)-1) {
                $strComm .= "/";
            }
            
            switch ($rs2[$k]['productcategory']) {
                case 1:
                    $company->set_lastactiondate1(date("YmdHis"));
                    break;
                case 2:
                    $company->set_lastactiondate2(date("YmdHis"));
                    break;
                case 3:
                    $company->set_lastactiondate3(date("YmdHis"));
                    break;
                default:
            }

            if ($myRecallDate=="" && $rs2[$k]['recalldate']!="") {
                $myRecallDate = $rs2[$k]['recalldate'];
                $myRecallTime = $rs2[$k]['recalltime'];
            }
            
        }
        
        
        $company->set_recalldate($myRecallDate);
        $company->set_recalltime($myRecallTime);
        
        $company->set_commstatus($strComm);
        $company->Savedata();
        
        //UPDATE PANELLINIOS
        $epagId = $company->get_id();
        $sql = "SELECT * FROM COMPANIES WHERE epag_id=?";
        $rsCompPanel = $dboPanel->getRS($sql, array($epagId));
        if ($rsCompPanel) {
            $companyPanel = new COMPANIES_PANEL($dboPanel, $rsCompPanel[0]['id']);        
            //STATUS ONLY
            $sqlStr = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=1";
            $rsStatus = $db1->getRS($sqlStr, array($epagId));
            $myStatus = $rsStatus? $rsStatus[0]['status']: 0;
            $companyPanel->set_epag_status($myStatus);

            $companyPanel->Savedata();
        }
        
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
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

    </script>
    
    <style>
            
        #l_productcategories {
            margin-top: 10px;
            margin-bottom: 10px;
            width: 60%;

        }
        #l_productcategories div.col-10, #l_productcategories div.col-2 {
            border-top: 1px dotted rgb(200,200,200);
            padding-top: 5px;
            padding-bottom: 5px;
        }


    </style>
    
    </head>
    
    <body class="form">
        
        <div class="form-container">
        
        <h1>Μαζική αλλαγή status επικοινωνίας</h1>
        
        
        
        <?php if ($msg!="") {echo "<h2>".$msg."</h2>";} else { ?>
        
        <form action="changeStatusBulk.php?ids=<?php echo $ids; ?>&confirm=1" method="POST">
        
            <?php

            $cStatus = new comboBox("cStatus", $db1, "SELECT id, description FROM STATUS", 
                    "id","description",0,$l->l("status"));
            $cStatus->get_comboBox(); 
            
            $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                    "id","fullname",0,"USER");
            $cUser->get_comboBox(); 
            
            $txtRecalldate = new textbox("txtRecalldate", $l->l('recall-date'),
            		"", "ΗΗ/ΜΜ/ΕΕΕΕ");
            $txtRecalldate->set_format("DATE");
            $txtRecalldate->set_locale($locale);
            $txtRecalldate->get_Textbox();

            
            
            $l_productcategories = new selectList("l_productcategories", "PRODUCT_CATEGORIES", "", $db1);
            $l_productcategories->set_descrField("description");
            $l_productcategories->set_orderby("id");
            $l_productcategories->set_label("Κατηγορίες προϊόντων");
            $l_productcategories->getList();
            
            $btnOK = new button("btnOK", $lg->l("ok"));
            $btnOK->get_button();
            
            

            ?>
            
            <div class="clear" style="height:20px;"></div>            
            
            <div>Κωδικοί καταχωρήσεων: <?php echo $ids; ?></div>
            
            <div class="clear" style="height:20px;"></div>  
            
        </form>
        
        <?php } ?>
        
        </div>
    </body>
    
</html>

