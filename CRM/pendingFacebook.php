<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT COMPANIES.id AS id, companyname, fb_page  FROM COMPANIES INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid WHERE COMPANIES_STATUS.productcategory = 4 AND COMPANIES_STATUS.status = 9 AND COMPANIES.fb_ok=0 ";

include "_theHeader.php";

?>

<h2 style="margin-left:1em">Facebook σε εκκρεμότητα</h2>
                
<div class="form-container">
    
    <?php
    
    $grid = new datagrid("grid", $db1, $sql, 
            array("id", "companyname", "fb_page"), 
            array("ID", "ΕΠΩΝΥΜΙΑ", "Σελίδα FB"), $ltoken);
    $grid->set_edit("editcompany.php");
    $grid->set_popup(FALSE);
    $grid->get_datagrid();
    
    ?>
    
</div>

<?php

include "_theFooter.php";