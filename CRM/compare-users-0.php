<?php

$sDate2 = date("Ymd"); //func::str14toDate($sDate, "-","EN");
$criteriaSDate2 = " tdatetime='".$sDate2."000000' ";
$criteriaStatus2 = " AND status IN (1,2) AND transactiontype=1 ";
$sql2 = "SELECT COUNT(id) AS MyCount, seller AS user FROM TRANSACTIONS A WHERE "
        . $criteriaSDate2 . $criteriaStatus2
        . " GROUP BY user";

//echo $sql;

$rs2=$db1->getRS($sql2);

echo "<table class=\"stats\"><tr>";
for ($i=0;$i<count($rs2);$i++) {
    $h = $rs2[$i]['MyCount'] * 5; 
    $myColor = $_SESSION['user_id']==$rs2[$i]['user']?"red":"white";
    echo "<td class=\"stats-head\" valign=\"bottom\"><div style=\"height:".$h."px; width:10px; background-color:$myColor; float:left\"></div></td>";
}
echo "</tr>";
echo "<tr>";
for ($i=0;$i<count($rs2);$i++) {
    $myUser = $rs2[$i]['user'];
    $user2 = func::vlookup("fullname", "USERS", "id=".$rs2[$i]['user'], $db1);
    $ar = explode(" ", $user2);
    $userInitials = mb_substr($ar[0], 0, 1, 'UTF-8') . mb_substr($ar[1], 0, 1, 'UTF-8');
    $userLastname = $ar[1];
    $dateFrom = $sDate2."000000";
    $dateTo = $sDate2."999999";
    $link = "getCompanies.php?user=$myUser&dateFrom=$dateFrom&dateTo=$dateTo&status=1,2";
    echo "<td><a class=\"fancybox\" href=\"$link\" title=\"$user2\">".$userInitials."</a> (<a class=\"fancybox\" href=\"$link\">".$rs2[$i]['MyCount']."</a>)"."</td>";
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