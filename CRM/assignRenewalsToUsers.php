<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$userIds = "";

$compCount = 100;
$expires1 = "";
$expires2 = "";
$category = 0;
$product = 0;
$statusDateTo = "";
$companystatus = 1;
$companyIds = "";


/*
if ($_POST) {
    
    $product = $_POST['c_product'];
    if ($product==0) {die("SELECT PRODUCT");}
    switch($product) {
        case 1: $productExpField = "expires"; break;
        case 2: $productExpField = "domain_expires"; break;
        case 3: $productExpField = "domain_expires"; break;
        case 4: $productExpField = "domain_expires"; break;
        default: $productExpField = "expires"; break;
    }
    
    if ($_POST['t_expireDate1']=="") {die("SELECT DATE 1");}
    $expires1 = textbox::getDate($_POST['t_expireDate1'], $locale); 
    if ($_POST['t_expireDate2']=="") {die("SELECT DATE 2");}
    $expires2 = textbox::getDate($_POST['t_expireDate2'], $locale);
    
    $compCount = $_POST['c_compCount'];
    
    $userIds = selectList::getVal('c_listUsers', $_POST);
    if ($userIds=="") { die("ΔΕΝ ΕΧΕΤΕ ΕΠΙΛΕΞΕΙ ΚΑΝΕΝΑ ΧΡΗΣΤΗ"); }
    $userIds2 = str_replace(array("[","]"), "", $userIds);
    $users = explode(",", $userIds2);
    $k = 0; //users index
    
    $sql = "SELECT COMPANIES.id FROM COMPANIES INNER JOIN "
            . "(SELECT * FROM COMPANIES_STATUS WHERE productcategory=$product AND status =9) CS "
            . "ON COMPANIES.id = CS.companyid "
            . "WHERE  COMPANIES.$productExpField >= ? AND COMPANIES.$productExpField <= ? "
            . "ORDER BY COMPANIES.id LIMIT $compCount";
    
    $rsCompanies = $db1->getRS($sql, array($expires1,$expires2));
    
    for ($i = 0; $i < count($rsCompanies); $i++) {
        
        $company = new COMPANIES($db1, $rsCompanies[$i]['id']);
        
        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=?";
        $rsStatus = $db1->getRS($sql, array($company->get_id(), $product));
        if ($rsStatus) {
            $company_status = new COMPANIES_STATUS($db1, $rsStatus[0]['id'], $rsStatus);
        }
        else {
            $company_status = new COMPANIES_STATUS($db1, 0);
        }
        
        if ($company_status->get_id()==0) {
            $company_status->set_companyid($company->get_id());
            $company_status->set_productcategory($product);
        }
        
        $company_status->set_status(3); //recall
        $company_status->set_recalldate(date("Ymd")."000000");
        $company_status->set_csdatetime(date("Ymd")."000000");
        $company_status->set_recalltime(9); //10:00
        
        //user
        $userRecall = 0;
        //find last user 'simfonise' for this product
        $companyId = $company->get_id();
        $sql = "SELECT company, SUBSTRING_INDEX(GROUP_CONCAT(user ORDER BY atimestamp DESC), ',', 1) AS user5 FROM ACTIONS WHERE status2=5 AND company=$companyId AND product_categories LIKE '[$product]' GROUP BY `company`";
        //echo $sql;
        $rsLastUser = $db1->getRS($sql);
        //var_dump($rsLastUser);
        $lastUserId = 0;
        $iLastUserName = "XXXXX";
        if (!empty($rsLastUser)) {
            $lastUserId = $rsLastUser[0]['user5'];
            $iLastUser = new USERS($db1, $lastUserId);
            $iLastUserName = $iLastUser->get_fullname();
        } 
        
        if ($lastUserId!=0) {
            $companyIds .= $rsCompanies[$i]['id']; //////
            $companyIds .= " $iLastUserName ";
            $lastUser = new USERS($db1, $lastUserId);
            if ($lastUser->get_active()==1) {
                $userRecall = $lastUser->get_id();
            }
            else {
                $userRecall = $users[$k];
                $k = $k == count($users)-1? 0: $k+1;
            }
            $company_status->set_userid($userRecall);
            $company_status->Savedata();
            
            $company->set_for_renewal(1);
            $company->Savedata();
            
            $iUserRecall = new USERS($db1, $userRecall);
            $iUserRecallName = $iUserRecall->get_fullname();
            $companyIds .= " => $iUserRecallName <br/>";
        }
    
    }
    
    
}
*/
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
    
    #t_statusDateTo {
        width:30%;
    }
    
    .renewals {
        width:100%; 
        max-width:600px;
    }
    
    table.renewals th {
        background-color: #fafafa;
        /*color:#fff;*/
        padding:10px 5px;
        text-align: left;
        font-weight: bold;
        border-top:1px solid #ccc;
        border-bottom:1px solid #ccc;
        
    }
    table.renewals td {
        border-bottom: 1px solid #ccc;
        padding:5px;
    }
    
    #btnAssign {
        display:none;
    }
    
    #c_compCount {
        width: 100px;
    }
    
    tr.footer-renewals td {
        padding:10px 5px;
        font-weight: bold;
    }
    
</style>
</head>
    
<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h1 style="margin-left: 20px;">Ανάθεση ανανεώσεων σε χρήστες</h1> 
        
        <form class="form">
            
            <?php
            
            
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
            
            
            echo '<div class="col-4">Προϊόν</div>';
            echo '<div class="col-8">';
            
            $c_product = new comboBox("c_product", $db1, "SELECT id, description FROM PRODUCT_CATEGORIES WHERE id IN (1,2,4,5)",
                    "id", "description", $product);
            $c_product->set_label("Status");
            echo $c_product->comboBox_simple();
            
            echo '</div>';            
            
            $c_compCount = new textbox("c_compCount", "Αριθμός ανανεώσεων", $compCount);
            $c_compCount->get_Textbox();            
            
            echo '<div style="clear:both;height:30px"></div>';
            
            echo "<input id=\"showCount\" type=\"button\" value=\"Εμφάνιση ανανεώσεων\" />";
            echo "&nbsp;<span id=\"countCompanies\"></span>";
            
            
            
            echo '<div style="clear:both;height:30px"></div>';
            
                        
            
            $btn = new button("btnAssign", "Ανάθεση", "button");
            echo $btn->get_button_simple();
            
            
            ?>
            
            <div class="clear;height:50px"></div>
            
            <div id="assignRes"></div>
            
            
        </form>
        
        
        
    </div>
    
    <?php include "blocks/footer.php"; ?> 
    
    <script>
        
        $(function() {
            
            $("form.form").submit(function(e){
                e.preventDefault();
            });
            
            $("#t_expireDate1").attr( 'autocomplete', 'off' );
            $("#t_expireDate2").attr( 'autocomplete', 'off' );
        
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
            
            
            $("#showCount").click(function() {
                
                $("#countCompanies").html("Please wait...");
                $("#btnAssign").hide();
                $("#assignRes").hide();
                
                var product = $("#c_product").val();
                var expires1 = $("#t_expireDate1").val();
                var expires2 = $("#t_expireDate2").val();
                var assignCount = $("#c_compCount").val();
                
                $.post( "getRenewalsCount.php", 
                    {
                        product: product,
                        expires1: expires1,
                        expires2: expires2,
                        assignCount: assignCount
                    },
                    function( data ) {
                        if (data==="") {
                            $("#countCompanies").html("<h2>Δεν υπάρχουν ανανεώσεις για τα κριτήρια που δώσατε.</h2>");
                        }
                        else {
                            $("#countCompanies").html(data);
                            $("#btnAssign").show();
                            $(".assign").change(function() {
                                calcAssignTotals();
                            });
                        }
                        
                    });
                
            });
            
            
            $("#btnAssign").click(function() {
                
                var product = $("#c_product").val();
                var expires1 = $("#t_expireDate1").val();
                var expires2 = $("#t_expireDate2").val();
                var assignCount = $("#c_compCount").val();
                
                var users = "";
                var assigns = "";
                $(".assign").each(function() {
                    var userId = $(this).data("userid");
                    var userAssign = $(this).val();
                    userAssign = userAssign===''?'0':userAssign;
                    
                    users = users===""? userId: users + "," + userId;
                    assigns = assigns===""? userAssign: assigns + "," + userAssign;
                    
                });
                
                $.post( "_assignRenewals.php", 
                    {
                        product: product,
                        expires1: expires1,
                        expires2: expires2,
                        assignCount: assignCount,
                        users: users,
                        assigns: assigns
                    },
                    function( data ) {
                        $("#assignRes").html(data);
                        $("#assignRes").show();
                        
                    });
                
            });
            
        });
        
        
        
        function calcAssignTotals() {
            
            var assignCount = 0;
            
            $(".assign").each(function() {
                assignCount += Number($(this).val());
                
            });
            
            $("#assign-count").html(assignCount);
            
        }
        
    </script>
    
    
    
</body>
    
</html>