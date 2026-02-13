<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

include_once "php/configEpag.php";
$dboEpag = new DB(conn_epag::$connstr, conn_epag::$username, conn_epag::$password);
// include_once "php/dataobjectsPanel.php";


$userIds = "";

$compCount = 100;
$expires1 = "";
$expires2 = "";
$category = 0;
$reference = 0;
$status = 0;
$product = 0;
$product_assign = isset($_POST['c_product_assign'])? $_POST['c_product_assign']: 1; //default = KX
$user = 0;
$statusDateFrom = "";
$statusDateTo = "";
$companyIds = "";
$log_companyids = "";

$statusEpagS = 0;
$statusEpagP = 0;
$statusEpagDateFrom = "";
$statusEpagDateTo = "";

$msg = "";
$userIds="";
if ($_POST) {
    $userIds = selectList::getVal('c_listUsers', $_POST);
    if ($userIds=="") {
        $msg = "Παρακαλώ επιλέξτε χρήστη";
    }
}

if ($_POST && $userIds!="") {
    
    //$epagStatus = $_POST['c_epagStatus'];
    $userIds = selectList::getVal('c_listUsers', $_POST);
    $userIds2 = str_replace(array("[","]"), "", $userIds);
    $users = explode(",", $userIds2);
    $k = 0; //users index
    
    $compCount = $_POST['c_compCount'];
    
    $statusEpagS = checkbox::getVal2($_POST, "t_statusEpagS");
    $statusEpagP = checkbox::getVal2($_POST, "t_statusEpagP");
    $statusEpagDateFrom = textbox::getDate($_POST['t_statusEpagDateFrom'], $locale);
    $statusEpagDateTo = textbox::getDate($_POST['t_statusEpagDateTo'], $locale);
    $statusEpagDateTo = substr($statusEpagDateTo, 0, 8) . "235959";
    
    $sql = "SELECT COMPANIES.* FROM COMPANIES ";
    $criteria = "";
       
    if (($_POST['c_status']>0 && $_POST['c_product']>0) || ($statusEpagS + $statusEpagP > 0)) {
    
        $sql .= "INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ";
        $companystatus = 2;
        $sql .= " WHERE (COMPANIES.status <> 1 OR COMPANIES.status IS NULL) ";
    }
    else {
        $sql .= " WHERE COMPANIES.status >0 ";
    }
    
    
    $params = array();
        
    if ($_POST['t_expireDate1']!="" && $_POST['t_expireDate2']!="") {
        $expires1 = textbox::getDate($_POST['t_expireDate1'], $locale); 
        $expires2 = textbox::getDate($_POST['t_expireDate2'], $locale); 
        $sql .= " AND COMPANIES.expires >= ? AND COMPANIES.expires <= ? ";
        array_push($params, $expires1);
        array_push($params, $expires2);

        $criteria .= "Ημερ. λήξης " . $_POST['t_expireDate1'] . "-" . $_POST['t_expireDate2'] . "<br/>";

    }
    
    if ($_POST['c_category']>0) {
        $category = $_POST['c_category'];    
        $sql .= " AND COMPANIES.basiccategory = ? ";
        array_push($params, $category);

        $criteria .= "Κατηγορία πελατών " . func::vlookup("description", "CATEGORIES", "id=$category", $db1) . "<br/>";

    }
    
    if ($_POST['c_reference']>0) {
        $reference = $_POST['c_reference'];    
        $sql .= " AND COMPANIES.reference = ? ";
        array_push($params, $reference);

        $criteria .= "Reference " . func::vlookup("description", "REFERENCE", "id=$reference", $db1) . "<br/>";

    }
    
    if ($_POST['c_area']>0) {
        $area = $_POST['c_area'];    
        $sql .= " AND area = ? ";
        array_push($params, $area);

        $criteria .= "Περιοχή " . func::vlookup("description", "AREAS", "id=$area", $db1) . "<br/>";

    }
    
    if ($_POST['c_city']>0) {
        $city = $_POST['c_city'];    
        $sql .= " AND city_id = ? ";
        array_push($params, $city);

        $criteria .= "Πόλη " . func::vlookup("description", "EP_CITIES", "id=$city", $db1) . "<br/>";

    }
    
    if ($_POST['c_status']>0) {
        $status = $_POST['c_status'];    
        $sql .= " AND COMPANIES_STATUS.status = ? ";
        array_push($params, $status);

        $criteria .= "Status " . func::vlookup("description", "STATUS", "id=$status", $db1) . "<br/>";

    }
    
    if ($_POST['c_product']>0) {
        $product = $_POST['c_product'];    
        $sql .= " AND COMPANIES_STATUS.productcategory = ? ";
        array_push($params, $product);

        $criteria .= "Κατ. προϊόντος " . func::vlookup("description", "PRODUCT_CATEGORIES", "id=$product", $db1) . "<br/>";

    }
    
    if ($_POST['c_user']>0) {
        $user = $_POST['c_user'];    
        $sql .= " AND COMPANIES_STATUS.userid = ? ";
        array_push($params, $user);

        $criteria .= "Χρήστης " . func::vlookup("fullname", "USERS", "id=$user", $db1) . "<br/>";

    }
    
    
    
    // if ($_POST['t_statusDateFrom']!="" ) {
    //     $statusDateFrom = $_POST['t_statusDateFrom'];
    //     $sql .= " AND COMPANIES_STATUS.csdatetime >= ? ";
    //     $str14Date = substr(func::dateTo14str($statusDateFrom), 0, 8)."000000";
    //     array_push($params, $str14Date);
    // } 
    
    // if ($_POST['t_statusDateTo']!="" ) {
    //     $statusDateTo = $_POST['t_statusDateTo'];
    //     $sql .= " AND COMPANIES_STATUS.csdatetime <= ? ";
    //     $str14Date = substr(func::dateTo14str($statusDateTo), 0, 8)."235959";
    //     array_push($params, $str14Date);
    // }


    if ($_POST['t_statusDateFrom']!="" && $_POST['t_statusDateTo']!="") {
        $statusDateFrom = $_POST['t_statusDateFrom'];
        $sql .= " AND COMPANIES_STATUS.csdatetime >= ? ";
        $str14Date = substr(func::dateTo14str($statusDateFrom), 0, 8)."000000";
        array_push($params, $str14Date);

        $statusDateTo = $_POST['t_statusDateTo'];
        $sql .= " AND COMPANIES_STATUS.csdatetime <= ? ";
        $str14Date = substr(func::dateTo14str($statusDateTo), 0, 8)."235959";
        array_push($params, $str14Date);

        $criteria .= "Ημερ. status "  . $_POST['t_statusDateFrom'] . "-" . $_POST['t_statusDateTo'] . "<br/>";

    } 


    if ($_POST['t_assignDateFrom']!="" && $_POST['t_assignDateTo']!="") {
        $assignDateFrom = $_POST['t_assignDateFrom'];
        $assignDateTo = $_POST['t_assignDateTo'];
        $sql .= " AND COMPANIES.id NOT IN (SELECT company FROM ACTIONS WHERE atimestamp>=? AND atimestamp<=?) ";
        $str14Date1 = func::grdate_to_date($assignDateFrom)." 00:00:00";
        array_push($params, $str14Date1);
        $str14Date2 = func::grdate_to_date($assignDateTo)." 23:59:59";
        array_push($params, $str14Date2);

        $criteria .= "Να μην έχει γίνει ανάθεση  "  . $_POST['t_assignDateFrom'] . "-" . $_POST['t_assignDateTo'] . "<br/>";
    } 
    
    
    
    
    if ($statusEpagS==1 || $statusEpagP==1) {
        $sEpagDateFromDT = func::str14toDateTime($statusEpagDateFrom, "-", "EN");
        $sEpagDateToDT = func::str14toDateTime($statusEpagDateTo, "-", "EN");
        $statusIds = "";
        if ($statusEpagS==1) { 
            $statusIds = "5"; 
            $criteria .= "ΣΥΜΦΩΝΗΣΕ EPAGELMATIAS <br/>";
        }
        if ($statusEpagP==1) { 
            $statusIds = $statusIds==""? "9": $statusIds . ",9"; 
            $criteria .= "ΠΛΗΡΩΣΕ EPAGELMATIAS <br/>";
        }

        $criteria .= "Ημερ. status EPAGELMATIAS " . $_POST['t_statusEpagDateFrom'] . "-" . $_POST['t_statusEpagDateTo'] . "<br/>";

        $sqlEpag = "SELECT COMPANIES.id FROM COMPANIES INNER JOIN ACTIONS "
                . "ON COMPANIES.id = ACTIONS.company "
                . "WHERE ACTIONS.status2 IN ($statusIds) "
                . "AND atimestamp >= '$sEpagDateFromDT' AND atimestamp<='$sEpagDateToDT'";
        $rsEpag = $dboEpag->getRS($sqlEpag);
        $EpagIds = "";
        for ($i = 0; $i < count($rsEpag); $i++) {
            if ($rsEpag[$i]['id']!="") {
                $EpagIds .= $rsEpag[$i]['id'];
                if ($i < count($rsEpag)-1) { $EpagIds .= ","; }
            }
        }
        $sql .= " AND COMPANIES.epag_id IN ($EpagIds) AND COMPANIES_STATUS.status NOT IN (3,5,9,6) AND COMPANIES_STATUS.productcategory=1 ";
        
        //t_statusEpag
        $statusEpag = selectList::getVal("t_statusEpag", $_POST);
        $statusEpag = str_replace("[", "", $statusEpag);
        $statusEpag = str_replace("]", "", $statusEpag);
        
        if ($t_statusEpag!="") {
            $sql .= " AND COMPANIES_STATUS.status IN ($t_statusEpag) ";
            $criteria .= "STATUS EPAGELMATIAS $statusEpag ";
        }

    }
    
    
    
    $sql .= " ORDER BY COMPANIES.id LIMIT $compCount ";
    
    $rsCompanies = $db1->getRS($sql, $params);
    
    $companyIds = "";
    
    for ($i = 0; $i < count($rsCompanies); $i++) {
        
        $company = new COMPANIES($db1, $rsCompanies[$i]['id'], $rsCompanies);
        
        $company->set_status(2);
        $company->set_commstatus(""); //pane sta new calls
        if ($statusEpagS==1 || $statusEpagP==1) {
            $company->set_mark(1);
        }
        else {
            $company->set_mark(0);
        }
        
        $company->Savedata();
        
        
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=?";
        //$productcategory = $product>0? $product: 1; /////xxxxxx
        $productcategory = $product_assign;
        $rsStatus = $db1->getRS($sql, array($company->get_id(), $productcategory));
        if ($rsStatus) {
            $company_status = new COMPANIES_STATUS($db1, $rsStatus[0]['id'], $rsStatus);
        }
        else {
            $company_status = new COMPANIES_STATUS($db1, 0);
        }
        
        if ($company_status->get_id()==0) {
            $company_status->set_companyid($company->get_id());
            $company_status->set_productcategory($productcategory);
        }
        
        $company_status->set_status(3); //recall
        $company_status->set_recalldate(date("Ymd")."000000");
        $company_status->set_csdatetime(date("Ymd")."000000");
        $company_status->set_recalltime(9); //10:00
        
        //an o xristis exei epikoinonisei me status arnitikos (4) 
        //or arnisi ananeosis proxoraw ston epomeno
        $companyAssigned = FALSE;
        for ($m = 0; $m < count($users); $m++) {
            $mySql = "SELECT * FROM ACTIONS WHERE status2 IN (4,15) AND company=?  AND user=?";
            $myRS = $db1->getRS($mySql, array($rsCompanies[$i]['id'], $users[$k]));

            $my_user_name = func::vlookup("fullname", "USERS", "id=".$users[$k], $db1);

            if (!$myRS) {
                $company_status->set_userid($users[$k]);
                $companyIds .= $rsCompanies[$i]['id'].' => ' . $users[$k] . "<br/>";

                $log_companyids .= $rsCompanies[$i]['id'].' => ' . $my_user_name  . "<br/>";

                $companyAssigned = TRUE;
                break;
            } 
            else {            
                $k = $k == count($users)-1? 0: $k+1;                    
            }            
        }
        //an oloi oi xristes exoun epikoinonisei (arnitikos) to vazw ston trexonta xristi
        if (!$companyAssigned) {
            $company_status->set_userid($users[$k]);
            $companyIds .= $rsCompanies[$i]['id'].' => ' . $users[$k] . "<br/>";

            $log_companyids .= $rsCompanies[$i]['id'].' => ' . $my_user_name  . "<br/>";

        }        
        $company_status->Savedata();  
        
        //create action
        $action = new ACTIONS($db1, 0);
        $action->set_company($company->get_id());
        $action->set_user($users[$k]);
        $action->set_status1(0);
        $action->set_status2(18);
        $action->set_product_categories("[" . $productcategory . "]");
        $action->set_comment("Assigned by " . $_SESSION['user_fullname']);
        $action->Savedata();
        
        //epomenos xristis gia ton epomeno pelati
        $k = $k == count($users)-1? 0: $k+1;
        
    }
    
    //$companyIds = substr($companyIds, 0, strlen($companyIds)-1);

    //log
    $assignment = new CUSTOMER_ASSIGNMENTS($db1, 0);
    $assignment->ca_datetime(date("YmdHis"));
    $assignment->title("ΑΝΑΘΕΣΗ ΠΕΛΑΤΩΝ");
    $assignment->details($criteria . "<br/>ΑΡΙΘΜΟΣ ΠΕΛΑΤΩΝ $compCount <br/><br/>$log_companyids<br/>");
    $user_id = $_SESSION['user_id'];
    $assignment->user($user_id);
    $assignment->Savedata();
    
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
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script type="text/javascript" src="js/code.js"></script>

<script>
$(document).ready(function() {	
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 450 });
    $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
});

</script>




<style>
    .form {
        max-width: 800px;
    }
    
    #c_listUsers {
        -webkit-column-count: 3; /* Chrome, Safari, Opera */
        -moz-column-count: 3; /* Firefox */
        column-count: 3;
        -webkit-column-gap: 40px; /* Chrome, Safari, Opera */
        -moz-column-gap: 40px; /* Firefox */
        column-gap: 40px;
        -webkit-column-rule-style: solid; /* Chrome, Safari, Opera */
        -moz-column-rule-style: solid; /* Firefox */
        column-rule-style: solid;
        -webkit-column-rule-width: 1px; /* Chrome, Safari, Opera */
        -moz-column-rule-width: 1px; /* Firefox */
        column-rule-width: 1px;
    }
    
    #t_expireDate1, #t_expireDate2 {
        width:30%;
    }
    
    #t_statusDateFrom, #t_statusDateTo {
        width:30%;
    }
    
    #t_statusPanelDateFrom, #t_statusPanelDateTo {
        width:30%;
    }
    
    #c_category {
        max-width: 100%;
    }
    
</style>
</head>
    
<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h1 style="margin-left: 20px;">Assign customers to users</h1>

        <p><?php echo $msg ?></p>
        
        <form class="form" action="assignCustToUsers.php" method="post">
            <?php
            /*
            $c_epagStatus = new comboBox("c_epagStatus", $db1, "SELECT * FROM STATUS", 
                    "id", "description", $epagStatus);            
            $c_epagStatus->set_label("Status EPAGELMATIAS");
            $c_epagStatus->get_comboBox();
            */
            echo '<div class="col-4">Ημερ. Λήξης</div>';
            echo '<div class="col-8">';
            
            $t_expireDate1 = new textbox("t_expireDate1", "", $expires1);
            $t_expireDate1->set_format("DATE");
            $t_expireDate1->set_locale($locale);
            echo $t_expireDate1->textboxSimple();
            
            echo '&nbsp; έως &nbsp;';
            
            $t_expireDate2 = new textbox("t_expireDate2", "", $expires2);
            $t_expireDate2->set_format("DATE");
            $t_expireDate2->set_locale($locale);
            echo $t_expireDate2->textboxSimple();
            
            echo '</div>';
            
            $c_category = new comboBox("c_category", $db1, 
                    "SELECT id, trim(description) AS description FROM CATEGORIES ORDER BY description",
                    "id", "description", $category);
            $c_category->set_label("Κατηγορία");
            $c_category->get_comboBox();
            
            
            $c_area = new comboBox("c_area", $db1, 
                    "SELECT id, trim(description) AS description FROM AREAS ORDER BY description",
                    "id", "description", $category);
            $c_area->set_label("Περιοχή");
            $c_area->get_comboBox();
            
            $c_city = new comboBox("c_city", $db1, 
                    "SELECT id, trim(description) AS description FROM EP_CITIES ORDER BY description",
                    "id", "description", $category);
            $c_city->set_label("Πόλη");
            $c_city->get_comboBox();
            
            
            
            
            $c_reference = new comboBox("c_reference", $db1, "SELECT id, description FROM REFERENCE",
                    "id", "description", $reference);
            $c_reference->set_label("Reference");
            $c_reference->get_comboBox();
            
            
            echo '<div class="col-4">Status</div>';
            echo '<div class="col-8">';
            
            $c_status = new comboBox("c_status", $db1, "SELECT id, description FROM STATUS",
                    "id", "description", $status);
            $c_status->set_label("Status");
            echo $c_status->comboBox_simple();
            
            echo '&nbsp; Προϊόν &nbsp;';
            
            $c_product = new comboBox("c_product", $db1, "SELECT id, description FROM PRODUCT_CATEGORIES",
                    "id", "description", $product);
            $c_product->set_label("Status");
            echo $c_product->comboBox_simple();
            
            echo '&nbsp; Χρήστης &nbsp;';
            
            $c_user = new comboBox("c_user", $db1, "SELECT id, fullname FROM USERS WHERE active=1 ",
                    "id", "fullname", $user);
            $c_user->set_label("User");
            echo $c_user->comboBox_simple();
            
            echo '</div>';
            
            
            echo '<div class="col-4">Ημερ. αλλαγής status &nbsp;</div><div class="col-8">';
            
            $t_statusDateFrom = new textbox("t_statusDateFrom", "", $statusDateFrom);
            $t_statusDateFrom->set_format("DATE");
            $t_statusDateFrom->set_locale($locale);
            echo $t_statusDateFrom->textboxSimple();
            
            echo '&nbsp; έως &nbsp;';
            
            $t_statusDateTo = new textbox("t_statusDateTo", "", $statusDateTo);
            $t_statusDateTo->set_format("DATE");
            $t_statusDateTo->set_locale($locale);
            echo $t_statusDateTo->textboxSimple();
            
            echo '</div>';


            //Οχι ανάθεση απο εως
            echo '<div class="col-4">Να μην έχει γίνει ανάθεση στο διάστημα &nbsp;</div><div class="col-8">';
            
            $t_statusDateFrom = new textbox("t_assignDateFrom", "", $assignDateFrom);
            $t_statusDateFrom->set_format("DATE");
            $t_statusDateFrom->set_locale($locale);
            echo $t_statusDateFrom->textboxSimple();
            
            echo '&nbsp; έως &nbsp;';
            
            $t_statusDateTo = new textbox("t_assignDateTo", "", $assignDateTo);
            $t_statusDateTo->set_format("DATE");
            $t_statusDateTo->set_locale($locale);
            echo $t_statusDateTo->textboxSimple();
            
            echo '</div>';
            
            
            
            
            //EPAG status  
            //status
            echo "<hr/>";
            echo '<div class="col-4">Status EPAG. &nbsp;</div><div class="col-8">';
            $t_statusEpagS = new checkbox("t_statusEpagS", "ΣΥΜΦΩΝΗΣΕ", $statusEpagS);
            echo $t_statusEpagS->get_Checkbox();            
            //echo '&nbsp; &nbsp;';            
            $t_statusEpagP = new checkbox("t_statusEpagP", "ΠΛΗΡΩΣΕ", $statusEpagP);
            echo $t_statusEpagP->get_Checkbox();
            echo '</div>';
            //dates
            echo '<div class="col-4">Ημερ. status EPAG. &nbsp;</div><div class="col-8">';
            $t_statusEpagDateFrom = new textbox("t_statusEpagDateFrom", "", $statusEpagDateFrom);
            $t_statusEpagDateFrom->set_format("DATE");
            $t_statusEpagDateFrom->set_locale($locale);
            echo $t_statusEpagDateFrom->textboxSimple();            
            echo '&nbsp; έως &nbsp;';            
            $t_statusEpagDateTo = new textbox("t_statusEpagDateTo", "", $statusEpagDateTo);
            $t_statusEpagDateTo->set_format("DATE");
            $t_statusEpagDateTo->set_locale($locale);
            echo $t_statusEpagDateTo->textboxSimple();
            echo '</div>';            
            
            echo "<div class=\"spacer-20\"></div)";
            $t_statusPanel = new selectList("t_statusPanel", "STATUS", "", $db1);
            $t_statusPanel->set_criteria("id IN (2,4,8,11,15,17)");
            $t_statusPanel->set_label("Status Panel.");
            $t_statusPanel->getList();
            
            echo "<hr/>";
            //epag status END
            
            
            
            
            $c_compCount = new textbox("c_compCount", "Count", $compCount);
            $c_compCount->get_Textbox();
            
            echo '<div style="clear:both;height:30px"></div>';            
            echo "<input id=\"showCount\" type=\"button\" value=\"Companies count\" />";
            echo "&nbsp;<span id=\"countCompanies\"></span>";
            
            echo '<div style="clear:both;height:30px"></div>';
            
            echo '<h2>Users</h2>';
            echo "<input id=\"selectall\" type=\"button\" value=\"Select/deselect all\" />";
            echo '<div style="clear:both;height:10px"></div>';
            
            $c_listUsers = new selectList("c_listUsers", "USERS", $userIds, $db1);
            $c_listUsers->set_criteria("active=1 AND is_agent=1");
            $c_listUsers->set_descrField("fullname");
            echo "<div class=\"three-cols\">";
            echo $c_listUsers->getSimpleList();
            echo "</div>";
            
            echo '<div style="clear:both;height:30px"></div>';

            //????????????????????
            // echo '<div class="col-4"><h2>Προϊόν ανάθεσης</h2></div>';
            // echo '<div class="col-8">';
            // $c_product_assign = new comboBox("c_product_assign", $db1, "SELECT id, description FROM PRODUCT_CATEGORIES",
            //         "id", "description", $product_assign);
            // $c_product_assign->set_label("Προϊόν ανάθεσης");
            // echo $c_product_assign->comboBox_simple();
            // echo '</div>';
            //????????????????????
            
            $btn = new button("btnAssign", "ASSIGN");
            echo $btn->get_button_simple()

            ?>
            
            <div class="clear;height:50px"></div>
            
            <?php
            if ($companyIds!="") {
            echo "COMPANIES ".$companyIds;
            }
            ?>
            
        </form>
        
        
        
        
    </div>
    
    <?php include "blocks/footer.php"; ?> 
    
    <script>
        
        $(function() {
            
            var mySelect = false;
            
            $("#selectall").click(function() {
                if (mySelect) {
                    $("#c_listUsers input:checkbox").prop('checked', false);
                    mySelect = false;
                }
                else {
                    $("#c_listUsers input:checkbox").prop('checked', true);
                    mySelect = true;
                }
            });
            
            $("#c_epagStatus").change(function() {
                //...
            });
            
            $("#showCount").click(function() {
                $("#countCompanies").html("....");
                
                var t_expireDate1 = $("#t_expireDate1").val();
                var t_expireDate2 = $("#t_expireDate2").val();
                var c_category = $("#c_category").val();
                var c_area = $("#c_area").val();
                var c_city = $("#c_city").val();
                var c_reference = $("#c_reference").val();
                var c_status = $("#c_status").val();                
                var c_product = $("#c_product").val();
                var c_user = $("#c_user").val();
                var t_statusDateFrom = $("#t_statusDateFrom").val();
                var t_statusDateTo = $("#t_statusDateTo").val();

                var t_assignDateFrom = $("#t_assignDateFrom").val();
                var t_assignDateTo = $("#t_assignDateTo").val();
                
                var t_statusEpagS = $("#t_statusEpagS").is(":checked")? 1: 0;
                var t_statusEpagP = $("#t_statusEpagP").is(":checked")? 1: 0;
                var t_statusEpagDateFrom = $("#t_statusEpagDateFrom").val();
                var t_statusEpagDateTo = $("#t_statusEpagDateTo").val();
                
                var t_statusPanel = "";
                $("#t_statusPanel input").each(function() {
                    if ($(this).is(":checked")) {
                        var statusAr = $(this).attr("id").split("___");
                        var statusid = statusAr[1];
                        t_statusPanel += statusid + ",";                        
                    }
                });
                t_statusPanel = t_statusPanel.substring(0, t_statusPanel.length-1);
                                
                $.post( "getCustomersCount.php", 
                    {
                        t_expireDate1: t_expireDate1,
                        t_expireDate2: t_expireDate2,
                        c_category: c_category,
                        c_area: c_area,
                        c_city: c_city,
                        c_reference: c_reference,
                        c_status: c_status,
                        c_product: c_product,
                        c_user: c_user,
                        t_statusDateFrom: t_statusDateFrom,
                        t_statusDateTo: t_statusDateTo,
                        t_assignDateFrom: t_assignDateFrom,
                        t_assignDateTo: t_assignDateTo,
                        t_statusEpagS: t_statusEpagS,
                        t_statusEpagP: t_statusEpagP,
                        t_statusEpagDateFrom: t_statusEpagDateFrom,
                        t_statusEpagDateTo: t_statusEpagDateTo,
                        t_statusPanel: t_statusPanel
                    },
                    function( data ) {
                        $("#countCompanies").html(data);
                    });
                
            });
            
        });
        
    </script>
    
    
    
</body>
    
</html>