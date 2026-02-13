<?php

/*$homesql = str_replace(
    "SLCT COMPANIES.id, companyname, online_url, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, '' AS LASTACTIONTIME, lastactiondate, active, COMPANIES.id AS id2, '' AS status2, '' AS user2, '' AS recalldate2, '' AS recalltime2, commstatus, for_renewal ", 
    "SELECT COMPANIES.id, companyname, email, city_id, contactperson, CONCAT_WS(', ', phone1, phone2, mobilephone) AS phones, online_url,  
    status, COMPANIES.user, recalldate, recalltime, area, basiccategory, subcategory, '' AS LASTACTIONTIME, 
    lastactiondate, active, COMPANIES.id AS id2, '' AS status2, '' AS user2, 
    '' AS recalldate2, '' AS recalltime2, commstatus, for_renewal  ", 
    $homesql);
//echo $homesql;
$rs = $db1->getRS($homesql);*/


$rsProductCategories = $db1->getRS("SELECT * FROM PRODUCT_CATEGORIES");
$rsStatus = $db1->getRS("SELECT * FROM STATUS");
$rsStatus2 = $db1->getRS("SELECT * FROM STATUS2");
$rsUsers = $db1->getRS("SELECT * FROM USERS");
$rsTimes = $db1->getRS("SELECT * FROM TIMES");
$rsAreas = $db1->getRS("SELECT * FROM AREAS");
$rsCategories = $db1->getRS("SELECT * FROM CATEGORIES");
//$rsCities = $db1->getRS("SELECT * FROM EP_CITIES");

for ($i=0;$i<count($rs);$i++) {
    $onlineurl = $rs[$i]['online_url'];
    $companyname = $rs[$i]['companyname'];
    $rs[$i]['companyname'] = "<a target=\"_blank\" href=\"$onlineurl\">$companyname</a>";
    
    if ($rs[$i]['active']==0) {
        $rs[$i]['id2'] = "<div style=\"border-left:5px solid rgb(255,200,200);padding:0px 10px 20px\">" . 
            $rs[$i]['id2']."</div>";
    }
    if ($rs[$i]['for_renewal']==1) {
        $rs[$i]['id2'] = "<div title=\"ΑΝΑΝΕΩΣΗ\" style=\"border-left:10px solid #09f; height:50px; padding-left:5px\">". $rs[$i]['id2'] . "</div>";
    }

    if ($rs[$i]['commstatus']!="") {

        $rs2 = explode("/", $rs[$i]['commstatus']);

        $strStatus = "";
        $strUser = "";
        $strRecallDate = ""; $strRecallTime = "";
        for ($k = 0; $k < count($rs2); $k++) {
            $rs2items = explode("|", $rs2[$k]);
            $myColor = func::vlookupRS("color", $rsProductCategories, $rs2items[1]);
            $myProductCategory = func::vlookupRS("shortdescription", $rsProductCategories, 
                    $rs2items[1]);
            $myStatus = func::vlookupRS("code", $rsStatus, $rs2items[2]);
            $myUser = func::vlookupRS("fullname", $rsUsers, $rs2items[3]);
            $myUser = str_replace(" ", "<br/>", $myUser);
            if ($rs2items[4]!="") {
                $myRecallDate = func::str14toDate($rs2items[4], "/");
                $myRecallDateShort = "(" . func::str14toDateDM($rs2items[4], "/") . ")";
            }
            else {
                $myRecallDate = "";
                $myRecallDateShort = "";
            }
            $myRecallTime = func::vlookupRS("description", $rsTimes, $rs2items[5]);
            if ($myRecallDate!="" && $strRecallDate=="") {
                $strRecallDate = $myRecallDate;
            }
            if ($myRecallTime!="" && $strRecallTime=="") {
                $strRecallTime = $myRecallTime;
            }

            $strStatus .= "<div style=\"border-left:5px solid $myColor;padding:3px\"><div class=\"col-5\">$myProductCategory <br/> $myStatus </div> <div class=\"col-7\">$myUser $myRecallDateShort</div> <div style=\"clear:both\"></div> </div>";


        }
        $rs[$i]['status2'] = $strStatus;
        $rs[$i]['recalldate2'] = $strRecallDate;
        $rs[$i]['recalltime2'] = $strRecallTime;
    }

    $rs[$i]['area'] = func::vlookupRS("description", $rsAreas, $rs[$i]['area']);
    $rs[$i]['basiccategory'] = func::vlookupRS("description", $rsCategories, $rs[$i]['basiccategory']);
    $rs[$i]['lastactiondate'] = func::str14toDate($rs[$i]['lastactiondate'],"/");
    //$rs[$i]['city_id'] = func::vlookupRS("description", $rsCities, $rs[$i]['city_id']);

    //unset($rs[$i]['recalldate']);
    //unset($rs[$i]['recalltime']);
    
}




