<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT COMPANIES.id AS id, companyname, domain_name  FROM COMPANIES INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid WHERE COMPANIES_STATUS.productcategory = 2 AND COMPANIES_STATUS.status = 9 AND COMPANIES.domain=0 ";

include "_theHeader.php";

?>

<h2 style="margin-left:1em">Domain σε εκκρεμότητα</h2>
                
<div class="form-container">
    
    <?php
    
    $grid = new datagrid("grid", $db1, $sql, 
            array("id", "companyname", "domain_name"), 
            array("ID", "ΕΠΩΝΥΜΙΑ", "DOMAIN"), $ltoken);
    $grid->set_edit("editcompany.php");
    $grid->set_popup(FALSE);
    $grid->get_datagrid();
    
    ?>
    
</div>

<?php

include "_theFooter.php";