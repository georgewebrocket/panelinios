<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');

if (strpos($_SESSION['user_access'],"1")===false) {                   
	header('Location: index.php');
}

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

$sql="";
$criteria = "";
$reference = 0;
$safm = "";

$criteriaBlockAccess = "";
if (($_SESSION['user_profile']==1)) {
	$blockstatusnegative = func::vlookup("keyvalue", "SETTINGS", "keycode='BLOCK-STATUS-NEGATIVE'", $db1);
	$criteriaBlockAccess = $blockstatusnegative==1? " AND status <> 4 ": "";
}


if (isset($_GET['search']) && $_GET['search']==1) {
    
    $chkInactive = 0; $qrActive = " AND COMPANIES.active=1 ";
    if ($_SESSION['user_profile']==3) {
        $qrActive = ""; //show all
        $chkInactive = checkbox::getVal2($_POST, "chk_inactive");
        if ($chkInactive==1) {
            $qrActive = " AND COMPANIES.active=0 "; //inactive only
        }
    }    
    
    $sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
            . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
            . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
            . "'' AS user2, '' AS recalldate2, '' AS recalltime2 "
            . "FROM COMPANIES WHERE (COMPANIES.id>0 $criteriaBlockAccess $qrActive) ";
    if ($_POST['s-id']!="") {
        $criteria = $l->l("id")."=".$_POST['s-id'];
        //$sql .= " AND (id=".$_POST['s-id'].")";
        $sql .= " AND (id IN (".$_POST['s-id']."))";
    }
    elseif ($_POST['s-catalogue-id']!="") {
        $criteria = $l->l("catalogue-id")."=".$_POST['s-catalogue-id'];
        $sql .= " AND (catalogueid=".$_POST['s-catalogue-id'].")";        
    }
    
    //$sql .= " (id>0) ";
//    if ($_POST['s-company-name']!="") {
//        $criteria .= $l->l("companyname")."=".$_POST['s-company-name']."/";
//        $str = str_replace("*", "%", $_POST['s-company-name']);
//        $sql .= " AND (companyname LIKE '".$str."')"; 
//    } 
    
    //h-company-name
    if ($_POST['h-company-name']>0) {
        $searchid = $_POST['h-company-name'];
        $criteria .= $l->l("companyname")."=".$_POST['s-company-name']."/";
        $sql .= " AND (id =$searchid)"; 
    }
    elseif ($_POST['s-company-name']!="") {
        $criteria .= $l->l("companyname")."=".$_POST['s-company-name']."/";
        $str = str_replace("*", "", $_POST['s-company-name']);
        $sql .= " AND (companyname LIKE '%".$str."%')"; 
    } 
    
    if ($_POST['s-phone']!="") {
        $criteria .= $l->l("phone")."=".$_POST['s-phone']."/";
        $str = str_replace("*", "%", $_POST['s-phone']);
        $str = str_replace(" ", "", $str);
        $str = str_replace("-", "", $str);
        $str = str_replace("/", "", $str);
        $str = "%" . $str . "%";
        //$sql .= " AND (digits(phone1) LIKE '".$str."' OR digits(phone2) LIKE '".$str."' OR digits(mobilephone) LIKE '".$str."') ";
		$sql .= " AND (phone1digits LIKE '".$str."' OR phone2digits LIKE '".$str."' OR mobiledigits LIKE '".$str."' OR faxdigits LIKE '".$str."') ";
    }
    
    if ($_POST['s-email']!="") {
        $sql .= " AND email LIKE '%".$_POST['s-email']."%' ";
        $criteria .= "Email=".$_POST['s-email']."/";
    }
    
    if ($_POST['cCategory']!=0) {
        $categorydescription = func::vlookup("description", "CATEGORIES", "id=".$_POST['cCategory'], $db1);
        $criteria .= $l->l("category")."=".$categorydescription."/";        
        $sql .= " AND (basiccategory =".$_POST['cCategory']." OR subcategory = ".$_POST['cCategory'].")";
    }
    
    if ($_POST['cReference']!=0) {
        $refdescription = func::vlookup("description", "REFERENCE", "id=".$_POST['cReference'], $db1);
        $criteria .= $l->l("reference")."=".$refdescription."/";        
        $sql .= " AND (reference =".$_POST['cReference'].")";
        $reference = $_POST['cReference'];
    }
    
    if ($_POST['cArea']!=0) {
        $areadescription = func::vlookup("description", "AREAS", "id=".$_POST['cArea'], $db1);
        $criteria .= $l->l("area")."=".$areadescription."/";        
        $sql .= " AND (area =".$_POST['cArea'].")";
    }
    
    if ($_POST['c_city_id']!=0) {
        $citydescription = func::vlookup("description", "EP_CITIES", "id=".$_POST['c_city_id'], $db1);
        $criteria .= "ΠΟΛΗ=".$citydescription."/";        
        $sql .= " AND (city_id =".$_POST['c_city_id'].")";
    }
    
    if ($_POST['cCompanyType']!=0) {
        $companytype = func::vlookup("description", "COMPANY_TYPES", "id=".$_POST['cCompanyType'], $db1);
        $criteria .= $l->l("company_type")."=".$companytype."/";        
        $sql .= " AND (company_type =".$_POST['cCompanyType'].")";
    }
    
    if ($_POST['cStatus']!=0) {
        $statusdescription = func::vlookup("description", "STATUS", "id=".$_POST['cStatus'], $db1);
        $criteria .= $l->l("status")."=".$statusdescription."/";
        $sql .= " AND (status =".$_POST['cStatus'].")";
    }
    
    if ($_POST['c_companystatus']!=0 || $_POST['c_productcategory']!=0 || $_POST['cUser']!=0 || $_POST['txtRecallDate']!="") {
        $sqlStatus = "";
        if ($_POST['c_companystatus']!=0) {
            $statusdescription = func::vlookup("description", "STATUS", "id=".$_POST['c_companystatus'], $db1);
            $criteria .= "Status επικ.=".$statusdescription."/";
            $sqlStatus = " AND status =".$_POST['c_companystatus'];
        }
        
        
        $sqlProductCategory = "";
        if ($_POST['c_productcategory']!=0) {
            $productcatdescription = func::vlookup("description", "PRODUCT_CATEGORIES", "id=".$_POST['c_productcategory'], $db1);
            $criteria .= "Κατηγ. προϊόντος=".$productcatdescription."/";
            $sqlProductCategory = " AND productcategory =".$_POST['c_productcategory'];
        }
        
        $sqlUser = "";
        if ($_POST['cUser']!=0) {                
            $userfullname = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
            $criteria .= $l->l("user")."=".$userfullname."/";
            $sqlUser = " AND userid =".$_POST['cUser'];
            
        }
        
        $sqlRecall = "";
        if ($_POST['txtRecallDate']!="") {
            $criteria .= $l->l("recall-date")."=".$_POST['txtRecallDate']."/";
            $recalldate = textbox::getDate($_POST['txtRecallDate'], $locale);
            $recalldate1 = substr($recalldate, 0, 8)."000000";
            $recalldate2 = substr($recalldate, 0, 8)."999999";
            $sqlRecall = " AND (CAST(recalldate AS UNSIGNED)>=".$recalldate1.")";
            $sqlRecall .= " AND (CAST(recalldate AS UNSIGNED)<=".$recalldate2.")";
            //$sqlRecall = " AND (recalldate>='".$recalldate1."')";
            //$sqlRecall .= " AND (recalldate<='".$recalldate2."')";
            //$sqlRecall .= " ORDER BY recalldate, recalltime ";
        }
        
        $sql .= " AND (id IN (SELECT companyid FROM COMPANIES_STATUS WHERE id>0 ". $sqlStatus . $sqlProductCategory . $sqlUser . $sqlRecall . " ORDER BY recalldate, recalltime))";
    
        
        
    }
    
    
    
    
    
    if ($_POST['txtDeliveryDateFrom']!="") {
        $criteria .= $l->l("delivery-date-from").">=".$_POST['txtDeliveryDateFrom']."/";
        $deliverydatefrom = textbox::getDate($_POST['txtDeliveryDateFrom'], $locale);
        $deliverydatefrom = substr($deliverydatefrom, 0, 8)."000000";
        $sql .= " AND (CAST(DeliveryDate AS UNSIGNED)>=".$deliverydatefrom.")";

    }
    
    if ($_POST['txtDeliveryDateTo']!="") {
        $criteria .= $l->l("delivery-date-to")."<=".$_POST['txtDeliveryDateTo']."/";
        $deliverydateto = textbox::getDate($_POST['txtDeliveryDateTo'], $locale);
        $deliverydateto = substr($deliverydateto, 0, 8)."999999";
        $sql .= " AND (CAST(DeliveryDate AS UNSIGNED)<=".$deliverydateto.")";

    }
    
    //$t_voucherid
    if ($_POST['t_voucherid']!="") {
        $criteria .= $l->l("Voucher")."=".$_POST['t_voucherid']."/"; 
        $str = str_replace("*", "%", $_POST['t_voucherid']);
        $sql .= " AND (voucherid LIKE '".$str."')";

    }
    
    
    if ($_POST['txtExpireDate']!="") {
        $criteria .= $l->l("expire-date")."<=".$_POST['txtExpireDate']."/";
        $expiredate = textbox::getDate($_POST['txtExpireDate'], $locale);
        $expiredate = substr($expiredate, 0, 8)."999999";
        $sql .= " AND (CAST(expires AS UNSIGNED)<=".$expiredate.")";

    }
    
    if ($_POST['t_domain_expires']!="") {
        $criteria .= "Λήξη domain <=".$_POST['t_domain_expires']."/";
        $expiredateDomain = textbox::getDate($_POST['t_domain_expires'], $locale);
        $expiredateDomain = substr($expiredateDomain, 0, 8)."999999";
        $sql .= " AND (CAST(domain_expires AS UNSIGNED)<=".$expiredateDomain.")";

    }
    
    if ($_POST['cOnlineStatus']>=0) {        
        $criteria .= $l->l("ON-LINE")."=".$_POST['cOnlineStatus']."/";
        $sql .= " AND (onlinestatus=".$_POST['cOnlineStatus'].")";
    }
    
    if ($_POST['c_courier']>0) {
        $courier = $_POST['c_courier'];
        $courierDescr = func::vlookup("description", "COURIER", "id=$courier", $db1);
        $criteria .= "COURIER = $courierDescr / ";
        $sql .= " AND (courier= $courier )";
    } 
    
    if ($_POST['c_hasAfm']>0) {
        if ($_POST['c_hasAfm']=="1") {
            $criteria .= "ΜΕ ΑΦΜ / ";
            $sql .= " AND (afm<> '' AND afm IS NOT NULL)";
        }
        if ($_POST['c_hasAfm']=="2") {
            $criteria .= "ΧΩΡΙΣ ΑΦΜ / ";
            $sql .= " AND (afm LIKE '' OR afm IS NULL)";
        }
    }
    
    $safm = $_POST['s-afm'];
    if ($safm!='') {
        $criteria .= " ΑΦΜ=$safm / ";
            $sql .= " AND (afm LIKE '$safm')";
    }
    
    $seoissues = checkbox::getVal2($_POST, "chk_issues");
    if ($seoissues==1) {
    	$criteria .= " Με εκκρεμότητες SEO / ";
    	$sql .= " AND (profession IS NULL OR profession=0 OR city_id IS NULL OR city_id=0)";
    }
        
    $userExportSql='ExportSql'.$_SESSION['user_id'];
    $_SESSION[$userExportSql]=$sql;   
    
    
}
    
//echo $sql;


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
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 800 });	
});

$(document).ready(function() {	
	$("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });	
});

</script>

<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

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
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        //$('#gridCompanies').tablesorter(); 
        
        
        $("#gridCompanies").tablesorter({ 
            sortList: [[3,0],[4,0]],
            headers: { 
                3: { 
                    sorter:'dates' 
                } 
            } 
        });
        
        
        });

</script>

<script>
    
    
    function SelectAll() {
        if ($('.checkrow:first').prop('checked')==false) {
            $('.checkrow').prop('checked', true);
        }
        else {
            $('.checkrow').prop('checked', false);
        }
        
    }

    function ChangeStatusBulk() {
        //get ids from checkboxes
        var ids = "";
        $( "input.checkrow:checked" ).each(function() {
                var value = $( this ).val();
                if (ids == "") {
                    ids = value;
                }
                else {
                    ids += ","+value;
                }            
        });
        //goto change status page if something is selected
        if (ids != "") {       
            openFancyFrame('changeStatusBulk.php?ids='+ids,600,400);
        }
        else {
            alert("Please select companies first");
        }
    }

    function openFancyFrame(myURL,w,h) {
      $.fancybox({
             'type' : 'iframe',
             'width' : w, 'height' : h,
             'href' : myURL
      });
    }


</script>





<style>
    
    .form-container {
        max-width: 1000px;
        min-height: 0px;
    }
    
    #gridCompanies {
        max-width: 1100px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    .main {
        min-height: 900px;
    }
    
    #gridCompanies th {
        cursor:pointer;
    }
    
    #gridCompanies td {
        vertical-align: top;
    }
    
</style>

<?php
$colorKX = func::vlookup("color", "PRODUCT_CATEGORIES", "id=1", $db1);
$colorDM = func::vlookup("color", "PRODUCT_CATEGORIES", "id=2", $db1);
?>

<style>
    #txtExpireDate {
        background-color: <?php echo $colorKX; ?>;
    }
    #t_domain_expires {
        background-color: <?php echo $colorDM; ?>;
    }
</style>


</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div style="clear:both"></div>
    
    <div class="main">
        
        <div class="col-9">
        
            <div class="col-3 col-md-12"><h2 style="margin-left:1em"><?php echo $l->l("search-catalogue") ?></h2></div>

            <div class="col-9 col-md-12" style="text-align:right">
                <a class="button fancybox" href="editcompany.php?id=0"><?php echo $l->l("add-new") ?></a>&nbsp;
                <?php if ($_SESSION['user_profile']>1) { ?>
                <a class="button fancybox500" href="importcsv.php"><?php echo $l->l("import-csv") ?></a>&nbsp;
                <a class="button fancybox500" href="exportcsv.php"><?php echo $l->l("export-csv") ?></a>&nbsp;
                <a onclick="SelectAll()" class="button" href="#">Select all</a>&nbsp;
                <a onclick="ChangeStatusBulk()" class="button" href="#"><?php echo $l->l("change-status") ?></a>&nbsp;
                <a class="button fancybox500" href="uc.php"><?php echo $l->l("print") ?></a> 
                <?php } ?>

            </div>

            <div style="clear:both"></div>

            <div class="form-container">

                <form action="home.php?search=1" method="POST">
                    <div class="col-6 col-md-12">
                        <div class="col-4"><?php echo $l->l("id") ?></div>
                        <div class="col-8"><input type="text" name="s-id" value="" placeholder="" /></div>

                        <div class="col-4"><?php echo $l->l("catalogue-id") ?></div>
                        <div class="col-8"><input type="text" name="s-catalogue-id" value="" placeholder="online κατάλογος" /></div>

<!--                        <div class="col-4"><?php echo $l->l("company-name") ?></div>
                        <div class="col-8"><input type="text" name="s-company-name" value="" placeholder="π.χ. tech*" /></div>-->
                        
                        <?php
                            $ac_companyName = new autocomplete("s-company-name", "COMPANIES", "", $db1);
                            $ac_companyName->set_label("Επιλογή εταιρείας");
                            $ac_companyName->set_descr_field("companyname");
                            $ac_companyName->set_hiddenid("h-company-name");
                            $ac_companyName->getAutocomplete();
                        ?>
                        
                        <div class="col-4"><?php echo $l->l("phone") ?></div>
                        <div class="col-8"><input type="text" name="s-phone" value="" placeholder="π.χ. 2107517" /></div>
                        
                        <div class="col-4">Email</div>
                        <div class="col-8"><input type="text" name="s-email" value="" placeholder="" /></div>

                        <?php
                        $cReference = new comboBox("cReference", $db1, 
                                "SELECT id, description FROM REFERENCE", 
                                "id","description",$reference,$l->l("reference"));
                        $cReference->get_comboBox();                        
                        ?>
                            
                            
                        <?php
//                        $cCategory = new comboBox("cCategory", $db1, "SELECT id, CONCAT((CASE parentid WHEN 0 THEN '+' ELSE '-' END),description) AS MyDescr FROM CATEGORIES ORDER BY parentid, description", 
//                                "id","MyDescr",0,$l->l("category"));
//                        $cCategory->get_comboBox();
                        
                        $ac_category = new autocomplete("s-category", "CATEGORIES", "", $db1);
                        $ac_category->set_label("Επιλ. κατηγορίας");
                        $ac_category->set_hiddenid("cCategory");
                        $ac_category->getAutocomplete();
                        
                        
                        ?>

                        

                        <?php
                        $cArea = new comboBox("cArea", $db1, "SELECT id, description FROM AREAS", 
                                "id","description",0,$l->l("area"));
                        $cArea->get_comboBox();                        
                        ?>
                        
                        <?php
                        $c_city_id = new comboBox("c_city_id", $db1, "SELECT id, description FROM EP_CITIES", 
                                "id","description",0,"ΠΟΛΗ");
                        $c_city_id->get_comboBox();                        
                        ?>

                        <?php
                        $cCompanyType = new comboBox("cCompanyType", $db1, "SELECT id, description FROM COMPANY_TYPES", 
                                "id","description",0,$l->l("company_type"));
                        $cCompanyType->get_comboBox();                        
                        ?>
                        
                        <div class="col-4">ΜΕ ΑΦΜ</div>
                        <div class="col-8">
                            <select name="c_hasAfm">
                                <option value="0">...</option>
                                <option value="1">ΝΑΙ</option>
                                <option value="2">ΟΧΙ</option>
                            </select>
                        </div>
                        
                        <div class="col-4">ΑΦΜ</div>
                        <div class="col-8"><input type="text" name="s-afm" value="<?php echo $safm; ?>" placeholder="000000000" /></div>
                        
                        <?php
                        
                        $cStatus = new comboBox("cStatus", $db1, "SELECT id, description FROM STATUS2", 
                                "id","description",0,"Status εταιρείας");
                        $cStatus->get_comboBox();
                        
                        ?>



                    </div>

                    <div class="col-6 col-md-12">

                        



                        <?php
                                                
                        $c_companystatus = new comboBox("c_companystatus", $db1, "SELECT * FROM STATUS WHERE active=1", "id","description",0,"Status επικοιν.");
                        $c_companystatus->get_comboBox();
                        
                        $c_productcategory = new comboBox("c_productcategory", $db1, "SELECT id, description FROM PRODUCT_CATEGORIES", "id","description",0,"Κατηγ. προϊόντος");
                        $c_productcategory->get_comboBox();
                                               
                        ?>
                        
                        


                        <?php
                        $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname",0,$l->l("user"));
                        $cUser->get_comboBox();                        
                        ?>



                        <?php
                        $txtRecallDate = new textbox("txtRecallDate", $l->l("Recall-date"), "");
                        $txtRecallDate->set_format("DATE");
                        $txtRecallDate->set_locale($locale);                        
                        $txtRecallDate->get_Textbox();
                        ?>
                        
                        <div class="col-4">On-Line</div>
                        <div class="col-8">
                            <select name="cOnlineStatus">
                                <option value="-1"></option>
                                <option value="1">ΝΑΙ</option>
                                <option value="0">ΟΧΙ</option>
                            </select>
                        </div>
                        
                        <?php
                        $txtDeliveryDateFrom = new textbox("txtDeliveryDateFrom", $l->l("Delivery-from"), "");
                        $txtDeliveryDateFrom->set_format("DATE");
                        $txtDeliveryDateFrom->set_locale($locale);                        
                        $txtDeliveryDateFrom->get_Textbox();
                        ?>
                        
                        <?php
                        $txtDeliveryDateTo = new textbox("txtDeliveryDateTo", $l->l("Delivery-to"), "");
                        $txtDeliveryDateTo->set_format("DATE");
                        $txtDeliveryDateTo->set_locale($locale);                        
                        $txtDeliveryDateTo->get_Textbox();
                        
                        
                        //voucherid
                        $t_voucherid = new textbox("t_voucherid", $l->l('Voucher'),"");                        
                        $t_voucherid->get_Textbox();
                        
                        
                        ?>
                        
                        <?php
                        
                        $txtExpireDate = new textbox("txtExpireDate", "Ημερ. λήξης KX", "");
                        $txtExpireDate->set_format("DATE");
                        $txtExpireDate->set_locale($locale);                        
                        $txtExpireDate->get_Textbox();
                        
                        
                        $t_domain_expires = new textbox("t_domain_expires", "Ημερ. λήξης DM", "");
                        $t_domain_expires->set_format("DATE");
                        $t_domain_expires->set_locale($locale);                        
                        $t_domain_expires->get_Textbox();
                        
                        
                        //COURIER
                        $c_courier = new comboBox("c_courier", $db1, 
                                "SELECT id, description FROM COURIER WHERE active=1", 
                                "id","description",0,"Courier");                                   
                        $c_courier->get_comboBox();
                        
                        $chk_issues = new checkbox("chk_issues", "Με εκκρεμότητες SEO", 0);
                        echo "<div class=\"col-4\">Με εκκρεμότητες SEO</div><div class=\"col-8\"><div class=\"col-2\">";
                        echo $chk_issues->checkboxSimple();
                        
                        if ($_SESSION['user_profile']==3) {
                            $chk_inactive = new checkbox("chk_inactive", "Μόνο ανενεργοί", 0);
                            echo "</div><div class=\"col-8\">Μόνο ανενεργοί</div><div class=\"col-2\">";
                            echo $chk_inactive->checkboxSimple();
                        }
                        echo "</div></div>";
                        
                        ?>
                        
                        


                    </div>

                    <div style="clear: both"></div>

                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />
                    <?php //if ($_SESSION['user_profile']>1) { ?>
                    Max rows: <input style="width:100px" type="text" name="txtMaxRows" value="50" />
                    <?php //} ?>

                </form>
            </div>

            <?php if ($sql!="") { ?>
            <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>



            <?php
                    //$sql .= " LIMIT 50"; //xxx
            
                    if (/*$_SESSION['user_profile']>1 &&*/ isset($_POST['txtMaxRows']) && is_numeric($_POST['txtMaxRows'])) {
                        $sql .= " LIMIT ".$_POST['txtMaxRows'];
                        
                    }
                    else {
                        $sql .= " LIMIT 50";
                    }
                    //echo $_POST['txtMaxRows'];
                    $rs = $db1->getRS($sql);
                    
                    
                    for ($i=0;$i<count($rs);$i++) {
                        if ($rs[$i]['active']==0) {
                            $rs[$i]['id2'] = "<div style=\"background-color:rgb(255,200,200);padding:10px\">" . 
                                $rs[$i]['id2']."</div>";
                        }
                        
                        $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? ORDER BY recalldate";
                        $rs2 = $db1->getRS($sql, array($rs[$i]['id']));
                        $strStatus = "";
                        $strUser = "";
                        $strRecallDate = ""; $strRecallTime = "";
                        for ($k = 0; $k < count($rs2); $k++) {
                            $myColor = func::vlookup("color", "PRODUCT_CATEGORIES", "id=".$rs2[$k]['productcategory'], $db1);
                            $myProductCategory = func::vlookup("shortdescription", "PRODUCT_CATEGORIES", "id=".$rs2[$k]['productcategory'], $db1);
                            $myStatus = func::vlookup("code", "STATUS", "id=".$rs2[$k]['status'], $db1);
                            $myUser = func::vlookup("fullname", "USERS", "id=".$rs2[$k]['userid'], $db1);
                            $myUser = str_replace(" ", "<br/>", $myUser);
                            $myRecallDate = func::str14toDate($rs2[$k]['recalldate'], "/");
                            $myRecallDateShort = func::str14toDateDM($rs2[$k]['recalldate'], "/");
                            $myRecallTime = func::vlookup("description", "TIMES", "id=".$rs2[$k]['recalltime'], $db1);
                            if ($myRecallDate!="" && $strRecallDate=="") {
                                $strRecallDate = $myRecallDate;
                            }
                            if ($myRecallTime!="" && $strRecallTime=="") {
                                $strRecallTime = $myRecallTime;
                            }
                            
                            $strStatus .= "<div style=\"background-color:$myColor;padding:3px\"><div class=\"col-5\">$myProductCategory <br/> $myStatus </div> <div class=\"col-7\">$myUser ($myRecallDateShort)</div> <div style=\"clear:both\"></div> </div>";
                            
                            
                        }
                        $rs[$i]['status2'] = $strStatus;
                        $rs[$i]['recalldate2'] = $strRecallDate;
                        $rs[$i]['recalltime2'] = $strRecallTime;
                    }
                    
                    
                    if ($_POST['c_companystatus']==4) { //arnitikos
                        for ($i=0;$i<count($rs);$i++) {
                            $rs[$i]['recalldate2'] = func::str14toDate($rs[$i]['lastactiondate'],"/");
                        }
                    }
                    
                    
                    $gridCompanies = new datagrid("gridCompanies", $db1, 
                            "", 
                            array("id2","companyname", "status2", "recalldate2","recalltime2", "phones","area","basiccategory","status"), 
                            array("ID","ΕΠΩΝΥΜΙΑ","Status επικ.", "RDATE","RTIME", $l->l("phones"),
                                "Περιοχή", "Κατηγορία", "Status"),
                            $ltoken, 0
                            );
                    $gridCompanies->set_rs($rs);
                    $gridCompanies->set_edit("editcompany.php");
                    $gridCompanies->set_select($l->l("select"));
                    $gridCompanies->set_colWidths(array("50","400", "400", "150", "100", "200","150","150","80",
                        "50","50"));
                    $gridCompanies->col_vlookup("status","status","STATUS2","shortdescription", $db1);  
                    //$gridCompanies->col_vlookup("user","user","USERS","fullname", $db1);
                    //$gridCompanies->col_vlookup("recalltime","recalltime","TIMES","description", $db1);
                    $gridCompanies->col_vlookup("area","area","AREAS","description", $db1); 
                    $gridCompanies->col_vlookup("basiccategory","basiccategory","CATEGORIES","description", $db1);
                    //$gridCompanies->set_colsFormat(array('','','','','','','','',''));
                    
                    $gridCompanies->get_datagrid();
                    
                    echo "<h2 style=\"margin:10px\">Total: ".count($gridCompanies->get_rs())."</h2>";


            }        

            ?>


        </div>
        
        <div class="col-3">
            <div style="position:fixed; right:0px; top:120px; width:24%; height: 450px;">
                <div style="padding: 0px 10px 20px 15px; border-left: 1px solid rgb(200,200,200); margin-left: 15px;">
                    <iframe src="messages.php?popupurl=editcompany" width="100%" height="550" frameborder="0"></iframe>



                </div>
            </div>
            
        </div>
        
        
        <div style="clear: both"></div>
        
        
    </div>
    
    <div style="clear: both"></div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>

