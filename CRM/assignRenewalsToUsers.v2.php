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
        background-color:#f00;
    }
    
    #c_compCount {
        width: 100px;
    }
    
    tr.footer-renewals td {
        padding:10px 5px;
        font-weight: bold;
    }

    #grid_companies {
        max-width:1200px;
    }

    #grid_companies tr {
        border-bottom:3px solid #ccc;
    }

    #grid_companies th:nth-child(2),
    #grid_companies td:nth-child(2) {
        width:300px;
    }
    
</style>
</head>
    
<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <h1 style="margin-left: 20px;">Ανάθεση ανανεώσεων σε χρήστες V.2</h1> 
        
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

        <div class='companies-list' style="margin-left:20px;"></div>
        
        
        
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
                
                $.post( "getRenewalsList.php", 
                    {
                        product: product,
                        expires1: expires1,
                        expires2: expires2,
                        assignCount: assignCount
                    },
                    function( data ) {
                        if (data==="") {
                            $(".companies-list").html("<h2>Δεν υπάρχουν ανανεώσεις για τα κριτήρια που δώσατε.</h2>");
                        }
                        else {
                            $(".companies-list").html(data);
                            $("#btnAssign").show();
                            // $(".assign").change(function() {
                            //     calcAssignTotals();
                            // });
                        }
                        $("#countCompanies").html("");
                        
                    });
                
            });
            
            
            $("#btnAssign").click(function() {
                
                var product = $("#c_product").val();
                var user = 0; //...
                
                var users = "";
                var customers = "";
                $("#grid_companies tbody tr").each(function() {
                    var user_id = $(this).find("select").val();

                    if (user_id>0) {
                        users = users===""? user_id: users + "," + user_id;
                        let customer_id = $(this).attr('id');
                        customers = customers===""? customer_id: customers + "," + customer_id;
                    }
                                        
                });

                console.log(users);
                console.log(customers);
                                
                $.post( "_assignRenewals.v2.php", 
                    {
                        product: product,
                        user: user,
                        users: users,
                        customers: customers
                    },
                    function( data ) {
                        alert("Έχινε ανάθεση των ανανεώσεων στους επιλεγμένους χρήστες."); 
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