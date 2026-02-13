<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

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

if (isset($_GET['search']) && $_GET['search']==1) {
    $sql = "SELECT id, companyname, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, "
            . "status, user, recalldate, recalltime, area, basiccategory, subcategory FROM COMPANIES WHERE (id>0) ";
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
        $str = $str . "%";
        //$sql .= " AND (digits(phone1) LIKE '".$str."' OR digits(phone2) LIKE '".$str."' OR digits(mobilephone) LIKE '".$str."') "; 
        //$sql .= " AND (digits(phone1) LIKE '".$str."')";
        //$sql .= " AND (phone1 LIKE '".$str."')";
        $sql .= " AND (phone1digits LIKE '".$str."' OR phone2digits LIKE '".$str."' "
                . "OR mobiledigits LIKE '".$str."' OR faxdigits LIKE '".$str."') "; 
    }
    
    if ($_POST['cCategory']!=0) {
        $categorydescription = func::vlookup("description", "CATEGORIES", "id=".$_POST['cCategory'], $db1);
        $criteria .= $l->l("category")."=".$categorydescription."/";        
        $sql .= " AND (basiccategory =".$_POST['cCategory']." OR subcategory = ".$_POST['cCategory'].")";
    }
    
    if ($_POST['cReference']!=0) {
        $refIds = "";
        $refs = $_POST['cReference'];
        foreach ($refs as $ref) {
            $refIds = func::ConcatSpecial($refIds, $ref, ",");
            $refdescription = func::vlookup("description", "REFERENCE", "id=$ref", $db1);
            $refdescriptions = func::ConcatSpecial($refdescriptions, $refdescription, ",");
        }
        if ($refIds == "") {
            $refIds = "0";
        }
        
        //$refdescription = func::vlookup("description", "REFERENCE", "id=".$_POST['cReference'], $db1);
        //$criteria .= $l->l("reference")."=".$refdescription."/";        
        //$sql .= " AND (reference =".$_POST['cReference'].")";
        
        $criteria .= $l->l("reference")."=".$refdescriptions."/";
        $sql .= " AND (reference IN ($refIds))";
        //$reference = $_POST['cReference'];
    }
    
    if ($_POST['cArea']!=0) {
        $areadescription = func::vlookup("description", "AREAS", "id=".$_POST['cArea'], $db1);
        $criteria .= $l->l("area")."=".$areadescription."/";        
        $sql .= " AND (area =".$_POST['cArea'].")";
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
    
    if ($_POST['cUser']!=0) {                
        $userfullname = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
        $criteria .= $l->l("user")."=".$userfullname."/";
        $sql .= " AND user=".$_POST['cUser'];
    }
    
    if ($_POST['txtRecallDate']!="") {
        $criteria .= $l->l("recall-date")."=".$_POST['txtRecallDate']."/";
        $recalldate = textbox::getDate($_POST['txtRecallDate'], $locale);
        $recalldate1 = substr($recalldate, 0, 8)."000000";
        $recalldate2 = substr($recalldate, 0, 8)."999999";
        $sql .= " AND (CAST(recalldate AS UNSIGNED)>=".$recalldate1.")";
        $sql .= " AND (CAST(recalldate AS UNSIGNED)<=".$recalldate2.")";
        $sql .= " ORDER BY recalldate, recalltime ";
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
        $sql .= " AND (expires<>'' AND CAST(expires AS UNSIGNED)<=".$expiredate.")";

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
    
    
    
//    elseif ($_POST['cStatus']!=0) {
//        $statusdescription = func::vlookup("description", "STATUS", "id=".$_POST['cStatus'], $db1);
//        switch ($_POST['cStatus']) {
//            /*case 2: //desmevmeno apo xristi
//                $userfullname = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
//                $criteria = $l->l("status")."=".$statusdescription." + ".
//                    $l->l("user")."=".$userfullname;
//                $sql .= "status =".$_POST['cStatus']." AND user=".$_POST['cUser'];
//                break;*/
//            case 3: //recall
//                if ($_POST['txtSDate']!="") {
//                    $criteria = $l->l("status")."=".$statusdescription." + ".$l->l("recall-date")."<=".$_POST['txtSDate'];
//                    $recalldate = textbox::getDate($_POST['txtSDate'], $locale);
//                    $recalldate = substr($recalldate, 0, 8)."999999";
//                    //echo $recalldate;
//                    $sql .= "status =".$_POST['cStatus']." AND CAST(recalldate AS UNSIGNED)<=".$recalldate;
//                    $sql .= " ORDER BY recalldate, recalltime ";
//                }
//                elseif ($_POST['cUser']!=0) {                
//                    $userfullname = func::vlookup("fullname", "USERS", "id=".$_POST['cUser'], $db1);
//                    $criteria = $l->l("status")."=".$statusdescription." + ".
//                        $l->l("user")."=".$userfullname;
//                    $sql .= "status =".$_POST['cStatus']." AND user=".$_POST['cUser'];
//                }
//                else {
//                    $criteria = $l->l("status")."=".$statusdescription;
//                    $sql .= "status =".$_POST['cStatus'];
//                }
//                //echo $sql;
//                break;
//            case 10: //kataxorimenos
//                $criteria = $l->l("status")."=".$statusdescription." + ".$l->l("expires")."<=".$_POST['txtSDate'];
//                $expdate = textbox::getDate($_POST['txtSDate'], $locale);
//                $expdate = substr($expdate, 0, 8)."999999";
//                //echo $recalldate;
//                $sql .= "status =".$_POST['cStatus']." AND CAST(expires AS UNSIGNED)<=".$expdate;
//                $sql .= " ORDER BY expires ";
//                //echo $sql;
//                break;
//            
//            case 6: //ektipothike / gia apostoli
//                $criteria = $l->l("status")."=".$statusdescription." + ".$l->l("for-courier")."<=".$_POST['txtSDate'];
//                $deliverydate = textbox::getDate($_POST['txtSDate'], $locale);
//                $deliverydate = substr($deliverydate, 0, 8)."999999";
//                //echo $recalldate;
//                $sql .= "status =".$_POST['cStatus']." AND CAST(DeliveryDate AS UNSIGNED)<=".$deliverydate;
//                $sql .= " ORDER BY DeliveryDate ";
//                //echo $sql;
//                break;
//                                 
//            
//            default :
//                $criteria = $l->l("status")."=".$statusdescription;
//                $sql .= "(status =".$_POST['cStatus'].") ";
//                if ($_POST['s-company-name']!="") {
//                    $criteria .= $l->l("companyname")."=".$_POST['s-company-name'];
//                    $str = str_replace("*", "%", $_POST['s-company-name']);
//                    $sql .= " AND (companyname LIKE '".$str."')"; 
//                }
//                break;
//            
//        }
//                
//    }
    //echo $sql;
    
    $userExportSql='ExportSql'.$_SESSION['user_id'];
    $_SESSION[$userExportSql]=$sql;   
    
    
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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
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
            headers: { 
                7: { 
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
        max-width: 925px;
        min-height: 0px;
    }
    
    #gridCompanies {
        max-width: 900px;
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
    
</style>


<script src="js/jquery.sumoselect.js"></script>
<link href="css/sumoselect.css" rel="stylesheet" type="text/css" />

<script>

$(function() {
    $('#cReference').SumoSelect({ okCancelInMulti: true, selectAll: true });
});



</script>

<style>
    
    #cReference {
        margin-bottom: 10px;
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

                        <?php
                        $cReference = new comboBox("cReference[]", $db1, 
                                "SELECT id, description FROM REFERENCE", 
                                "id","description",$reference,$l->l("reference"));
                        $cReference->set_extraAttr("multiple=\"multiple\"");
                        $cReference->get_comboBox();  
                        
                        
                        
                        ?>
                        
                        <div style="clear: both; height: 10px"></div>
                            
                            
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


                    </div>

                    <div class="col-6 col-md-12">

                        <?php
                        $cStatus = new comboBox("cStatus", $db1, "SELECT id, description FROM STATUS", 
                                "id","description",0,$l->l("status"));
                        $cStatus->get_comboBox();
                                               
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
                        $txtExpireDate = new textbox("txtExpireDate", $l->l("expires"), "");
                        $txtExpireDate->set_format("DATE");
                        $txtExpireDate->set_locale($locale);                        
                        $txtExpireDate->get_Textbox();
                        
                        
                        //COURIER
                        $c_courier = new comboBox("c_courier", $db1, 
                                "SELECT id, description FROM COURIER WHERE active=1", 
                                "id","description",0,"Courier");                                   
                        $c_courier->get_comboBox();
                        
                        
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
            
                    $gridCompanies = new datagrid("gridCompanies", $db1, 
                            $sql, 
                            array("id","companyname","phones","area","basiccategory",
                                "status","user","recalldate","recalltime"), 
                            array($l->l("id"),$l->l("companyname"),$l->l("phones"),
                                "Περιοχή","Κατηγορία",$l->l("status"),"USER","RDATE","RTIME"),
                            $ltoken, 0, 
                            TRUE,"editcompany.php",$lg->l("open")
                            );
                    $gridCompanies->set_select($l->l("select"));
                    $gridCompanies->set_colWidths(array("50","400","200","150","150","150","100","100","100","50","50"));
                    $gridCompanies->col_vlookup("status","status","STATUS","description", $db1);  
                    $gridCompanies->col_vlookup("user","user","USERS","fullname", $db1);
                    $gridCompanies->col_vlookup("recalltime","recalltime","TIMES","description", $db1);
                    $gridCompanies->col_vlookup("area","area","AREAS","description", $db1); 
                    $gridCompanies->col_vlookup("basiccategory","basiccategory","CATEGORIES","description", $db1);
                    $gridCompanies->set_colsFormat(array('','','','','','','','DATE',''));
                    
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

