<?php

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$userId = $_GET['user'];

$userProfile = $_SESSION['user_profile'];

$date = isset($_REQUEST['date'])? $_REQUEST['date']: "today";
if ($date=='today') {
    $dateCriteria = " AND followup_date<= " . date("Ymd") . "235959";
}
elseif ($date=="all") {
    $dateCriteria = "";
}
else {
    $dateCriteria = " AND followup_date = '$date'";
}

if ($userProfile==1) {
    
    $sql = "SELECT N'' AS Mark, N'' AS Nr, VOUCHERS.customer AS id, VOUCHERS.customername AS customername, 
        VOUCHERS.deliverydate, VOUCHERS.lastcommnotes AS LASTCOMMNOTES, 
        VOUCHERS.lastcommdate AS LASTCOMMDATE, N'' AS COURIERLINK, VOUCHERS.vcode2, VOUCHERS.vcode3,
        VOUCHERS.id AS voucherid, VOUCHERS.vcode2, N'' AS delivered, N'' as returned, N'' as pending, 
        N'' AS edit, VOUCHERS.userid, VOUCHERS.followup_date, VOUCHERS.followup_time, VOUCHERS.courier_notes,
        AREAS.description AS AREA
        FROM VOUCHERS INNER JOIN COMPANIES ON VOUCHERS.customer = COMPANIES.id LEFT JOIN AREAS ON COMPANIES.area = AREAS.id WHERE VOUCHERS.courier_status =1 AND VOUCHERS.vcode2 IS NOT NULL AND VOUCHERS.userid = ? $dateCriteria ORDER BY VOUCHERS.deliverydate";    
    $params = array($userId);
    $rs = $db1->getRS($sql, $params);
}
elseif (in_array($userProfile, array(2,3))) {
    $sql = "SELECT N'' AS Mark,  N'' AS Nr, VOUCHERS.customer AS id, VOUCHERS.customername AS customername, 
        VOUCHERS.deliverydate, VOUCHERS.lastcommnotes AS LASTCOMMNOTES, 
        VOUCHERS.lastcommdate AS LASTCOMMDATE, N'' AS COURIERLINK, VOUCHERS.vcode2, VOUCHERS.vcode3,
        VOUCHERS.id AS voucherid, VOUCHERS.vcode2, N'' AS delivered, N'' as returned, N'' as pending, 
        N'' AS edit, VOUCHERS.userid , VOUCHERS.followup_date, VOUCHERS.followup_time, VOUCHERS.courier_notes,
        AREAS.description AS AREA  
        FROM VOUCHERS INNER JOIN COMPANIES ON VOUCHERS.customer = COMPANIES.id LEFT JOIN AREAS ON COMPANIES.area = AREAS.id WHERE VOUCHERS.courier_status =1 AND VOUCHERS.vcode2 IS NOT NULL $dateCriteria ORDER BY VOUCHERS.deliverydate";
    $rs = $db1->getRS($sql);
}

$rsUsers = $db1->getRS("SELECT * FROM USERS");
$rsTimes = $db1->getRS("SELECT * FROM TIMES");

for ($i = 0; $i < count($rs); $i++) {
    $rs[$i]['Nr'] = $i + 1;
    
    $vcode2 = $rs[$i]['vcode2'];
    if ($vcode2!="") {
        
        $rs[$i]['COURIERLINK'] = "<a target=\"_blank\"  href=\"https://redcourier.gr/track-and-trace/?voucher=$vcode2\">RED $vcode2</a>";
    }
    $vcode3 = $rs[$i]['vcode3'];
    if ($vcode3!="") {
        $rs[$i]['COURIERLINK'] .= "<br/><a target=\"_blank\"  href=\"https://www.acscourier.net/el/track-and-trace?p_p_id=$vcode3\">ACS $vcode3</a>";
    }
    
    $voucherid = $rs[$i]['voucherid'];
    $companyid = $rs[$i]['id'];
    
    $rs[$i]['customername'] = "<a title=\"Άνοιγμα καρτέλας πελάτη\" target=\"_blank\" class=\"edit-company tooltip\" href=\"editcompany.php?id=$companyid&voucherid=$voucherid\">".$rs[$i]['customername']."</a>";
    $rs[$i]['edit'] = "<a title=\"Άνοιγμα καρτέλας πελάτη\" target=\"_blank\" class=\"edit-company tooltip\" href=\"editcompany.php?id=$companyid&voucherid=$voucherid\"><span class=\"fa fa-edit\"></span></a>";
        
    $rs[$i]['delivered'] = "<span title=\"Αλλαγή status->Παραδόθηκε\" data-voucher=\"$voucherid\" class=\"fa fa-check-circle-o voucher-delivered tooltip\"><span>";
    $rs[$i]['returned'] = "<span title=\"Αλλαγή status->Επιστροφή\" data-voucher=\"$voucherid\" class=\"fa fa-reply voucher-returned tooltip\"><span>";
    
    $rs[$i]['pending'] = "<span title=\"Αλλαγή status->Ολοκλήρωση\" data-voucher=\"$voucherid\" class=\"fa fa-hourglass voucher-pending tooltip\"><span>";
    
    $userFullame = trim(func::vlookupRS("fullname", $rsUsers, $rs[$i]['userid']));
    $userAr = explode(" ", $userFullame);
    $myUsername = count($userAr)==2? $userAr[1]: $userAr[0];
    $rs[$i]['userid'] = "<span title=\"$userFullame\">$myUsername</span>";
    
    $strDeliveryDate = func::str14toDate($rs[$i]['deliverydate'],"-", "EN");
    $dateDeliveryDate = new DateTime($strDeliveryDate);
    $dateNow = new DateTime();
    $x1 = func::days($dateNow);
    $x2 = func::days($dateDeliveryDate);
    $daysDiff = $x1 - $x2;
    $rs[$i]['deliverydate'] = func::str14toDate($rs[$i]['deliverydate']);
    if ($daysDiff>=8) {
        $rs[$i]['Mark'] = "<div class=\"tooltip\" title=\"Καθυστερημένο\" style=\"background-color:rgb(250,100,100); width:15px; height:15px; border-radius:8px\"></div>";
    }
    $rs[$i]['LASTCOMMDATE'] = func::str14toDate($rs[$i]['LASTCOMMDATE']);
    
    $rs[$i]['followup_date'] = func::str14toDate($rs[$i]['followup_date']);
    $rs[$i]['followup_time'] = func::vlookupRS("description", $rsTimes, $rs[$i]['followup_time']);
    
    $rs[$i]['LASTCOMMNOTES'] = "<div style=\"background-color:rgb(255,255,150)\">" . 
            $rs[$i]['courier_notes'] . "</div><br/>" . $rs[$i]['LASTCOMMNOTES'];
    
}

$grid = new datagrid("grid", $db1, "", 
        array("Nr","voucherid", "deliverydate", "customername", "AREA", "COURIERLINK", "LASTCOMMDATE", "LASTCOMMNOTES", "userid","followup_date", "followup_time", "edit",  "delivered", "returned", "pending", "Mark"), 
        array("#","VID", "Ημ. παραδ.", "Πελάτης", "Περιοχή", "Tracking", "Ημερ. επικοινωνίας", "Σχόλια", "Agent", "Ημερ. Followup", "Ώρα Followup", "Διόρθωση",  "ΠΑΡΑΔΟΘΗΚΕ", "ΕΠΙΣΤΡΟΦΗ", "ΣΕ ΕΚΚΡΕΜ.", ""), 
        $ltoken);

$grid->set_rs($rs);

$sql = "SELECT `followup_date`, '' AS MYDATE, count(id) AS MYCOUNT FROM `VOUCHERS` WHERE courier_status=1 AND COALESCE(followup_date,'')<>'' GROUP BY followup_date, MYDATE ORDER BY followup_date";
$rsDates = $db1->getRS($sql);

for ($i = 0; $i < count($rsDates); $i++) {
    $rsDates[$i]['MYDATE'] = func::str14toDate($rsDates[$i]['followup_date']);
}


//$grid->set_colsFormat(array("","","",""));

$myStyle = <<<EOT
<style>        
    #grid {
        max-width: 1200px;
    }
        
    #grid th {
        cursor: pointer;
    }
    
    #grid td:nth-child(12), 
    #grid td:nth-child(13),
    #grid td:nth-child(14),
    #grid td:nth-child(15) {
        text-align: center;
        color: rgb(150,150,150);
        cursor:pointer;
        font-size:20px;
    }
    #grid td:nth-child(12):hover, 
    #grid td:nth-child(13):hover,
    #grid td:nth-child(14):hover,
    #grid td:nth-child(15):hover {
        color: rgb(0,0,0);
    }

    #grid th:nth-child(8),
    #grid td:nth-child(8) {
        width: 300px;
        max-width: 300px;
        white-space: normal;
        overflow-wrap: break-word; /* break long words only if needed */
        word-break: normal;
    }  
        
    .table-footer {
        display:none;
    }
        
    .btn-date {
        margin-bottom: 10px;
        display: inline-block;
    }
        
        
</style>
EOT;


include '_theHeader.php';

$sDate = func::str14toDate($date);
if ($sDate=="today") {
    $sDate = "σήμερα";
}
if ($sDate=="all") {
    $sDate = "όλα";
}
echo "<h1>Voucher σε εκκρεμότητα ($sDate)</h1>";

$linkToday = "pendingVouchers.php?user=$userId&date=today";
echo "<a class=\"button btn-date\" href=\"$linkToday\">Σήμερα</a> &nbsp;";

for ($i = 0; $i < count($rsDates); $i++) {
    $myDate = $rsDates[$i]['MYDATE'];
    $link = "pendingVouchers.php?user=$userId&date=" . $rsDates[$i]['followup_date'];
    echo "<a class=\"button btn-date\" href=\"$link\">$myDate</a> &nbsp;";
}

$linkAll = "pendingVouchers.php?user=$userId&date=all";
echo "<a class=\"button btn-date\" href=\"$linkAll\">Όλα</a> &nbsp;";

echo "<div class=\"spacer-20\"></div>";
            
$grid->get_datagrid();

echo "<br/><h3>Σύνολο ".count($rs)."</h3>";

$myScript = <<<EOT

        <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
   
   <script>

    $(function() {
            
        $(".edit-company, .voucher-tracking").click(function() {
            $(this).parent().css("background-color","rgb(200,250,200)");
   
        });
        
        $(".voucher-delivered").click(function() {
            var voucherid = $(this).data("voucher");
            var parentEl = $(this).parent();
            $.post("_voucherChangeStatus.php", 
                {voucherid: voucherid,
                courierstatus: 5}, 
                function(data) {
                    alert(data);
                    parentEl.css("background-color","rgb(200,250,200)");
                }
            );
        }); 
        
        $(".voucher-returned").click(function() {
            var voucherid = $(this).data("voucher");
            var parentEl = $(this).parent();
            $.post("_voucherChangeStatus.php", 
                {voucherid: voucherid,
                courierstatus: 6}, 
                function(data) {
                    alert(data);
                    parentEl.css("background-color","rgb(200,250,200)");
                }
            );
        }); 
        
        $(".voucher-pending").click(function() {
            var voucherid = $(this).data("voucher");
            var parentEl = $(this).parent();
            $.post("_voucherChangeStatus.php", 
                {voucherid: voucherid,
                courierstatus: 1}, 
                function(data) {
                    alert(data);
                    parentEl.css("background-color","rgb(200,250,200)");
                }
            );
        });
        
        
        $("#grid").tablesorter({ 
            /*sortList: [[3,0],[4,0]],*/
            headers: { 
                2: { 
                    sorter:'dates' 
                }, 
                9: { 
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
EOT;

include '_theFooter.php';