<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$tUserId = $_REQUEST['userid'];
$tDateFrom = $_REQUEST['datefrom'];
$tDateFrom = func::dateTo14str($tDateFrom);
$tDateTo = $_REQUEST['dateto'];
$tDateTo = func::dateTo14str($tDateTo);

$rs = FALSE; $totalHours = 0; $totalAmount = 0;
$totalHoursOff = 0; $totalDaysOff = 0;

$sql = "SELECT id, mdate, timein, timeout, hoursoff, N'' AS hours, N'' AS amount, dayoff, N'' as edit FROM USER_WORKDATA WHERE userid=? AND mdate>=? AND mdate<=? ORDER BY mdate";
$rs = $db1->getRS($sql, array($tUserId, $tDateFrom, $tDateTo));

if ($rs) {
    
    $user = new USERS($db1, $tUserId);
    $userFullname = $user->get_fullname();
    $userFullname1 = $userFullname;
    $userCostPerHour = $user->get_costperhour();
    
    for ($i = 0; $i < count($rs); $i++) {
        $rs[$i]['hours'] = $rs[$i]['timeout'] - $rs[$i]['timein'] - $rs[$i]['hoursoff'];
        $rs[$i]['amount'] = $rs[$i]['hours'] * $userCostPerHour;
        $totalHours += $rs[$i]['hours'];
        $totalAmount += $rs[$i]['amount']; 
        $totalHoursOff += $rs[$i]['hoursoff']; 
        $totalDaysOff += $rs[$i]['dayoff']; 
        
        //edit link
        $myDate = $rs[$i]['mdate'];
        $rs[$i]['edit'] = "<a class=\"fancybox\" href=\"editUserWorkData.php?userid=$tUserId&mdate=$myDate\"><span class=\"fa fa-edit\"></span></a>";
        
        //CHANGE DISPLAY
        $rs[$i]['mdate'] = func::str14toDate($rs[$i]['mdate']);
        $rs[$i]['timein'] = func::nrToHours($rs[$i]['timein']);
        $rs[$i]['timeout'] = func::nrToHours($rs[$i]['timeout']);
        $rs[$i]['hours'] = func::nrToHours($rs[$i]['hours']);
    
    }
    
    $grid = new datagrid("grid", $db1, "", 
        array("id", "mdate", "timein", "timeout", "hoursoff", "hours", "dayoff", "amount", "edit"), 
        array("Id", "Ημερ/νία", "Είσοδος", "Έξοδος", "Ωρ. αδ.", "Ώρες", "Αδεια", "Ποσό", "Διόρθωση"), $ltoken);
    $grid->set_rs($rs);
    $grid->set_colsFormat(array("","", "", "", "", "", "",  "CURRENCY", ""));
    $grid->set_colWidths(array(50, 100, 100, 100, 100,100,100,100,100));
    
}

$myStyle = <<<EOT
<style>        
    #grid {
        max-width: 800px;
    }
    #grid td:nth-child(3), #grid td:nth-child(4), 
        #grid td:nth-child(5), #grid td:nth-child(6) {
        text-align: right;
    }    
    #grid td:nth-child(7) {
        text-align: center;
    }
</style>
EOT;

include '_theHeader.php';


echo "<h1>Εργαζόμενος: $userFullname1</h1>";
$userCostPerHour = func::nrToCurrency($userCostPerHour);
echo "<h1>Ωρομίσθιο: $userCostPerHour</h1>";


if ($rs) { 
    $grid->get_datagrid();
    
    $totalHours = func::nrToHours($totalHours);
    $totalAmount = func::nrToCurrency($totalAmount);
    
    echo "<div class=\"clear\" style=\"height:30px\"></div>";
    echo "<h3>Σύνολο ωρών: $totalHours</h3>";
    echo "<h3>Συνολικό ποσό: $totalAmount</h3>";
    echo "<h3>Σύνολο ωρών άδειας: $totalHoursOff</h3>";
    echo "<h3>Σύνολο αδειών: $totalDaysOff</h3>";
    
}

$myScript = <<<EOT
    <script>
        $(function() {
            $("#grid").tableExport({formats: ["xlsx","xls", "csv"]});            
        });

    </script>    
        
EOT;

include '_theFooter.php';
