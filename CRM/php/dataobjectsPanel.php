<?php

class COMPANIES_PANEL
{

protected $_myconn, $_id, $_companyname, $_companyname_en, $_phone1, $_phone2, $_fax, $_mobilephone, $_contactperson, $_basiccategory, $_reference, $_area, $_geo_x, $_geo_y, $_address, $_zipcode, $_email, $_website, $_facebook, $_twitter, $_package, $_discount, $_price, $_vatzone, $_catalogueid, $_expires, $_username, $_password, $_user, $_userdataentry, $_recalldate, $_recalltime, $_status, $_comment, $_history, $_show_phone1, $_show_phone2, $_show_mobilephone, $_show_email, $_LinkedIn, $_linkedin_dm, $_ShortDescription, $_FullDescription, $_DeliveryDate, $_DeliveryTime, $_DeliveryNotes, $_subcategory, $_dataentrydatetime, $_voucherid, $_invoiceid, $_invoiceprinted, $_afm, $_doy, $_eponimia, $_courier_ok, $_courier_notes, $_courier_return, $_courier_delivery_date, $_courier_status, $_lockedbyuser, $_lockuser, $_onlinestatus, $_onlinedatetime, $_phonecode, $_company_type, $_seo_manually_set, $_courier, $_city_id, $_vn_category, $_vn_keywords, $_vn_expires, $_phone1digits, $_phone2digits, $_faxdigits, $_mobiledigits, $_companyname_dm, $_address_dm, $_phone1_dm, $_phone2_dm, $_fax_dm, $_email_dm, $_mobile_dm, $_website_dm, $_geox_dm, $_geoy_dm, $_zipcode_dm, $_facebook_dm, $_twitter_dm, $_shortdescr_dm, $_fulldescr_dm, $_basiccat_dm, $_area_dm, $_keywords_dm, $_cityid_dm, $_CUSID, $_allphonesdigits, $_nodoubles, $_lastactiondate, $_haswebsite, $_profession, $_profession_dm, $_password_dm, $_expires_dm, $_active, $_googleplus, $_googleplus_dm, $_pinterest, $_pinterest_dm, $_sites, $_sites_dm, $_workinghours, $_workinghours_dm, $_workingmonths, $_workingmonths_dm, $_domain, $_domain_name, $_domain_expires, $_languages, $_languages_dm, $_commstatus, $_lastactiondate1, $_lastactiondate2, $_lastactiondate3, $_package2, $_discount2, $_price2, $_epag_id, $_epag_status, $_epag_expires ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM COMPANIES WHERE id=?"; 
        $all_rows = $this->_myconn->getRS($ssql, array($_id)); 
        } 
    else if ($_ssql!='') { 
        $ssql = $_ssql; 
        $all_rows = $this->_myconn->getRS($ssql); 
        } 
    else { 
        $rows = $my_rows; 
        $all_rows = arrayfunctions::filter_by_value($rows, 'id', $this->_id); 
    }
    $icount = count($all_rows); 

    if ($icount==1) { 
        $this->_companyname = $all_rows[0]['companyname']; 
        $this->_companyname_en = $all_rows[0]['companyname_en']; 
        $this->_phone1 = $all_rows[0]['phone1']; 
        $this->_phone2 = $all_rows[0]['phone2']; 
        $this->_fax = $all_rows[0]['fax']; 
        $this->_mobilephone = $all_rows[0]['mobilephone']; 
        $this->_contactperson = $all_rows[0]['contactperson']; 
        $this->_basiccategory = $all_rows[0]['basiccategory']; 
        $this->_reference = $all_rows[0]['reference']; 
        $this->_area = $all_rows[0]['area']; 
        $this->_geo_x = $all_rows[0]['geo_x']; 
        $this->_geo_y = $all_rows[0]['geo_y']; 
        $this->_address = $all_rows[0]['address']; 
        $this->_zipcode = $all_rows[0]['zipcode']; 
        $this->_email = $all_rows[0]['email']; 
        $this->_website = $all_rows[0]['website']; 
        $this->_facebook = $all_rows[0]['facebook']; 
        $this->_twitter = $all_rows[0]['twitter']; 
        $this->_package = $all_rows[0]['package']; 
        $this->_discount = $all_rows[0]['discount']; 
        $this->_price = $all_rows[0]['price']; 
        $this->_vatzone = $all_rows[0]['vatzone']; 
        $this->_catalogueid = $all_rows[0]['catalogueid']; 
        $this->_expires = $all_rows[0]['expires']; 
        $this->_username = $all_rows[0]['username']; 
        $this->_password = $all_rows[0]['password']; 
        $this->_user = $all_rows[0]['user']; 
        $this->_userdataentry = $all_rows[0]['userdataentry']; 
        $this->_recalldate = $all_rows[0]['recalldate']; 
        $this->_recalltime = $all_rows[0]['recalltime']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_history = $all_rows[0]['history']; 
        $this->_show_phone1 = $all_rows[0]['show_phone1']; 
        $this->_show_phone2 = $all_rows[0]['show_phone2']; 
        $this->_show_mobilephone = $all_rows[0]['show_mobilephone']; 
        $this->_show_email = $all_rows[0]['show_email']; 
        $this->_LinkedIn = $all_rows[0]['LinkedIn']; 
        $this->_linkedin_dm = $all_rows[0]['linkedin_dm']; 
        $this->_ShortDescription = $all_rows[0]['ShortDescription']; 
        $this->_FullDescription = $all_rows[0]['FullDescription']; 
        $this->_DeliveryDate = $all_rows[0]['DeliveryDate']; 
        $this->_DeliveryTime = $all_rows[0]['DeliveryTime']; 
        $this->_DeliveryNotes = $all_rows[0]['DeliveryNotes']; 
        $this->_subcategory = $all_rows[0]['subcategory']; 
        $this->_dataentrydatetime = $all_rows[0]['dataentrydatetime']; 
        $this->_voucherid = $all_rows[0]['voucherid']; 
        $this->_invoiceid = $all_rows[0]['invoiceid']; 
        $this->_invoiceprinted = $all_rows[0]['invoiceprinted']; 
        $this->_afm = $all_rows[0]['afm']; 
        $this->_doy = $all_rows[0]['doy']; 
        $this->_eponimia = $all_rows[0]['eponimia']; 
        $this->_courier_ok = $all_rows[0]['courier_ok']; 
        $this->_courier_notes = $all_rows[0]['courier_notes']; 
        $this->_courier_return = $all_rows[0]['courier_return']; 
        $this->_courier_delivery_date = $all_rows[0]['courier_delivery_date']; 
        $this->_courier_status = $all_rows[0]['courier_status']; 
        $this->_lockedbyuser = $all_rows[0]['lockedbyuser']; 
        $this->_lockuser = $all_rows[0]['lockuser']; 
        $this->_onlinestatus = $all_rows[0]['onlinestatus']; 
        $this->_onlinedatetime = $all_rows[0]['onlinedatetime']; 
        $this->_phonecode = $all_rows[0]['phonecode']; 
        $this->_company_type = $all_rows[0]['company_type']; 
        $this->_seo_manually_set = $all_rows[0]['seo_manually_set']; 
        $this->_courier = $all_rows[0]['courier']; 
        $this->_city_id = $all_rows[0]['city_id']; 
        $this->_vn_category = $all_rows[0]['vn_category']; 
        $this->_vn_keywords = $all_rows[0]['vn_keywords']; 
        $this->_vn_expires = $all_rows[0]['vn_expires']; 
        $this->_phone1digits = $all_rows[0]['phone1digits']; 
        $this->_phone2digits = $all_rows[0]['phone2digits']; 
        $this->_faxdigits = $all_rows[0]['faxdigits']; 
        $this->_mobiledigits = $all_rows[0]['mobiledigits']; 
        $this->_companyname_dm = $all_rows[0]['companyname_dm']; 
        $this->_address_dm = $all_rows[0]['address_dm']; 
        $this->_phone1_dm = $all_rows[0]['phone1_dm']; 
        $this->_phone2_dm = $all_rows[0]['phone2_dm']; 
        $this->_fax_dm = $all_rows[0]['fax_dm']; 
        $this->_email_dm = $all_rows[0]['email_dm']; 
        $this->_mobile_dm = $all_rows[0]['mobile_dm']; 
        $this->_website_dm = $all_rows[0]['website_dm']; 
        $this->_geox_dm = $all_rows[0]['geox_dm']; 
        $this->_geoy_dm = $all_rows[0]['geoy_dm']; 
        $this->_zipcode_dm = $all_rows[0]['zipcode_dm']; 
        $this->_facebook_dm = $all_rows[0]['facebook_dm']; 
        $this->_twitter_dm = $all_rows[0]['twitter_dm']; 
        $this->_shortdescr_dm = $all_rows[0]['shortdescr_dm']; 
        $this->_fulldescr_dm = $all_rows[0]['fulldescr_dm']; 
        $this->_basiccat_dm = $all_rows[0]['basiccat_dm']; 
        $this->_area_dm = $all_rows[0]['area_dm']; 
        $this->_keywords_dm = $all_rows[0]['keywords_dm']; 
        $this->_cityid_dm = $all_rows[0]['cityid_dm']; 
        $this->_CUSID = $all_rows[0]['CUSID']; 
        $this->_allphonesdigits = $all_rows[0]['allphonesdigits']; 
        $this->_nodoubles = $all_rows[0]['nodoubles']; 
        $this->_lastactiondate = $all_rows[0]['lastactiondate']; 
        $this->_haswebsite = $all_rows[0]['haswebsite']; 
        $this->_profession = $all_rows[0]['profession']; 
        $this->_profession_dm = $all_rows[0]['profession_dm']; 
        $this->_password_dm = $all_rows[0]['password_dm']; 
        $this->_expires_dm = $all_rows[0]['expires_dm']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_googleplus = $all_rows[0]['googleplus']; 
        $this->_googleplus_dm = $all_rows[0]['googleplus_dm']; 
        $this->_pinterest = $all_rows[0]['pinterest']; 
        $this->_pinterest_dm = $all_rows[0]['pinterest_dm']; 
        $this->_sites = $all_rows[0]['sites']; 
        $this->_sites_dm = $all_rows[0]['sites_dm']; 
        $this->_workinghours = $all_rows[0]['workinghours']; 
        $this->_workinghours_dm = $all_rows[0]['workinghours_dm']; 
        $this->_workingmonths = $all_rows[0]['workingmonths']; 
        $this->_workingmonths_dm = $all_rows[0]['workingmonths_dm']; 
        $this->_domain = $all_rows[0]['domain']; 
        $this->_domain_name = $all_rows[0]['domain_name']; 
        $this->_domain_expires = $all_rows[0]['domain_expires']; 
        $this->_languages = $all_rows[0]['languages']; 
        $this->_languages_dm = $all_rows[0]['languages_dm']; 
        $this->_commstatus = $all_rows[0]['commstatus']; 
        $this->_lastactiondate1 = $all_rows[0]['lastactiondate1']; 
        $this->_lastactiondate2 = $all_rows[0]['lastactiondate2']; 
        $this->_lastactiondate3 = $all_rows[0]['lastactiondate3']; 
        $this->_package2 = $all_rows[0]['package2']; 
        $this->_discount2 = $all_rows[0]['discount2']; 
        $this->_price2 = $all_rows[0]['price2']; 
        $this->_epag_id = $all_rows[0]['epag_id']; 
        $this->_epag_status = $all_rows[0]['epag_status']; 
        $this->_epag_expires = $all_rows[0]['epag_expires']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_companyname() { 
    return $this->_companyname; 
} 
public function set_companyname($val) { 
    $this->_companyname = $val; 
} 

public function get_companyname_en() { 
    return $this->_companyname_en; 
} 
public function set_companyname_en($val) { 
    $this->_companyname_en = $val; 
} 

public function get_phone1() { 
    return $this->_phone1; 
} 
public function set_phone1($val) { 
    $this->_phone1 = $val; 
} 

public function get_phone2() { 
    return $this->_phone2; 
} 
public function set_phone2($val) { 
    $this->_phone2 = $val; 
} 

public function get_fax() { 
    return $this->_fax; 
} 
public function set_fax($val) { 
    $this->_fax = $val; 
} 

public function get_mobilephone() { 
    return $this->_mobilephone; 
} 
public function set_mobilephone($val) { 
    $this->_mobilephone = $val; 
} 

public function get_contactperson() { 
    return $this->_contactperson; 
} 
public function set_contactperson($val) { 
    $this->_contactperson = $val; 
} 

public function get_basiccategory() { 
    return $this->_basiccategory; 
} 
public function set_basiccategory($val) { 
    $this->_basiccategory = $val; 
} 

public function get_reference() { 
    return $this->_reference; 
} 
public function set_reference($val) { 
    $this->_reference = $val; 
} 

public function get_area() { 
    return $this->_area; 
} 
public function set_area($val) { 
    $this->_area = $val; 
} 

public function get_geo_x() { 
    return $this->_geo_x; 
} 
public function set_geo_x($val) { 
    $this->_geo_x = $val; 
} 

public function get_geo_y() { 
    return $this->_geo_y; 
} 
public function set_geo_y($val) { 
    $this->_geo_y = $val; 
} 

public function get_address() { 
    return $this->_address; 
} 
public function set_address($val) { 
    $this->_address = $val; 
} 

public function get_zipcode() { 
    return $this->_zipcode; 
} 
public function set_zipcode($val) { 
    $this->_zipcode = $val; 
} 

public function get_email() { 
    return $this->_email; 
} 
public function set_email($val) { 
    $this->_email = $val; 
} 

public function get_website() { 
    return $this->_website; 
} 
public function set_website($val) { 
    $this->_website = $val; 
} 

public function get_facebook() { 
    return $this->_facebook; 
} 
public function set_facebook($val) { 
    $this->_facebook = $val; 
} 

public function get_twitter() { 
    return $this->_twitter; 
} 
public function set_twitter($val) { 
    $this->_twitter = $val; 
} 

public function get_package() { 
    return $this->_package; 
} 
public function set_package($val) { 
    $this->_package = $val; 
} 

public function get_discount() { 
    return $this->_discount; 
} 
public function set_discount($val) { 
    $this->_discount = $val; 
} 

public function get_price() { 
    return $this->_price; 
} 
public function set_price($val) { 
    $this->_price = $val; 
} 

public function get_vatzone() { 
    return $this->_vatzone; 
} 
public function set_vatzone($val) { 
    $this->_vatzone = $val; 
} 

public function get_catalogueid() { 
    return $this->_catalogueid; 
} 
public function set_catalogueid($val) { 
    $this->_catalogueid = $val; 
} 

public function get_expires() { 
    return $this->_expires; 
} 
public function set_expires($val) { 
    $this->_expires = $val; 
} 

public function get_username() { 
    return $this->_username; 
} 
public function set_username($val) { 
    $this->_username = $val; 
} 

public function get_password() { 
    return $this->_password; 
} 
public function set_password($val) { 
    $this->_password = $val; 
} 

public function get_user() { 
    return $this->_user; 
} 
public function set_user($val) { 
    $this->_user = $val; 
} 

public function get_userdataentry() { 
    return $this->_userdataentry; 
} 
public function set_userdataentry($val) { 
    $this->_userdataentry = $val; 
} 

public function get_recalldate() { 
    return $this->_recalldate; 
} 
public function set_recalldate($val) { 
    $this->_recalldate = $val; 
} 

public function get_recalltime() { 
    return $this->_recalltime; 
} 
public function set_recalltime($val) { 
    $this->_recalltime = $val; 
} 

public function get_status() { 
    return $this->_status; 
} 
public function set_status($val) { 
    $this->_status = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_history() { 
    return $this->_history; 
} 
public function set_history($val) { 
    $this->_history = $val; 
} 

public function get_show_phone1() { 
    return $this->_show_phone1; 
} 
public function set_show_phone1($val) { 
    $this->_show_phone1 = $val; 
} 

public function get_show_phone2() { 
    return $this->_show_phone2; 
} 
public function set_show_phone2($val) { 
    $this->_show_phone2 = $val; 
} 

public function get_show_mobilephone() { 
    return $this->_show_mobilephone; 
} 
public function set_show_mobilephone($val) { 
    $this->_show_mobilephone = $val; 
} 

public function get_show_email() { 
    return $this->_show_email; 
} 
public function set_show_email($val) { 
    $this->_show_email = $val; 
} 

public function get_LinkedIn() { 
    return $this->_LinkedIn; 
} 
public function set_LinkedIn($val) { 
    $this->_LinkedIn = $val; 
} 

public function get_linkedin_dm() { 
    return $this->_linkedin_dm; 
} 
public function set_linkedin_dm($val) { 
    $this->_linkedin_dm = $val; 
} 

public function get_ShortDescription() { 
    return $this->_ShortDescription; 
} 
public function set_ShortDescription($val) { 
    $this->_ShortDescription = $val; 
} 

public function get_FullDescription() { 
    return $this->_FullDescription; 
} 
public function set_FullDescription($val) { 
    $this->_FullDescription = $val; 
} 

public function get_DeliveryDate() { 
    return $this->_DeliveryDate; 
} 
public function set_DeliveryDate($val) { 
    $this->_DeliveryDate = $val; 
} 

public function get_DeliveryTime() { 
    return $this->_DeliveryTime; 
} 
public function set_DeliveryTime($val) { 
    $this->_DeliveryTime = $val; 
} 

public function get_DeliveryNotes() { 
    return $this->_DeliveryNotes; 
} 
public function set_DeliveryNotes($val) { 
    $this->_DeliveryNotes = $val; 
} 

public function get_subcategory() { 
    return $this->_subcategory; 
} 
public function set_subcategory($val) { 
    $this->_subcategory = $val; 
} 

public function get_dataentrydatetime() { 
    return $this->_dataentrydatetime; 
} 
public function set_dataentrydatetime($val) { 
    $this->_dataentrydatetime = $val; 
} 

public function get_voucherid() { 
    return $this->_voucherid; 
} 
public function set_voucherid($val) { 
    $this->_voucherid = $val; 
} 

public function get_invoiceid() { 
    return $this->_invoiceid; 
} 
public function set_invoiceid($val) { 
    $this->_invoiceid = $val; 
} 

public function get_invoiceprinted() { 
    return $this->_invoiceprinted; 
} 
public function set_invoiceprinted($val) { 
    $this->_invoiceprinted = $val; 
} 

public function get_afm() { 
    return $this->_afm; 
} 
public function set_afm($val) { 
    $this->_afm = $val; 
} 

public function get_doy() { 
    return $this->_doy; 
} 
public function set_doy($val) { 
    $this->_doy = $val; 
} 

public function get_eponimia() { 
    return $this->_eponimia; 
} 
public function set_eponimia($val) { 
    $this->_eponimia = $val; 
} 

public function get_courier_ok() { 
    return $this->_courier_ok; 
} 
public function set_courier_ok($val) { 
    $this->_courier_ok = $val; 
} 

public function get_courier_notes() { 
    return $this->_courier_notes; 
} 
public function set_courier_notes($val) { 
    $this->_courier_notes = $val; 
} 

public function get_courier_return() { 
    return $this->_courier_return; 
} 
public function set_courier_return($val) { 
    $this->_courier_return = $val; 
} 

public function get_courier_delivery_date() { 
    return $this->_courier_delivery_date; 
} 
public function set_courier_delivery_date($val) { 
    $this->_courier_delivery_date = $val; 
} 

public function get_courier_status() { 
    return $this->_courier_status; 
} 
public function set_courier_status($val) { 
    $this->_courier_status = $val; 
} 

public function get_lockedbyuser() { 
    return $this->_lockedbyuser; 
} 
public function set_lockedbyuser($val) { 
    $this->_lockedbyuser = $val; 
} 

public function get_lockuser() { 
    return $this->_lockuser; 
} 
public function set_lockuser($val) { 
    $this->_lockuser = $val; 
} 

public function get_onlinestatus() { 
    return $this->_onlinestatus; 
} 
public function set_onlinestatus($val) { 
    $this->_onlinestatus = $val; 
} 

public function get_onlinedatetime() { 
    return $this->_onlinedatetime; 
} 
public function set_onlinedatetime($val) { 
    $this->_onlinedatetime = $val; 
} 

public function get_phonecode() { 
    return $this->_phonecode; 
} 
public function set_phonecode($val) { 
    $this->_phonecode = $val; 
} 

public function get_company_type() { 
    return $this->_company_type; 
} 
public function set_company_type($val) { 
    $this->_company_type = $val; 
} 

public function get_seo_manually_set() { 
    return $this->_seo_manually_set; 
} 
public function set_seo_manually_set($val) { 
    $this->_seo_manually_set = $val; 
} 

public function get_courier() { 
    return $this->_courier; 
} 
public function set_courier($val) { 
    $this->_courier = $val; 
} 

public function get_city_id() { 
    return $this->_city_id; 
} 
public function set_city_id($val) { 
    $this->_city_id = $val; 
} 

public function get_vn_category() { 
    return $this->_vn_category; 
} 
public function set_vn_category($val) { 
    $this->_vn_category = $val; 
} 

public function get_vn_keywords() { 
    return $this->_vn_keywords; 
} 
public function set_vn_keywords($val) { 
    $this->_vn_keywords = $val; 
} 

public function get_vn_expires() { 
    return $this->_vn_expires; 
} 
public function set_vn_expires($val) { 
    $this->_vn_expires = $val; 
} 

public function get_phone1digits() { 
    return $this->_phone1digits; 
} 
public function set_phone1digits($val) { 
    $this->_phone1digits = $val; 
} 

public function get_phone2digits() { 
    return $this->_phone2digits; 
} 
public function set_phone2digits($val) { 
    $this->_phone2digits = $val; 
} 

public function get_faxdigits() { 
    return $this->_faxdigits; 
} 
public function set_faxdigits($val) { 
    $this->_faxdigits = $val; 
} 

public function get_mobiledigits() { 
    return $this->_mobiledigits; 
} 
public function set_mobiledigits($val) { 
    $this->_mobiledigits = $val; 
} 

public function get_companyname_dm() { 
    return $this->_companyname_dm; 
} 
public function set_companyname_dm($val) { 
    $this->_companyname_dm = $val; 
} 

public function get_address_dm() { 
    return $this->_address_dm; 
} 
public function set_address_dm($val) { 
    $this->_address_dm = $val; 
} 

public function get_phone1_dm() { 
    return $this->_phone1_dm; 
} 
public function set_phone1_dm($val) { 
    $this->_phone1_dm = $val; 
} 

public function get_phone2_dm() { 
    return $this->_phone2_dm; 
} 
public function set_phone2_dm($val) { 
    $this->_phone2_dm = $val; 
} 

public function get_fax_dm() { 
    return $this->_fax_dm; 
} 
public function set_fax_dm($val) { 
    $this->_fax_dm = $val; 
} 

public function get_email_dm() { 
    return $this->_email_dm; 
} 
public function set_email_dm($val) { 
    $this->_email_dm = $val; 
} 

public function get_mobile_dm() { 
    return $this->_mobile_dm; 
} 
public function set_mobile_dm($val) { 
    $this->_mobile_dm = $val; 
} 

public function get_website_dm() { 
    return $this->_website_dm; 
} 
public function set_website_dm($val) { 
    $this->_website_dm = $val; 
} 

public function get_geox_dm() { 
    return $this->_geox_dm; 
} 
public function set_geox_dm($val) { 
    $this->_geox_dm = $val; 
} 

public function get_geoy_dm() { 
    return $this->_geoy_dm; 
} 
public function set_geoy_dm($val) { 
    $this->_geoy_dm = $val; 
} 

public function get_zipcode_dm() { 
    return $this->_zipcode_dm; 
} 
public function set_zipcode_dm($val) { 
    $this->_zipcode_dm = $val; 
} 

public function get_facebook_dm() { 
    return $this->_facebook_dm; 
} 
public function set_facebook_dm($val) { 
    $this->_facebook_dm = $val; 
} 

public function get_twitter_dm() { 
    return $this->_twitter_dm; 
} 
public function set_twitter_dm($val) { 
    $this->_twitter_dm = $val; 
} 

public function get_shortdescr_dm() { 
    return $this->_shortdescr_dm; 
} 
public function set_shortdescr_dm($val) { 
    $this->_shortdescr_dm = $val; 
} 

public function get_fulldescr_dm() { 
    return $this->_fulldescr_dm; 
} 
public function set_fulldescr_dm($val) { 
    $this->_fulldescr_dm = $val; 
} 

public function get_basiccat_dm() { 
    return $this->_basiccat_dm; 
} 
public function set_basiccat_dm($val) { 
    $this->_basiccat_dm = $val; 
} 

public function get_area_dm() { 
    return $this->_area_dm; 
} 
public function set_area_dm($val) { 
    $this->_area_dm = $val; 
} 

public function get_keywords_dm() { 
    return $this->_keywords_dm; 
} 
public function set_keywords_dm($val) { 
    $this->_keywords_dm = $val; 
} 

public function get_cityid_dm() { 
    return $this->_cityid_dm; 
} 
public function set_cityid_dm($val) { 
    $this->_cityid_dm = $val; 
} 

public function get_CUSID() { 
    return $this->_CUSID; 
} 
public function set_CUSID($val) { 
    $this->_CUSID = $val; 
} 

public function get_allphonesdigits() { 
    return $this->_allphonesdigits; 
} 
public function set_allphonesdigits($val) { 
    $this->_allphonesdigits = $val; 
} 

public function get_nodoubles() { 
    return $this->_nodoubles; 
} 
public function set_nodoubles($val) { 
    $this->_nodoubles = $val; 
} 

public function get_lastactiondate() { 
    return $this->_lastactiondate; 
} 
public function set_lastactiondate($val) { 
    $this->_lastactiondate = $val; 
} 

public function get_haswebsite() { 
    return $this->_haswebsite; 
} 
public function set_haswebsite($val) { 
    $this->_haswebsite = $val; 
} 

public function get_profession() { 
    return $this->_profession; 
} 
public function set_profession($val) { 
    $this->_profession = $val; 
} 

public function get_profession_dm() { 
    return $this->_profession_dm; 
} 
public function set_profession_dm($val) { 
    $this->_profession_dm = $val; 
} 

public function get_password_dm() { 
    return $this->_password_dm; 
} 
public function set_password_dm($val) { 
    $this->_password_dm = $val; 
} 

public function get_expires_dm() { 
    return $this->_expires_dm; 
} 
public function set_expires_dm($val) { 
    $this->_expires_dm = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_googleplus() { 
    return $this->_googleplus; 
} 
public function set_googleplus($val) { 
    $this->_googleplus = $val; 
} 

public function get_googleplus_dm() { 
    return $this->_googleplus_dm; 
} 
public function set_googleplus_dm($val) { 
    $this->_googleplus_dm = $val; 
} 

public function get_pinterest() { 
    return $this->_pinterest; 
} 
public function set_pinterest($val) { 
    $this->_pinterest = $val; 
} 

public function get_pinterest_dm() { 
    return $this->_pinterest_dm; 
} 
public function set_pinterest_dm($val) { 
    $this->_pinterest_dm = $val; 
} 

public function get_sites() { 
    return $this->_sites; 
} 
public function set_sites($val) { 
    $this->_sites = $val; 
} 

public function get_sites_dm() { 
    return $this->_sites_dm; 
} 
public function set_sites_dm($val) { 
    $this->_sites_dm = $val; 
} 

public function get_workinghours() { 
    return $this->_workinghours; 
} 
public function set_workinghours($val) { 
    $this->_workinghours = $val; 
} 

public function get_workinghours_dm() { 
    return $this->_workinghours_dm; 
} 
public function set_workinghours_dm($val) { 
    $this->_workinghours_dm = $val; 
} 

public function get_workingmonths() { 
    return $this->_workingmonths; 
} 
public function set_workingmonths($val) { 
    $this->_workingmonths = $val; 
} 

public function get_workingmonths_dm() { 
    return $this->_workingmonths_dm; 
} 
public function set_workingmonths_dm($val) { 
    $this->_workingmonths_dm = $val; 
} 

public function get_domain() { 
    return $this->_domain; 
} 
public function set_domain($val) { 
    $this->_domain = $val; 
} 

public function get_domain_name() { 
    return $this->_domain_name; 
} 
public function set_domain_name($val) { 
    $this->_domain_name = $val; 
} 

public function get_domain_expires() { 
    return $this->_domain_expires; 
} 
public function set_domain_expires($val) { 
    $this->_domain_expires = $val; 
} 

public function get_languages() { 
    return $this->_languages; 
} 
public function set_languages($val) { 
    $this->_languages = $val; 
} 

public function get_languages_dm() { 
    return $this->_languages_dm; 
} 
public function set_languages_dm($val) { 
    $this->_languages_dm = $val; 
} 

public function get_commstatus() { 
    return $this->_commstatus; 
} 
public function set_commstatus($val) { 
    $this->_commstatus = $val; 
} 

public function get_lastactiondate1() { 
    return $this->_lastactiondate1; 
} 
public function set_lastactiondate1($val) { 
    $this->_lastactiondate1 = $val; 
} 

public function get_lastactiondate2() { 
    return $this->_lastactiondate2; 
} 
public function set_lastactiondate2($val) { 
    $this->_lastactiondate2 = $val; 
} 

public function get_lastactiondate3() { 
    return $this->_lastactiondate3; 
} 
public function set_lastactiondate3($val) { 
    $this->_lastactiondate3 = $val; 
} 

public function get_package2() { 
    return $this->_package2; 
} 
public function set_package2($val) { 
    $this->_package2 = $val; 
} 

public function get_discount2() { 
    return $this->_discount2; 
} 
public function set_discount2($val) { 
    $this->_discount2 = $val; 
} 

public function get_price2() { 
    return $this->_price2; 
} 
public function set_price2($val) { 
    $this->_price2 = $val; 
} 

public function get_epag_id() { 
    return $this->_epag_id; 
} 
public function set_epag_id($val) { 
    $this->_epag_id = $val; 
} 

public function get_epag_status() { 
    return $this->_epag_status; 
} 
public function set_epag_status($val) { 
    $this->_epag_status = $val; 
} 

public function get_epag_expires() { 
    return $this->_epag_expires; 
} 
public function set_epag_expires($val) { 
    $this->_epag_expires = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO COMPANIES ( 
    companyname,
    companyname_en,
    phone1,
    phone2,
    fax,
    mobilephone,
    contactperson,
    basiccategory,
    reference,
    area,
    geo_x,
    geo_y,
    address,
    zipcode,
    email,
    website,
    facebook,
    twitter,
    package,
    discount,
    price,
    vatzone,
    catalogueid,
    expires,
    username,
    password,
    user,
    userdataentry,
    recalldate,
    recalltime,
    status,
    comment,
    history,
    show_phone1,
    show_phone2,
    show_mobilephone,
    show_email,
    LinkedIn,
    linkedin_dm,
    ShortDescription,
    FullDescription,
    DeliveryDate,
    DeliveryTime,
    DeliveryNotes,
    subcategory,
    dataentrydatetime,
    voucherid,
    invoiceid,
    invoiceprinted,
    afm,
    doy,
    eponimia,
    courier_ok,
    courier_notes,
    courier_return,
    courier_delivery_date,
    courier_status,
    lockedbyuser,
    lockuser,
    onlinestatus,
    onlinedatetime,
    phonecode,
    company_type,
    seo_manually_set,
    courier,
    city_id,
    vn_category,
    vn_keywords,
    vn_expires,
    phone1digits,
    phone2digits,
    faxdigits,
    mobiledigits,
    companyname_dm,
    address_dm,
    phone1_dm,
    phone2_dm,
    fax_dm,
    email_dm,
    mobile_dm,
    website_dm,
    geox_dm,
    geoy_dm,
    zipcode_dm,
    facebook_dm,
    twitter_dm,
    shortdescr_dm,
    fulldescr_dm,
    basiccat_dm,
    area_dm,
    keywords_dm,
    cityid_dm,
    CUSID,
    allphonesdigits,
    nodoubles,
    lastactiondate,
    haswebsite,
    profession,
    profession_dm,
    password_dm,
    expires_dm,
    active,
    googleplus,
    googleplus_dm,
    pinterest,
    pinterest_dm,
    sites,
    sites_dm,
    workinghours,
    workinghours_dm,
    workingmonths,
    workingmonths_dm,
    domain,
    domain_name,
    domain_expires,
    languages,
    languages_dm,
    commstatus,
    lastactiondate1,
    lastactiondate2,
    lastactiondate3,
    package2,
    discount2,
    price2,
    epag_id,
    epag_status,
    epag_expires
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyname, 
        $this->_companyname_en, 
        $this->_phone1, 
        $this->_phone2, 
        $this->_fax, 
        $this->_mobilephone, 
        $this->_contactperson, 
        $this->_basiccategory, 
        $this->_reference, 
        $this->_area, 
        $this->_geo_x, 
        $this->_geo_y, 
        $this->_address, 
        $this->_zipcode, 
        $this->_email, 
        $this->_website, 
        $this->_facebook, 
        $this->_twitter, 
        $this->_package, 
        $this->_discount, 
        $this->_price, 
        $this->_vatzone, 
        $this->_catalogueid, 
        $this->_expires, 
        $this->_username, 
        $this->_password, 
        $this->_user, 
        $this->_userdataentry, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_status, 
        $this->_comment, 
        $this->_history, 
        $this->_show_phone1, 
        $this->_show_phone2, 
        $this->_show_mobilephone, 
        $this->_show_email, 
        $this->_LinkedIn, 
        $this->_linkedin_dm, 
        $this->_ShortDescription, 
        $this->_FullDescription, 
        $this->_DeliveryDate, 
        $this->_DeliveryTime, 
        $this->_DeliveryNotes, 
        $this->_subcategory, 
        $this->_dataentrydatetime, 
        $this->_voucherid, 
        $this->_invoiceid, 
        $this->_invoiceprinted, 
        $this->_afm, 
        $this->_doy, 
        $this->_eponimia, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_lockedbyuser, 
        $this->_lockuser, 
        $this->_onlinestatus, 
        $this->_onlinedatetime, 
        $this->_phonecode, 
        $this->_company_type, 
        $this->_seo_manually_set, 
        $this->_courier, 
        $this->_city_id, 
        $this->_vn_category, 
        $this->_vn_keywords, 
        $this->_vn_expires, 
        $this->_phone1digits, 
        $this->_phone2digits, 
        $this->_faxdigits, 
        $this->_mobiledigits, 
        $this->_companyname_dm, 
        $this->_address_dm, 
        $this->_phone1_dm, 
        $this->_phone2_dm, 
        $this->_fax_dm, 
        $this->_email_dm, 
        $this->_mobile_dm, 
        $this->_website_dm, 
        $this->_geox_dm, 
        $this->_geoy_dm, 
        $this->_zipcode_dm, 
        $this->_facebook_dm, 
        $this->_twitter_dm, 
        $this->_shortdescr_dm, 
        $this->_fulldescr_dm, 
        $this->_basiccat_dm, 
        $this->_area_dm, 
        $this->_keywords_dm, 
        $this->_cityid_dm, 
        $this->_CUSID, 
        $this->_allphonesdigits, 
        $this->_nodoubles, 
        $this->_lastactiondate, 
        $this->_haswebsite, 
        $this->_profession, 
        $this->_profession_dm, 
        $this->_password_dm, 
        $this->_expires_dm, 
        $this->_active, 
        $this->_googleplus, 
        $this->_googleplus_dm, 
        $this->_pinterest, 
        $this->_pinterest_dm, 
        $this->_sites, 
        $this->_sites_dm, 
        $this->_workinghours, 
        $this->_workinghours_dm, 
        $this->_workingmonths, 
        $this->_workingmonths_dm, 
        $this->_domain, 
        $this->_domain_name, 
        $this->_domain_expires, 
        $this->_languages, 
        $this->_languages_dm, 
        $this->_commstatus, 
        $this->_lastactiondate1, 
        $this->_lastactiondate2, 
        $this->_lastactiondate3, 
        $this->_package2, 
        $this->_discount2, 
        $this->_price2, 
        $this->_epag_id, 
        $this->_epag_status, 
        $this->_epag_expires)); 
    $ssql = $this->_myconn->getLastIDsql('COMPANIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE COMPANIES set 
        companyname = ?, 
        companyname_en = ?, 
        phone1 = ?, 
        phone2 = ?, 
        fax = ?, 
        mobilephone = ?, 
        contactperson = ?, 
        basiccategory = ?, 
        reference = ?, 
        area = ?, 
        geo_x = ?, 
        geo_y = ?, 
        address = ?, 
        zipcode = ?, 
        email = ?, 
        website = ?, 
        facebook = ?, 
        twitter = ?, 
        package = ?, 
        discount = ?, 
        price = ?, 
        vatzone = ?, 
        catalogueid = ?, 
        expires = ?, 
        username = ?, 
        password = ?, 
        user = ?, 
        userdataentry = ?, 
        recalldate = ?, 
        recalltime = ?, 
        status = ?, 
        comment = ?, 
        history = ?, 
        show_phone1 = ?, 
        show_phone2 = ?, 
        show_mobilephone = ?, 
        show_email = ?, 
        LinkedIn = ?, 
        linkedin_dm = ?, 
        ShortDescription = ?, 
        FullDescription = ?, 
        DeliveryDate = ?, 
        DeliveryTime = ?, 
        DeliveryNotes = ?, 
        subcategory = ?, 
        dataentrydatetime = ?, 
        voucherid = ?, 
        invoiceid = ?, 
        invoiceprinted = ?, 
        afm = ?, 
        doy = ?, 
        eponimia = ?, 
        courier_ok = ?, 
        courier_notes = ?, 
        courier_return = ?, 
        courier_delivery_date = ?, 
        courier_status = ?, 
        lockedbyuser = ?, 
        lockuser = ?, 
        onlinestatus = ?, 
        onlinedatetime = ?, 
        phonecode = ?, 
        company_type = ?, 
        seo_manually_set = ?, 
        courier = ?, 
        city_id = ?, 
        vn_category = ?, 
        vn_keywords = ?, 
        vn_expires = ?, 
        phone1digits = ?, 
        phone2digits = ?, 
        faxdigits = ?, 
        mobiledigits = ?, 
        companyname_dm = ?, 
        address_dm = ?, 
        phone1_dm = ?, 
        phone2_dm = ?, 
        fax_dm = ?, 
        email_dm = ?, 
        mobile_dm = ?, 
        website_dm = ?, 
        geox_dm = ?, 
        geoy_dm = ?, 
        zipcode_dm = ?, 
        facebook_dm = ?, 
        twitter_dm = ?, 
        shortdescr_dm = ?, 
        fulldescr_dm = ?, 
        basiccat_dm = ?, 
        area_dm = ?, 
        keywords_dm = ?, 
        cityid_dm = ?, 
        CUSID = ?, 
        allphonesdigits = ?, 
        nodoubles = ?, 
        lastactiondate = ?, 
        haswebsite = ?, 
        profession = ?, 
        profession_dm = ?, 
        password_dm = ?, 
        expires_dm = ?, 
        active = ?, 
        googleplus = ?, 
        googleplus_dm = ?, 
        pinterest = ?, 
        pinterest_dm = ?, 
        sites = ?, 
        sites_dm = ?, 
        workinghours = ?, 
        workinghours_dm = ?, 
        workingmonths = ?, 
        workingmonths_dm = ?, 
        domain = ?, 
        domain_name = ?, 
        domain_expires = ?, 
        languages = ?, 
        languages_dm = ?, 
        commstatus = ?, 
        lastactiondate1 = ?, 
        lastactiondate2 = ?, 
        lastactiondate3 = ?, 
        package2 = ?, 
        discount2 = ?, 
        price2 = ?, 
        epag_id = ?, 
        epag_status = ?, 
        epag_expires = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyname, 
        $this->_companyname_en, 
        $this->_phone1, 
        $this->_phone2, 
        $this->_fax, 
        $this->_mobilephone, 
        $this->_contactperson, 
        $this->_basiccategory, 
        $this->_reference, 
        $this->_area, 
        $this->_geo_x, 
        $this->_geo_y, 
        $this->_address, 
        $this->_zipcode, 
        $this->_email, 
        $this->_website, 
        $this->_facebook, 
        $this->_twitter, 
        $this->_package, 
        $this->_discount, 
        $this->_price, 
        $this->_vatzone, 
        $this->_catalogueid, 
        $this->_expires, 
        $this->_username, 
        $this->_password, 
        $this->_user, 
        $this->_userdataentry, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_status, 
        $this->_comment, 
        $this->_history, 
        $this->_show_phone1, 
        $this->_show_phone2, 
        $this->_show_mobilephone, 
        $this->_show_email, 
        $this->_LinkedIn, 
        $this->_linkedin_dm, 
        $this->_ShortDescription, 
        $this->_FullDescription, 
        $this->_DeliveryDate, 
        $this->_DeliveryTime, 
        $this->_DeliveryNotes, 
        $this->_subcategory, 
        $this->_dataentrydatetime, 
        $this->_voucherid, 
        $this->_invoiceid, 
        $this->_invoiceprinted, 
        $this->_afm, 
        $this->_doy, 
        $this->_eponimia, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_lockedbyuser, 
        $this->_lockuser, 
        $this->_onlinestatus, 
        $this->_onlinedatetime, 
        $this->_phonecode, 
        $this->_company_type, 
        $this->_seo_manually_set, 
        $this->_courier, 
        $this->_city_id, 
        $this->_vn_category, 
        $this->_vn_keywords, 
        $this->_vn_expires, 
        $this->_phone1digits, 
        $this->_phone2digits, 
        $this->_faxdigits, 
        $this->_mobiledigits, 
        $this->_companyname_dm, 
        $this->_address_dm, 
        $this->_phone1_dm, 
        $this->_phone2_dm, 
        $this->_fax_dm, 
        $this->_email_dm, 
        $this->_mobile_dm, 
        $this->_website_dm, 
        $this->_geox_dm, 
        $this->_geoy_dm, 
        $this->_zipcode_dm, 
        $this->_facebook_dm, 
        $this->_twitter_dm, 
        $this->_shortdescr_dm, 
        $this->_fulldescr_dm, 
        $this->_basiccat_dm, 
        $this->_area_dm, 
        $this->_keywords_dm, 
        $this->_cityid_dm, 
        $this->_CUSID, 
        $this->_allphonesdigits, 
        $this->_nodoubles, 
        $this->_lastactiondate, 
        $this->_haswebsite, 
        $this->_profession, 
        $this->_profession_dm, 
        $this->_password_dm, 
        $this->_expires_dm, 
        $this->_active, 
        $this->_googleplus, 
        $this->_googleplus_dm, 
        $this->_pinterest, 
        $this->_pinterest_dm, 
        $this->_sites, 
        $this->_sites_dm, 
        $this->_workinghours, 
        $this->_workinghours_dm, 
        $this->_workingmonths, 
        $this->_workingmonths_dm, 
        $this->_domain, 
        $this->_domain_name, 
        $this->_domain_expires, 
        $this->_languages, 
        $this->_languages_dm, 
        $this->_commstatus, 
        $this->_lastactiondate1, 
        $this->_lastactiondate2, 
        $this->_lastactiondate3, 
        $this->_package2, 
        $this->_discount2, 
        $this->_price2, 
        $this->_epag_id, 
        $this->_epag_status, 
        $this->_epag_expires,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM COMPANIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}





class COMPANIES_EPAG
{

protected $_myconn, $_id, $_companyname, $_companyname_en, $_phone1, $_phone2, $_fax, $_mobilephone, $_contactperson, $_basiccategory, $_reference, $_area, $_geo_x, $_geo_y, $_address, $_zipcode, $_email, $_website, $_facebook, $_twitter, $_package, $_discount, $_price, $_vatzone, $_catalogueid, $_expires, $_username, $_password, $_user, $_userdataentry, $_recalldate, $_recalltime, $_status, $_comment, $_history, $_show_phone1, $_show_phone2, $_show_mobilephone, $_show_email, $_LinkedIn, $_linkedin_dm, $_ShortDescription, $_FullDescription, $_DeliveryDate, $_DeliveryTime, $_DeliveryNotes, $_subcategory, $_dataentrydatetime, $_voucherid, $_invoiceid, $_invoiceprinted, $_afm, $_doy, $_eponimia, $_courier_ok, $_courier_notes, $_courier_return, $_courier_delivery_date, $_courier_status, $_lockedbyuser, $_lockuser, $_onlinestatus, $_onlinedatetime, $_phonecode, $_company_type, $_seo_manually_set, $_courier, $_city_id, $_vn_category, $_vn_keywords, $_vn_expires, $_phone1digits, $_phone2digits, $_faxdigits, $_mobiledigits, $_companyname_dm, $_address_dm, $_phone1_dm, $_phone2_dm, $_fax_dm, $_email_dm, $_mobile_dm, $_website_dm, $_geox_dm, $_geoy_dm, $_zipcode_dm, $_facebook_dm, $_twitter_dm, $_shortdescr_dm, $_fulldescr_dm, $_basiccat_dm, $_area_dm, $_keywords_dm, $_cityid_dm, $_CUSID, $_allphonesdigits, $_nodoubles, $_lastactiondate, $_haswebsite, $_profession, $_profession_dm, $_password_dm, $_expires_dm, $_active, $_googleplus, $_googleplus_dm, $_pinterest, $_pinterest_dm, $_sites, $_sites_dm, $_workinghours, $_workinghours_dm, $_workingmonths, $_workingmonths_dm, $_domain, $_domain_name, $_domain_expires, $_languages, $_languages_dm, $_commstatus, $_lastactiondate1, $_lastactiondate2, $_lastactiondate3, $_package2, $_discount2, $_price2 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM COMPANIES WHERE id=?"; 
        $all_rows = $this->_myconn->getRS($ssql, array($_id)); 
        } 
    else if ($_ssql!='') { 
        $ssql = $_ssql; 
        $all_rows = $this->_myconn->getRS($ssql); 
        } 
    else { 
        $rows = $my_rows; 
        $all_rows = arrayfunctions::filter_by_value($rows, 'id', $this->_id); 
    }
    $icount = count($all_rows); 

    if ($icount==1) { 
        $this->_companyname = $all_rows[0]['companyname']; 
        $this->_companyname_en = $all_rows[0]['companyname_en']; 
        $this->_phone1 = $all_rows[0]['phone1']; 
        $this->_phone2 = $all_rows[0]['phone2']; 
        $this->_fax = $all_rows[0]['fax']; 
        $this->_mobilephone = $all_rows[0]['mobilephone']; 
        $this->_contactperson = $all_rows[0]['contactperson']; 
        $this->_basiccategory = $all_rows[0]['basiccategory']; 
        $this->_reference = $all_rows[0]['reference']; 
        $this->_area = $all_rows[0]['area']; 
        $this->_geo_x = $all_rows[0]['geo_x']; 
        $this->_geo_y = $all_rows[0]['geo_y']; 
        $this->_address = $all_rows[0]['address']; 
        $this->_zipcode = $all_rows[0]['zipcode']; 
        $this->_email = $all_rows[0]['email']; 
        $this->_website = $all_rows[0]['website']; 
        $this->_facebook = $all_rows[0]['facebook']; 
        $this->_twitter = $all_rows[0]['twitter']; 
        $this->_package = $all_rows[0]['package']; 
        $this->_discount = $all_rows[0]['discount']; 
        $this->_price = $all_rows[0]['price']; 
        $this->_vatzone = $all_rows[0]['vatzone']; 
        $this->_catalogueid = $all_rows[0]['catalogueid']; 
        $this->_expires = $all_rows[0]['expires']; 
        $this->_username = $all_rows[0]['username']; 
        $this->_password = $all_rows[0]['password']; 
        $this->_user = $all_rows[0]['user']; 
        $this->_userdataentry = $all_rows[0]['userdataentry']; 
        $this->_recalldate = $all_rows[0]['recalldate']; 
        $this->_recalltime = $all_rows[0]['recalltime']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_history = $all_rows[0]['history']; 
        $this->_show_phone1 = $all_rows[0]['show_phone1']; 
        $this->_show_phone2 = $all_rows[0]['show_phone2']; 
        $this->_show_mobilephone = $all_rows[0]['show_mobilephone']; 
        $this->_show_email = $all_rows[0]['show_email']; 
        $this->_LinkedIn = $all_rows[0]['LinkedIn']; 
        $this->_linkedin_dm = $all_rows[0]['linkedin_dm']; 
        $this->_ShortDescription = $all_rows[0]['ShortDescription']; 
        $this->_FullDescription = $all_rows[0]['FullDescription']; 
        $this->_DeliveryDate = $all_rows[0]['DeliveryDate']; 
        $this->_DeliveryTime = $all_rows[0]['DeliveryTime']; 
        $this->_DeliveryNotes = $all_rows[0]['DeliveryNotes']; 
        $this->_subcategory = $all_rows[0]['subcategory']; 
        $this->_dataentrydatetime = $all_rows[0]['dataentrydatetime']; 
        $this->_voucherid = $all_rows[0]['voucherid']; 
        $this->_invoiceid = $all_rows[0]['invoiceid']; 
        $this->_invoiceprinted = $all_rows[0]['invoiceprinted']; 
        $this->_afm = $all_rows[0]['afm']; 
        $this->_doy = $all_rows[0]['doy']; 
        $this->_eponimia = $all_rows[0]['eponimia']; 
        $this->_courier_ok = $all_rows[0]['courier_ok']; 
        $this->_courier_notes = $all_rows[0]['courier_notes']; 
        $this->_courier_return = $all_rows[0]['courier_return']; 
        $this->_courier_delivery_date = $all_rows[0]['courier_delivery_date']; 
        $this->_courier_status = $all_rows[0]['courier_status']; 
        $this->_lockedbyuser = $all_rows[0]['lockedbyuser']; 
        $this->_lockuser = $all_rows[0]['lockuser']; 
        $this->_onlinestatus = $all_rows[0]['onlinestatus']; 
        $this->_onlinedatetime = $all_rows[0]['onlinedatetime']; 
        $this->_phonecode = $all_rows[0]['phonecode']; 
        $this->_company_type = $all_rows[0]['company_type']; 
        $this->_seo_manually_set = $all_rows[0]['seo_manually_set']; 
        $this->_courier = $all_rows[0]['courier']; 
        $this->_city_id = $all_rows[0]['city_id']; 
        $this->_vn_category = $all_rows[0]['vn_category']; 
        $this->_vn_keywords = $all_rows[0]['vn_keywords']; 
        $this->_vn_expires = $all_rows[0]['vn_expires']; 
        $this->_phone1digits = $all_rows[0]['phone1digits']; 
        $this->_phone2digits = $all_rows[0]['phone2digits']; 
        $this->_faxdigits = $all_rows[0]['faxdigits']; 
        $this->_mobiledigits = $all_rows[0]['mobiledigits']; 
        $this->_companyname_dm = $all_rows[0]['companyname_dm']; 
        $this->_address_dm = $all_rows[0]['address_dm']; 
        $this->_phone1_dm = $all_rows[0]['phone1_dm']; 
        $this->_phone2_dm = $all_rows[0]['phone2_dm']; 
        $this->_fax_dm = $all_rows[0]['fax_dm']; 
        $this->_email_dm = $all_rows[0]['email_dm']; 
        $this->_mobile_dm = $all_rows[0]['mobile_dm']; 
        $this->_website_dm = $all_rows[0]['website_dm']; 
        $this->_geox_dm = $all_rows[0]['geox_dm']; 
        $this->_geoy_dm = $all_rows[0]['geoy_dm']; 
        $this->_zipcode_dm = $all_rows[0]['zipcode_dm']; 
        $this->_facebook_dm = $all_rows[0]['facebook_dm']; 
        $this->_twitter_dm = $all_rows[0]['twitter_dm']; 
        $this->_shortdescr_dm = $all_rows[0]['shortdescr_dm']; 
        $this->_fulldescr_dm = $all_rows[0]['fulldescr_dm']; 
        $this->_basiccat_dm = $all_rows[0]['basiccat_dm']; 
        $this->_area_dm = $all_rows[0]['area_dm']; 
        $this->_keywords_dm = $all_rows[0]['keywords_dm']; 
        $this->_cityid_dm = $all_rows[0]['cityid_dm']; 
        $this->_CUSID = $all_rows[0]['CUSID']; 
        $this->_allphonesdigits = $all_rows[0]['allphonesdigits']; 
        $this->_nodoubles = $all_rows[0]['nodoubles']; 
        $this->_lastactiondate = $all_rows[0]['lastactiondate']; 
        $this->_haswebsite = $all_rows[0]['haswebsite']; 
        $this->_profession = $all_rows[0]['profession']; 
        $this->_profession_dm = $all_rows[0]['profession_dm']; 
        $this->_password_dm = $all_rows[0]['password_dm']; 
        $this->_expires_dm = $all_rows[0]['expires_dm']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_googleplus = $all_rows[0]['googleplus']; 
        $this->_googleplus_dm = $all_rows[0]['googleplus_dm']; 
        $this->_pinterest = $all_rows[0]['pinterest']; 
        $this->_pinterest_dm = $all_rows[0]['pinterest_dm']; 
        $this->_sites = $all_rows[0]['sites']; 
        $this->_sites_dm = $all_rows[0]['sites_dm']; 
        $this->_workinghours = $all_rows[0]['workinghours']; 
        $this->_workinghours_dm = $all_rows[0]['workinghours_dm']; 
        $this->_workingmonths = $all_rows[0]['workingmonths']; 
        $this->_workingmonths_dm = $all_rows[0]['workingmonths_dm']; 
        $this->_domain = $all_rows[0]['domain']; 
        $this->_domain_name = $all_rows[0]['domain_name']; 
        $this->_domain_expires = $all_rows[0]['domain_expires']; 
        $this->_languages = $all_rows[0]['languages']; 
        $this->_languages_dm = $all_rows[0]['languages_dm']; 
        $this->_commstatus = $all_rows[0]['commstatus']; 
        $this->_lastactiondate1 = $all_rows[0]['lastactiondate1']; 
        $this->_lastactiondate2 = $all_rows[0]['lastactiondate2']; 
        $this->_lastactiondate3 = $all_rows[0]['lastactiondate3']; 
        $this->_package2 = $all_rows[0]['package2']; 
        $this->_discount2 = $all_rows[0]['discount2']; 
        $this->_price2 = $all_rows[0]['price2']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_companyname() { 
    return $this->_companyname; 
} 
public function set_companyname($val) { 
    $this->_companyname = $val; 
} 

public function get_companyname_en() { 
    return $this->_companyname_en; 
} 
public function set_companyname_en($val) { 
    $this->_companyname_en = $val; 
} 

public function get_phone1() { 
    return $this->_phone1; 
} 
public function set_phone1($val) { 
    $this->_phone1 = $val; 
} 

public function get_phone2() { 
    return $this->_phone2; 
} 
public function set_phone2($val) { 
    $this->_phone2 = $val; 
} 

public function get_fax() { 
    return $this->_fax; 
} 
public function set_fax($val) { 
    $this->_fax = $val; 
} 

public function get_mobilephone() { 
    return $this->_mobilephone; 
} 
public function set_mobilephone($val) { 
    $this->_mobilephone = $val; 
} 

public function get_contactperson() { 
    return $this->_contactperson; 
} 
public function set_contactperson($val) { 
    $this->_contactperson = $val; 
} 

public function get_basiccategory() { 
    return $this->_basiccategory; 
} 
public function set_basiccategory($val) { 
    $this->_basiccategory = $val; 
} 

public function get_reference() { 
    return $this->_reference; 
} 
public function set_reference($val) { 
    $this->_reference = $val; 
} 

public function get_area() { 
    return $this->_area; 
} 
public function set_area($val) { 
    $this->_area = $val; 
} 

public function get_geo_x() { 
    return $this->_geo_x; 
} 
public function set_geo_x($val) { 
    $this->_geo_x = $val; 
} 

public function get_geo_y() { 
    return $this->_geo_y; 
} 
public function set_geo_y($val) { 
    $this->_geo_y = $val; 
} 

public function get_address() { 
    return $this->_address; 
} 
public function set_address($val) { 
    $this->_address = $val; 
} 

public function get_zipcode() { 
    return $this->_zipcode; 
} 
public function set_zipcode($val) { 
    $this->_zipcode = $val; 
} 

public function get_email() { 
    return $this->_email; 
} 
public function set_email($val) { 
    $this->_email = $val; 
} 

public function get_website() { 
    return $this->_website; 
} 
public function set_website($val) { 
    $this->_website = $val; 
} 

public function get_facebook() { 
    return $this->_facebook; 
} 
public function set_facebook($val) { 
    $this->_facebook = $val; 
} 

public function get_twitter() { 
    return $this->_twitter; 
} 
public function set_twitter($val) { 
    $this->_twitter = $val; 
} 

public function get_package() { 
    return $this->_package; 
} 
public function set_package($val) { 
    $this->_package = $val; 
} 

public function get_discount() { 
    return $this->_discount; 
} 
public function set_discount($val) { 
    $this->_discount = $val; 
} 

public function get_price() { 
    return $this->_price; 
} 
public function set_price($val) { 
    $this->_price = $val; 
} 

public function get_vatzone() { 
    return $this->_vatzone; 
} 
public function set_vatzone($val) { 
    $this->_vatzone = $val; 
} 

public function get_catalogueid() { 
    return $this->_catalogueid; 
} 
public function set_catalogueid($val) { 
    $this->_catalogueid = $val; 
} 

public function get_expires() { 
    return $this->_expires; 
} 
public function set_expires($val) { 
    $this->_expires = $val; 
} 

public function get_username() { 
    return $this->_username; 
} 
public function set_username($val) { 
    $this->_username = $val; 
} 

public function get_password() { 
    return $this->_password; 
} 
public function set_password($val) { 
    $this->_password = $val; 
} 

public function get_user() { 
    return $this->_user; 
} 
public function set_user($val) { 
    $this->_user = $val; 
} 

public function get_userdataentry() { 
    return $this->_userdataentry; 
} 
public function set_userdataentry($val) { 
    $this->_userdataentry = $val; 
} 

public function get_recalldate() { 
    return $this->_recalldate; 
} 
public function set_recalldate($val) { 
    $this->_recalldate = $val; 
} 

public function get_recalltime() { 
    return $this->_recalltime; 
} 
public function set_recalltime($val) { 
    $this->_recalltime = $val; 
} 

public function get_status() { 
    return $this->_status; 
} 
public function set_status($val) { 
    $this->_status = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_history() { 
    return $this->_history; 
} 
public function set_history($val) { 
    $this->_history = $val; 
} 

public function get_show_phone1() { 
    return $this->_show_phone1; 
} 
public function set_show_phone1($val) { 
    $this->_show_phone1 = $val; 
} 

public function get_show_phone2() { 
    return $this->_show_phone2; 
} 
public function set_show_phone2($val) { 
    $this->_show_phone2 = $val; 
} 

public function get_show_mobilephone() { 
    return $this->_show_mobilephone; 
} 
public function set_show_mobilephone($val) { 
    $this->_show_mobilephone = $val; 
} 

public function get_show_email() { 
    return $this->_show_email; 
} 
public function set_show_email($val) { 
    $this->_show_email = $val; 
} 

public function get_LinkedIn() { 
    return $this->_LinkedIn; 
} 
public function set_LinkedIn($val) { 
    $this->_LinkedIn = $val; 
} 

public function get_linkedin_dm() { 
    return $this->_linkedin_dm; 
} 
public function set_linkedin_dm($val) { 
    $this->_linkedin_dm = $val; 
} 

public function get_ShortDescription() { 
    return $this->_ShortDescription; 
} 
public function set_ShortDescription($val) { 
    $this->_ShortDescription = $val; 
} 

public function get_FullDescription() { 
    return $this->_FullDescription; 
} 
public function set_FullDescription($val) { 
    $this->_FullDescription = $val; 
} 

public function get_DeliveryDate() { 
    return $this->_DeliveryDate; 
} 
public function set_DeliveryDate($val) { 
    $this->_DeliveryDate = $val; 
} 

public function get_DeliveryTime() { 
    return $this->_DeliveryTime; 
} 
public function set_DeliveryTime($val) { 
    $this->_DeliveryTime = $val; 
} 

public function get_DeliveryNotes() { 
    return $this->_DeliveryNotes; 
} 
public function set_DeliveryNotes($val) { 
    $this->_DeliveryNotes = $val; 
} 

public function get_subcategory() { 
    return $this->_subcategory; 
} 
public function set_subcategory($val) { 
    $this->_subcategory = $val; 
} 

public function get_dataentrydatetime() { 
    return $this->_dataentrydatetime; 
} 
public function set_dataentrydatetime($val) { 
    $this->_dataentrydatetime = $val; 
} 

public function get_voucherid() { 
    return $this->_voucherid; 
} 
public function set_voucherid($val) { 
    $this->_voucherid = $val; 
} 

public function get_invoiceid() { 
    return $this->_invoiceid; 
} 
public function set_invoiceid($val) { 
    $this->_invoiceid = $val; 
} 

public function get_invoiceprinted() { 
    return $this->_invoiceprinted; 
} 
public function set_invoiceprinted($val) { 
    $this->_invoiceprinted = $val; 
} 

public function get_afm() { 
    return $this->_afm; 
} 
public function set_afm($val) { 
    $this->_afm = $val; 
} 

public function get_doy() { 
    return $this->_doy; 
} 
public function set_doy($val) { 
    $this->_doy = $val; 
} 

public function get_eponimia() { 
    return $this->_eponimia; 
} 
public function set_eponimia($val) { 
    $this->_eponimia = $val; 
} 

public function get_courier_ok() { 
    return $this->_courier_ok; 
} 
public function set_courier_ok($val) { 
    $this->_courier_ok = $val; 
} 

public function get_courier_notes() { 
    return $this->_courier_notes; 
} 
public function set_courier_notes($val) { 
    $this->_courier_notes = $val; 
} 

public function get_courier_return() { 
    return $this->_courier_return; 
} 
public function set_courier_return($val) { 
    $this->_courier_return = $val; 
} 

public function get_courier_delivery_date() { 
    return $this->_courier_delivery_date; 
} 
public function set_courier_delivery_date($val) { 
    $this->_courier_delivery_date = $val; 
} 

public function get_courier_status() { 
    return $this->_courier_status; 
} 
public function set_courier_status($val) { 
    $this->_courier_status = $val; 
} 

public function get_lockedbyuser() { 
    return $this->_lockedbyuser; 
} 
public function set_lockedbyuser($val) { 
    $this->_lockedbyuser = $val; 
} 

public function get_lockuser() { 
    return $this->_lockuser; 
} 
public function set_lockuser($val) { 
    $this->_lockuser = $val; 
} 

public function get_onlinestatus() { 
    return $this->_onlinestatus; 
} 
public function set_onlinestatus($val) { 
    $this->_onlinestatus = $val; 
} 

public function get_onlinedatetime() { 
    return $this->_onlinedatetime; 
} 
public function set_onlinedatetime($val) { 
    $this->_onlinedatetime = $val; 
} 

public function get_phonecode() { 
    return $this->_phonecode; 
} 
public function set_phonecode($val) { 
    $this->_phonecode = $val; 
} 

public function get_company_type() { 
    return $this->_company_type; 
} 
public function set_company_type($val) { 
    $this->_company_type = $val; 
} 

public function get_seo_manually_set() { 
    return $this->_seo_manually_set; 
} 
public function set_seo_manually_set($val) { 
    $this->_seo_manually_set = $val; 
} 

public function get_courier() { 
    return $this->_courier; 
} 
public function set_courier($val) { 
    $this->_courier = $val; 
} 

public function get_city_id() { 
    return $this->_city_id; 
} 
public function set_city_id($val) { 
    $this->_city_id = $val; 
} 

public function get_vn_category() { 
    return $this->_vn_category; 
} 
public function set_vn_category($val) { 
    $this->_vn_category = $val; 
} 

public function get_vn_keywords() { 
    return $this->_vn_keywords; 
} 
public function set_vn_keywords($val) { 
    $this->_vn_keywords = $val; 
} 

public function get_vn_expires() { 
    return $this->_vn_expires; 
} 
public function set_vn_expires($val) { 
    $this->_vn_expires = $val; 
} 

public function get_phone1digits() { 
    return $this->_phone1digits; 
} 
public function set_phone1digits($val) { 
    $this->_phone1digits = $val; 
} 

public function get_phone2digits() { 
    return $this->_phone2digits; 
} 
public function set_phone2digits($val) { 
    $this->_phone2digits = $val; 
} 

public function get_faxdigits() { 
    return $this->_faxdigits; 
} 
public function set_faxdigits($val) { 
    $this->_faxdigits = $val; 
} 

public function get_mobiledigits() { 
    return $this->_mobiledigits; 
} 
public function set_mobiledigits($val) { 
    $this->_mobiledigits = $val; 
} 

public function get_companyname_dm() { 
    return $this->_companyname_dm; 
} 
public function set_companyname_dm($val) { 
    $this->_companyname_dm = $val; 
} 

public function get_address_dm() { 
    return $this->_address_dm; 
} 
public function set_address_dm($val) { 
    $this->_address_dm = $val; 
} 

public function get_phone1_dm() { 
    return $this->_phone1_dm; 
} 
public function set_phone1_dm($val) { 
    $this->_phone1_dm = $val; 
} 

public function get_phone2_dm() { 
    return $this->_phone2_dm; 
} 
public function set_phone2_dm($val) { 
    $this->_phone2_dm = $val; 
} 

public function get_fax_dm() { 
    return $this->_fax_dm; 
} 
public function set_fax_dm($val) { 
    $this->_fax_dm = $val; 
} 

public function get_email_dm() { 
    return $this->_email_dm; 
} 
public function set_email_dm($val) { 
    $this->_email_dm = $val; 
} 

public function get_mobile_dm() { 
    return $this->_mobile_dm; 
} 
public function set_mobile_dm($val) { 
    $this->_mobile_dm = $val; 
} 

public function get_website_dm() { 
    return $this->_website_dm; 
} 
public function set_website_dm($val) { 
    $this->_website_dm = $val; 
} 

public function get_geox_dm() { 
    return $this->_geox_dm; 
} 
public function set_geox_dm($val) { 
    $this->_geox_dm = $val; 
} 

public function get_geoy_dm() { 
    return $this->_geoy_dm; 
} 
public function set_geoy_dm($val) { 
    $this->_geoy_dm = $val; 
} 

public function get_zipcode_dm() { 
    return $this->_zipcode_dm; 
} 
public function set_zipcode_dm($val) { 
    $this->_zipcode_dm = $val; 
} 

public function get_facebook_dm() { 
    return $this->_facebook_dm; 
} 
public function set_facebook_dm($val) { 
    $this->_facebook_dm = $val; 
} 

public function get_twitter_dm() { 
    return $this->_twitter_dm; 
} 
public function set_twitter_dm($val) { 
    $this->_twitter_dm = $val; 
} 

public function get_shortdescr_dm() { 
    return $this->_shortdescr_dm; 
} 
public function set_shortdescr_dm($val) { 
    $this->_shortdescr_dm = $val; 
} 

public function get_fulldescr_dm() { 
    return $this->_fulldescr_dm; 
} 
public function set_fulldescr_dm($val) { 
    $this->_fulldescr_dm = $val; 
} 

public function get_basiccat_dm() { 
    return $this->_basiccat_dm; 
} 
public function set_basiccat_dm($val) { 
    $this->_basiccat_dm = $val; 
} 

public function get_area_dm() { 
    return $this->_area_dm; 
} 
public function set_area_dm($val) { 
    $this->_area_dm = $val; 
} 

public function get_keywords_dm() { 
    return $this->_keywords_dm; 
} 
public function set_keywords_dm($val) { 
    $this->_keywords_dm = $val; 
} 

public function get_cityid_dm() { 
    return $this->_cityid_dm; 
} 
public function set_cityid_dm($val) { 
    $this->_cityid_dm = $val; 
} 

public function get_CUSID() { 
    return $this->_CUSID; 
} 
public function set_CUSID($val) { 
    $this->_CUSID = $val; 
} 

public function get_allphonesdigits() { 
    return $this->_allphonesdigits; 
} 
public function set_allphonesdigits($val) { 
    $this->_allphonesdigits = $val; 
} 

public function get_nodoubles() { 
    return $this->_nodoubles; 
} 
public function set_nodoubles($val) { 
    $this->_nodoubles = $val; 
} 

public function get_lastactiondate() { 
    return $this->_lastactiondate; 
} 
public function set_lastactiondate($val) { 
    $this->_lastactiondate = $val; 
} 

public function get_haswebsite() { 
    return $this->_haswebsite; 
} 
public function set_haswebsite($val) { 
    $this->_haswebsite = $val; 
} 

public function get_profession() { 
    return $this->_profession; 
} 
public function set_profession($val) { 
    $this->_profession = $val; 
} 

public function get_profession_dm() { 
    return $this->_profession_dm; 
} 
public function set_profession_dm($val) { 
    $this->_profession_dm = $val; 
} 

public function get_password_dm() { 
    return $this->_password_dm; 
} 
public function set_password_dm($val) { 
    $this->_password_dm = $val; 
} 

public function get_expires_dm() { 
    return $this->_expires_dm; 
} 
public function set_expires_dm($val) { 
    $this->_expires_dm = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_googleplus() { 
    return $this->_googleplus; 
} 
public function set_googleplus($val) { 
    $this->_googleplus = $val; 
} 

public function get_googleplus_dm() { 
    return $this->_googleplus_dm; 
} 
public function set_googleplus_dm($val) { 
    $this->_googleplus_dm = $val; 
} 

public function get_pinterest() { 
    return $this->_pinterest; 
} 
public function set_pinterest($val) { 
    $this->_pinterest = $val; 
} 

public function get_pinterest_dm() { 
    return $this->_pinterest_dm; 
} 
public function set_pinterest_dm($val) { 
    $this->_pinterest_dm = $val; 
} 

public function get_sites() { 
    return $this->_sites; 
} 
public function set_sites($val) { 
    $this->_sites = $val; 
} 

public function get_sites_dm() { 
    return $this->_sites_dm; 
} 
public function set_sites_dm($val) { 
    $this->_sites_dm = $val; 
} 

public function get_workinghours() { 
    return $this->_workinghours; 
} 
public function set_workinghours($val) { 
    $this->_workinghours = $val; 
} 

public function get_workinghours_dm() { 
    return $this->_workinghours_dm; 
} 
public function set_workinghours_dm($val) { 
    $this->_workinghours_dm = $val; 
} 

public function get_workingmonths() { 
    return $this->_workingmonths; 
} 
public function set_workingmonths($val) { 
    $this->_workingmonths = $val; 
} 

public function get_workingmonths_dm() { 
    return $this->_workingmonths_dm; 
} 
public function set_workingmonths_dm($val) { 
    $this->_workingmonths_dm = $val; 
} 

public function get_domain() { 
    return $this->_domain; 
} 
public function set_domain($val) { 
    $this->_domain = $val; 
} 

public function get_domain_name() { 
    return $this->_domain_name; 
} 
public function set_domain_name($val) { 
    $this->_domain_name = $val; 
} 

public function get_domain_expires() { 
    return $this->_domain_expires; 
} 
public function set_domain_expires($val) { 
    $this->_domain_expires = $val; 
} 

public function get_languages() { 
    return $this->_languages; 
} 
public function set_languages($val) { 
    $this->_languages = $val; 
} 

public function get_languages_dm() { 
    return $this->_languages_dm; 
} 
public function set_languages_dm($val) { 
    $this->_languages_dm = $val; 
} 

public function get_commstatus() { 
    return $this->_commstatus; 
} 
public function set_commstatus($val) { 
    $this->_commstatus = $val; 
} 

public function get_lastactiondate1() { 
    return $this->_lastactiondate1; 
} 
public function set_lastactiondate1($val) { 
    $this->_lastactiondate1 = $val; 
} 

public function get_lastactiondate2() { 
    return $this->_lastactiondate2; 
} 
public function set_lastactiondate2($val) { 
    $this->_lastactiondate2 = $val; 
} 

public function get_lastactiondate3() { 
    return $this->_lastactiondate3; 
} 
public function set_lastactiondate3($val) { 
    $this->_lastactiondate3 = $val; 
} 

public function get_package2() { 
    return $this->_package2; 
} 
public function set_package2($val) { 
    $this->_package2 = $val; 
} 

public function get_discount2() { 
    return $this->_discount2; 
} 
public function set_discount2($val) { 
    $this->_discount2 = $val; 
} 

public function get_price2() { 
    return $this->_price2; 
} 
public function set_price2($val) { 
    $this->_price2 = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO COMPANIES ( 
    companyname,
    companyname_en,
    phone1,
    phone2,
    fax,
    mobilephone,
    contactperson,
    basiccategory,
    reference,
    area,
    geo_x,
    geo_y,
    address,
    zipcode,
    email,
    website,
    facebook,
    twitter,
    package,
    discount,
    price,
    vatzone,
    catalogueid,
    expires,
    username,
    password,
    user,
    userdataentry,
    recalldate,
    recalltime,
    status,
    comment,
    history,
    show_phone1,
    show_phone2,
    show_mobilephone,
    show_email,
    LinkedIn,
    linkedin_dm,
    ShortDescription,
    FullDescription,
    DeliveryDate,
    DeliveryTime,
    DeliveryNotes,
    subcategory,
    dataentrydatetime,
    voucherid,
    invoiceid,
    invoiceprinted,
    afm,
    doy,
    eponimia,
    courier_ok,
    courier_notes,
    courier_return,
    courier_delivery_date,
    courier_status,
    lockedbyuser,
    lockuser,
    onlinestatus,
    onlinedatetime,
    phonecode,
    company_type,
    seo_manually_set,
    courier,
    city_id,
    vn_category,
    vn_keywords,
    vn_expires,
    phone1digits,
    phone2digits,
    faxdigits,
    mobiledigits,
    companyname_dm,
    address_dm,
    phone1_dm,
    phone2_dm,
    fax_dm,
    email_dm,
    mobile_dm,
    website_dm,
    geox_dm,
    geoy_dm,
    zipcode_dm,
    facebook_dm,
    twitter_dm,
    shortdescr_dm,
    fulldescr_dm,
    basiccat_dm,
    area_dm,
    keywords_dm,
    cityid_dm,
    CUSID,
    allphonesdigits,
    nodoubles,
    lastactiondate,
    haswebsite,
    profession,
    profession_dm,
    password_dm,
    expires_dm,
    active,
    googleplus,
    googleplus_dm,
    pinterest,
    pinterest_dm,
    sites,
    sites_dm,
    workinghours,
    workinghours_dm,
    workingmonths,
    workingmonths_dm,
    domain,
    domain_name,
    domain_expires,
    languages,
    languages_dm,
    commstatus,
    lastactiondate1,
    lastactiondate2,
    lastactiondate3,
    package2,
    discount2,
    price2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyname, 
        $this->_companyname_en, 
        $this->_phone1, 
        $this->_phone2, 
        $this->_fax, 
        $this->_mobilephone, 
        $this->_contactperson, 
        $this->_basiccategory, 
        $this->_reference, 
        $this->_area, 
        $this->_geo_x, 
        $this->_geo_y, 
        $this->_address, 
        $this->_zipcode, 
        $this->_email, 
        $this->_website, 
        $this->_facebook, 
        $this->_twitter, 
        $this->_package, 
        $this->_discount, 
        $this->_price, 
        $this->_vatzone, 
        $this->_catalogueid, 
        $this->_expires, 
        $this->_username, 
        $this->_password, 
        $this->_user, 
        $this->_userdataentry, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_status, 
        $this->_comment, 
        $this->_history, 
        $this->_show_phone1, 
        $this->_show_phone2, 
        $this->_show_mobilephone, 
        $this->_show_email, 
        $this->_LinkedIn, 
        $this->_linkedin_dm, 
        $this->_ShortDescription, 
        $this->_FullDescription, 
        $this->_DeliveryDate, 
        $this->_DeliveryTime, 
        $this->_DeliveryNotes, 
        $this->_subcategory, 
        $this->_dataentrydatetime, 
        $this->_voucherid, 
        $this->_invoiceid, 
        $this->_invoiceprinted, 
        $this->_afm, 
        $this->_doy, 
        $this->_eponimia, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_lockedbyuser, 
        $this->_lockuser, 
        $this->_onlinestatus, 
        $this->_onlinedatetime, 
        $this->_phonecode, 
        $this->_company_type, 
        $this->_seo_manually_set, 
        $this->_courier, 
        $this->_city_id, 
        $this->_vn_category, 
        $this->_vn_keywords, 
        $this->_vn_expires, 
        $this->_phone1digits, 
        $this->_phone2digits, 
        $this->_faxdigits, 
        $this->_mobiledigits, 
        $this->_companyname_dm, 
        $this->_address_dm, 
        $this->_phone1_dm, 
        $this->_phone2_dm, 
        $this->_fax_dm, 
        $this->_email_dm, 
        $this->_mobile_dm, 
        $this->_website_dm, 
        $this->_geox_dm, 
        $this->_geoy_dm, 
        $this->_zipcode_dm, 
        $this->_facebook_dm, 
        $this->_twitter_dm, 
        $this->_shortdescr_dm, 
        $this->_fulldescr_dm, 
        $this->_basiccat_dm, 
        $this->_area_dm, 
        $this->_keywords_dm, 
        $this->_cityid_dm, 
        $this->_CUSID, 
        $this->_allphonesdigits, 
        $this->_nodoubles, 
        $this->_lastactiondate, 
        $this->_haswebsite, 
        $this->_profession, 
        $this->_profession_dm, 
        $this->_password_dm, 
        $this->_expires_dm, 
        $this->_active, 
        $this->_googleplus, 
        $this->_googleplus_dm, 
        $this->_pinterest, 
        $this->_pinterest_dm, 
        $this->_sites, 
        $this->_sites_dm, 
        $this->_workinghours, 
        $this->_workinghours_dm, 
        $this->_workingmonths, 
        $this->_workingmonths_dm, 
        $this->_domain, 
        $this->_domain_name, 
        $this->_domain_expires, 
        $this->_languages, 
        $this->_languages_dm, 
        $this->_commstatus, 
        $this->_lastactiondate1, 
        $this->_lastactiondate2, 
        $this->_lastactiondate3, 
        $this->_package2, 
        $this->_discount2, 
        $this->_price2)); 
    $ssql = $this->_myconn->getLastIDsql('COMPANIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE COMPANIES set 
        companyname = ?, 
        companyname_en = ?, 
        phone1 = ?, 
        phone2 = ?, 
        fax = ?, 
        mobilephone = ?, 
        contactperson = ?, 
        basiccategory = ?, 
        reference = ?, 
        area = ?, 
        geo_x = ?, 
        geo_y = ?, 
        address = ?, 
        zipcode = ?, 
        email = ?, 
        website = ?, 
        facebook = ?, 
        twitter = ?, 
        package = ?, 
        discount = ?, 
        price = ?, 
        vatzone = ?, 
        catalogueid = ?, 
        expires = ?, 
        username = ?, 
        password = ?, 
        user = ?, 
        userdataentry = ?, 
        recalldate = ?, 
        recalltime = ?, 
        status = ?, 
        comment = ?, 
        history = ?, 
        show_phone1 = ?, 
        show_phone2 = ?, 
        show_mobilephone = ?, 
        show_email = ?, 
        LinkedIn = ?, 
        linkedin_dm = ?, 
        ShortDescription = ?, 
        FullDescription = ?, 
        DeliveryDate = ?, 
        DeliveryTime = ?, 
        DeliveryNotes = ?, 
        subcategory = ?, 
        dataentrydatetime = ?, 
        voucherid = ?, 
        invoiceid = ?, 
        invoiceprinted = ?, 
        afm = ?, 
        doy = ?, 
        eponimia = ?, 
        courier_ok = ?, 
        courier_notes = ?, 
        courier_return = ?, 
        courier_delivery_date = ?, 
        courier_status = ?, 
        lockedbyuser = ?, 
        lockuser = ?, 
        onlinestatus = ?, 
        onlinedatetime = ?, 
        phonecode = ?, 
        company_type = ?, 
        seo_manually_set = ?, 
        courier = ?, 
        city_id = ?, 
        vn_category = ?, 
        vn_keywords = ?, 
        vn_expires = ?, 
        phone1digits = ?, 
        phone2digits = ?, 
        faxdigits = ?, 
        mobiledigits = ?, 
        companyname_dm = ?, 
        address_dm = ?, 
        phone1_dm = ?, 
        phone2_dm = ?, 
        fax_dm = ?, 
        email_dm = ?, 
        mobile_dm = ?, 
        website_dm = ?, 
        geox_dm = ?, 
        geoy_dm = ?, 
        zipcode_dm = ?, 
        facebook_dm = ?, 
        twitter_dm = ?, 
        shortdescr_dm = ?, 
        fulldescr_dm = ?, 
        basiccat_dm = ?, 
        area_dm = ?, 
        keywords_dm = ?, 
        cityid_dm = ?, 
        CUSID = ?, 
        allphonesdigits = ?, 
        nodoubles = ?, 
        lastactiondate = ?, 
        haswebsite = ?, 
        profession = ?, 
        profession_dm = ?, 
        password_dm = ?, 
        expires_dm = ?, 
        active = ?, 
        googleplus = ?, 
        googleplus_dm = ?, 
        pinterest = ?, 
        pinterest_dm = ?, 
        sites = ?, 
        sites_dm = ?, 
        workinghours = ?, 
        workinghours_dm = ?, 
        workingmonths = ?, 
        workingmonths_dm = ?, 
        domain = ?, 
        domain_name = ?, 
        domain_expires = ?, 
        languages = ?, 
        languages_dm = ?, 
        commstatus = ?, 
        lastactiondate1 = ?, 
        lastactiondate2 = ?, 
        lastactiondate3 = ?, 
        package2 = ?, 
        discount2 = ?, 
        price2 = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyname, 
        $this->_companyname_en, 
        $this->_phone1, 
        $this->_phone2, 
        $this->_fax, 
        $this->_mobilephone, 
        $this->_contactperson, 
        $this->_basiccategory, 
        $this->_reference, 
        $this->_area, 
        $this->_geo_x, 
        $this->_geo_y, 
        $this->_address, 
        $this->_zipcode, 
        $this->_email, 
        $this->_website, 
        $this->_facebook, 
        $this->_twitter, 
        $this->_package, 
        $this->_discount, 
        $this->_price, 
        $this->_vatzone, 
        $this->_catalogueid, 
        $this->_expires, 
        $this->_username, 
        $this->_password, 
        $this->_user, 
        $this->_userdataentry, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_status, 
        $this->_comment, 
        $this->_history, 
        $this->_show_phone1, 
        $this->_show_phone2, 
        $this->_show_mobilephone, 
        $this->_show_email, 
        $this->_LinkedIn, 
        $this->_linkedin_dm, 
        $this->_ShortDescription, 
        $this->_FullDescription, 
        $this->_DeliveryDate, 
        $this->_DeliveryTime, 
        $this->_DeliveryNotes, 
        $this->_subcategory, 
        $this->_dataentrydatetime, 
        $this->_voucherid, 
        $this->_invoiceid, 
        $this->_invoiceprinted, 
        $this->_afm, 
        $this->_doy, 
        $this->_eponimia, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_lockedbyuser, 
        $this->_lockuser, 
        $this->_onlinestatus, 
        $this->_onlinedatetime, 
        $this->_phonecode, 
        $this->_company_type, 
        $this->_seo_manually_set, 
        $this->_courier, 
        $this->_city_id, 
        $this->_vn_category, 
        $this->_vn_keywords, 
        $this->_vn_expires, 
        $this->_phone1digits, 
        $this->_phone2digits, 
        $this->_faxdigits, 
        $this->_mobiledigits, 
        $this->_companyname_dm, 
        $this->_address_dm, 
        $this->_phone1_dm, 
        $this->_phone2_dm, 
        $this->_fax_dm, 
        $this->_email_dm, 
        $this->_mobile_dm, 
        $this->_website_dm, 
        $this->_geox_dm, 
        $this->_geoy_dm, 
        $this->_zipcode_dm, 
        $this->_facebook_dm, 
        $this->_twitter_dm, 
        $this->_shortdescr_dm, 
        $this->_fulldescr_dm, 
        $this->_basiccat_dm, 
        $this->_area_dm, 
        $this->_keywords_dm, 
        $this->_cityid_dm, 
        $this->_CUSID, 
        $this->_allphonesdigits, 
        $this->_nodoubles, 
        $this->_lastactiondate, 
        $this->_haswebsite, 
        $this->_profession, 
        $this->_profession_dm, 
        $this->_password_dm, 
        $this->_expires_dm, 
        $this->_active, 
        $this->_googleplus, 
        $this->_googleplus_dm, 
        $this->_pinterest, 
        $this->_pinterest_dm, 
        $this->_sites, 
        $this->_sites_dm, 
        $this->_workinghours, 
        $this->_workinghours_dm, 
        $this->_workingmonths, 
        $this->_workingmonths_dm, 
        $this->_domain, 
        $this->_domain_name, 
        $this->_domain_expires, 
        $this->_languages, 
        $this->_languages_dm, 
        $this->_commstatus, 
        $this->_lastactiondate1, 
        $this->_lastactiondate2, 
        $this->_lastactiondate3, 
        $this->_package2, 
        $this->_discount2, 
        $this->_price2,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM COMPANIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}