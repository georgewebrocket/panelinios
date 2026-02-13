<?php

$sDate2 = date("Y-m-d"); //func::str14toDate($sDate, "-","EN");
$criteriaSDate2 = " date_format(atimestamp,'%Y-%m-%d')='".$sDate2."' ";
$criteriaStatus2 = " AND Status2 = 5";
$sql2 = "SELECT COUNT(id) AS MyCount, user FROM ACTIONS A WHERE "
        . $criteriaSDate2 . $criteriaStatus2
        . " GROUP BY user";

//echo $sql;

$rs2=$db1->getRS($sql2);

echo "<table class=\"stats\"><tr>";
for ($i=0;$i<count($rs2);$i++) {
    $h = $rs2[$i]['MyCount'] * 5;    
    echo "<td class=\"stats-head\" valign=\"bottom\"><div style=\"height:".$h."px; width:10px; background-color:white; float:left\"></div></td>";
}
echo "</tr>";
echo "<tr>";
for ($i=0;$i<count($rs2);$i++) {
    $user2 = func::vlookup("fullname", "USERS", "id=".$rs2[$i]['user'], $db1);
    $ar = explode(" ", $user2);
    $userInitials = mb_substr($ar[0], 0, 1) . mb_substr($ar[1], 0, 1);
    echo "<td><a href=\"#\" title=\"$user2\">".$user2."</a> (<a href=\"\">".$rs2[$i]['MyCount']."</a>)"."</td>";
}

echo "</tr></table>";

?>

<style>
    
    table.stats td {
        border-left: 1px dotted white;
        padding:5px;
        color:white;
        font-size: 12px;
        width: 80px;
        text-wrap: normal;
    }
    
    td.stats-head {
        border-bottom: 1px dotted white;    
    }
    
    
    
</style>