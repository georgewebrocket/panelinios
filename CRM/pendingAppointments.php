<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT COMPANIES.id AS id, companyname, srv_services, srv_date, TIMES.description AS MYTIME,
        USERS.fullname, CONCAT(Phone1, '<br/>', Phone2, '<br/>', Mobilephone) AS phone, srv_comments, 
        PROFESSIONS.description AS profession, EP_CITIES.description AS MYCITY
        FROM COMPANIES INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid 
        LEFT JOIN TIMES ON COMPANIES.srv_field1 = TIMES.id 
        LEFT JOIN USERS ON COMPANIES.srv_salesman = USERS.id
        LEFT JOIN PROFESSIONS ON COMPANIES.profession = PROFESSIONS.id
        LEFT JOIN EP_CITIES ON COMPANIES.city_id = EP_CITIES.id
        WHERE COMPANIES_STATUS.productcategory = 7 AND COMPANIES_STATUS.status = 5 AND COMPANIES.srv_status=0 ";
//echo $sql;
$myStyle = <<<EOT
<style>
    #grid { max-width: 1600px;}
</style>
EOT;
include "_theHeader.php";

?>

<script>        
    
    $.tablesorter.addParser({ 
        // set a unique id 
        id: 'dates', 
        is: function(s) { 
            // return false so this parser is not auto detected 
            return false; 
        }, 
        format: function(s) { 
            // format your data for normalization 01/12/2014 -> 20141201
            var ar = s.split("/");
            return ar[2]+ar[1]+ar[0];            
        }, 
        // set type, either numeric or text 
        type: 'text' 
    }); 
    
    
    $(function() {
        $("#grid").tablesorter({ 
            sortList: [[1,0]],
            headers: { 
                7: { 
                    sorter:'dates' 
                } 
            } 
        });        
        
        });

</script>

<h2 style="margin-left:1em">Ραντεβού σε εκκρεμότητα</h2>
                
<div class="form-container">
    
    <?php
    
    $grid = new datagrid("grid", $db1, $sql, 
            array("id", "companyname", "profession", "phone", "MYCITY", "fullname", "srv_services", "srv_date", "MYTIME", "srv_comments"), 
            array("ID", "ΕΠΩΝΥΜΙΑ", "ΕΠΑΓΓΕΛΜΑ",  "ΤΗΛ", "ΠΟΛΗ", "ΥΠΕΥΘΥΝΟΣ", "Προϊόντα/Υπηρεσίες", "Ημερ.", "Ώρα", "Σχόλια"), 
            $ltoken);
    $grid->set_edit("editcompany.php");
    $grid->GetItemsFromIds($db1, "srv_services", "SRV_SERVICES");
    $grid->set_colsFormat(array("","","","","","","","DATE","",""));
    $grid->set_popup(FALSE);
    $grid->get_datagrid();

    $total = count($grid->get_rs());
    echo "<h2>ΣΥΝΟΛΟ {$total} ραντεβού</h2>";
    
    ?>
    
</div>

<?php
$myScript = <<<EOT
<script>
        $(function() {
            $("#grid").tableExport({formats: ["xlsx","xls", "csv"]});
            
            
            $(".btn.xls, .btn.xlsx, .btn.csv").click(function() {
                //console.log("Export to Excel/CSV");
                $.post("_ulog.php", 
                {
                    action:"Export to Excel/CSV - Ασφάλειες"
                }, function(data) {
                    console.log(data);
                });
                
            });
            
            
        });

    </script>
EOT;
include "_theFooter.php";