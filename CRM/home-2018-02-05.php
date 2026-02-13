<?php
/*
ini_set('display_errors',1); 
error_reporting(E_ALL);
*/
require_once('php/session.php');

if (!isset($_SESSION['user_access'])) {                   
	header('Location: index.php');
}

if (strpos($_SESSION['user_access'],"[1]")===false) {                   
	header('Location: index.php');
}

$userid = $_SESSION['user_id'];

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
	$criteriaBlockAccess = $blockstatusnegative==1? " status = 4 ": "";
}

$s_id = "";
$s_catalogueid = "";
$s_companyname = "";
$s_phone = "";
$s_email = "";
$s_category = 0;
$s_reference = 0;
$s_area = 0;
$s_city = 0;
$s_companytype = 0;
$s_status = 0;
$s_commstatus = 0;
$s_productcategory = 0;
$s_user = 0;
$s_recalldate = "";
$s_statusdate1 = "";
$s_statusdate2 = "";
$s_deliveryDateFrom = "";
$s_deliveryDateTo = "";
$s_voucherid = "";
$s_ExpireDate = "";
$s_domain_expires = "";
$s_OnlineStatus = -1;
$s_hasdomain = -1;
$s_courier = 0;
$s_hasAfm = 0;
$s_afm = "";
$s_seoissues = 0;   
$s_inactive = 0;
$s_sort = "id";
$s_recordsperpage = 30;




if (isset($_REQUEST['newcalls']) && $_REQUEST['newcalls']==1) {
    
    $recalldate = date("Ymd")."999999";
    
    /*$sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
        . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
        . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
        . "'' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus " 
        . " FROM COMPANIES WHERE active=1 AND (recalldate IS NULL OR recalldate='') AND id IN (SELECT companyid FROM COMPANIES_STATUS WHERE userid=$userid AND (CAST(recalldate AS UNSIGNED))<=$recalldate AND status=3) ORDER BY id LIMIT 30";*/
    
    $sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
        . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
        . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
        . "'' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus " 
        . " FROM COMPANIES WHERE active=1 AND (commstatus IS NULL OR commstatus='') AND id IN (SELECT companyid FROM COMPANIES_STATUS WHERE userid=$userid AND (CAST(recalldate AS UNSIGNED))<=$recalldate AND status=3) ORDER BY id LIMIT 30";
    
    //$sql .= " LIMIT 30 ";
}

if (isset($_REQUEST['recalls']) && $_REQUEST['recalls']==1) {
    
    $recalldate = date("Ymd")."999999";
    
    /*$sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
        . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
        . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
        . "'' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus " 
        . " FROM COMPANIES WHERE active=1 AND recalldate IS NOT NULL AND recalldate<>'' AND id IN (SELECT companyid FROM COMPANIES_STATUS WHERE userid=$userid AND (CAST(recalldate AS UNSIGNED))<=$recalldate AND status=3) ORDER BY id";*/
    
    $sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
        . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
        . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
        . "'' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus " 
        . " FROM COMPANIES WHERE active=1 AND commstatus IS NOT  NULL AND commstatus <>'' AND id IN (SELECT companyid FROM COMPANIES_STATUS WHERE userid=$userid AND (CAST(recalldate AS UNSIGNED))<=$recalldate AND status=3) ORDER BY id";
    
}





if ($_REQUEST['BtnSearch']=="SEARCH") {
    
    $s_id = $_REQUEST['s-id'];
    $s_catalogueid = $_REQUEST['s-catalogue-id'];
    $s_companyname = $_REQUEST['h-company-name'];
    $s_phone = $_REQUEST['s-phone'];
    $s_email = $_REQUEST['s-email'];
    $s_category = $_REQUEST['cCategory'];
    $s_reference = $_REQUEST['cReference'];
    $s_area = $_REQUEST['cArea'];
    $s_city = $_REQUEST['c_city_id'];
    $s_companytype = $_REQUEST['cCompanyType'];
    $s_status = $_REQUEST['cStatus'];
    $s_commstatus = $_REQUEST['c_companystatus'];
    $s_productcategory = $_REQUEST['c_productcategory'];
    $s_user = $_REQUEST['cUser'];
    $s_recalldate = $_REQUEST['txtRecallDate'];
    $s_statusdate1 = $_REQUEST['t_statusdate1'];
    $s_statusdate2 = $_REQUEST['t_statusdate2'];
    $s_deliveryDateFrom = $_REQUEST['txtDeliveryDateFrom'];
    $s_deliveryDateTo = $_REQUEST['txtDeliveryDateTo'];
    $s_voucherid = $_REQUEST['t_voucherid'];
    $s_ExpireDate = $_REQUEST['txtExpireDate'];
    $s_domain_expires = $_REQUEST['t_domain_expires'];
    $s_OnlineStatus = $_REQUEST['cOnlineStatus'];
    $s_hasdomain = $_REQUEST['c_hasdomain'];
    $s_courier = $_REQUEST['c_courier'];
    $s_hasAfm = $_REQUEST['c_hasAfm'];
    $s_afm = $_REQUEST['s-afm'];
    $s_seoissues = checkbox::getVal2($_REQUEST, "chk_issues");;   
    $s_inactive	= checkbox::getVal2($_REQUEST, "chk_inactive");
    $s_sort = $_REQUEST['c_sort'];
    if (isset($_REQUEST['t_recordsperpage'])) {
        $s_recordsperpage = $_REQUEST['t_recordsperpage'];
    }
    
    
    $chkInactive = 0; $qrActive = " AND COMPANIES.active=1 ";
    if ($_SESSION['user_profile']==3) {
        $qrActive = ""; //show all
        $chkInactive = checkbox::getVal2($_REQUEST, "chk_inactive");
        if ($chkInactive==1) {
            $qrActive = " AND COMPANIES.active=0 "; //inactive only
        }
    }    
    
    $sql = " FROM COMPANIES WHERE (COMPANIES.id>0  $qrActive) ";
    if ($_REQUEST['s-id']!="") {
        $criteria = $l->l("id")."=".$_REQUEST['s-id'];
        //$sql .= " AND (id=".$_REQUEST['s-id'].")";
        $sql .= " AND (id IN (".$_REQUEST['s-id']."))";
    }
    elseif ($_REQUEST['s-catalogue-id']!="") {
        $criteria = $l->l("catalogue-id")."=".$_REQUEST['s-catalogue-id'];
        $sql .= " AND (catalogueid=".$_REQUEST['s-catalogue-id'].")";        
    }
    
    //$sql .= " (id>0) ";
//    if ($_REQUEST['s-company-name']!="") {
//        $criteria .= $l->l("companyname")."=".$_REQUEST['s-company-name']."/";
//        $str = str_replace("*", "%", $_REQUEST['s-company-name']);
//        $sql .= " AND (companyname LIKE '".$str."')"; 
//    } 
    
    //h-company-name
    if ($_REQUEST['s-company-name']=="") {
        $s_companyname = "";
    }
    elseif ($_REQUEST['h-company-name']>0) {
        $searchid = $_REQUEST['h-company-name'];
        $criteria .= $l->l("companyname")."=".$_REQUEST['s-company-name']."/";
        $sql .= " AND (id =$searchid)"; 
    }
    elseif ($_REQUEST['s-company-name']!="") {
        $criteria .= $l->l("companyname")."=".$_REQUEST['s-company-name']."/";
        $str = str_replace("*", "", $_REQUEST['s-company-name']);
        $sql .= " AND (companyname LIKE '%".$str."%')"; 
    } 
    
    if ($_REQUEST['s-phone']!="") {
        $criteria .= $l->l("phone")."=".$_REQUEST['s-phone']."/";
        $str = str_replace("*", "%", $_REQUEST['s-phone']);
        $str = str_replace(" ", "", $str);
        $str = str_replace("-", "", $str);
        $str = str_replace("/", "", $str);
        $str = "%" . $str . "%";
        //$sql .= " AND (digits(phone1) LIKE '".$str."' OR digits(phone2) LIKE '".$str."' OR digits(mobilephone) LIKE '".$str."') ";
		$sql .= " AND (phone1digits LIKE '".$str."' OR phone2digits LIKE '".$str."' OR mobiledigits LIKE '".$str."' OR faxdigits LIKE '".$str."') ";
    }
    
    if ($_REQUEST['s-email']!="") {
        $sql .= " AND email LIKE '%".$_REQUEST['s-email']."%' ";
        $criteria .= "Email=".$_REQUEST['s-email']."/";
    }
    
    if ($_REQUEST['cCategory']!=0) {
        $categorydescription = func::vlookup("description", "CATEGORIES", "id=".$_REQUEST['cCategory'], $db1);
        $criteria .= $l->l("category")."=".$categorydescription."/";        
        $sql .= " AND (basiccategory =".$_REQUEST['cCategory']." OR subcategory = ".$_REQUEST['cCategory'].")";
    }
    
    if ($_REQUEST['cReference']!=0) {
        $refdescription = func::vlookup("description", "REFERENCE", "id=".$_REQUEST['cReference'], $db1);
        $criteria .= $l->l("reference")."=".$refdescription."/";        
        $sql .= " AND (reference =".$_REQUEST['cReference'].")";
        $reference = $_REQUEST['cReference'];
    }
    
    if ($_REQUEST['cArea']!=0) {
        $areadescription = func::vlookup("description", "AREAS", "id=".$_REQUEST['cArea'], $db1);
        $criteria .= $l->l("area")."=".$areadescription."/";        
        $sql .= " AND (area =".$_REQUEST['cArea'].")";
    }
    
    if ($_REQUEST['c_city_id']!=0) {
        $citydescription = func::vlookup("description", "EP_CITIES", "id=".$_REQUEST['c_city_id'], $db1);
        $criteria .= "ΠΟΛΗ=".$citydescription."/";        
        $sql .= " AND (city_id =".$_REQUEST['c_city_id'].")";
    }
    
    if ($_REQUEST['cCompanyType']!=0) {
        $companytype = func::vlookup("description", "COMPANY_TYPES", "id=".$_REQUEST['cCompanyType'], $db1);
        $criteria .= $l->l("company_type")."=".$companytype."/";        
        $sql .= " AND (company_type =".$_REQUEST['cCompanyType'].")";
    }
    
    if ($_REQUEST['cStatus']!=0) {
        $statusdescription = func::vlookup("description", "STATUS2", "id=".$_REQUEST['cStatus'], $db1);
        $criteria .= $l->l("status")." = ".$statusdescription."/";
        $sql .= " AND (status =".$_REQUEST['cStatus'].")";
    }
    
    
    $sqlProductCategory = "";
    if ($_REQUEST['c_companystatus']!=0 || $_REQUEST['c_productcategory']!=0 || $_REQUEST['cUser']!=0 || $_REQUEST['txtRecallDate']!="" || $_REQUEST['t_statusdate1']!="" || $_REQUEST['t_statusdate2']!="") {
        $sqlStatus = "";
        if ($_REQUEST['c_companystatus']!=0) {
            $statusdescription = func::vlookup("description", "STATUS", "id=".$_REQUEST['c_companystatus'], $db1);
            $criteria .= "Status επικ.=".$statusdescription."/";
            $sqlStatus = " AND status =".$_REQUEST['c_companystatus'];
        }
        
        if ($_REQUEST['c_productcategory']!=0) {
            $productcatdescription = func::vlookup("description", "PRODUCT_CATEGORIES", "id=".$_REQUEST['c_productcategory'], $db1);
            $criteria .= "Κατηγ. προϊόντος=".$productcatdescription."/";
            $sqlProductCategory = " AND productcategory =".$_REQUEST['c_productcategory'];
        }
        
        $sqlUser = "";
        if ($_REQUEST['cUser']!=0) {                
            $userfullname = func::vlookup("fullname", "USERS", "id=".$_REQUEST['cUser'], $db1);
            $criteria .= $l->l("user")."=".$userfullname."/";
            $sqlUser = " AND userid =".$_REQUEST['cUser'];
            
        }
        
        $sqlRecall = "";
        if ($_REQUEST['txtRecallDate']!="") {
            $criteria .= $l->l("recall-date")."=".$_REQUEST['txtRecallDate']."/";
            $recalldate = textbox::getDate($_REQUEST['txtRecallDate'], $locale);
            $recalldate1 = substr($recalldate, 0, 8)."000000";
            $recalldate2 = substr($recalldate, 0, 8)."999999";
            $sqlRecall = " AND (CAST(recalldate AS UNSIGNED)>=".$recalldate1.")";
            $sqlRecall .= " AND (CAST(recalldate AS UNSIGNED)<=".$recalldate2.")";
            
        }
        
        $sqlStatusDates = "";
        if ($_REQUEST['t_statusdate1']!="") {            
            $criteria .= "Ημ. status ".$_REQUEST['t_statusdate1']."-".$_REQUEST['t_statusdate2'];
            $statusdate1 = textbox::getDate($_REQUEST['t_statusdate1'], $locale);
            if ($_REQUEST['t_statusdate2']!="") {
                $statusdate2 = textbox::getDate($_REQUEST['t_statusdate2'], $locale);
            }
            else {
                $statusdate2 = textbox::getDate($_REQUEST['t_statusdate1'], $locale);
            }
            $statusdate2 = substr($statusdate2, 0, 8)."999999";
            
            $sqlStatusDates = " AND csdatetime >= '$statusdate1' AND csdatetime <= '$statusdate2' ";
            
            
            
        }
        
        $sql .= " AND (id IN (SELECT companyid FROM COMPANIES_STATUS WHERE id>0 " . $sqlStatus . $sqlProductCategory . $sqlUser . $sqlRecall . $sqlStatusDates . "))";
    
        
        
    }
    
    if ($criteriaBlockAccess!="" && $_REQUEST['c_companystatus']!=3) {
        $sql .= " AND (id NOT IN (SELECT companyid FROM COMPANIES_STATUS WHERE status=4))";
    }
    
    
    
    if ($_REQUEST['txtDeliveryDateFrom']!="") {
        $criteria .= $l->l("delivery-date-from").">=".$_REQUEST['txtDeliveryDateFrom']."/";
        $deliverydatefrom = textbox::getDate($_REQUEST['txtDeliveryDateFrom'], $locale);
        $deliverydatefrom = substr($deliverydatefrom, 0, 8)."000000";
        $sql .= " AND (CAST(DeliveryDate AS UNSIGNED)>=".$deliverydatefrom.")";

    }
    
    if ($_REQUEST['txtDeliveryDateTo']!="") {
        $criteria .= $l->l("delivery-date-to")."<=".$_REQUEST['txtDeliveryDateTo']."/";
        $deliverydateto = textbox::getDate($_REQUEST['txtDeliveryDateTo'], $locale);
        $deliverydateto = substr($deliverydateto, 0, 8)."999999";
        $sql .= " AND (CAST(DeliveryDate AS UNSIGNED)<=".$deliverydateto.")";

    }
    
    //$t_voucherid
    if ($_REQUEST['t_voucherid']!="") {
        $criteria .= $l->l("Voucher")."=".$_REQUEST['t_voucherid']."/"; 
        $str = str_replace("*", "%", $_REQUEST['t_voucherid']);
        $sql .= " AND (voucherid LIKE '".$str."')";

    }
    
    
    if ($_REQUEST['txtExpireDate']!="") {
        $criteria .= $l->l("expire-date")."<=".$_REQUEST['txtExpireDate']."/";
        $expiredate = textbox::getDate($_REQUEST['txtExpireDate'], $locale);
        $expiredate = substr($expiredate, 0, 8)."999999";
        $sql .= " AND (CAST(expires AS UNSIGNED)<=".$expiredate.")";

    }
    
    if ($_REQUEST['t_domain_expires']!="") {
        $criteria .= "Λήξη domain <=".$_REQUEST['t_domain_expires']."/";
        $expiredateDomain = textbox::getDate($_REQUEST['t_domain_expires'], $locale);
        $expiredateDomain = substr($expiredateDomain, 0, 8)."999999";
        $sql .= " AND (CAST(domain_expires AS UNSIGNED)<=".$expiredateDomain.")";

    }
    
    if ($_REQUEST['cOnlineStatus']>=0) {        
        $criteria .= $l->l("ON-LINE")."=".$_REQUEST['cOnlineStatus']."/";
        $sql .= " AND (onlinestatus=".$_REQUEST['cOnlineStatus'].")";
    }
    
    if ($_REQUEST['c_hasdomain']>=0) {        
        $criteria .= "Domain = ".$_REQUEST['c_hasdomain']."/";
        $sql .= " AND (domain = ".$_REQUEST['c_hasdomain'].")";
    }
    
    if ($_REQUEST['c_courier']>0) {
        $courier = $_REQUEST['c_courier'];
        $courierDescr = func::vlookup("description", "COURIER", "id=$courier", $db1);
        $criteria .= "COURIER = $courierDescr / ";
        $sql .= " AND (courier= $courier )";
    } 
    
    if ($_REQUEST['c_hasAfm']>0) {
        if ($_REQUEST['c_hasAfm']=="1") {
            $criteria .= "ΜΕ ΑΦΜ / ";
            $sql .= " AND (afm<> '' AND afm IS NOT NULL)";
        }
        if ($_REQUEST['c_hasAfm']=="2") {
            $criteria .= "ΧΩΡΙΣ ΑΦΜ / ";
            $sql .= " AND (afm LIKE '' OR afm IS NULL)";
        }
    }
    
    $safm = $_REQUEST['s-afm'];
    if ($safm!='') {
        $criteria .= " ΑΦΜ=$safm / ";
            $sql .= " AND (afm LIKE '$safm')";
    }
    
    $seoissues = checkbox::getVal2($_REQUEST, "chk_issues");
    if ($seoissues==1) {
    	$criteria .= " Με εκκρεμότητες SEO / ";
    	$sql .= " AND (profession IS NULL OR profession=0 OR city_id IS NULL OR city_id=0)";
    }
        
    $userExportSql='ExportSql'.$_SESSION['user_id'];
    
    
    
    $sqlID = "SELECT COMPANIES.id " . $sql;
    $sql = "SELECT COMPANIES.id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
            . "status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, "
            . "'' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, "
            . "'' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus " . $sql;
    
    if ($s_sort!="") {
        $sql .= " ORDER BY $s_sort, id";
    }
    elseif ($_REQUEST['c_companystatus'] == 4) {
        $sql .= " ORDER BY lastactiondate, id ";
        $s_sort = "lastactiondate";
    }
    elseif ($_REQUEST['c_companystatus'] == 3) {
        $sql .= " ORDER BY recalldate, recalltime, id ";
        $s_sort = "recalldate, recalltime";
    }
    else {
        $sql .= " ORDER BY id ";
        $s_sort = "id";
    }
    
    
    $_SESSION[$userExportSql]=$sql;      
    
    
}



echo "<!--".$sql."-->";

$fields = "";
$orderBy = "";
$params = NULL;
$curPage = 0; 
if (isset($_GET['page'])) {
    $curPage = $_GET['page'];
}
//$rowsperpage = 30;
$rowsperpage = $s_recordsperpage;

$myURL = "http://".$_SERVER[HTTP_HOST].$_SERVER['REQUEST_URI'];
$pos = strpos($myURL, "&page=");
if ($pos) {
   $link = substr($myURL, 0, $pos)."&page="; 
}
else {
   $link = $_SERVER['REQUEST_URI']."&page=";  
}
$rsPage = new RS_PAGE($db1, $sql, $fields, $orderBy, $rowsperpage, $curPage, $params, $link);
$rs = $rsPage->getRS();

//print_r($rs);

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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 800 });	
});

$(document).ready(function() {	
	$("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });	
});

</script>

<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
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
            /*sortList: [[3,0],[4,0]],*/
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
    
    
    $(function() {
        
        $("#col_recalldate2").trigger("click");
        $("#col_recalltime2").trigger("click");
        //col_recalltime2
        
    });


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
    
    #t_statusdate1, #t_statusdate2 {
        width:40%;
    }
    
    .paginate {
        padding:5px;
        background-color: rgb(220,220,220);
        margin:3px;
    }
    .paginate-current {
        padding:5px;
        background-color: rgb(100,150,200);
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

                <form action="home.php?search=1" method="GET">
                    <div class="col-6 col-md-12">
                        <div class="col-4"><?php echo $l->l("id") ?></div>
                        <div class="col-8"><input type="text" name="s-id" value="<?php echo $s_id; ?>" placeholder="" /></div>

                        <div class="col-4"><?php echo $l->l("catalogue-id") ?></div>
                        <div class="col-8"><input type="text" name="s-catalogue-id" value="<?php echo $s_catalogueid; ?>" placeholder="online κατάλογος" /></div>

<!--                        <div class="col-4"><?php echo $l->l("company-name") ?></div>
                        <div class="col-8"><input type="text" name="s-company-name" value="" placeholder="π.χ. tech*" /></div>-->
                        
                        <?php
                            $ac_companyName = new autocomplete("s-company-name", "COMPANIES", $s_companyname, $db1);
                            $ac_companyName->set_label("Επιλογή εταιρείας");
                            $ac_companyName->set_descr_field("companyname");
                            $ac_companyName->set_hiddenid("h-company-name");
                            $ac_companyName->getAutocomplete();
                        ?>
                        
                        <div class="col-4"><?php echo $l->l("phone") ?></div>
                        <div class="col-8"><input type="text" name="s-phone" value="<?php echo $s_phone; ?>" placeholder="π.χ. 2107517" /></div>
                        
                        <div class="col-4">Email</div>
                        <div class="col-8"><input type="text" name="s-email" value="<?php echo $s_email; ?>" placeholder="" /></div>

                        <?php
                        $cReference = new comboBox("cReference", $db1, 
                                "SELECT id, description FROM REFERENCE", 
                                "id","description", $s_reference, $l->l("reference"));
                        $cReference->get_comboBox();                        
                        ?>
                            
                            
                        <?php
//                        $cCategory = new comboBox("cCategory", $db1, "SELECT id, CONCAT((CASE parentid WHEN 0 THEN '+' ELSE '-' END),description) AS MyDescr FROM CATEGORIES ORDER BY parentid, description", 
//                                "id","MyDescr",0,$l->l("category"));
//                        $cCategory->get_comboBox();
                        
                        $ac_category = new autocomplete("s-category", "CATEGORIES", $s_category, $db1);
                        $ac_category->set_label("Επιλ. κατηγορίας");
                        $ac_category->set_hiddenid("cCategory");
                        $ac_category->getAutocomplete();
                        
                        
                        ?>

                        

                        <?php
                        $cArea = new comboBox("cArea", $db1, "SELECT id, description FROM AREAS", 
                                "id","description", $s_area, $l->l("area"));
                        $cArea->get_comboBox();                        
                        ?>
                        
                        <?php
                        $c_city_id = new comboBox("c_city_id", $db1, "SELECT id, description FROM EP_CITIES", 
                                "id","description", $s_city,"ΠΟΛΗ");
                        $c_city_id->get_comboBox();                        
                        ?>

                        <?php
                        $cCompanyType = new comboBox("cCompanyType", $db1, "SELECT id, description FROM COMPANY_TYPES", 
                                "id","description", $s_companytype, $l->l("company_type"));
                        $cCompanyType->get_comboBox();                        
                        ?>
                        
<!--                        <div class="col-4">ΜΕ ΑΦΜ</div>
                        <div class="col-8">
                            <select name="c_hasAfm">
                                <option value="0">...</option>
                                <option value="1">ΝΑΙ</option>
                                <option value="2">ΟΧΙ</option>
                            </select>
                        </div>-->
                        
                        <?php
                        $c_hasafm = new comboBox("c_hasAfm", $db1, "", "id",
                                "description", $s_hasAfm);
                        $myrs = array();
                        $myrs[0] = array('id' => '0', 'description'=> '...');
                        $myrs[1] = array('id' => '1', 'description'=> 'ΝΑΙ');
                        $myrs[2] = array('id' => '2', 'description'=> 'ΟΧΙ');
                        $c_hasafm->set_rs($myrs);
                        $c_hasafm->get_comboBox();
                        
                        ?>
                        
                        <div class="col-4">ΑΦΜ</div>
                        <div class="col-8"><input type="text" name="s-afm" value="<?php echo $s_afm; ?>" placeholder="000000000" /></div>
                        
                        <?php
                        
                        $cStatus = new comboBox("cStatus", $db1, "SELECT id, description FROM STATUS2", 
                                "id","description", $s_status, "Status εταιρείας");
                        $cStatus->get_comboBox();
                        
                        ?>



                    </div>

                    <div class="col-6 col-md-12">

                        



                        <?php
                                                
                        $c_companystatus = new comboBox("c_companystatus", $db1, "SELECT * FROM STATUS WHERE active=1", "id","description", $s_commstatus, "Status επικοιν.");
                        $c_companystatus->get_comboBox();
                        
                        $c_productcategory = new comboBox("c_productcategory", $db1, "SELECT id, description FROM PRODUCT_CATEGORIES", "id","description", $s_productcategory, "Κατηγ. προϊόντος");
                        $c_productcategory->get_comboBox();
                        
                        //t_statusdate1
                        $t_statusdate1 = new textbox("t_statusdate1", "Ημερ. status 1", $s_statusdate1);
                        $t_statusdate1->set_format("DATE");
                        $t_statusdate1->set_locale($locale);                        
                        echo "<div class=\"col-4\">Ημερ. επικοιν.</div><div class=\"col-8\">";
                        echo $t_statusdate1->textboxSimple();
                        $t_statusdate2 = new textbox("t_statusdate2", "Ημερ. status 2", $s_statusdate2);
                        $t_statusdate2->set_format("DATE");
                        $t_statusdate2->set_locale($locale);
                        echo " - ";
                        echo $t_statusdate2->textboxSimple();
                        echo "</div><div class=\"clear\"></div>";
                        ?>
                        
                        


                        <?php
                        $cUser = new comboBox("cUser", $db1, "SELECT id, fullname FROM USERS", 
                                "id","fullname", $s_user, $l->l("user"));
                        $cUser->get_comboBox();                        
                        ?>



                        <?php
                        $txtRecallDate = new textbox("txtRecallDate", $l->l("Recall-date"), $s_recalldate);
                        $txtRecallDate->set_format("DATE");
                        $txtRecallDate->set_locale($locale);                        
                        $txtRecallDate->get_Textbox();
                        ?>
                        
<!--                        <div class="col-4">On-Line</div>
                        <div class="col-8">
                            <select name="cOnlineStatus">
                                <option value="-1"></option>
                                <option value="1">ΝΑΙ</option>
                                <option value="0">ΟΧΙ</option>
                            </select>
                        </div>-->
                        
                        <?php
                        $cOnlineStatus = new comboBox("cOnlineStatus", $db1, "", "id",
                                "description", $s_OnlineStatus, "On-Line");
                        $myrs = array();
                        $myrs[0] = array('id' => '-1', 'description'=> '...');
                        $myrs[1] = array('id' => '1', 'description'=> 'ΝΑΙ');
                        $myrs[2] = array('id' => '0', 'description'=> 'ΟΧΙ');
                        $cOnlineStatus->set_zerochoice(FALSE);
                        $cOnlineStatus->set_rs($myrs);
                        $cOnlineStatus->get_comboBox();
                        
                        $c_hasdomain = new comboBox("c_hasdomain", $db1, "", "id",
                                "description", $s_hasdomain, "Έχει Domain");                        
                        $c_hasdomain->set_zerochoice(FALSE);
                        $c_hasdomain->set_rs($myrs);
                        $c_hasdomain->get_comboBox();
                        
                        ?>
                        
                        <?php
                        
                        $txtDeliveryDateFrom = new textbox("txtDeliveryDateFrom", 
                                $l->l("Delivery-from"), $s_deliveryDateFrom);
                        $txtDeliveryDateFrom->set_format("DATE");
                        $txtDeliveryDateFrom->set_locale($locale);                        
                        $txtDeliveryDateFrom->get_Textbox();
                        ?>
                        
                        <?php
                        $txtDeliveryDateTo = new textbox("txtDeliveryDateTo", 
                                $l->l("Delivery-to"), $s_deliveryDateTo);
                        $txtDeliveryDateTo->set_format("DATE");
                        $txtDeliveryDateTo->set_locale($locale);                        
                        $txtDeliveryDateTo->get_Textbox();
                        
                        
                        //voucherid
                        $t_voucherid = new textbox("t_voucherid", $l->l('Voucher'), 
                                $s_voucherid);                        
                        $t_voucherid->get_Textbox();
                        
                        
                        ?>
                        
                        <?php
                        
                        $txtExpireDate = new textbox("txtExpireDate", "Ημερ. λήξης KX", 
                                $s_ExpireDate);
                        $txtExpireDate->set_format("DATE");
                        $txtExpireDate->set_locale($locale);                        
                        $txtExpireDate->get_Textbox();
                        
                        
                        $t_domain_expires = new textbox("t_domain_expires", 
                                "Ημερ. λήξης DM", $s_domain_expires);
                        $t_domain_expires->set_format("DATE");
                        $t_domain_expires->set_locale($locale);                        
                        $t_domain_expires->get_Textbox();
                        
                        
                        //COURIER
                        $c_courier = new comboBox("c_courier", $db1, 
                                "SELECT id, description FROM COURIER WHERE active=1", 
                                "id","description", $s_courier, "Courier");                                   
                        $c_courier->get_comboBox();
                        
                        $chk_issues = new checkbox("chk_issues", "Με εκκρεμότητες SEO", 
                                $s_seoissues);
                        echo "<div class=\"col-4\">Με εκκρεμότητες SEO</div><div class=\"col-8\"><div class=\"col-2\">";
                        echo $chk_issues->checkboxSimple();
                        
                        if ($_SESSION['user_profile']==3) {
                            $chk_inactive = new checkbox("chk_inactive", "Μόνο ανενεργοί", $s_inactive);
                            echo "</div><div class=\"col-8\">Μόνο ανενεργοί</div><div class=\"col-2\">";
                            echo $chk_inactive->checkboxSimple();
                        }
                        echo "</div></div>";
                        
                        ?>
                        
                        


                    </div>

                    <div style="clear: both"></div>
                    
                    Ταξινόμηση &nbsp; 

                    
                    <?php
                    $c_sort = new comboBox("c_sort", $db1, "", "id",
                            "description", $s_sort);
                    $myrs = array();
                    $myrs[0] = array('id' => '', 'description'=> '...');
                    $myrs[1] = array('id' => 'id', 'description'=> 'ID');
                    $myrs[2] = array('id' => 'TRIM(companyname)', 'description'=> 'Επωνυμία');
                    $myrs[3] = array('id' => 'recalldate, recalltime', 'description'=> 'Ημερ/ώρα Recall');
                    $myrs[4] = array('id' => 'lastactiondate', 'description'=> 'Τελ. ημερ. επικοιν');
                    $c_sort->set_rs($myrs);
                    $c_sort->set_zerochoice(FALSE);
                    echo $c_sort->comboBox_simple();

                    ?>

                    <input name="BtnSearch" type="submit" value="SEARCH" />
                    <input type="reset" value="<?php echo $lg->l("reset"); ?>" />
                    <?php if ($_SESSION['user_profile']>1) { ?>
                    Records / page: <input style="width:100px" type="text" name="t_recordsperpage" value="<?php echo $s_recordsperpage; ?>" />
                    <?php } ?>
                    
                    
                    <?php
                    if ($_SESSION['user_profile']>0) {
                        echo <<<EOT
                        <br/><a href="home.php?recalls=1">
                        <input type="button" value="RECALLS" />
                        </a>
                        &nbsp;
                        <a href="home.php?newcalls=1">
                        <input type="button" value="NEW CALLS" />
                        </a>
                        &nbsp;
                        <a href="pendingVouchers.php?user=$userid">
                        <input type="button" value="PENDING VOUCHERS" />
                        </a>
EOT;
                    }
                    ?>
                    
                    

                </form>
            </div>

            <?php if ($sql!="") { ?>
            <h2 class="search-results"><?php echo $lg->l("search-results")." [".$lg->l("criteria")." :: ".$criteria."]"; ?></h2>



            <?php
                    
                    
                include('_home_rsproc.php');
                
                if ($_REQUEST['c_companystatus']==4) { //arnitikos
                    for ($i=0;$i<count($rs);$i++) {
                        $rs[$i]['recalldate2'] = $rs[$i]['lastactiondate'];
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
                $gridCompanies->col_vlookupRS("status","status",$rsStatus2,"shortdescription");  
                //$gridCompanies->col_vlookupRS("area","area",$rsAreas, "description"); 
                //$gridCompanies->col_vlookupRS("basiccategory","basiccategory",$rsCategories,"description");
                $gridCompanies->get_datagrid();

                echo "<div class=\"spacer-10\"></div>";

                echo "<div style=\"padding:10px\">";                    
                $rsPage->getFirst();
                $rsPage->getPrev();
                $rsPage->getPageLinks();
                $rsPage->getNext();
                $rsPage->getLast();
                echo " ".$rsPage->getCount()." records ";
                echo "</div>";

                if ($_SESSION["user_profile"]==3) {
                    $sql = str_replace("SELECT", "SLCT", $sql);
                    echo "<br/>&nbsp;&nbsp;&nbsp;<a class=\"button\" href=\"getExcel.php?filename=home&sql=$sql\">Export to Excel</a>";
                }


            }        

            ?>


        </div>
        
        <div class="col-3">
            <div style="position:fixed; right:0px; top:120px; width:24%; height: 450px;">
                <div style="padding: 0px 10px 20px 15px; border-left: 1px solid rgb(200,200,200); margin-left: 15px;">
<!--                    <iframe src="messages.php?popupurl=editcompany" width="100%" height="550" frameborder="0"></iframe>-->



                </div>
            </div>
            
        </div>
        
        
        <div style="clear: both"></div>
        
        
    </div>
    
    <div style="clear: both"></div>
    
    
    <?php include "blocks/footer.php"; ?>   
    
    
</body>
</html>

