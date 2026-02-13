<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

include_once "php/configPanel.php";
include_once "php/dataobjectsPanel.php";

$dboCRM5 = new DB(connCRM5::$connstr, connCRM5::$username, connCRM5::$password);

$compCount = 100;

$category = 0;
$reference = 0;

$status = 0;
$product = 0;
$user = 0;
$statusDateFrom = "";
$statusDateTo = "";
$statusPanelS = 0;
$statusPanelP = 0;
$statusPanelDateFrom = "";
$statusPanelDateTo = "";
$companyIds = "";
$companyExpCount = 0;

if ($_POST) {
	
	$compCount = $_POST['c_compCount'];
	
	$sql = "SELECT DISTINCT COMPANIES.* FROM COMPANIES ".
			"INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid ".
			"WHERE COMPANIES.status NOT IN(11,12) ";

	$params = array();
	
	if ($_POST['c_category']>0) {
        $category = $_POST['c_category'];   
        $sql .= " AND COMPANIES.basiccategory = ? ";
        array_push($params, $category);
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
	
	if ($_POST['c_reference']>0) {
        $reference = $_POST['c_reference']; 
        $sql .= " AND COMPANIES.reference = ? ";
        array_push($params, $reference);
    }
	
	$sql .= " ORDER BY COMPANIES.id LIMIT $compCount ";
	
    //echo $sql;   

    //exit();

    $rsCompanies = $db1->getRS($sql, $params);
	//Export customers
	for ($i = 0; $i < count($rsCompanies); $i++) {     
		//Export customers to crm 5
		$company = new COMPANIES($dboCRM5, $rsCompanies[$i]['id'],$rsCompanies);		
		$company->set_id(0);
		$company->set_export2crm5(1);
		
		if($company->Savedata()){			
			//Change status to company into crm 4
			$company = new COMPANIES($db1, $rsCompanies[$i]['id'],$rsCompanies);
			$company->set_export2crm5(1);
			if($company->Savedata()){$companyExpCount++;}
		}
	}
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
			$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 450 });
		});
	</script>
	
	<style>
		.form {
			max-width: 800px;
		}
		
		#c_category {
			max-width: 100%;
		}
		
		.message{
			color: green;
			font-weight: bold;
		}
	</style>
</head>

<body>
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>    

    <div class="main">  
        <h1 style="margin-left: 20px;">Export customers to CRM 5</h1>
        <form class="form" action="exportCustToCrm5.php" method="post">
			<?php
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
			
			echo "<hr/>";   
			
            $c_compCount = new textbox("c_compCount", "Count", $compCount);
            $c_compCount->get_Textbox();            

            echo '<div style="clear:both;height:30px"></div>'; 
            echo "<input id=\"showCount\" type=\"button\" value=\"Companies count\" />";
            echo "&nbsp;<span id=\"countCompanies\"></span>";            

            echo '<div style="clear:both;height:30px"></div>';            

            $btn = new button("btnExport", "EXPORT");

            echo $btn->get_button_simple();
            ?>           

            <div class="clear;height:50px"></div>

            <?php

            if ($companyExpCount > 0) {
				echo '<div style="clear:both;height:20px"></div>';
				echo "<div class=\"message\">".$companyExpCount." Εταιρίες μεταφέρθηκαν στο CRM 5 </div>";
				echo '<div style="clear:both;height:20px"></div>';
            }

            ?>
		</form>
	</div>
    <?php include "blocks/footer.php"; ?> 
	
	<script>
        $(function() {
			$("#showCount").click(function() {
                $("#countCompanies").html("....");                

                var c_category = $("#c_category").val(); 
				var c_area = $("#c_area").val();
				var c_city = $("#c_city").val();
				var c_reference = $("#c_reference").val();

                $.post( "getCustToCrm5Count.php", 
                    {
                        c_category: c_category,
						c_area: c_area,
						c_city: c_city,
						c_reference: c_reference
                        
                    },

                    function( data ) {
                        $("#countCompanies").html(data);
						
                    });
            });
			
		});
    </script>

</body>
</html>