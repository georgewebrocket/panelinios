<?php

$sDate2 = date("Ymd"); //func::str14toDate($sDate, "-","EN");
//$sDate2 = "20191210";
$criteriaSDate2 = " tdatetime='".$sDate2."000000' ";
$criteriaStatus2 = " AND status IN (1,2) AND transactiontype=1 ";
$sql2 = "SELECT COUNT(id) AS MyCount, seller AS user FROM TRANSACTIONS A WHERE "
        . $criteriaSDate2 . $criteriaStatus2
        . " GROUP BY user ORDER BY MyCount DESC";

//echo $sql;

$rs2=$db1->getRS($sql2);

$rsUsers = $db1->getRS("SELECT * FROM USERS");

//echo "<table class=\"stats\"><tr>";

for ($i=0;$i<count($rs2);$i++) {
       
    $w = $rs2[$i]['MyCount'] * 10; 
    $myColor = $_SESSION['user_id']==$rs2[$i]['user']?"#f93":"#82c250";
    //$user2 = func::vlookupRS("fullname", $rsUsers, $rs2[$i]['user']);
    $myUser = $rs2[$i]['user'];
    $user2 = func::vlookupRS("fullname", $rsUsers, $rs2[$i]['user']);
    $ar = explode(" ", $user2);
    $userInitials = mb_substr($ar[0], 0, 1, 'UTF-8') . mb_substr($ar[1], 0, 1, 'UTF-8');
    $userLastname = $ar[1];
    $dateFrom = $sDate2."000000";
    $dateTo = $sDate2."999999";
    $link = "getCompanies.php?user=$myUser&dateFrom=$dateFrom&dateTo=$dateTo&status=1,2";
    
    echo "<div style=\"background-color:#ccc; margin-bottom:15px; border-radius:10px; overflow:hidden; position:relative\">";
    echo "<div style=\"width:{$w}px; background-color:$myColor;height:20px\"></div>";
    echo "<div style=\"position:absolute; top:3px; left:5px; font-size:14px\"><a class=\"fancybox\" href=\"$link\" title=\"$user2\">".$userInitials."</a> (<a class=\"fancybox\" href=\"$link\">".$rs2[$i]['MyCount']."</a>)</div>";
    echo "</div>";
    
    
}

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