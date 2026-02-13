<?php

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COURIER",$lang,$db1);

$userid = $_SESSION['user_id'];
$criteria="";

$pending = 0;
$paradothike_courier = 0;
$epistrofi_courier = 0;

$delivered = 0;
$returned = 0;
$stay = 0;
$attiki = 0;
$eparxia = 0;
$date1 = "";
$date2 = "";


if ($_POST) {
   // $sql="SELECT DISTINCT id AS voucherid, customer AS id, vcode, customer, '' AS companyname, '' AS area, deliverydate, amount AS price, courier_delivery_date, courier_status, customer AS ID2 FROM VOUCHERS INNER JOIN (SELECT companyid FROM COMPANIES_STATUS WHERE status IN (6,7)) CS ON VOUCHERS.customer = CS.companyid WHERE 1=1 ";
    $sql="SELECT DISTINCT id AS voucherid, customer AS id, vcode, vcode2, vcode3, customer, '' AS companyname, '' AS area, deliverydate, amount AS price, courier_delivery_date, courier_status, customer AS ID2, courier_notes FROM VOUCHERS  WHERE 1=1 ";
    if (($_SESSION['user_profile']==4)) {
        $courierId = func::vlookup("id", "COURIER", "user=$userid", $db1);
        $sql .= " AND (courier=$courierId) ";
    }
    
    $pending = checkbox::getVal2($_POST, "chk_pending");
    $paradothike_courier = checkbox::getVal2($_POST, "chk_paradothike_courier");
    $epistrofi_courier = checkbox::getVal2($_POST, "chk_epistrofi_courier");
    
    $delivered = checkbox::getVal2($_POST, "chk_paradothike");
    $returned = checkbox::getVal2($_POST, "chk_epistrofi");
    $stay = checkbox::getVal2($_POST, "chk_paramoni");
    $attiki = checkbox::getVal2($_POST, "chk_attiki");
    $eparxia = checkbox::getVal2($_POST, "chk_eparxia");
    $date1 = textbox::getDate($_POST['t_DeliveryDate'], $locale);
    //echo $date1 . "<br/>";
    $date2 = textbox::getDate($_POST['t_DeliveryDateTo'], $locale);
    //echo $date2 . "<br/>";
    
}


if (isset($_POST['t_id']) && $_POST['t_id']!="") {
    $sql .= " AND (customer = ".$_POST['t_id'].") ";
    $criteria .="CUSTOMER ID = ".$_POST['t_id']." / ";
}

if (isset($_POST['t_Voucher']) && $_POST['t_Voucher']!="") {
    //$searchVoucher = str_replace("*", "%", $_POST['t_Voucher']);
    $searchVoucher = $_POST['t_Voucher'];
    
    //$sql .= " AND (id = $searchVoucher OR vcode LIKE '%".$searchVoucher."%' OR vcode2 LIKE '%".$searchVoucher."%' OR vcode3 LIKE '%$searchVoucher%') ";

    $sql .= " AND (id = $searchVoucher OR vcode LIKE '$searchVoucher' OR vcode2 LIKE '$searchVoucher' OR vcode3 LIKE '$searchVoucher') ";
    
    $criteria .="VOUCHER = ".$_POST['t_Voucher']." / ";
}

if (isset($_POST['t_DeliveryDate']) && $_POST['t_DeliveryDate']!="") {
    $deliveryDate = textbox::getDate($_POST['t_DeliveryDate'], $locale);
    $deliveryDateTo = textbox::getDate($_POST['t_DeliveryDateTo'], $locale);
    if ($deliveryDateTo=='') {
        $sql .= " AND (deliverydate = '".$deliveryDate."') ";
        $criteria .="ΠΑΡΑΔΟΣΗ = ".$_POST['t_DeliveryDate']." / ";
    }
    else {
        $sql .= " AND (deliverydate >= '".$deliveryDate."' AND deliverydate<='".$deliveryDateTo."') ";
        $criteria .="ΠΑΡΑΔΟΣΗ = ".$_POST['t_DeliveryDate']."-".$_POST['t_DeliveryDateTo']." / ";     
    }
}

if (isset($_POST['chk_attiki']) && $_POST['chk_attiki']=="1" && $_POST['chk_eparxia']!="1") {
    $sql .= " AND (customer IN (SELECT id FROM COMPANIES WHERE area = 1120)) ";
    $criteria .="ΠΕΡΙΟΧΗ = ΑΤΤΙΚΗ / ";
}

if (isset($_POST['chk_eparxia']) && $_POST['chk_eparxia']=="1" && $_POST['chk_attiki']!="1") {
    $sql .= " AND (customer IN (SELECT id FROM COMPANIES WHERE area <> 1120)) ";
    $criteria .="ΠΕΡΙΟΧΗ = ΕΠΑΡΧΙΑ / ";
}

if (isset($_POST['c_courier']) && $_POST['c_courier']>0) {
    $courier = $_POST['c_courier'];
    $courierDescr = func::vlookup("description", "COURIER", "id=$courier", $db1);
    $criteria .= "COURIER = $courierDescr / ";
    $sql .= " AND (courier= $courier )";
} 

$sqlstatus = "";



if (isset($_POST['chk_pending']) && $_POST['chk_pending']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status IN (1))", " OR ");
    $criteria .="ΣΕ ΕΚΚΡΕΜΟΤΗΤΑ / ";
}
if (isset($_POST['chk_paradothike_courier']) && $_POST['chk_paradothike_courier']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status IN (5))", " OR ");
    $criteria .="ΠΑΡΑΔΟΘΗΚΕ (COURIER) / ";
}
if (isset($_POST['chk_epistrofi_courier']) && $_POST['chk_epistrofi_courier']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status IN (6))", " OR ");
    $criteria .="ΕΠΙΣΤΡΟΦΗ  (COURIER) / ";
}

if (isset($_POST['chk_paradothike']) && $_POST['chk_paradothike']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status=2)", " OR ");
    $criteria .="ΠΑΡΑΔΟΘΗΚΕ / ";
}
if (isset($_POST['chk_epistrofi']) && $_POST['chk_epistrofi']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status=3)", " OR ");
    $criteria .="ΕΠΙΣΤΡΟΦΗ / ";
}
if (isset($_POST['chk_paramoni']) && $_POST['chk_paramoni']=="1") {
    $sqlstatus = func::ConcatSpecial($sqlstatus, "(courier_status=4)", " OR ");
    $criteria .="ΠΑΡΑΜΟΝΗ / ";
}

if ($sqlstatus!="") {
    $sql .= " AND (".$sqlstatus.") ";
}

if ($sql!="") {
    $sql .= " ORDER BY deliverydate, customer ";
    //echo $sql;
    $myRS = $db1->getRS($sql);
    echo "<!--";
    for ($i = 0; $i < count($myRS); $i++) {
        echo $myRS[$i]['id'].",";
    }
    echo "-->";
}



/*
if (isset($_GET['deliverednotpayed']) && $_GET['deliverednotpayed']==1) {
    $sql = "SELECT DISTINCT VOUCHERS.id AS voucherid, customer AS id, vcode, customer, '' AS companyname, '' AS area, deliverydate, VOUCHERS.amount AS price, courier_delivery_date, courier_status, customer AS ID2, courier_notes FROM VOUCHERS INNER JOIN TRANSACTIONS
ON VOUCHERS.transactionids = TRANSACTIONS.id
WHERE VOUCHERS.`courier_status` IN (2,5) AND TRANSACTIONS.status = 1 ";
    $myRS = $db1->getRS($sql);
    
}
//echo $sql;

$rs = $db1->getRS($sql);
if ($rs) {
    $companyids = "";
    for ($i = 0; $i < count($rs); $i++) {
       $companyids .= $rs[$i]['customer'].",";
    }
    $companyids = substr($companyids,0,strlen($companyids)-1);
    //echo $companyids;
    $rsCompanies = $db1->getRS("SELECT id, companyname, area, courier_status FROM COMPANIES WHERE id IN ($companyids)");
    $rsAreas = $db1->getRS("SELECT * FROM AREAS");
    //$rsCourierStatus = $db1->getRS("SELECT * FROM COURIER_STATUS");
    for ($i = 0; $i < count($rs); $i++) {
        $rs[$i]['companyname'] = func::vlookupRS("companyname", $rsCompanies, $rs[$i]['customer']);
        $rs[$i]['area'] = func::vlookupRS("area", $rsCompanies, $rs[$i]['customer']);
        //$rs[$i]['area'] = func::vlookupRS("description", $rsAreas, $areaId);
        //$rs[$i]['courier_status'] = func::vlookupRS("description", $rsCourierStatus, $rs[$i]['courier_status']);
    }
}
*/

$courier2 = 0;
if (isset($_GET['deliverednotpayed']) && $_GET['deliverednotpayed']==1) {
    $sql = "SELECT DISTINCT VOUCHERS.id AS voucherid, customer AS id, vcode, customer, '' AS companyname, '' AS area, deliverydate, VOUCHERS.amount AS price, courier_delivery_date, courier_status, customer AS ID2, courier_notes FROM VOUCHERS INNER JOIN TRANSACTIONS
ON VOUCHERS.transactionids = TRANSACTIONS.id
WHERE VOUCHERS.`courier_status` IN (2,5) AND TRANSACTIONS.status = 1 ";

    $courier2 = $_POST['c_courier2'];
    $courierDescr2 = func::vlookup("description", "COURIER", "id=$courier2", $db1);
    $criteria .= "COURIER = $courierDescr2 / ";
    if ($courier2>0) {
        $sql .= " AND (courier= $courier2 )";
    }
    //echo $sql;

    $myRS = $db1->getRS($sql);
    
}

$rs = $db1->getRS($sql);
if ($rs) {
    $companyids = "";
    for ($i = 0; $i < count($rs); $i++) {
       $companyids .= $rs[$i]['customer'].",";
    }
    $companyids = substr($companyids,0,strlen($companyids)-1);
    //echo $companyids;
    $rsCompanies = $db1->getRS("SELECT id, companyname, area, courier_status FROM COMPANIES WHERE id IN ($companyids)");
    $rsAreas = $db1->getRS("SELECT * FROM AREAS");
    //$rsCourierStatus = $db1->getRS("SELECT * FROM COURIER_STATUS");
    for ($i = 0; $i < count($rs); $i++) {
        $rs[$i]['companyname'] = func::vlookupRS("companyname", $rsCompanies, $rs[$i]['customer']);
        $rs[$i]['area'] = func::vlookupRS("area", $rsCompanies, $rs[$i]['customer']);
        //$rs[$i]['area'] = func::vlookupRS("description", $rsAreas, $areaId);
        //$rs[$i]['courier_status'] = func::vlookupRS("description", $rsCourierStatus, $rs[$i]['courier_status']);
    }
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
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
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 550 });	
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
        //$('#gridCourier').tablesorter();
        
        $("#gridCourier").tablesorter({ 
            headers: { 
                4: { 
                    sorter:'dates' 
                } 
            } 
        });
        
        
        });
        
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
        max-width: 875px;
        min-height: 0px;
    }
    
    #gridCourier {
        width:90%;
        max-width: 1400px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
    form {
/*        margin: 0px;
        margin-bottom: 1em;*/
    }
    
    #gridCourier th {
        cursor:pointer;
    }
    
    #gridCourier tr td:nth-child(13) { text-align: center;}
    
</style>


</head>

<body>
    
    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?>
    
    <div class="main">
        
        <div class="col-8 col-md-12">
            <h2 style="margin-left:1em"><?php echo $l->l("Courier"); ?></h2>
            
            
            
            <form name="form-search" action="courier.php" method="POST">
                
                <div class="col-6">
                    
                    <div class="col-4">ID πελάτη</div>
                    <div class="col-8"><input type="text" name="t_id" value="" placeholder="" /></div>
					
                    <div class="col-4">Voucher (ID/Code/Code2/Code3)</div>
                    <div class="col-8"><input type="text" name="t_Voucher" value="" placeholder="" /></div>
					
                    <?php
                    //echo $dateFrom;
                    $t_DeliveryDate = new textbox("t_DeliveryDate", $l->l("Ημερ. Παράδ. από"), "$date1");
                    $t_DeliveryDate->set_format("DATE");
                    $t_DeliveryDate->set_locale($locale);                        
                    $t_DeliveryDate->get_Textbox();
                    
                    $t_DeliveryDateTo = new textbox("t_DeliveryDateTo", $l->l("Ημερ. Παράδ. έως"), "$date2");
                    $t_DeliveryDateTo->set_format("DATE");
                    $t_DeliveryDateTo->set_locale($locale);                        
                    $t_DeliveryDateTo->get_Textbox();
                    
                    $chk_attiki = new checkbox("chk_attiki", $l->l("Αττική"), $attiki);
                    $chk_attiki->get_Checkbox();

                    $chk_eparxia = new checkbox("chk_eparxia", $l->l("Επαρχία"), $eparxia);
                    $chk_eparxia->get_Checkbox();
                
                    ?>
                    <div style="clear: both"></div>
                </div>
                    
                
                <div class="col-6" style="padding-left: 20px;">
                
                    <?php
					
                    $chk_pending = new checkbox("chk_pending", $l->l("Σε εκκρεμότητα"), $pending);
                    $chk_pending->get_Checkbox();
                    
                    $chk_paradothike_courier = new checkbox("chk_paradothike_courier", $l->l("Παρδόθηκε (courier)"), $paradothike_courier);
                    $chk_paradothike_courier->get_Checkbox();
                    
                    $chk_epistrofi_courier = new checkbox("chk_epistrofi_courier", $l->l("Επιστροφή (courier)"), $epistrofi_courier);
                    $chk_epistrofi_courier->get_Checkbox();
					
                    $chk_paradothike = new checkbox("chk_paradothike", $l->l("Παραδόθηκε"), $delivered);
                    $chk_paradothike->get_Checkbox();

                    $chk_epistrofi = new checkbox("chk_epistrofi", $l->l("Επιστροφή"), $returned);
                    $chk_epistrofi->get_Checkbox();

                    $chk_paramoni = new checkbox("chk_paramoni", $l->l("Παραμονή"), $stay);
                    $chk_paramoni->get_Checkbox();
                    
                    if (($_SESSION['user_profile']==3)) {
                        $c_courier = new comboBox("c_courier", $db1, 
                                "SELECT id, description FROM COURIER WHERE active=1", 
                                "id","description",0,"Courier");                                   
                        $c_courier->get_comboBox();
                        
                    }

                    ?>
                    <div style="clear: both"></div>
                    <input name="BtnSearch" type="submit" value="<?php echo $lg->l("search"); ?>" /> &nbsp;
                    <!-- <a class="button" href="courier.php?deliverednotpayed=1">Έχουν παραδοθεί αλλά δεν έχουν πληρωθεί</a> -->
                </div>
                
                <div style="clear: both"></div>
                
            </form>


            <form action="courier.php?deliverednotpayed=1" method="post">
                <?php
                
                $c_courier2 = new comboBox("c_courier2", $db1, 
                        "SELECT id, description FROM COURIER WHERE active=1", 
                        "id","description", $courier2 ,"Courier");                                   
                echo $c_courier2->comboBox_simple();
                
                ?>
                <input name="BtnSearch2" type="submit" value="Έχουν παραδοθεί αλλά δεν έχουν πληρωθεί" />
                <div style="clear: both"></div>
            </form>


        
        
        </div>
        
        <div style="clear: both"></div>        
        <div class="col-9">
            
            <?php if ($criteria!="") { echo "<h3 style=\"margin-left:1em\">ΚΡΙΤΗΡΙΑ: ".$criteria."</h3>";} ?>

            <?php if ($sql!="") { 
                    $gridCourier = new datagrid("gridCourier", $db1, 
                            "", 
                            array("id","voucherid", "vcode","vcode2","vcode3","companyname",
                                "area","deliverydate",
                                "courier_status", "courier_notes", "courier_delivery_date", 
                                "price","ID2"), 
                            array($l->l("Cust.ID"),"VID", "VCODE", "VCODE2", "VCODE3",
                                $l->l("Εταιρεία"),
                                $l->l("Περιοχή"),$l->l("Ημ. Παράδ."),
                                $l->l("Status"), "Σημ. courier", $l->l("Ημ. Παρ. Cour."),
                                $l->l("Αξία"),"Άνοιγμα"),
                            $ltoken, 0,
                            FALSE,"editVoucherCourier.php",$lg->l("open")
                            );
                    
                    $gridCourier->set_rs($rs);
                    
                    $strSQL = "SELECT * FROM AREAS";
                    $rsAreas = $db1->getRS($strSQL);
                    
                    $strSQL = "SELECT * FROM COURIERSTATUS";
                    $rsCourierStatus = $db1->getRS($strSQL);
                    
                    $gridCourier->set_colWidths(array("50","150","150","150","150","350","150","150",
                        "50","150","50","50"));
                    $gridCourier->set_colsFormat(array("","","","", "","","","DATE","","","DATE","CURRENCY",""));
                    $gridCourier->col_vlookupRS("area", "area", $rsAreas, "description"); 
					
                    if (strpos($_SESSION['user_access'], "[3]")!==false) {
                            $gridCourier->col_func("ID2","ID2","<a title=\"Άνοιγμα καρτέλας εταιρείας\" class=\"tooltip\" target=\"_blank\"  href=\"editcompany.php?id=??\"><span class=\"fa fa-edit fa-lg\"></span></a>", "??");
                    } 
                    else {
                            $gridCourier->col_func("ID2","ID2","", "??");
                    }
					
                    $gridCourier->col_vlookupRS("courier_status","courier_status",$rsCourierStatus,
                            "description");
                    $gridCourier->get_datagrid();
                    
                    echo "<br/><h3>TOTAL: ".count($gridCourier->get_rs())."</h3>";
                    $totalAmount = 0;

                    for ($i=0;$i<count($myRS);$i++) {
                        $totalAmount += $myRS[$i]['price'];
                    }
                    //var_dump($myRS);
                    $totalAmount = func::nrToCurrency($totalAmount);
                    echo "<br/><h3>Ποσό: ".$totalAmount." ευρώ</h3>";
                                        
            }
            else{
                //echo '<h2 class="search-results">'.$l->l("no-data").'</h2>';
            }
            ?>
        </div> 


		<!--<div class="col-3">
            <div style="position:fixed; right:0px; top:120px; width:24%; height: 450px;">
                <div style="padding: 0px 10px 20px 15px; border-left: 1px solid rgb(200,200,200); margin-left: 15px;">
                    <iframe src="messages.php?popupurl=editcompanycourier" width="100%" height="700" frameborder="0"></iframe>



                </div>
            </div>
            
        </div>-->
		
        
        <div style="clear: both"></div>        
        
    </div>
    
    <div style="clear: both"></div>    
    
    <?php include "blocks/footer.php"; ?>       
    
</body>
</html>




