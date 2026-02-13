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

if (isset($_GET['search']) && $_GET['search']==1) {
    if ($_POST['txtDateFrom']!="") {
        $dateFrom = textbox::getDate($_POST['txtDateFrom'], $locale); //str14
        $yy = substr($dateFrom, 0, 4);
        $mm = substr($dateFrom, 4, 2);
        $dd = substr($dateFrom, 6, 2);
        $dateFrom = $yy."-".$mm."-".$dd." 00:00:00";
        
        if ($_POST['txtDateTo']!="") {
            $dateTo = textbox::getDate($_POST['txtDateTo'], $locale); //str14
            $yy = substr($dateTo, 0, 4);
            $mm = substr($dateTo, 4, 2);
            $dd = substr($dateTo, 6, 2);
        }        
        $dateTo = $yy."-".$mm."-".$dd." 23:59:59";
        
        $sql1 = "SELECT company FROM ACTIONS WHERE status2=5 AND atimestamp>='".$dateFrom."' AND atimestamp<='".$dateTo."'";            
        $criteria = $lg->l("DATES").":".$_POST['txtDateFrom']."-".$_POST['txtDateTo'];
        
        
        if ($_POST['cUser']<>0) {
            $username = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
            $sql1 .= " AND user=".$_POST['cUser'];            
            $criteria = $lg->l("USER").":".$username." / ".$criteria;            
        }
        
        $sql = "SELECT companyid AS id, companyid AS companyname, companyid AS price, companyid AS DeliveryDate, productcategory, `status`, userid FROM COMPANIES_STATUS WHERE companyid IN (".$sql1.") ";  
        //echo $sql;
        
        $productCategories = selectList::getVal("l_productcategories", $_POST);
        if ($productCategories!="") {
            $productCategories = str_replace(array("[", "]"), "", $productCategories);
            $sql .= " AND productcategory IN ($productCategories) ";
            $criteria .= " / Κατηγ. προίόντων = $productCategories";
        }
        echo $sql;
        
        switch ($_POST['cReportType']) {
            case "ALL": 
                $sql .=" AND (COMPANIES_STATUS.`status` IN (5,6,7,8,9,10))";
                break;
            case "PAYED":
                $sql .=" AND (COMPANIES_STATUS.`status` IN (9, 10))";
                $criteria .=" / ΕΧΟΥΝ ΠΛΗΡΩΣΕΙ";
                break;
            case "CANCELED1":
                $sql .=" WHERE (COMPANIES_STATUS.status =4)";
                $criteria .=" / ΑΚΥΡΑ";
                break;
            case "CANCELED2":
                $sql .=" WHERE (COMPANIES_STATUS.status =8)";
                $criteria .=" / ΑΠΕΣΤΑΛΜΕΝΑ-ΑΚΥΡΑ";
                break;
            case "PENDING":
                $sql .=" WHERE (COMPANIES_STATUS.status IN (5, 6, 7))";
                $criteria .=" / ΣΕ ΕΚΚΡΕΜΟΤΗΤΑ";
                break;
            default :
                //$sql .=" WHERE (COMPANIES.`status`<>8)";
                break;
        }
        $sql .=" ORDER BY userid, companyname";
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />

<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>

<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

<script>
    $(document).ready(function() {	

        $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 550 });	


        $("#gridReportUC").tablesorter({ 
                headers: { 
                    5: { 
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

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script>        
    $(function() {
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

</script>



<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridReportUC {
        max-width: 1100px;
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

                <form action="reportUserClearance2.php?search=1" method="POST">
                    <div class="col-12">
                        <?php
                        $txtDateFrom = new textbox("txtDateFrom", $lg->l("date-from"), "");
                        $txtDateFrom->set_format("DATE");
                        $txtDateFrom->set_locale($locale);                        
                        $txtDateFrom->get_Textbox();
                        
                        $txtDateTo = new textbox("txtDateTo", $lg->l("date-to"), "");
                        $txtDateTo->set_format("DATE");
                        $txtDateTo->set_locale($locale);                        
                        $txtDateTo->get_Textbox();
                        
                        $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname",0,$lg->l("user"));
                        $cUser->get_comboBox();
                        
                        $l_productcategories = new selectList("l_productcategories", "PRODUCT_CATEGORIES", "", $db1);
                        $l_productcategories->set_descrField("description");
                        $l_productcategories->set_orderby("id");
                        $l_productcategories->set_label("Κατηγορίες προϊόντων");
                        $l_productcategories->getList();
                        
                        
                        ?> 
                        <div class="col-4">ΕΠΙΛΟΓΗ</div>
                        <div class="col-8">
                            <select name="cReportType">
                                <option value="ALL">Όλες οι καταχωρήσεις</option>
                                <option value="PENDING">Σε εκκρεμότητα</option>
                                <option value="PAYED">Πλήρωσε + Καταχ. στο site</option>
                                <option value="CANCELED1">Ακύρωση</option>
                                <option value="CANCELED2">Επιστροφή-Ακύρωση</option>
                                
                            </select>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                    

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />

                </form>
            </div>

            <?php if ($sql!="") { ?>
            <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>

                <?php
                    $gridReportNE = new datagrid("gridReportUC", $db1, 
                            $sql, 
                            array("id","companyname","productcategory", "status","userid","price","DeliveryDate"), 
                            array($l->l("id"),$l->l("companyname"),"Κατηγ. πρ.",   $l->l("status"),$l->l("user"),$l->l("price"),"ΗΜ.ΠΑΡΑΔ."),
                            $ltoken, 0,
                            TRUE,"editcompany.php",$lg->l("open")
                            );
                                                           
                    $gridReportNE->set_colWidths(array("50","200","150", "150","150","100","50","50"));
                    $gridReportNE->set_colsFormat(array("","","",  "","","CURRENCY","DATE"));
                    $gridReportNE->col_vlookup("companyname","companyname","COMPANIES","companyname", $db1);
                    $gridReportNE->col_vlookup("productcategory","productcategory","PRODUCT_CATEGORIES","description", $db1);
                    $gridReportNE->col_vlookup("price","price","COMPANIES","price", $db1);
                    $gridReportNE->col_vlookup("DeliveryDate","DeliveryDate","COMPANIES","DeliveryDate", $db1);
                    $gridReportNE->col_vlookup("status","status","STATUS","description", $db1); 
                    $gridReportNE->col_vlookup("userid","userid","USERS","fullname", $db1);
                    $gridReportNE->get_datagrid();
                    
                    echo "<br/><h3>ΣΥΝΟΛΟ ΚΑΤΑΧΩΡΗΣΕΩΝ ".count($gridReportNE->get_rs())."</h3>";
                    echo "<h3>ΣΥΝΟΛΙΚΟ ΠΟΣΟ ".func::format(func::rsSum($gridReportNE->get_rs(),"price"), "CURRENCY")."</h3>";
                                        
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
    
</body>
</html>


