<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("REPORTUSERCLEARANCE",$lang,$db1);

$sql="";
$criteria = "";
$msg = "";

$post_txtDateFrom = "";
$post_txtDateTo = "";
$post_cUser = 0;
$post_l_productcategories = "";
$post_cReportType = "ALL";



if (isset($_GET['search']) && $_GET['search']==1) {
    $post_txtDateFrom = $_POST['txtDateFrom'];
    $post_txtDateTo = $_POST['txtDateTo'];
    $post_cUser = $_POST['cUser'];
    // $post_l_productcategories = $_POST['l_productcategories'];
    $post_cReportType = $_POST['cReportType'];
    
    if ($_POST['txtDateFrom']!="") {
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        /*$yy = substr($dateFrom, 0, 4);
        $mm = substr($dateFrom, 4, 2);
        $dd = substr($dateFrom, 6, 2);
        $dateFrom = $yy."-".$mm."-".$dd." 00:00:00";*/
        
        if ($_POST['txtDateTo']!="") {
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
            /*$yy = substr($dateTo, 0, 4);
            $mm = substr($dateTo, 4, 2);
            $dd = substr($dateTo, 6, 2);*/
        }
        else {
            $dateTo = $dateFrom;
        }
        //$dateTo = $yy."-".$mm."-".$dd." 23:59:59";
        $dateTo = substr($dateTo, 0, 8)."235959";
        
        $sql = "SELECT company AS id, company AS companyname, TRANSACTIONS.amount AS price, company AS DeliveryDate, product_category, '' AS status, seller FROM TRANSACTIONS INNER JOIN PACKAGES ON TRANSACTIONS.package=PACKAGES.id WHERE transactiontype=1 AND tdatetime>='".$dateFrom."' AND tdatetime<='".$dateTo."'";            
        $criteria = $lg->l("DATES").":".$_POST['txtDateFrom']."-".$_POST['txtDateTo'];
        
        
        if ($_POST['cUser']<>0) {
            $username = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
            $sql .= " AND seller=".$_POST['cUser'];            
            $criteria = $lg->l("USER").":".$username." / ".$criteria;            
        }
        
        //$sql = "SELECT companyid AS id, companyid AS companyname, companyid AS price, companyid AS DeliveryDate, productcategory, `status`, userid FROM COMPANIES_STATUS WHERE companyid IN (".$sql1.") ";  
        //echo $sql;
        
        $productCategories = selectList::getVal("l_productcategories", $_POST);
        $post_l_productcategories = $productCategories;
        if ($productCategories!="") {            
            $productCategories = str_replace(array("[", "]"), "", $productCategories);
            $sql .= " AND PACKAGES.product_category IN ($productCategories) ";            
            $criteria .= " / Κατηγ. προίόντων = $productCategories";
        }
        //echo $sql;
        
        switch ($_POST['cReportType']) {
            case "ALL": 
                //$sql .=" AND (COMPANIES_STATUS.`status` IN (5,6,7,8,9,10))";
                break;
            case "PAYED":
                $sql .=" AND (TRANSACTIONS.`status` =2)";
                $criteria .=" / ΕΧΟΥΝ ΠΛΗΡΩΣΕΙ";
                break;
            case "CANCELED1":
                $sql .=" AND (TRANSACTIONS.`status` =3 AND (returned=0 OR returned IS NULL))";
                $criteria .=" / ΑΚΥΡΩΣΗ ΑΠΟΣΤΟΛΗΣ";
                break; 
            case "CANCELED2":
                $sql .=" AND (TRANSACTIONS.`status` =3 AND returned=1)";
                $criteria .=" / ΕΠΙΣΤΡΟΦΗ ΑΠΟΣΤΟΛΗΣ";
                break;
            case "PENDING":
                $sql .=" AND (TRANSACTIONS.`status` =1)";
                $criteria .=" / ΣΕ ΕΚΚΡΕΜΟΤΗΤΑ";
                break;
            default :
                //$sql .=" WHERE (COMPANIES.`status`<>8)";
                break;
        }
        $sql .=" ORDER BY seller";
        //echo $sql;
    }
    else{
        $msg = $lg->l("select-date");
        $sql="";
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include "_head.php"; ?>

<script>
    $(document).ready(function() {	

        $("#gridReportUC").tablesorter({ 
                headers: { 
                    6: { 
                        sorter:'dates' 
                    } 
                } 
            });

    });
    
    
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
    
    
</script>


<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReportUC {
        max-width: 1200px;
        margin-left: 1em;
    }
    
    #gridReportUC th:hover {
        cursor:pointer;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    form {
/*        margin: 0px;
        margin-bottom: 1em;*/
    }
    
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

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-3"><h2 style="margin-left:1em"><?php echo $l->l("SALES") ?></h2></div>
        <div style="clear: both"></div>        
        <div class="col-12">
            
            <div class="form-container">

                <form action="reportUserClearance.php?search=1" method="POST">
                    <div class="col-12">
                        <?php
                        $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), $post_txtDateFrom);
                        $txtDateFrom->set_format("DATE");
                        $txtDateFrom->set_locale($locale);                        
                        $txtDateFrom->get_Textbox();
                        
                        $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), $post_txtDateTo);
                        $txtDateTo->set_format("DATE");
                        $txtDateTo->set_locale($locale);                        
                        $txtDateTo->get_Textbox();
                        
                        $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname", $post_cUser, $lg->l("user"));
                        $cUser->get_comboBox();
                        
                        $l_productcategories = new selectList("l_productcategories", "PRODUCT_CATEGORIES", $post_l_productcategories, $db1);
                        $l_productcategories->set_descrField("description");
                        $l_productcategories->set_orderby("id");
                        $l_productcategories->set_label("Κατηγορίες προϊόντων");
                        $l_productcategories->getList();
                        
                        
                        ?> 
                        <div class="col-4">ΕΠΙΛΟΓΗ</div>
                        <div class="col-8">
                            <select name="cReportType">
                                <option value="ALL" <?php echo $post_cReportType=="ALL"? "selected": "" ?> >Όλες οι καταχωρήσεις</option>
                                <option value="PENDING" <?php echo $post_cReportType=="PENDING"? "selected": "" ?>>Σε εκκρεμότητα</option>
                                <option value="PAYED" <?php echo $post_cReportType=="PAYED"? "selected": "" ?>>Πλήρωσε</option>
                                <option value="CANCELED1" <?php echo $post_cReportType=="CANCELED1"? "selected": "" ?>>Ακύρωση αποστολής</option>
                                <option value="CANCELED2" <?php echo $post_cReportType=="CANCELED2"? "selected": "" ?>>Επιστροφή αποστολής</option>
                                
                            </select>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                    

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

                </form>
            </div>

            <?php if ($sql!="") { ?>
            <!-- <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2> -->

                <?php
                $rs = $db1->getRS($sql);
                for ($i = 0; $i < count($rs); $i++) {
                      $rs[$i]['status'] = func::vlookup("status", "COMPANIES_STATUS", "companyid=" .  $rs[$i]['id'] . " AND productcategory=". $rs[$i]['product_category'], $db1);
                      //$rs[$i]['status'] = func::vlookup("description", "STATUS", "id=". $rs[$i]['status'], $db1);
                                
                }
                
                $gridReportNE = new datagrid("gridReportUC", $db1, 
                    "", 
                    array("id","companyname","product_category", "status","seller","price","DeliveryDate"), 
                    array($l->l("id"),"Επωνυμία","Κατηγ. πρ.",  "Status", "Χρήστης", "Ποσό","Ημερ. παραδ."),
                    $ltoken, 0,
                    TRUE,"editcompany.php",$lg->l("open")
                    );
                
                $gridReportNE->set_rs($rs);

                $gridReportNE->set_colWidths(array("50","200","150", "150","150","100","50","50"));
                $gridReportNE->set_colsFormat(array("","","",  "","","CURRENCY","DATE"));
                $gridReportNE->col_vlookup("companyname","companyname","COMPANIES","companyname", $db1);
                //$gridReportNE->GetItemsFromIds($db1, "product_categories", "PRODUCT_CATEGORIES");
                $gridReportNE->col_vlookup("product_category","product_category","PRODUCT_CATEGORIES","description", $db1);
                //$gridReportNE->col_vlookup("price","price","TRANSACT","price", $db1);
                $gridReportNE->col_vlookup("DeliveryDate","DeliveryDate","COMPANIES","DeliveryDate", $db1);
                $gridReportNE->col_vlookup("status","status","STATUS","description", $db1); 
                $gridReportNE->col_vlookup("seller","seller","USERS","fullname", $db1);
                $gridReportNE->get_datagrid();

                echo "<br/><h3 style=\"margin-left:20px\">ΣΥΝΟΛΟ ΚΑΤΑΧΩΡΗΣΕΩΝ ".count($gridReportNE->get_rs())."</h3>";
                echo "<h3 style=\"margin-left:20px\">ΣΥΝΟΛΙΚΟ ΠΟΣΟ ".func::format(func::rsSum($gridReportNE->get_rs(),"price"), "CURRENCY")."</h3>";
                                        
            }
            else{
                echo '<h2 class="search-results">'.$msg.'</h2>';
            }
            ?>
        </div>       
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?> 
    
    <script>
        $(function() {
            $("#gridReportUC").tableExport({formats: ["xlsx","xls", "csv"]});
            
        });

    </script>
    
</body>
</html>


