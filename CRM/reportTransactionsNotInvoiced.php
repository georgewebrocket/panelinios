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

$companyType = isset($_REQUEST['c_companyType'])? $_REQUEST['c_companyType']: 0;
$datefrom = isset($_REQUEST['t_datefrom'])? $_REQUEST['t_datefrom']: "";
$dateto = isset($_REQUEST['t_dateto'])? $_REQUEST['t_dateto']: "";
$eponimia = isset($_REQUEST['t_eponimia'])? $_REQUEST['t_eponimia']: "";
$profession = isset($_REQUEST['c_profession'])? $_REQUEST['c_profession']: 0;

$rs = NULL;

if (isset($_GET['btnSearch'])) {
    
    $params = array();
    $sql = "SELECT COMPANIES.id, COMPANIES.eponimia, TRANSACTIONS.tdatetime, TRANSACTIONS.amount FROM TRANSACTIONS "; // WHERE transactiontype=1 AND (invoiced=0 OR invoiced IS NULL) ";
    
    $sql .= " INNER JOIN COMPANIES ON TRANSACTIONS.company = COMPANIES.id ";
    

    $sql .= " WHERE TRANSACTIONS.transactiontype=1 AND TRANSACTIONS.status=2 AND (TRANSACTIONS.invoiced=0 OR TRANSACTIONS.invoiced IS NULL) ";

    if ($companyType!=0) {
        $sql .= " AND COMPANIES.company_type = ? ";
        array_push($params, $companyType);
    }
    
    if ($profession!=0) {
        $sql .= " AND COMPANIES.profession = ? ";
        array_push($params, $profession);
    }

    if ($eponimia!="") {
        $sql .= " AND COMPANIES.eponimia LIKE CONCAT('%', ? , '%') ";
        array_push($params, $eponimia);
    }

    if ($datefrom!="") {
        $sql .= " AND TRANSACTIONS.tdatetime>= ? ";
        array_push($params, func::dateTo14str($datefrom));
    }

    if ($dateto!="") {
        $sql .= " AND TRANSACTIONS.tdatetime<= ? ";
        array_push($params, func::dateTo14str($dateto));
    }
    $sql .= " ORDER BY TRANSACTIONS.tdatetime ";

    echo "<!--$sql-->";

    $rs = $db1->getRS($sql, $params);

    if ($rs) {
        $grid = new datagrid("grid", $db1, "", 
                array("id","eponimia","tdatetime", "amount"), 
                array("ID","ΠΕΛΑΤΗΣ","ΗΜΕΡ", "ΠΟΣΟ"), 
                $ltoken);
        $grid->set_rs($rs);
        $grid->col_sum("amount");
        $grid->set_colsFormat(array("","","DATE", "CURRENCY"));
        $grid->set_edit("editcompany.php");
        $grid->set_popup(FALSE);

    }

}


$myStyle = <<<EOT
<style>        
    #grid {
        max-width: 1100px;
    }
        
    form {
        max-width: 1000px;
        margin-left:0px;
    }
    
</style>
EOT;



include '_theHeader.php';

?>

<h1>Χρεώσεις για τιμολόγηση</h1>

<form action="reportTransactionsNotInvoiced.php" method="get">
    
    <?php
    
    $c_companyType = new comboBox("c_companyType", $db1, 
            "SELECT * FROM COMPANY_TYPES", 
            "id", "description",
            $companyType);
    $c_companyType->set_label("ΤΥΠΟΣ ΕΤΑΙΡΕΙΑΣ");
    $c_companyType->get_comboBox();
    
    $c_profession = new comboBox("c_profession", $db1, 
            "SELECT * FROM PROFESSIONS", 
            "id", "description",
            $companyType);
    $c_profession->set_label("ΕΠΑΓΓΕΛΜΑ");
    $c_profession->get_comboBox();
    
    $t_datefrom = new textbox("t_datefrom", "Ημερ. από", $datefrom);
    $t_datefrom->set_format("DATE");
    $t_datefrom->get_Textbox();
    
    $t_dateto = new textbox("t_dateto", "έως", $dateto);
    $t_dateto->set_format("DATE");
    $t_dateto->get_Textbox();
    
    $t_eponimia = new textbox("t_eponimia", "ΕΠΩΝΥΜΙΑ", $eponimia);
    $t_eponimia->get_Textbox();
    
    $btnSearch = new button("btnSearch", "ΑΝΑΖΗΤΗΣΗ");
    $btnSearch->get_button();
    
    
    ?>
    
    <div class="clear"></div>
    
</form>



<?php

if ($rs) {
    
    $grid->get_datagrid();
    echo "<br/><br/>";
    echo "<h3>ΣΥΝΟΛΟ " . count($rs) . "<h3>";
    
    
}


include '_theFooter.php';