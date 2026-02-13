<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$userIds = "";

$compCount = 100;
$expires1 = "";
$expires2 = "";
$category = 0;
$reference = 0;
$status = 0;
$product = 0;
$statusDateFrom = "";
$statusDateTo = "";
$companyIds = "";

if ($_POST) {
    
    //$epagStatus = $_POST['c_epagStatus'];
    $userIds = selectList::getVal('c_listUsers', $_POST);
    $userIds2 = str_replace(array("[","]"), "", $userIds);
    $users = explode(",", $userIds2);
    $k = 0; //users index
    
    $compCount = $_POST['c_compCount'];
    
    $sql = "SELECT COMPANIES.* FROM COMPANIES ";
    /*if ($_POST['c_status']>0 && $_POST['c_product']>0) {
        $sql .= "INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ";
    }
    $sql .= " WHERE COMPANIES.status = 1 ";*/
    
    if ($_POST['c_status']>0 && $_POST['c_product']>0) {
        $sql .= "INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ";
        $companystatus = 2;
        $sql .= " WHERE (COMPANIES.status <> 1 OR COMPANIES.status IS NULL) ";
    }
    else {
        $sql .= " WHERE COMPANIES.status = 1 ";
    }
    
    
    $params = array();
    /*if ($epagStatus>0) {
        $sql .= " AND epag_status = ? ";
        array_push($params, $epagStatus);
    } */
    
    if ($_POST['t_expireDate1']!="" && $_POST['t_expireDate2']!="") {
        $expires1 = textbox::getDate($_POST['t_expireDate1'], $locale); 
        $expires2 = textbox::getDate($_POST['t_expireDate2'], $locale); 
        $sql .= " AND COMPANIES.expires >= ? AND COMPANIES.expires <= ? ";
        array_push($params, $expires1);
        array_push($params, $expires2);
    }
    
    if ($_POST['c_category']>0) {
        $category = $_POST['c_category'];    
        $sql .= " AND COMPANIES.basiccategory = ? ";
        array_push($params, $category);
    }
    
    if ($_POST['c_reference']>0) {
        $reference = $_POST['c_reference'];    
        $sql .= " AND COMPANIES.reference = ? ";
        array_push($params, $reference);
    }
    
    if ($_POST['c_area']>0) {
        $area = $_POST['c_area'];    
        $sql .= " AND area = ? ";
        array_push($params, $area);
    }
    
    if ($_POST['c_city']>0) {
        $city = $_POST['c_city'];    
        $sql .= " AND city_id = ? ";
        array_push($params, $city);
    }
    
    if ($_POST['c_status']>0) {
        $status = $_POST['c_status'];    
        $sql .= " AND COMPANIES_STATUS.status = ? ";
        array_push($params, $status);
    }
    
    if ($_POST['c_product']>0) {
        $product = $_POST['c_product'];    
        $sql .= " AND COMPANIES_STATUS.productcategory = ? ";
        array_push($params, $product);
    }
    
    
    if ($_POST['t_statusDateFrom']!="" ) {
        $statusDateFrom = $_POST['t_statusDateFrom'];
        $sql .= " AND COMPANIES_STATUS.csdatetime >= ? ";
        $str14Date = substr(func::dateTo14str($statusDateFrom), 0, 8)."000000";
        array_push($params, $str14Date);
    } 
    
    if ($_POST['t_statusDateTo']!="" ) {
        $statusDateTo = $_POST['t_statusDateTo'];
        $sql .= " AND COMPANIES_STATUS.csdatetime <= ? ";
        $str14Date = substr(func::dateTo14str($statusDateTo), 0, 8)."235959";
        array_push($params, $str14Date);
    }
    
    
    
    $sql .= " ORDER BY COMPANIES.id LIMIT $compCount ";
    //echo $sql;
    //var_dump($params);
    //exit();
    $rsCompanies = $db1->getRS($sql, $params);
    
    $companyIds = "";
    
    for ($i = 0; $i < count($rsCompanies); $i++) {
        
        $company = new COMPANIES($db1, $rsCompanies[$i]['id'], $rsCompanies);
        //echo $company->get_id() . "-". $company->get_companyname()."<br/>";
        
        $company->set_status(2);
        $company->set_commstatus(""); //pane sta new calls
        $company->Savedata();
        
        
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=?";
        $productcategory = $product>0? $product: 1; /////xxxxxx
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
            if (!$myRS) {
                $company_status->set_userid($users[$k]);
                $companyIds .= $rsCompanies[$i]['id'].' => ' . $users[$k] . "<br/>";
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
    
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Panellinios - CRM</title>
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
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 800, 'height' : 450 });
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
    
    #c_category {
        max-width: 100%;
    }
    
</style>
</head>
    
<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h1>Assign customers to users</h1>
        
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
                
                
                var t_expireDate1 = $("#t_expireDate1").val();
                var t_expireDate2 = $("#t_expireDate2").val();
                var c_category = $("#c_category").val();
                var c_area = $("#c_area").val();
                var c_city = $("#c_city").val();
                var c_reference = $("#c_reference").val();
                var c_status = $("#c_status").val();                
                var c_product = $("#c_product").val();
                var t_statusDateFrom = $("#t_statusDateFrom").val();
                var t_statusDateTo = $("#t_statusDateTo").val();
                                
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
                        t_statusDateFrom: t_statusDateFrom,
                        t_statusDateTo: t_statusDateTo
                        
                    },
                    function( data ) {
                        $("#countCompanies").html(data);
                    });
                
            });
            
        });
        
    </script>
    
    
    
</body>
    
</html>