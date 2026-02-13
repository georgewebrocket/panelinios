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

$tDateFrom = isset($_POST['t_dateFrom'])? $_POST['t_dateFrom']: "";
$tDateTo = isset($_POST['t_dateTo'])? $_POST['t_dateTo']: "";
$rs = FALSE; $totalHours = 0; $totalAmount = 0;
$totalDaysOff = 0; $totalHoursOff = 0;
$totalDays = 0;

if ($_POST) {
    $dateFrom = textbox::getDate($_POST['t_dateFrom'], $locale);
    $dateTo = textbox::getDate($_POST['t_dateTo'], $locale);
    $sql = "SELECT userid AS id, userid AS username, SUM(timeout-timein-hoursoff) AS tHours, "
            . "N'' AS tAmount, SUM(dayoff) AS tDaysOff, SUM(hoursoff) AS tHoursOff, "
            . "N'' AS tEdit, COUNT(id) AS DAYS FROM USER_WORKDATA "
            . "WHERE mdate>=? AND mdate<=? GROUP BY userid";
    $rs = $db1->getRS($sql, array($dateFrom, $dateTo));
    
    //var_dump($rs);
    
    if ($rs) {
        $rsUsers = $db1->getRS("SELECT * FROM USERS");
        for ($i = 0; $i < count($rs); $i++) {
            $rs[$i]['username'] = func::vlookupRS("fullname", $rsUsers, $rs[$i]['id']); 
            
            $rs[$i]['tAmount'] = $rs[$i]['tHours'] * func::vlookupRS("costperhour", $rsUsers, $rs[$i]['id']); 
            $totalHours += $rs[$i]['tHours'];
            $totalDays += $rs[$i]['DAYS'];
            $totalDaysOff += $rs[$i]['tDaysOff'];
            $totalHoursOff += $rs[$i]['tHoursOff'];
            $totalAmount += $rs[$i]['tAmount'];
            //CHANGE HOURS DISPLAY
            //$rs[$i]['tHours'] -= $rs[$i]['tHoursOff'];  //////          
            $rs[$i]['tHours'] = func::nrToHours($rs[$i]['tHours']);
            
            $myUserId = $rs[$i]['id'];            
            $rs[$i]['tEdit'] = "<a target=\"_blank\" href=\"reportUserWorkDataPerUser.php?userid=$myUserId&datefrom=$tDateFrom&dateto=$tDateTo\"><span class=\"fa fa-eye\"></span></a>";
            
        }
        $grid = new datagrid("grid", $db1, "", 
                array("id", "username", "DAYS", "tHours", "tAmount",  "tDaysOff", "tHoursOff", "tEdit"), 
                array("ID", "ΕΡΓΑΖΟΜΕΝΟΣ", "ΗΜΕΡΕΣ", "ΩΡΕΣ", "ΠΟΣΟ",  "ΗΜΕΡ. ΑΔΕΙΑΣ", "ΩΡΕΣ ΑΔ.", "Αναλυτικά"), 
                $ltoken);
        $grid->set_rs($rs);
        $grid->set_colsFormat(array("","", "", "", "CURRENCY", "", "CURRENCY", ""));
        $grid->set_colWidths(array(50, 400, 100, 100, 100, 100, 100, 100));
        
    }
    
}

$myStyle = <<<EOT
<style>
        form {
            max-width: 600px;
            margin-left:0px;
        }
        #grid {
            max-width: 800px;
        }
        #grid td:nth-child(1), #grid td:nth-child(2), #grid td:nth-child(3) {
            text-align: left;
        }
        #grid td:nth-child(4) {
            text-align: right;
        }
        #grid td:nth-child(5) {
            text-align: center;
        }
</style>
EOT;


include '_theHeader.php';

?>


<form action="" method="post">
    
    <?php
    
    $t_dateFrom = new textbox("t_dateFrom", "ΗΜΕΡ. ΑΠΟ", $tDateFrom, "ΗΗ/ΜΜ/ΕΕΕΕ");
    $t_dateFrom->set_format("DATE");
    $t_dateFrom->get_Textbox();
    
    $t_dateTo = new textbox("t_dateTo", "ΗΜΕΡ. ΕΩΣ", $tDateTo, "ΗΗ/ΜΜ/ΕΕΕΕ");
    $t_dateTo->set_format("DATE");
    $t_dateTo->get_Textbox();
    
    $btnOK = new button("btnOK", "SEARCH");
    $btnOK->get_button();
    
    
    ?>
    <div class="clear"></div>
    
</form>

<?php

if ($rs) { 
    $grid->get_datagrid();
    
    $totalHours = func::nrToHours($totalHours);
    $totalAmount = func::nrToCurrency($totalAmount);
    
    echo "<div class=\"clear\" style=\"height:30px\"></div>";
    echo "<h3>Σύνολο ημερών: $totalDays</h3>";
    echo "<h3>Σύνολο ωρών: $totalHours</h3>";
    echo "<h3>Συνολικό ποσό: $totalAmount</h3>";
    echo "<h3>Σύνολο αδειών: $totalDaysOff</h3>";
    echo "<h3>Σύνολο ωρών άδειας: $totalHoursOff</h3>";
    
} 
    
?>


<?php

$myScript = <<<EOT
    <script>
        $(function() {
            $("#grid").tableExport({formats: ["xlsx","xls", "csv"]});            
        });

    </script>    
        
EOT;

include '_theFooter.php';
