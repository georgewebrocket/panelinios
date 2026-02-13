<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

require_once("php/configPanel.php");
require_once("php/dataobjectsPanel.php");

if (isset($_GET['start'])) {
    $start = $_GET['start'];
    $sql = "SELECT id FROM COMPANIES WHERE mark=0 AND reference<=12 AND id>=$start ORDER BY id";
}
else {
    $sql = "SELECT id FROM COMPANIES WHERE mark=0 AND reference<=12 ORDER BY id";
}

$rs = $db1->getRS($sql);

for ($i = 0; $i < count($rs); $i++) {
    $id = $rs[$i]['id'];
    $companyEpag = new COMPANIES_EPAG($db1, $id); //....id
    $epagId = $companyEpag->get_id();

    $dboPanel = new DB(conn_panel::$connstr, conn_panel::$username, conn_panel::$password);

    $sql = "SELECT * FROM COMPANIES WHERE epag_id=?";
    $rsCompPanel = $dboPanel->getRS($sql, array($epagId));
    if ($rsCompPanel) {
        $companyPanel = new COMPANIES_PANEL($dboPanel, $rsCompPanel[0]['id']);
    }
    else {
        $companyPanel = new COMPANIES_PANEL($dboPanel, 0);
        $companyPanel->set_status(1);
    }

    if ($companyPanel->get_status()==1) {

        $companyPanel->set_companyname($companyEpag->get_companyname());
        $companyPanel->set_companyname_en($companyEpag->get_companyname_en());
        $companyPanel->set_phone1($companyEpag->get_phone1());
        $companyPanel->set_phone2($companyEpag->get_phone2());
        $companyPanel->set_fax($companyEpag->get_fax());
        $companyPanel->set_mobilephone($companyEpag->get_mobilephone());
        $companyPanel->set_basiccategory($companyEpag->get_basiccategory());
        $companyPanel->set_reference($companyEpag->get_reference());
        $companyPanel->set_area($companyEpag->get_area());
        $companyPanel->set_geo_x($companyEpag->get_geo_x());
        $companyPanel->set_geo_y($companyEpag->get_geo_y());
        $companyPanel->set_address($companyEpag->get_address());
        $companyPanel->set_zipcode($companyEpag->get_zipcode());
        $companyPanel->set_email($companyEpag->get_email());
        $companyPanel->set_website($companyEpag->get_website());
        $companyPanel->set_facebook($companyEpag->get_facebook());
        $companyPanel->set_twitter($companyEpag->get_twitter());
        $companyPanel->set_vatzone($companyEpag->get_vatzone());



        //EXPIRES
        $epagExpireDate14 = $companyEpag->get_expires();
        $epagExpireDate = func::str14toDate($epagExpireDate14, "-", "EN");
        $newExp = strtotime("-2 months", strtotime($epagExpireDate));
        $newExp14 = date("Ymd", $newExp)."000000";
        $companyPanel->set_expires($newExp14);

        //COMMENT
        $sqlStr = "SELECT company, SUBSTRING_INDEX( GROUP_CONCAT(CAST(comment AS CHAR) ORDER BY `atimestamp` DESC),',',1) AS LASTCOMMENT FROM ACTIONS WHERE company = ? AND `status2` = 5 AND product_categories LIKE '%[1]%'";
        $rsComment = $db1->getRS($sqlStr, array($epagId));
        $actionComment = $rsComment? $rsComment[0]['LASTCOMMENT']: "";
        $companyPanel->set_comment($companyEpag->get_comment() . " / " . $actionComment);

        $companyPanel->set_show_phone1($companyEpag->get_show_phone1());
        $companyPanel->set_show_phone2($companyEpag->get_show_phone2());
        $companyPanel->set_show_mobilephone($companyEpag->get_show_mobilephone());
        $companyPanel->set_show_email($companyEpag->get_show_email());
        $companyPanel->set_LinkedIn($companyEpag->get_LinkedIn());
        $companyPanel->set_linkedin_dm($companyEpag->get_linkedin_dm());
        $companyPanel->set_ShortDescription($companyEpag->get_ShortDescription());
        $companyPanel->set_FullDescription($companyEpag->get_FullDescription());
        $companyPanel->set_subcategory($companyEpag->get_subcategory());
        $companyPanel->set_afm($companyEpag->get_afm());
        $companyPanel->set_doy($companyEpag->get_doy());
        $companyPanel->set_eponimia($companyEpag->get_eponimia());
        $companyPanel->set_onlinestatus($companyEpag->get_onlinestatus());
        $companyPanel->set_onlinedatetime($companyEpag->get_onlinedatetime());
        $companyPanel->set_phonecode($companyEpag->get_phonecode());
        $companyPanel->set_company_type($companyEpag->get_company_type());
        $companyPanel->set_seo_manually_set($companyEpag->get_seo_manually_set());
        $companyPanel->set_city_id($companyEpag->get_city_id());
        $companyPanel->set_vn_category($companyEpag->get_vn_category());
        $companyPanel->set_vn_keywords($companyEpag->get_vn_keywords());
        $companyPanel->set_vn_expires($companyEpag->get_vn_expires());
        $companyPanel->set_phone1digits($companyEpag->get_phone1digits());
        $companyPanel->set_phone2digits($companyEpag->get_phone2digits());
        $companyPanel->set_faxdigits($companyEpag->get_faxdigits());
        $companyPanel->set_mobiledigits($companyEpag->get_mobiledigits());
        $companyPanel->set_companyname_dm($companyEpag->get_companyname_dm());
        $companyPanel->set_address_dm($companyEpag->get_address_dm());
        $companyPanel->set_phone1_dm($companyEpag->get_phone1_dm());
        $companyPanel->set_phone2_dm($companyEpag->get_phone2_dm());
        $companyPanel->set_fax_dm($companyEpag->get_fax_dm());
        $companyPanel->set_email_dm($companyEpag->get_email_dm());
        $companyPanel->set_mobile_dm($companyEpag->get_mobile_dm());
        $companyPanel->set_website_dm($companyEpag->get_website_dm());
        $companyPanel->set_geox_dm($companyEpag->get_geox_dm());
        $companyPanel->set_geoy_dm($companyEpag->get_geoy_dm());
        $companyPanel->set_zipcode_dm($companyEpag->get_zipcode_dm());
        $companyPanel->set_facebook_dm($companyEpag->get_facebook_dm());
        $companyPanel->set_twitter_dm($companyEpag->get_twitter_dm());
        $companyPanel->set_shortdescr_dm($companyEpag->get_shortdescr_dm());
        $companyPanel->set_fulldescr_dm($companyEpag->get_fulldescr_dm());
        $companyPanel->set_basiccat_dm($companyEpag->get_basiccat_dm());
        $companyPanel->set_area_dm($companyEpag->get_area_dm());
        $companyPanel->set_keywords_dm($companyEpag->get_keywords_dm());
        $companyPanel->set_cityid_dm($companyEpag->get_cityid_dm());
        $companyPanel->set_CUSID($companyEpag->get_CUSID());
        $companyPanel->set_allphonesdigits($companyEpag->get_allphonesdigits());
        $companyPanel->set_nodoubles($companyEpag->get_nodoubles());
        $companyPanel->set_haswebsite($companyEpag->get_haswebsite());
        $companyPanel->set_profession($companyEpag->get_profession());
        $companyPanel->set_profession_dm($companyEpag->get_profession_dm());
        $companyPanel->set_expires_dm($companyEpag->get_expires_dm());
        $companyPanel->set_active($companyEpag->get_active());
        $companyPanel->set_googleplus($companyEpag->get_googleplus());
        $companyPanel->set_googleplus_dm($companyEpag->get_googleplus_dm());
        $companyPanel->set_pinterest($companyEpag->get_pinterest());
        $companyPanel->set_pinterest_dm($companyEpag->get_pinterest_dm());
        $companyPanel->set_sites($companyEpag->get_sites());
        $companyPanel->set_sites_dm($companyEpag->get_sites_dm());
        $companyPanel->set_workinghours($companyEpag->get_workinghours());
        $companyPanel->set_workinghours_dm($companyEpag->get_workinghours_dm());
        $companyPanel->set_workingmonths($companyEpag->get_workingmonths());
        $companyPanel->set_workingmonths_dm($companyEpag->get_workingmonths_dm());    

    }

    $companyPanel->set_catalogueid($companyEpag->get_catalogueid());

    //STATUS
    $sqlStr = "SELECT * FROM COMPANIES_STATUS WHERE companyid=? AND productcategory=1";
    $rsStatus = $db1->getRS($sqlStr, array($epagId));
    $myStatus = $rsStatus? $rsStatus[0]['status']: 0;
    $companyPanel->set_epag_status($myStatus);

    $companyPanel->set_epag_expires($companyEpag->get_expires());

    $companyPanel->set_epag_id($companyEpag->get_id());

    $companyPanel->Savedata();

    echo "EPAG. $id / PANEL. ".$companyPanel->get_id() . "<br/>";
}

echo ' 
    <audio autoplay>
      <source src="tada.ogg" type="audio/ogg">
      <source src="tada.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
    </audio>';