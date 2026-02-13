<?php

require_once 'db.php';
require_once 'utils.php';


class mlng
{
    protected $_rows, $_lang;

    public function __construct($page, $lang, $conn) {	
        $ssql = "SELECT * FROM MLNG WHERE page = ?"; //?????
        $this->_rows = $conn->getRS($ssql, array($page));
        $this->_lang = $lang;        
    }

    public function l($keycode) {
        if ($this->_rows) {
            $myrow = arrayfunctions::filter_by_value($this->_rows, 'keycode', $keycode);
            if (count($myrow)==1) {
                return $myrow[0][$this->_lang];
            }
            else {
                return $keycode;
            }
        }
        else {
            return $keycode;
        }
    }
}


class USERS
{

protected $_myconn, $_rs, $_id, $_fullname, $_username, $_password, $_active, $_userprofile, $_useraccess, $_costperhour, $_is_agent, $_time_start, $_time_stop, $_working_days ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM USERS WHERE id=?"; 
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
        $this->_fullname = $all_rows[0]['fullname']; 
        $this->_username = $all_rows[0]['username']; 
        $this->_password = $all_rows[0]['password']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_userprofile = $all_rows[0]['userprofile']; 
        $this->_useraccess = $all_rows[0]['useraccess']; 
        $this->_costperhour = $all_rows[0]['costperhour']; 
        $this->_is_agent = $all_rows[0]['is_agent']; 
        $this->_time_start = $all_rows[0]['time_start']; 
        $this->_time_stop = $all_rows[0]['time_stop']; 
        $this->_working_days = $all_rows[0]['working_days']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_fullname() { 
    return $this->_fullname; 
} 
public function set_fullname($val) { 
    $this->_fullname = $val; 
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

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_userprofile() { 
    return $this->_userprofile; 
} 
public function set_userprofile($val) { 
    $this->_userprofile = $val; 
} 

public function get_useraccess() { 
    return $this->_useraccess; 
} 
public function set_useraccess($val) { 
    $this->_useraccess = $val; 
} 

public function get_costperhour() { 
    return $this->_costperhour; 
} 
public function set_costperhour($val) { 
    $this->_costperhour = $val; 
} 

public function get_is_agent() { 
    return $this->_is_agent; 
} 
public function set_is_agent($val) { 
    $this->_is_agent = $val; 
} 

public function get_time_start() { 
    return $this->_time_start; 
} 
public function set_time_start($val) { 
    $this->_time_start = $val; 
} 

public function get_time_stop() { 
    return $this->_time_stop; 
} 
public function set_time_stop($val) { 
    $this->_time_stop = $val; 
} 

public function get_working_days() { 
    return $this->_working_days; 
} 
public function set_working_days($val) { 
    $this->_working_days = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO USERS ( 
    fullname,
    username,
    password,
    active,
    userprofile,
    useraccess,
    costperhour,
    is_agent,
    time_start,
    time_stop,
    working_days
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_fullname, 
        $this->_username, 
        $this->_password, 
        $this->_active, 
        $this->_userprofile, 
        $this->_useraccess, 
        $this->_costperhour, 
        $this->_is_agent, 
        $this->_time_start, 
        $this->_time_stop, 
        $this->_working_days)); 
    $ssql = $this->_myconn->getLastIDsql('USERS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE USERS set 
        fullname = ?, 
        username = ?, 
        password = ?, 
        active = ?, 
        userprofile = ?, 
        useraccess = ?, 
        costperhour = ?, 
        is_agent = ?, 
        time_start = ?, 
        time_stop = ?, 
        working_days = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_fullname, 
        $this->_username, 
        $this->_password, 
        $this->_active, 
        $this->_userprofile, 
        $this->_useraccess, 
        $this->_costperhour, 
        $this->_is_agent, 
        $this->_time_start, 
        $this->_time_stop, 
        $this->_working_days,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM USERS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}










class USERLOG
{

protected $_myconn, $_rs, $_id, $_userid, $_username, $_ipaddress, $_ipaddress2, $_action, $_uldatetime, $_comment ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM USERLOG WHERE id=?"; 
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
        $this->_userid = $all_rows[0]['userid']; 
        $this->_username = $all_rows[0]['username']; 
        $this->_ipaddress = $all_rows[0]['ipaddress']; 
        $this->_ipaddress2 = $all_rows[0]['ipaddress2']; 
        $this->_action = $all_rows[0]['action']; 
        $this->_uldatetime = $all_rows[0]['uldatetime']; 
        $this->_comment = $all_rows[0]['comment']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
} 

public function get_username() { 
    return $this->_username; 
} 
public function set_username($val) { 
    $this->_username = $val; 
} 

public function get_ipaddress() { 
    return $this->_ipaddress; 
} 
public function set_ipaddress($val) { 
    $this->_ipaddress = $val; 
} 

public function get_ipaddress2() { 
    return $this->_ipaddress2; 
} 
public function set_ipaddress2($val) { 
    $this->_ipaddress2 = $val; 
} 

public function get_action() { 
    return $this->_action; 
} 
public function set_action($val) { 
    $this->_action = $val; 
} 

public function get_uldatetime() { 
    return $this->_uldatetime; 
} 
public function set_uldatetime($val) { 
    $this->_uldatetime = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO USERLOG ( 
    userid,
    username,
    ipaddress,
    ipaddress2,
    action,
    uldatetime,
    comment
    ) VALUES (?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_userid, 
        $this->_username, 
        $this->_ipaddress, 
        $this->_ipaddress2, 
        $this->_action, 
        $this->_uldatetime, 
        $this->_comment)); 
    $ssql = $this->_myconn->getLastIDsql('USERLOG');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE USERLOG set 
        userid = ?, 
        username = ?, 
        ipaddress = ?, 
        ipaddress2 = ?, 
        action = ?, 
        uldatetime = ?, 
        comment = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_userid, 
        $this->_username, 
        $this->_ipaddress, 
        $this->_ipaddress2, 
        $this->_action, 
        $this->_uldatetime, 
        $this->_comment,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM USERLOG WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}












class VAT
{

protected $_myconn, $_id, $_zone, $_value ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM VAT WHERE id=?";
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
        $this->_zone = $all_rows[0]['zone'];
        $this->_value = $all_rows[0]['value'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_zone() {
    return $this->_zone;
}
public function set_zone($val) {
    $this->_zone = $val;
}

public function get_value() {
    return $this->_value;
}
public function set_value($val) {
    $this->_value = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO VAT (
    zone,
    value
    ) VALUES (?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_zone,
        $this->_value));
    $ssql = $this->_myconn->getLastIDsql('VAT');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE VAT set
        zone = ?,
        value = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_zone,
        $this->_value,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM VAT WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class AREAS
{

protected $_myconn, $_id, $_description, $_parentid, $_level, $_active, $_nodes ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM AREAS WHERE id=?";
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
        $this->_description = $all_rows[0]['description'];
        $this->_parentid = $all_rows[0]['parentid'];
        $this->_level = $all_rows[0]['level'];
        $this->_active = $all_rows[0]['active'];
        $this->_nodes = $all_rows[0]['nodes'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_description() {
    return $this->_description;
}
public function set_description($val) {
    $this->_description = $val;
}

public function get_parentid() {
    return $this->_parentid;
}
public function set_parentid($val) {
    $this->_parentid = $val;
}

public function get_level() {
    return $this->_level;
}
public function set_level($val) {
    $this->_level = $val;
}

public function get_active() {
    return $this->_active;
}
public function set_active($val) {
    $this->_active = $val;
}

public function get_nodes() {
    return $this->_nodes;
}
public function set_nodes($val) {
    $this->_nodes = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO AREAS (
    description,
    parentid,
    level,
    active,
    nodes
    ) VALUES (?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_parentid,
        $this->_level,
        $this->_active,
        $this->_nodes));
        $ssql = $this->_myconn->getLastIDsql('AREAS');
        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE AREAS set
        description = ?,
        parentid = ?,
        level = ?,
        active = ?,
        nodes = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_parentid,
        $this->_level,
        $this->_active,
        $this->_nodes,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    /*return true;*/
    //EXTRA
    if ($this->UpdateParent()) {
        return true;
    }
    else {
        return false;
    }
}

public function Delete() {
    $ssql = "DELETE FROM AREAS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
    /*
    else {
        return true;
    }
    */
    //EXTRA
    if ($this->UpdateParent()) {
        return true;
    }
    else {
        return false;
    }

}

//EXTRA
private function UpdateParent() {
    if ($this->_parentid==0) {
        return TRUE;
    }
    $parent = new AREAS($this->_myconn,$this->_parentid);
    $sql = "SELECT COUNT(*) AS MyCount FROM AREAS WHERE parentid = ".$this->_parentid;
    $res = $this->_myconn->getRS($sql);
    if ($res[0]['MyCount']>0) {
        $parent->set_nodes(1);
    }
    else {
        $parent->set_nodes(0);
    }
    return $parent->Savedata();
}


} 








class COMPANIES_BASE
{

protected $_myconn, $_rs, $_id, $_companyname, $_companyname_en, $_phone1, $_phone2, $_fax, $_mobilephone, $_contactperson, $_basiccategory, $_reference, $_area, $_geo_x, $_geo_y, $_address, $_zipcode, $_email, $_website, $_facebook, $_twitter, $_package, $_discount, $_price, $_vatzone, $_catalogueid, $_expires, $_username, $_password, $_user, $_userdataentry, $_recalldate, $_recalltime, $_status, $_comment, $_history, $_show_phone1, $_show_phone2, $_show_mobilephone, $_show_email, $_LinkedIn, $_linkedin_dm, $_ShortDescription, $_FullDescription, $_DeliveryDate, $_DeliveryTime, $_DeliveryNotes, $_subcategory, $_dataentrydatetime, $_voucherid, $_invoiceid, $_invoiceprinted, $_afm, $_doy, $_eponimia, $_courier_ok, $_courier_notes, $_courier_return, $_courier_delivery_date, $_courier_status, $_lockedbyuser, $_lockuser, $_onlinestatus, $_onlinedatetime, $_phonecode, $_company_type, $_seo_manually_set, $_courier, $_city_id, $_vn_category, $_vn_keywords, $_vn_expires, $_phone1digits, $_phone2digits, $_faxdigits, $_mobiledigits, $_companyname_dm, $_address_dm, $_phone1_dm, $_phone2_dm, $_fax_dm, $_email_dm, $_mobile_dm, $_website_dm, $_geox_dm, $_geoy_dm, $_zipcode_dm, $_facebook_dm, $_twitter_dm, $_shortdescr_dm, $_fulldescr_dm, $_basiccat_dm, $_area_dm, $_keywords_dm, $_cityid_dm, $_CUSID, $_allphonesdigits, $_nodoubles, $_lastactiondate, $_haswebsite, $_profession, $_profession_dm, $_password_dm, $_expires_dm, $_active, $_googleplus, $_googleplus_dm, $_pinterest, $_pinterest_dm, $_sites, $_sites_dm, $_workinghours, $_workinghours_dm, $_workingmonths, $_workingmonths_dm, $_domain, $_domain_name, $_domain_expires, $_languages, $_languages_dm, $_commstatus, $_lastactiondate1, $_lastactiondate2, $_lastactiondate3, $_package2, $_discount2, $_price2, $_aux_field, $_instagram, $_instagram_dm, $_courier_address, $_courier_city, $_courier_zipcode, $_courier_phone, $_fb_package, $_fb_discount, $_fb_price, $_fb_expires, $_fb_page, $_fb_comments, $_fb_months, $_fb_ok, $_ga_package, $_ga_discount, $_ga_price, $_ga_expires, $_ga_page, $_ga_comments, $_ga_keywords, $_ga_months, $_ga_ok, $_mark, $_region, $_courier_region, $_for_renewal, $_srv_app, $_srv_date, $_srv_services, $_srv_status, $_srv_salesman, $_srv_result, $_srv_comments, $_srv_price, $_srv_field1, $_srv_field2, $_srv_field3, $_log_code, $_parent_record, $_child_record ;

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
        $this->_aux_field = $all_rows[0]['aux_field']; 
        $this->_instagram = $all_rows[0]['instagram']; 
        $this->_instagram_dm = $all_rows[0]['instagram_dm']; 
        $this->_courier_address = $all_rows[0]['courier_address']; 
        $this->_courier_city = $all_rows[0]['courier_city']; 
        $this->_courier_zipcode = $all_rows[0]['courier_zipcode']; 
        $this->_courier_phone = $all_rows[0]['courier_phone']; 
        $this->_fb_package = $all_rows[0]['fb_package']; 
        $this->_fb_discount = $all_rows[0]['fb_discount']; 
        $this->_fb_price = $all_rows[0]['fb_price']; 
        $this->_fb_expires = $all_rows[0]['fb_expires']; 
        $this->_fb_page = $all_rows[0]['fb_page']; 
        $this->_fb_comments = $all_rows[0]['fb_comments']; 
        $this->_fb_months = $all_rows[0]['fb_months']; 
        $this->_fb_ok = $all_rows[0]['fb_ok']; 
        $this->_ga_package = $all_rows[0]['ga_package']; 
        $this->_ga_discount = $all_rows[0]['ga_discount']; 
        $this->_ga_price = $all_rows[0]['ga_price']; 
        $this->_ga_expires = $all_rows[0]['ga_expires']; 
        $this->_ga_page = $all_rows[0]['ga_page']; 
        $this->_ga_comments = $all_rows[0]['ga_comments']; 
        $this->_ga_keywords = $all_rows[0]['ga_keywords']; 
        $this->_ga_months = $all_rows[0]['ga_months']; 
        $this->_ga_ok = $all_rows[0]['ga_ok']; 
        $this->_mark = $all_rows[0]['mark']; 
        $this->_region = $all_rows[0]['region']; 
        $this->_courier_region = $all_rows[0]['courier_region']; 
        $this->_for_renewal = $all_rows[0]['for_renewal']; 
        $this->_srv_app = $all_rows[0]['srv_app']; 
        $this->_srv_date = $all_rows[0]['srv_date']; 
        $this->_srv_services = $all_rows[0]['srv_services']; 
        $this->_srv_status = $all_rows[0]['srv_status']; 
        $this->_srv_salesman = $all_rows[0]['srv_salesman']; 
        $this->_srv_result = $all_rows[0]['srv_result']; 
        $this->_srv_comments = $all_rows[0]['srv_comments']; 
        $this->_srv_price = $all_rows[0]['srv_price']; 
        $this->_srv_field1 = $all_rows[0]['srv_field1']; 
        $this->_srv_field2 = $all_rows[0]['srv_field2']; 
        $this->_srv_field3 = $all_rows[0]['srv_field3']; 
        $this->_log_code = $all_rows[0]['log_code']; 
        $this->_parent_record = $all_rows[0]['parent_record']; 
        $this->_child_record = $all_rows[0]['child_record']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
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

public function get_aux_field() { 
    return $this->_aux_field; 
} 
public function set_aux_field($val) { 
    $this->_aux_field = $val; 
} 

public function get_instagram() { 
    return $this->_instagram; 
} 
public function set_instagram($val) { 
    $this->_instagram = $val; 
} 

public function get_instagram_dm() { 
    return $this->_instagram_dm; 
} 
public function set_instagram_dm($val) { 
    $this->_instagram_dm = $val; 
} 

public function get_courier_address() { 
    return $this->_courier_address; 
} 
public function set_courier_address($val) { 
    $this->_courier_address = $val; 
} 

public function get_courier_city() { 
    return $this->_courier_city; 
} 
public function set_courier_city($val) { 
    $this->_courier_city = $val; 
} 

public function get_courier_zipcode() { 
    return $this->_courier_zipcode; 
} 
public function set_courier_zipcode($val) { 
    $this->_courier_zipcode = $val; 
} 

public function get_courier_phone() { 
    return $this->_courier_phone; 
} 
public function set_courier_phone($val) { 
    $this->_courier_phone = $val; 
} 

public function get_fb_package() { 
    return $this->_fb_package; 
} 
public function set_fb_package($val) { 
    $this->_fb_package = $val; 
} 

public function get_fb_discount() { 
    return $this->_fb_discount; 
} 
public function set_fb_discount($val) { 
    $this->_fb_discount = $val; 
} 

public function get_fb_price() { 
    return $this->_fb_price; 
} 
public function set_fb_price($val) { 
    $this->_fb_price = $val; 
} 

public function get_fb_expires() { 
    return $this->_fb_expires; 
} 
public function set_fb_expires($val) { 
    $this->_fb_expires = $val; 
} 

public function get_fb_page() { 
    return $this->_fb_page; 
} 
public function set_fb_page($val) { 
    $this->_fb_page = $val; 
} 

public function get_fb_comments() { 
    return $this->_fb_comments; 
} 
public function set_fb_comments($val) { 
    $this->_fb_comments = $val; 
} 

public function get_fb_months() { 
    return $this->_fb_months; 
} 
public function set_fb_months($val) { 
    $this->_fb_months = $val; 
} 

public function get_fb_ok() { 
    return $this->_fb_ok; 
} 
public function set_fb_ok($val) { 
    $this->_fb_ok = $val; 
} 

public function get_ga_package() { 
    return $this->_ga_package; 
} 
public function set_ga_package($val) { 
    $this->_ga_package = $val; 
} 

public function get_ga_discount() { 
    return $this->_ga_discount; 
} 
public function set_ga_discount($val) { 
    $this->_ga_discount = $val; 
} 

public function get_ga_price() { 
    return $this->_ga_price; 
} 
public function set_ga_price($val) { 
    $this->_ga_price = $val; 
} 

public function get_ga_expires() { 
    return $this->_ga_expires; 
} 
public function set_ga_expires($val) { 
    $this->_ga_expires = $val; 
} 

public function get_ga_page() { 
    return $this->_ga_page; 
} 
public function set_ga_page($val) { 
    $this->_ga_page = $val; 
} 

public function get_ga_comments() { 
    return $this->_ga_comments; 
} 
public function set_ga_comments($val) { 
    $this->_ga_comments = $val; 
} 

public function get_ga_keywords() { 
    return $this->_ga_keywords; 
} 
public function set_ga_keywords($val) { 
    $this->_ga_keywords = $val; 
} 

public function get_ga_months() { 
    return $this->_ga_months; 
} 
public function set_ga_months($val) { 
    $this->_ga_months = $val; 
} 

public function get_ga_ok() { 
    return $this->_ga_ok; 
} 
public function set_ga_ok($val) { 
    $this->_ga_ok = $val; 
} 

public function get_mark() { 
    return $this->_mark; 
} 
public function set_mark($val) { 
    $this->_mark = $val; 
} 

public function get_region() { 
    return $this->_region; 
} 
public function set_region($val) { 
    $this->_region = $val; 
} 

public function get_courier_region() { 
    return $this->_courier_region; 
} 
public function set_courier_region($val) { 
    $this->_courier_region = $val; 
} 

public function get_for_renewal() { 
    return $this->_for_renewal; 
} 
public function set_for_renewal($val) { 
    $this->_for_renewal = $val; 
} 

public function get_srv_app() { 
    return $this->_srv_app; 
} 
public function set_srv_app($val) { 
    $this->_srv_app = $val; 
} 

public function get_srv_date() { 
    return $this->_srv_date; 
} 
public function set_srv_date($val) { 
    $this->_srv_date = $val; 
} 

public function get_srv_services() { 
    return $this->_srv_services; 
} 
public function set_srv_services($val) { 
    $this->_srv_services = $val; 
} 

public function get_srv_status() { 
    return $this->_srv_status; 
} 
public function set_srv_status($val) { 
    $this->_srv_status = $val; 
} 

public function get_srv_salesman() { 
    return $this->_srv_salesman; 
} 
public function set_srv_salesman($val) { 
    $this->_srv_salesman = $val; 
} 

public function get_srv_result() { 
    return $this->_srv_result; 
} 
public function set_srv_result($val) { 
    $this->_srv_result = $val; 
} 

public function get_srv_comments() { 
    return $this->_srv_comments; 
} 
public function set_srv_comments($val) { 
    $this->_srv_comments = $val; 
} 

public function get_srv_price() { 
    return $this->_srv_price; 
} 
public function set_srv_price($val) { 
    $this->_srv_price = $val; 
} 

public function get_srv_field1() { 
    return $this->_srv_field1; 
} 
public function set_srv_field1($val) { 
    $this->_srv_field1 = $val; 
} 

public function get_srv_field2() { 
    return $this->_srv_field2; 
} 
public function set_srv_field2($val) { 
    $this->_srv_field2 = $val; 
} 

public function get_srv_field3() { 
    return $this->_srv_field3; 
} 
public function set_srv_field3($val) { 
    $this->_srv_field3 = $val; 
} 

public function get_log_code() { 
    return $this->_log_code; 
} 
public function set_log_code($val) { 
    $this->_log_code = $val; 
} 

public function get_parent_record() { 
    return $this->_parent_record; 
} 
public function set_parent_record($val) { 
    $this->_parent_record = $val; 
} 

public function get_child_record() { 
    return $this->_child_record; 
} 
public function set_child_record($val) { 
    $this->_child_record = $val; 
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
    aux_field,
    instagram,
    instagram_dm,
    courier_address,
    courier_city,
    courier_zipcode,
    courier_phone,
    fb_package,
    fb_discount,
    fb_price,
    fb_expires,
    fb_page,
    fb_comments,
    fb_months,
    fb_ok,
    ga_package,
    ga_discount,
    ga_price,
    ga_expires,
    ga_page,
    ga_comments,
    ga_keywords,
    ga_months,
    ga_ok,
    mark,
    region,
    courier_region,
    for_renewal,
    srv_app,
    srv_date,
    srv_services,
    srv_status,
    srv_salesman,
    srv_result,
    srv_comments,
    srv_price,
    srv_field1,
    srv_field2,
    srv_field3,
    log_code,
    parent_record,
    child_record
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
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
        $this->_aux_field, 
        $this->_instagram, 
        $this->_instagram_dm, 
        $this->_courier_address, 
        $this->_courier_city, 
        $this->_courier_zipcode, 
        $this->_courier_phone, 
        $this->_fb_package, 
        $this->_fb_discount, 
        $this->_fb_price, 
        $this->_fb_expires, 
        $this->_fb_page, 
        $this->_fb_comments, 
        $this->_fb_months, 
        $this->_fb_ok, 
        $this->_ga_package, 
        $this->_ga_discount, 
        $this->_ga_price, 
        $this->_ga_expires, 
        $this->_ga_page, 
        $this->_ga_comments, 
        $this->_ga_keywords, 
        $this->_ga_months, 
        $this->_ga_ok, 
        $this->_mark, 
        $this->_region, 
        $this->_courier_region, 
        $this->_for_renewal, 
        $this->_srv_app, 
        $this->_srv_date, 
        $this->_srv_services, 
        $this->_srv_status, 
        $this->_srv_salesman, 
        $this->_srv_result, 
        $this->_srv_comments, 
        $this->_srv_price, 
        $this->_srv_field1, 
        $this->_srv_field2, 
        $this->_srv_field3, 
        $this->_log_code, 
        $this->_parent_record, 
        $this->_child_record)); 
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
        aux_field = ?, 
        instagram = ?, 
        instagram_dm = ?, 
        courier_address = ?, 
        courier_city = ?, 
        courier_zipcode = ?, 
        courier_phone = ?, 
        fb_package = ?, 
        fb_discount = ?, 
        fb_price = ?, 
        fb_expires = ?, 
        fb_page = ?, 
        fb_comments = ?, 
        fb_months = ?, 
        fb_ok = ?, 
        ga_package = ?, 
        ga_discount = ?, 
        ga_price = ?, 
        ga_expires = ?, 
        ga_page = ?, 
        ga_comments = ?, 
        ga_keywords = ?, 
        ga_months = ?, 
        ga_ok = ?, 
        mark = ?, 
        region = ?, 
        courier_region = ?, 
        for_renewal = ?, 
        srv_app = ?, 
        srv_date = ?, 
        srv_services = ?, 
        srv_status = ?, 
        srv_salesman = ?, 
        srv_result = ?, 
        srv_comments = ?, 
        srv_price = ?, 
        srv_field1 = ?, 
        srv_field2 = ?, 
        srv_field3 = ?, 
        log_code = ?, 
        parent_record = ?, 
        child_record = ?
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
        $this->_aux_field, 
        $this->_instagram, 
        $this->_instagram_dm, 
        $this->_courier_address, 
        $this->_courier_city, 
        $this->_courier_zipcode, 
        $this->_courier_phone, 
        $this->_fb_package, 
        $this->_fb_discount, 
        $this->_fb_price, 
        $this->_fb_expires, 
        $this->_fb_page, 
        $this->_fb_comments, 
        $this->_fb_months, 
        $this->_fb_ok, 
        $this->_ga_package, 
        $this->_ga_discount, 
        $this->_ga_price, 
        $this->_ga_expires, 
        $this->_ga_page, 
        $this->_ga_comments, 
        $this->_ga_keywords, 
        $this->_ga_months, 
        $this->_ga_ok, 
        $this->_mark, 
        $this->_region, 
        $this->_courier_region, 
        $this->_for_renewal, 
        $this->_srv_app, 
        $this->_srv_date, 
        $this->_srv_services, 
        $this->_srv_status, 
        $this->_srv_salesman, 
        $this->_srv_result, 
        $this->_srv_comments, 
        $this->_srv_price, 
        $this->_srv_field1, 
        $this->_srv_field2, 
        $this->_srv_field3, 
        $this->_log_code, 
        $this->_parent_record, 
        $this->_child_record,
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









class COMPANIES extends COMPANIES_BASE
{
    
    protected $_dbo, $_id, $_rs;
    
    public function __construct($dbo, $id, $rs = NULL, $sql = '') { 
        parent::__construct($dbo, $id, $rs, $sql);
        $this->_dbo = $dbo;
        $this->_id = $id;
        
        $this->_rs = parent::get_rs();
        
        if ($id==0) {
            parent::set_courier_status(1);
        }                
    }
    
    public function get_field($fieldName) {
	return $this->_rs[$fieldName];
    }
    
    public function set_companyname($val, $setDate = TRUE) {
        if (parent::get_companyname() != $val && $setDate) {
            parent::set_companyname_dm(date('YmdHis'));            
        }
        parent::set_companyname($val);
    }
    
    public function set_phone1($val, $setDate = TRUE) {
        if (parent::get_phone1() != $val && $setDate) {
            parent::set_phone1_dm(date('YmdHis'));            
        }
        parent::set_phone1($val);
        parent::set_phone1digits(func::digits($val));
    }
    
    public function set_phone2($val, $setDate = TRUE) {
        if (parent::get_phone2() != $val && $setDate) {
            parent::set_phone2_dm(date('YmdHis'));            
        }
        parent::set_phone2($val);
        parent::set_phone2digits(func::digits($val));
    }
    
    public function set_fax($val, $setDate = TRUE) {
        if (parent::get_fax() != $val && $setDate) {
            parent::set_fax_dm(date('YmdHis'));            
        }
        parent::set_fax($val);
        parent::set_faxdigits(func::digits($val));
    }
	 
    public function set_mobilephone($val, $setDate = TRUE) {
        if (parent::get_mobilephone() != $val && $setDate) {
            parent::set_mobile_dm(date('YmdHis'));             
        }
        parent::set_mobilephone($val);
        parent::set_mobiledigits(func::digits($val));        
    }
	 
    public function set_basiccategory($val, $setDate = TRUE) {
        if (parent::get_basiccategory() != $val && $setDate) {
            parent::set_basiccat_dm(date('YmdHis'));
        }
        parent::set_basiccategory($val);
    }
	 
    public function set_area($val, $setDate = TRUE) {
        if (parent::get_area() != $val && $setDate) {
            parent::set_area_dm(date('YmdHis'));            
        }
        parent::set_area($val);
    }
	
    public function set_geo_x($val, $setDate = TRUE) {
        if (parent::get_geo_x() != $val && $setDate) {
            parent::set_geox_dm(date('YmdHis'));            
        }
        parent::set_geo_x($val);
    }
	 
    public function set_geo_y($val, $setDate = TRUE) {
        if (parent::get_geo_y() != $val && $setDate) {
            parent::set_geoy_dm(date('YmdHis'));            
        }
        parent::set_geo_y($val);
    }
	 
    public function set_address($val, $setDate = TRUE) {
        if (parent::get_address() != $val && $setDate) {
            parent::set_address_dm(date('YmdHis'));            
        }
        parent::set_address($val);
    }
	 
    public function set_zipcode($val, $setDate = TRUE) {
        if (parent::get_zipcode() != $val && $setDate) {
            parent::set_zipcode_dm(date('YmdHis'));            
        }
        parent::set_zipcode($val);
    }
    
    
    public function set_email($val, $setDate = TRUE) {
        if (parent::get_email() != $val && $setDate) {
            parent::set_email_dm(date('YmdHis'));            
        }
        parent::set_email($val);
    }
    
    public function get_website() {
        return stripslashes(parent::get_website());
    }    
	 
    public function set_website($val, $setDate = TRUE) {
        if (parent::get_website() != $val && $setDate) {
            parent::set_website_dm(date('YmdHis'));            
        }
        parent::set_website($val);
    }
    
    
    public function get_facebook() {
        return stripslashes(parent::get_facebook());
    }
	 
    public function set_facebook($val, $setDate = TRUE) {
        if (parent::get_facebook() != $val && $setDate) {
            parent::set_facebook_dm(date('YmdHis'));            
        }
        parent::set_facebook($val);
    }
    
    public function get_twitter() {
        return stripslashes(parent::get_twitter());
    }
	 
    public function set_twitter($val, $setDate = TRUE) {
        if (parent::get_twitter() != $val && $setDate) {
            parent::set_twitter_dm(date('YmdHis'));            
        }
        parent::set_twitter($val);
    }
	 
    public function set_LinkedIn($val, $setDate = TRUE) {
        if (parent::get_LinkedIn() != $val && $setDate) {
            parent::set_linkedin_dm(date('YmdHis'));
        }
        parent::set_LinkedIn($val);
    }
	 
    public function set_ShortDescription($val, $setDate = TRUE) {
        if (parent::get_ShortDescription() != $val && $setDate) {
            parent::set_shortdescr_dm(date('YmdHis'));            
        }
        parent::set_ShortDescription($val);
    }
	 
	public function set_FullDescription($val, $setDate = TRUE) {
        if (parent::get_FullDescription() != $val && $setDate) {
            parent::set_fulldescr_dm(date('YmdHis'));            
        }
        parent::set_FullDescription($val);
    }
	 
	public function set_city_id($val, $setDate = TRUE) {
        if (parent::get_city_id() != $val && $setDate) {
            parent::set_cityid_dm(date('YmdHis'));            
        }
        parent::set_city_id($val);
    }
	 
    public function set_vn_keywords($val, $setDate = TRUE) {
        if (parent::get_vn_keywords() != $val && $setDate) {
            parent::set_keywords_dm(date('YmdHis'));            
        }
        parent::set_vn_keywords($val);
    }
	 
    public function set_profession($val, $setDate = TRUE) {
        if (parent::get_profession() != $val && $setDate) {
            parent::set_profession_dm(date('YmdHis'));            
        }
        parent::set_profession($val);
    }
	 
    public function set_googleplus($val, $setDate = TRUE) {
        if (parent::get_googleplus() != $val && $setDate) {
            parent::set_googleplus_dm(date('YmdHis'));            
        }
        parent::set_googleplus($val);
    }
	 
    public function set_pinterest($val, $setDate = TRUE) {
        if (parent::get_pinterest() != $val && $setDate) {
            parent::set_pinterest_dm(date('YmdHis'));            
        }
        parent::set_pinterest($val);
    }
	 
    public function set_instagram($val, $setDate = TRUE) {
        if (parent::get_instagram() != $val && $setDate) {
            parent::set_instagram_dm(date('YmdHis'));            
        }
        parent::set_instagram($val);
    }
	 
    public function set_sites($val, $setDate = TRUE) {
        if (parent::get_sites() != $val && $setDate) {
            parent::set_sites_dm(date('YmdHis'));            
        }
        parent::set_sites($val);
    }
	 
    public function set_workinghours($val, $setDate = TRUE) {
        if (parent::get_workinghours() != $val && $setDate) {
            parent::set_workinghours_dm(date('YmdHis'));            
        }
        parent::set_workinghours($val);
    }
	 
    public function set_workingmonths($val, $setDate = TRUE) {
        if (parent::get_workingmonths() != $val && $setDate) {
            parent::set_workingmonths_dm(date('YmdHis'));            
        }
        parent::set_workingmonths($val);
    }
    
    public function set_expires($val, $setDate = TRUE) {
        if (parent::get_expires() != $val && $setDate) {
            parent::set_expires_dm(date('YmdHis'));            
        }
        parent::set_expires($val);
    }
    
    public function set_password($val, $setDate = TRUE) {
        if (parent::get_password() != $val && $setDate) {
            parent::set_password_dm(date('YmdHis'));            
        }
        parent::set_password($val);
    }
    
    
    public function get_eponimia() {
        return stripslashes(parent::get_eponimia());
    }
    
    public function get_city() { 
        $city = func::vlookup("description", "EP_CITIES", "id=" . parent::get_city_id(), $this->_dbo);
        return $city; 
    }
    
    public function set_id($val) {
        $this->_id = $val;
    }
    
    public function get_courier_city_descr() { 
        $couriercity = func::vlookup("description", "EP_CITIES", "id=".parent::get_courier_city(), $this->_dbo);
        return $couriercity; 
    }
    
    public function Savedata() {
        $allPhoneDigits = parent::get_phone1digits() . "-" .
            parent::get_phone2digits()  . "-" .
            parent::get_faxdigits()  . "-" .
            parent::get_mobiledigits();
        parent::set_allphonesdigits($allPhoneDigits);
        
        $res = parent::Savedata();
        
        return $res;
        
 }
    
    
}







class CATEGORIES_BASE
{

protected $_myconn, $_rs, $_id, $_parentid, $_description, $_description_en, $_level, $_path, $_active, $_nodes, $_seo_title, $_seo_description, $_seo_url, $_photo, $_icon ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM CATEGORIES WHERE id=?"; 
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
        $this->_parentid = $all_rows[0]['parentid']; 
        $this->_description = $all_rows[0]['description']; 
        $this->_description_en = $all_rows[0]['description_en']; 
        $this->_level = $all_rows[0]['level']; 
        $this->_path = $all_rows[0]['path']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_nodes = $all_rows[0]['nodes']; 
        $this->_seo_title = $all_rows[0]['seo_title']; 
        $this->_seo_description = $all_rows[0]['seo_description']; 
        $this->_seo_url = $all_rows[0]['seo_url']; 
        $this->_photo = $all_rows[0]['photo']; 
        $this->_icon = $all_rows[0]['icon']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_parentid() { 
    return $this->_parentid; 
} 
public function set_parentid($val) { 
    $this->_parentid = $val; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_description_en() { 
    return $this->_description_en; 
} 
public function set_description_en($val) { 
    $this->_description_en = $val; 
} 

public function get_level() { 
    return $this->_level; 
} 
public function set_level($val) { 
    $this->_level = $val; 
} 

public function get_path() { 
    return $this->_path; 
} 
public function set_path($val) { 
    $this->_path = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_nodes() { 
    return $this->_nodes; 
} 
public function set_nodes($val) { 
    $this->_nodes = $val; 
} 

public function get_seo_title() { 
    return $this->_seo_title; 
} 
public function set_seo_title($val) { 
    $this->_seo_title = $val; 
} 

public function get_seo_description() { 
    return $this->_seo_description; 
} 
public function set_seo_description($val) { 
    $this->_seo_description = $val; 
} 

public function get_seo_url() { 
    return $this->_seo_url; 
} 
public function set_seo_url($val) { 
    $this->_seo_url = $val; 
} 

public function get_photo() { 
    return $this->_photo; 
} 
public function set_photo($val) { 
    $this->_photo = $val; 
} 

public function get_icon() { 
    return $this->_icon; 
} 
public function set_icon($val) { 
    $this->_icon = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO CATEGORIES ( 
    parentid,
    description,
    description_en,
    level,
    path,
    active,
    nodes,
    seo_title,
    seo_description,
    seo_url,
    photo,
    icon
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_parentid, 
        $this->_description, 
        $this->_description_en, 
        $this->_level, 
        $this->_path, 
        $this->_active, 
        $this->_nodes, 
        $this->_seo_title, 
        $this->_seo_description, 
        $this->_seo_url, 
        $this->_photo, 
        $this->_icon)); 
    $ssql = $this->_myconn->getLastIDsql('CATEGORIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE CATEGORIES set 
        parentid = ?, 
        description = ?, 
        description_en = ?, 
        level = ?, 
        path = ?, 
        active = ?, 
        nodes = ?, 
        seo_title = ?, 
        seo_description = ?, 
        seo_url = ?, 
        photo = ?, 
        icon = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_parentid, 
        $this->_description, 
        $this->_description_en, 
        $this->_level, 
        $this->_path, 
        $this->_active, 
        $this->_nodes, 
        $this->_seo_title, 
        $this->_seo_description, 
        $this->_seo_url, 
        $this->_photo, 
        $this->_icon,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM CATEGORIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class CATEGORIES extends CATEGORIES_BASE
{
    
    protected $_dbo, $_id;
    
    public function __construct($dbo, $id, $rs = NULL, $sql = '') { 
        parent::__construct($dbo, $id, $rs, $sql);
        $this->_dbo = $dbo;
        $this->_id = $id;
    }
    
    //EXTRA FUNCTIONS
    
    public function Savedata() {
        $res = parent::Savedata();
        
        //EXTRA
        if ($res) {
            if ($this->UpdateParent()) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
        
    }
    
    public function Delete() {
        $ssql = "DELETE FROM CATEGORIES WHERE id=?";
        $result = $this->_myconn->execSQL($ssql, array($this->_id));
        if ($result===false) {
            return false;
        }
        /*
        else {
            return true;
        }
        */
        //EXTRA
        if ($this->UpdateParent()) {
            return true;
        }
        else {
            return false;
        }
    }

        
    private function UpdateParent() {
        if ($this->_parentid==0) {
            return TRUE;
        }
        $parent = new CATEGORIES($this->_myconn,$this->_parentid);
        $sql = "SELECT COUNT(*) AS MyCount FROM CATEGORIES WHERE parentid = ".$this->_parentid;
        $res = $this->_myconn->getRS($sql);
        if ($res[0]['MyCount']>0) {
            $parent->set_nodes(1);
        }
        else {
            $parent->set_nodes(0);
        }
        return $parent->Savedata();
    }

}  




class USER_PROFILES
{

protected $_myconn, $_id, $_description, $_flag1, $_flag2, $_flag3, $_flag4, $_flag5, $_flag6, $_flag7, $_flag8, $_flag9, $_flag10 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM USER_PROFILES WHERE id=?";
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
        $this->_description = $all_rows[0]['description'];
        $this->_flag1 = $all_rows[0]['flag1'];
        $this->_flag2 = $all_rows[0]['flag2'];
        $this->_flag3 = $all_rows[0]['flag3'];
        $this->_flag4 = $all_rows[0]['flag4'];
        $this->_flag5 = $all_rows[0]['flag5'];
        $this->_flag6 = $all_rows[0]['flag6'];
        $this->_flag7 = $all_rows[0]['flag7'];
        $this->_flag8 = $all_rows[0]['flag8'];
        $this->_flag9 = $all_rows[0]['flag9'];
        $this->_flag10 = $all_rows[0]['flag10'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_description() {
    return $this->_description;
}
public function set_description($val) {
    $this->_description = $val;
}

public function get_flag1() {
    return $this->_flag1;
}
public function set_flag1($val) {
    $this->_flag1 = $val;
}

public function get_flag2() {
    return $this->_flag2;
}
public function set_flag2($val) {
    $this->_flag2 = $val;
}

public function get_flag3() {
    return $this->_flag3;
}
public function set_flag3($val) {
    $this->_flag3 = $val;
}

public function get_flag4() {
    return $this->_flag4;
}
public function set_flag4($val) {
    $this->_flag4 = $val;
}

public function get_flag5() {
    return $this->_flag5;
}
public function set_flag5($val) {
    $this->_flag5 = $val;
}

public function get_flag6() {
    return $this->_flag6;
}
public function set_flag6($val) {
    $this->_flag6 = $val;
}

public function get_flag7() {
    return $this->_flag7;
}
public function set_flag7($val) {
    $this->_flag7 = $val;
}

public function get_flag8() {
    return $this->_flag8;
}
public function set_flag8($val) {
    $this->_flag8 = $val;
}

public function get_flag9() {
    return $this->_flag9;
}
public function set_flag9($val) {
    $this->_flag9 = $val;
}

public function get_flag10() {
    return $this->_flag10;
}
public function set_flag10($val) {
    $this->_flag10 = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO USER_PROFILES (
    description,
    flag1,
    flag2,
    flag3,
    flag4,
    flag5,
    flag6,
    flag7,
    flag8,
    flag9,
    flag10
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_flag1,
        $this->_flag2,
        $this->_flag3,
        $this->_flag4,
        $this->_flag5,
        $this->_flag6,
        $this->_flag7,
        $this->_flag8,
        $this->_flag9,
        $this->_flag10));
    $ssql = $this->_myconn->getLastIDsql('USER_PROFILES');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE USER_PROFILES set
        description = ?,
        flag1 = ?,
        flag2 = ?,
        flag3 = ?,
        flag4 = ?,
        flag5 = ?,
        flag6 = ?,
        flag7 = ?,
        flag8 = ?,
        flag9 = ?,
        flag10 = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_flag1,
        $this->_flag2,
        $this->_flag3,
        $this->_flag4,
        $this->_flag5,
        $this->_flag6,
        $this->_flag7,
        $this->_flag8,
        $this->_flag9,
        $this->_flag10,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM USER_PROFILES WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 



class PACKAGES
{

protected $_myconn, $_id, $_description, $_comment, $_price, $_duration, $_active, $_online_package, $_comment2, $_basic, $_product_category ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM PACKAGES WHERE id=?"; 
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
        $this->_description = $all_rows[0]['description']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_price = $all_rows[0]['price']; 
        $this->_duration = $all_rows[0]['duration']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_online_package = $all_rows[0]['online_package']; 
        $this->_comment2 = $all_rows[0]['comment2']; 
        $this->_basic = $all_rows[0]['basic']; 
        $this->_product_category = $all_rows[0]['product_category']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_price() { 
    return $this->_price; 
} 
public function set_price($val) { 
    $this->_price = $val; 
} 

public function get_duration() { 
    return $this->_duration; 
} 
public function set_duration($val) { 
    $this->_duration = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_online_package() { 
    return $this->_online_package; 
} 
public function set_online_package($val) { 
    $this->_online_package = $val; 
} 

public function get_comment2() { 
    return $this->_comment2; 
} 
public function set_comment2($val) { 
    $this->_comment2 = $val; 
} 

public function get_basic() { 
    return $this->_basic; 
} 
public function set_basic($val) { 
    $this->_basic = $val; 
} 

public function get_product_category() { 
    return $this->_product_category; 
} 
public function set_product_category($val) { 
    $this->_product_category = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO PACKAGES ( 
    description,
    comment,
    price,
    duration,
    active,
    online_package,
    comment2,
    basic,
    product_category
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_comment, 
        $this->_price, 
        $this->_duration, 
        $this->_active, 
        $this->_online_package, 
        $this->_comment2, 
        $this->_basic, 
        $this->_product_category)); 
    $ssql = $this->_myconn->getLastIDsql('PACKAGES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE PACKAGES set 
        description = ?, 
        comment = ?, 
        price = ?, 
        duration = ?, 
        active = ?, 
        online_package = ?, 
        comment2 = ?, 
        basic = ?, 
        product_category = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_comment, 
        $this->_price, 
        $this->_duration, 
        $this->_active, 
        $this->_online_package, 
        $this->_comment2, 
        $this->_basic, 
        $this->_product_category,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM PACKAGES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}





class PROFESSIONS
{

protected $_myconn, $_rs, $_id, $_description ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM PROFESSIONS WHERE id=?";
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
        $this->_description = $all_rows[0]['description'];

        $this->_rs = $all_rows[0];

    }
}

public function get_id() {
    return $this->_id;
}

public function get_rs() {
    return $this->_rs;
}

public function get_description() {
    return $this->_description;
}
public function set_description($val) {
    $this->_description = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO PROFESSIONS (
    description
    ) VALUES (?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description));
    $ssql = $this->_myconn->getLastIDsql('PROFESSIONS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE PROFESSIONS set
        description = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM PROFESSIONS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 






class DISCOUNTS
{

protected $_myconn, $_id, $_package, $_discount, $_datestart, $_datestop, $_active ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM DISCOUNTS WHERE id=?";
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
        $this->_package = $all_rows[0]['package'];
        $this->_discount = $all_rows[0]['discount'];
        $this->_datestart = $all_rows[0]['datestart'];
        $this->_datestop = $all_rows[0]['datestop'];
        $this->_active = $all_rows[0]['active'];
    }
}

public function get_id() {
    return $this->_id;
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

public function get_datestart() {
    return $this->_datestart;
}
public function set_datestart($val) {
    $this->_datestart = $val;
}

public function get_datestop() {
    return $this->_datestop;
}
public function set_datestop($val) {
    $this->_datestop = $val;
}

public function get_active() {
    return $this->_active;
}
public function set_active($val) {
    $this->_active = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO DISCOUNTS (
    package,
    discount,
    datestart,
    datestop,
    active
    ) VALUES (?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_package,
        $this->_discount,
        $this->_datestart,
        $this->_datestop,
        $this->_active));
    $ssql = $this->_myconn->getLastIDsql('DISCOUNTS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE DISCOUNTS set
        package = ?,
        discount = ?,
        datestart = ?,
        datestop = ?,
        active = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_package,
        $this->_discount,
        $this->_datestart,
        $this->_datestop,
        $this->_active,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM DISCOUNTS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 





class ACTIONS
{

protected $_myconn, $_rs, $_id, $_company, $_user, $_status1, $_status2, $_adatetime, $_atimestamp, $_comment, $_product_categories, $_voucherid ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM ACTIONS WHERE id=?"; 
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
        $this->_company = $all_rows[0]['company']; 
        $this->_user = $all_rows[0]['user']; 
        $this->_status1 = $all_rows[0]['status1']; 
        $this->_status2 = $all_rows[0]['status2']; 
        $this->_adatetime = $all_rows[0]['adatetime']; 
        $this->_atimestamp = $all_rows[0]['atimestamp']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_product_categories = $all_rows[0]['product_categories']; 
        $this->_voucherid = $all_rows[0]['voucherid']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_company() { 
    return $this->_company; 
} 
public function set_company($val) { 
    $this->_company = $val; 
} 

public function get_user() { 
    return $this->_user; 
} 
public function set_user($val) { 
    $this->_user = $val; 
} 

public function get_status1() { 
    return $this->_status1; 
} 
public function set_status1($val) { 
    $this->_status1 = $val; 
} 

public function get_status2() { 
    return $this->_status2; 
} 
public function set_status2($val) { 
    $this->_status2 = $val; 
} 

public function get_adatetime() { 
    return $this->_adatetime; 
} 
public function set_adatetime($val) { 
    $this->_adatetime = $val; 
} 

public function get_atimestamp() { 
    return $this->_atimestamp; 
} 
public function set_atimestamp($val) { 
    $this->_atimestamp = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_product_categories() { 
    return $this->_product_categories; 
} 
public function set_product_categories($val) { 
    $this->_product_categories = $val; 
} 

public function get_voucherid() { 
    return $this->_voucherid; 
} 
public function set_voucherid($val) { 
    $this->_voucherid = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO ACTIONS ( 
    company,
    user,
    status1,
    status2,
    adatetime,
    atimestamp,
    comment,
    product_categories,
    voucherid
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_user, 
        $this->_status1, 
        $this->_status2, 
        $this->_adatetime, 
        $this->_atimestamp, 
        $this->_comment, 
        $this->_product_categories, 
        $this->_voucherid)); 
    $ssql = $this->_myconn->getLastIDsql('ACTIONS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE ACTIONS set 
        company = ?, 
        user = ?, 
        status1 = ?, 
        status2 = ?, 
        adatetime = ?, 
        atimestamp = ?, 
        comment = ?, 
        product_categories = ?, 
        voucherid = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_user, 
        $this->_status1, 
        $this->_status2, 
        $this->_adatetime, 
        $this->_atimestamp, 
        $this->_comment, 
        $this->_product_categories, 
        $this->_voucherid,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM ACTIONS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}





class ACTIONS_DELETED
{

protected $_myconn, $_id, $_company, $_user, $_status1, $_status2, $_adatetime, $_atimestamp, $_comment, $_product_categories, $_userDel ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM ACTIONS_DELETED WHERE id=?"; 
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
        $this->_company = $all_rows[0]['company']; 
        $this->_user = $all_rows[0]['user']; 
        $this->_status1 = $all_rows[0]['status1']; 
        $this->_status2 = $all_rows[0]['status2']; 
        $this->_adatetime = $all_rows[0]['adatetime']; 
        $this->_atimestamp = $all_rows[0]['atimestamp']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_product_categories = $all_rows[0]['product_categories']; 
        $this->_userDel = $all_rows[0]['userDel']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_company() { 
    return $this->_company; 
} 
public function set_company($val) { 
    $this->_company = $val; 
} 

public function get_user() { 
    return $this->_user; 
} 
public function set_user($val) { 
    $this->_user = $val; 
} 

public function get_status1() { 
    return $this->_status1; 
} 
public function set_status1($val) { 
    $this->_status1 = $val; 
} 

public function get_status2() { 
    return $this->_status2; 
} 
public function set_status2($val) { 
    $this->_status2 = $val; 
} 

public function get_adatetime() { 
    return $this->_adatetime; 
} 
public function set_adatetime($val) { 
    $this->_adatetime = $val; 
} 

public function get_atimestamp() { 
    return $this->_atimestamp; 
} 
public function set_atimestamp($val) { 
    $this->_atimestamp = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_product_categories() { 
    return $this->_product_categories; 
} 
public function set_product_categories($val) { 
    $this->_product_categories = $val; 
} 

public function get_userDel() { 
    return $this->_userDel; 
} 
public function set_userDel($val) { 
    $this->_userDel = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO ACTIONS_DELETED ( 
    company,
    user,
    status1,
    status2,
    adatetime,
    atimestamp,
    comment,
    product_categories,
    userDel
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_user, 
        $this->_status1, 
        $this->_status2, 
        $this->_adatetime, 
        $this->_atimestamp, 
        $this->_comment, 
        $this->_product_categories, 
        $this->_userDel)); 
    $ssql = $this->_myconn->getLastIDsql('ACTIONS_DELETED');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE ACTIONS_DELETED set 
        company = ?, 
        user = ?, 
        status1 = ?, 
        status2 = ?, 
        adatetime = ?, 
        atimestamp = ?, 
        comment = ?, 
        product_categories = ?, 
        userDel = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_user, 
        $this->_status1, 
        $this->_status2, 
        $this->_adatetime, 
        $this->_atimestamp, 
        $this->_comment, 
        $this->_product_categories, 
        $this->_userDel,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM ACTIONS_DELETED WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class MESSAGES_BASE
{

protected $_myconn, $_id, $_title, $_message, $_sender, $_receiver, $_mdatetime, $_read, $_senddatetime, $_readdatetime, $_companyid, $_conversation ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM MESSAGES WHERE id=?";
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
        $this->_title = $all_rows[0]['title'];
        $this->_message = $all_rows[0]['message'];
        $this->_sender = $all_rows[0]['sender'];
        $this->_receiver = $all_rows[0]['receiver'];
        $this->_mdatetime = $all_rows[0]['mdatetime'];
        $this->_read = $all_rows[0]['read'];
        $this->_senddatetime = $all_rows[0]['senddatetime'];
        $this->_readdatetime = $all_rows[0]['readdatetime'];
	$this->_companyid = $all_rows[0]['companyid'];
	$this->_conversation = $all_rows[0]['conversation']; 	
    }
}

public function get_id() {
    return $this->_id;
}

public function get_title() {
    return $this->_title;
}
public function set_title($val) {
    $this->_title = $val;
}

public function get_message() {
    return $this->_message;
}
public function set_message($val) {
    $this->_message = $val;
}

public function get_sender() {
    return $this->_sender;
}
public function set_sender($val) {
    $this->_sender = $val;
}

public function get_receiver() {
    return $this->_receiver;
}
public function set_receiver($val) {
    $this->_receiver = $val;
}

public function get_mdatetime() {
    return $this->_mdatetime;
}
public function set_mdatetime($val) {
    $this->_mdatetime = $val;
}

public function get_read() {
    return $this->_read;
}
public function set_read($val) {
    $this->_read = $val;
}

public function get_senddatetime() {
    return $this->_senddatetime;
}
public function set_senddatetime($val) {
    $this->_senddatetime = $val;
}

public function get_readdatetime() {
    return $this->_readdatetime;
}
public function set_readdatetime($val) {
    $this->_readdatetime = $val;
}

public function get_companyid() {
    return $this->_companyid;
}
public function set_companyid($val) {
    $this->_companyid = $val;
}

public function get_conversation() { 
    return $this->_conversation; 
} 
public function set_conversation($val) { 
    $this->_conversation = $val; 
}


public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO `MESSAGES` (
    `title`,
    `message`,
    `sender`,
    `receiver`,
    `read`,
    `senddatetime`,
    `readdatetime`,
    `companyid`,
    conversation
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_title,
        $this->_message,
        $this->_sender,
        $this->_receiver,
        $this->_read,
        $this->_senddatetime,
        $this->_readdatetime,
	$this->_companyid,
        $this->_conversation
        ));
    $ssql = $this->_myconn->getLastIDsql('MESSAGES');
        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE MESSAGES set
        `title` = ?,
        `message` = ?,
        `sender` = ?,
        `receiver` = ?,
        `read` = ?,
        `senddatetime` = ?,
        `readdatetime` = ?,
	`companyid` = ?,
        conversation = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_title,
        $this->_message,
        $this->_sender,
        $this->_receiver,
        $this->_read,
        $this->_senddatetime,
        $this->_readdatetime,
	$this->_companyid,
        $this->_conversation,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM MESSAGES WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 




class MESSAGES extends MESSAGES_BASE
{
    
    protected $_dbo, $_id;
    
    public function __construct($dbo, $id, $rs = NULL, $sql = '') { 
        parent::__construct($dbo, $id, $rs, $sql);
        $this->_dbo = $dbo;
        $this->_id = $id;
    }
    
    public function Savedata() {        
        if (parent::get_conversation()=="" || parent::get_conversation()==0) {
            $conversation = new CONVERSATIONS($this->_dbo, 0);
            $conversation->set_sender(parent::get_sender());
            $conversation->set_receiver(parent::get_receiver());
            $conversation->set_isread(0);
            $conversation->set_lastdatetime(date("YmdHis"));
            $conversation->Savedata();
            parent::set_conversation($conversation->get_id());
        }        
        else {
            $conversation = new CONVERSATIONS($this->_dbo, parent::get_conversation());
            $conversation->set_isread(0);
            $conversation->set_lastdatetime(date("YmdHis"));
            $conversation->Savedata();
            parent::set_conversation($conversation->get_id());
            
        }
        parent::Savedata();
        
    }
    
    
}





class CONVERSATIONS
{

protected $_myconn, $_id, $_sender, $_receiver, $_isread, $_lastdatetime ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM CONVERSATIONS WHERE id=?"; 
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
        $this->_sender = $all_rows[0]['sender']; 
        $this->_receiver = $all_rows[0]['receiver']; 
        $this->_isread = $all_rows[0]['isread']; 
        $this->_lastdatetime = $all_rows[0]['lastdatetime']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_sender() { 
    return $this->_sender; 
} 
public function set_sender($val) { 
    $this->_sender = $val; 
} 

public function get_receiver() { 
    return $this->_receiver; 
} 
public function set_receiver($val) { 
    $this->_receiver = $val; 
} 

public function get_isread() { 
    return $this->_isread; 
} 
public function set_isread($val) { 
    $this->_isread = $val; 
} 

public function get_lastdatetime() { 
    return $this->_lastdatetime; 
} 
public function set_lastdatetime($val) { 
    $this->_lastdatetime = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO CONVERSATIONS ( 
    sender,
    receiver,
    isread,
    lastdatetime
    ) VALUES (?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_sender, 
        $this->_receiver, 
        $this->_isread, 
        $this->_lastdatetime)); 
    $ssql = $this->_myconn->getLastIDsql('CONVERSATIONS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE CONVERSATIONS set 
        sender = ?, 
        receiver = ?, 
        isread = ?, 
        lastdatetime = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_sender, 
        $this->_receiver, 
        $this->_isread, 
        $this->_lastdatetime,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM CONVERSATIONS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class PRINTJOB
{

protected $_myconn, $_id, $_user, $_ptemplate, $_printername, $_pdatetime, $_pstatus ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM PRINTJOB WHERE id=?";
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
        $this->_user = $all_rows[0]['user'];
        $this->_ptemplate = $all_rows[0]['ptemplate'];
        $this->_printername = $all_rows[0]['printername'];
        $this->_pdatetime = $all_rows[0]['pdatetime'];
        $this->_pstatus = $all_rows[0]['pstatus'];
    }
    //EXTRA
    else {
        $this->_pstatus = 1;
    }
}

public function get_id() {
    return $this->_id;
}

public function get_user() {
    return $this->_user;
}
public function set_user($val) {
    $this->_user = $val;
}

public function get_ptemplate() {
    return $this->_ptemplate;
}
public function set_ptemplate($val) {
    $this->_ptemplate = $val;
}

public function get_printername() {
    return $this->_printername;
}
public function set_printername($val) {
    $this->_printername = $val;
}

public function get_pdatetime() {
    return $this->_pdatetime;
}
public function set_pdatetime($val) {
    $this->_pdatetime = $val;
}

public function get_pstatus() {
    return $this->_pstatus;
}
public function set_pstatus($val) {
    $this->_pstatus = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO PRINTJOB (
    user,
    ptemplate,
    printername,
    pstatus
    ) VALUES (?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_user,
        $this->_ptemplate,
        $this->_printername,
        $this->_pstatus));
    $ssql = $this->_myconn->getLastIDsql('PRINTJOB');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE PRINTJOB set
        user = ?,
        ptemplate = ?,
        printername = ?,
        pstatus = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_user,
        $this->_ptemplate,
        $this->_printername,
        $this->_pstatus,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM PRINTJOB WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 



class PRINTDETAILS
{

protected $_myconn, $_id, $_jobid, $_bookmark, $_ptext, $_pformat ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM PRINTDETAILS WHERE id=?";
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
        $this->_jobid = $all_rows[0]['jobid'];
        $this->_bookmark = $all_rows[0]['bookmark'];
        $this->_ptext = $all_rows[0]['ptext'];
        $this->_pformat = $all_rows[0]['pformat'];
    }
    //EXTRA
    else {
        $this->_pformat = "";
    }
}

public function get_id() {
    return $this->_id;
}

public function get_jobid() {
    return $this->_jobid;
}
public function set_jobid($val) {
    $this->_jobid = $val;
}

public function get_bookmark() {
    return $this->_bookmark;
}
public function set_bookmark($val) {
    $this->_bookmark = $val;
}

public function get_ptext() {
    return $this->_ptext;
}
public function set_ptext($val) {
    $this->_ptext = $val;
}

public function get_pformat() {
    return $this->_pformat;
}
public function set_pformat($val) {
    $this->_pformat = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO PRINTDETAILS (
    jobid,
    bookmark,
    ptext,
    pformat
    ) VALUES (?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_jobid,
        $this->_bookmark,
        $this->_ptext,
        $this->_pformat));
    $ssql = $this->_myconn->getLastIDsql('PRINTDETAILS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE PRINTDETAILS set
        jobid = ?,
        bookmark = ?,
        ptext = ?,
        pformat = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_jobid,
        $this->_bookmark,
        $this->_ptext,
        $this->_pformat,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM PRINTDETAILS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 



class PRINTERS
{

protected $_myconn, $_id, $_printername, $_comment ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM PRINTERS WHERE id=?";
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
        $this->_printername = $all_rows[0]['printername'];
        $this->_comment = $all_rows[0]['comment'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_printername() {
    return $this->_printername;
}
public function set_printername($val) {
    $this->_printername = $val;
}

public function get_comment() {
    return $this->_comment;
}
public function set_comment($val) {
    $this->_comment = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO PRINTERS (
    printername,
    comment
    ) VALUES (?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_printername,
        $this->_comment));
    $ssql = $this->_myconn->getLastIDsql('PRINTERS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE PRINTERS set
        printername = ?,
        comment = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_printername,
        $this->_comment,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM PRINTERS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 




class PRINTSETTINGS
{

protected $_myconn, $_id, $_description, $_pfunction, $_printer, $_template, $_comment ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM PRINTSETTINGS WHERE id=?";
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
        $this->_description = $all_rows[0]['description'];
        $this->_pfunction = $all_rows[0]['pfunction'];
        $this->_printer = $all_rows[0]['printer'];
        $this->_template = $all_rows[0]['template'];
        $this->_comment = $all_rows[0]['comment'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_description() {
    return $this->_description;
}
public function set_description($val) {
    $this->_description = $val;
}

public function get_pfunction() {
    return $this->_pfunction;
}
public function set_pfunction($val) {
    $this->_pfunction = $val;
}

public function get_printer() {
    return $this->_printer;
}
public function set_printer($val) {
    $this->_printer = $val;
}

public function get_template() {
    return $this->_template;
}
public function set_template($val) {
    $this->_template = $val;
}

public function get_comment() {
    return $this->_comment;
}
public function set_comment($val) {
    $this->_comment = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO PRINTSETTINGS (
    description,
    pfunction,
    printer,
    template,
    comment
    ) VALUES (?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_pfunction,
        $this->_printer,
        $this->_template,
        $this->_comment));
    $ssql = $this->_myconn->getLastIDsql('PRINTSETTINGS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE PRINTSETTINGS set
        description = ?,
        pfunction = ?,
        printer = ?,
        template = ?,
        comment = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_pfunction,
        $this->_printer,
        $this->_template,
        $this->_comment,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM PRINTSETTINGS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}






class COURIER
{

protected $_myconn, $_id, $_description, $_vouchertemplate, $_vouchercount, $_user, $_active, $_comment, $_copies, $_printvoucher, $_voucher_export_to_excel ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM COURIER WHERE id=?"; 
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
        $this->_description = $all_rows[0]['description']; 
        $this->_vouchertemplate = $all_rows[0]['vouchertemplate']; 
        $this->_vouchercount = $all_rows[0]['vouchercount']; 
        $this->_user = $all_rows[0]['user']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_copies = $all_rows[0]['copies']; 
        $this->_printvoucher = $all_rows[0]['printvoucher']; 
        $this->_voucher_export_to_excel = $all_rows[0]['voucher_export_to_excel']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_vouchertemplate() { 
    return $this->_vouchertemplate; 
} 
public function set_vouchertemplate($val) { 
    $this->_vouchertemplate = $val; 
} 

public function get_vouchercount() { 
    return $this->_vouchercount; 
} 
public function set_vouchercount($val) { 
    $this->_vouchercount = $val; 
} 

public function get_user() { 
    return $this->_user; 
} 
public function set_user($val) { 
    $this->_user = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_copies() { 
    return $this->_copies; 
} 
public function set_copies($val) { 
    $this->_copies = $val; 
} 

public function get_printvoucher() { 
    return $this->_printvoucher; 
} 
public function set_printvoucher($val) { 
    $this->_printvoucher = $val; 
} 

public function get_voucher_export_to_excel() { 
    return $this->_voucher_export_to_excel; 
} 
public function set_voucher_export_to_excel($val) { 
    $this->_voucher_export_to_excel = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO COURIER ( 
    description,
    vouchertemplate,
    vouchercount,
    user,
    active,
    comment,
    copies,
    printvoucher,
    voucher_export_to_excel
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_vouchertemplate, 
        $this->_vouchercount, 
        $this->_user, 
        $this->_active, 
        $this->_comment, 
        $this->_copies, 
        $this->_printvoucher, 
        $this->_voucher_export_to_excel)); 
    $ssql = $this->_myconn->getLastIDsql('COURIER');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE COURIER set 
        description = ?, 
        vouchertemplate = ?, 
        vouchercount = ?, 
        user = ?, 
        active = ?, 
        comment = ?, 
        copies = ?, 
        printvoucher = ?, 
        voucher_export_to_excel = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_vouchertemplate, 
        $this->_vouchercount, 
        $this->_user, 
        $this->_active, 
        $this->_comment, 
        $this->_copies, 
        $this->_printvoucher, 
        $this->_voucher_export_to_excel,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM COURIER WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}






class TRANSACTIONS
{

protected $_myconn, $_id, $_company, $_tdatetime, $_amount, $_vatpercentage, $_vat, $_package, $_description, $_price, $_discount, $_seller, $_payedamount, $_status, $_comment, $_transactiontype, $_newsales, $_TDATETIME2, $_resell, $_resend, $_invoiced, $_returned, $_vouchered ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM TRANSACTIONS WHERE id=?"; 
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
        $this->_company = $all_rows[0]['company']; 
        $this->_tdatetime = $all_rows[0]['tdatetime']; 
        $this->_amount = $all_rows[0]['amount']; 
        $this->_vatpercentage = $all_rows[0]['vatpercentage']; 
        $this->_vat = $all_rows[0]['vat']; 
        $this->_package = $all_rows[0]['package']; 
        $this->_description = $all_rows[0]['description']; 
        $this->_price = $all_rows[0]['price']; 
        $this->_discount = $all_rows[0]['discount']; 
        $this->_seller = $all_rows[0]['seller']; 
        $this->_payedamount = $all_rows[0]['payedamount']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_transactiontype = $all_rows[0]['transactiontype']; 
        $this->_newsales = $all_rows[0]['newsales']; 
        $this->_TDATETIME2 = $all_rows[0]['TDATETIME2']; 
        $this->_resell = $all_rows[0]['resell']; 
        $this->_resend = $all_rows[0]['resend']; 
        $this->_invoiced = $all_rows[0]['invoiced']; 
        $this->_returned = $all_rows[0]['returned']; 
        $this->_vouchered = $all_rows[0]['vouchered']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_company() { 
    return $this->_company; 
} 
public function set_company($val) { 
    $this->_company = $val; 
} 

public function get_tdatetime() { 
    return $this->_tdatetime; 
} 
public function set_tdatetime($val) { 
    $this->_tdatetime = $val; 
} 

public function get_amount() { 
    return $this->_amount; 
} 
public function set_amount($val) { 
    $this->_amount = $val; 
} 

public function get_vatpercentage() { 
    return $this->_vatpercentage; 
} 
public function set_vatpercentage($val) { 
    $this->_vatpercentage = $val; 
} 

public function get_vat() { 
    return $this->_vat; 
} 
public function set_vat($val) { 
    $this->_vat = $val; 
} 

public function get_package() { 
    return $this->_package; 
} 
public function set_package($val) { 
    $this->_package = $val; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_price() { 
    return $this->_price; 
} 
public function set_price($val) { 
    $this->_price = $val; 
} 

public function get_discount() { 
    return $this->_discount; 
} 
public function set_discount($val) { 
    $this->_discount = $val; 
} 

public function get_seller() { 
    return $this->_seller; 
} 
public function set_seller($val) { 
    $this->_seller = $val; 
} 

public function get_payedamount() { 
    return $this->_payedamount; 
} 
public function set_payedamount($val) { 
    $this->_payedamount = $val; 
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

public function get_transactiontype() { 
    return $this->_transactiontype; 
} 
public function set_transactiontype($val) { 
    $this->_transactiontype = $val; 
} 

public function get_newsales() { 
    return $this->_newsales; 
} 
public function set_newsales($val) { 
    $this->_newsales = $val; 
} 

public function get_TDATETIME2() { 
    return $this->_TDATETIME2; 
} 
public function set_TDATETIME2($val) { 
    $this->_TDATETIME2 = $val; 
} 

public function get_resell() { 
    return $this->_resell; 
} 
public function set_resell($val) { 
    $this->_resell = $val; 
} 

public function get_resend() { 
    return $this->_resend; 
} 
public function set_resend($val) { 
    $this->_resend = $val; 
} 

public function get_invoiced() { 
    return $this->_invoiced; 
} 
public function set_invoiced($val) { 
    $this->_invoiced = $val; 
} 

public function get_returned() { 
    return $this->_returned; 
} 
public function set_returned($val) { 
    $this->_returned = $val; 
} 

public function get_vouchered() { 
    return $this->_vouchered; 
} 
public function set_vouchered($val) { 
    $this->_vouchered = $val; 
}


public function Savedata() { 
    if ($this->_id==0) { 
        /*EXTRA*/
        $this->_vouchered = 0;
        
        $ssql = "INSERT INTO TRANSACTIONS ( 
            company,
            tdatetime,
            amount,
            vatpercentage,
            vat,
            package,
            description,
            price,
            discount,
            seller,
            payedamount,
            status,
            comment,
            transactiontype,
            newsales,
            TDATETIME2,
            resell,
            resend,
            invoiced,
            returned,
            vouchered
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
        $result = $this->_myconn->execSQL($ssql, array( 
            $this->_company, 
            $this->_tdatetime, 
            $this->_amount, 
            $this->_vatpercentage, 
            $this->_vat, 
            $this->_package, 
            $this->_description, 
            $this->_price, 
            $this->_discount, 
            $this->_seller, 
            $this->_payedamount, 
            $this->_status, 
            $this->_comment, 
            $this->_transactiontype, 
            $this->_newsales, 
            $this->_TDATETIME2, 
            $this->_resell, 
            $this->_resend, 
            $this->_invoiced, 
            $this->_returned,
            $this->_vouchered)); 
        $ssql = $this->_myconn->getLastIDsql('TRANSACTIONS');

            $newrows = $this->_myconn->getRS($ssql); 
            $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE TRANSACTIONS set 
            company = ?, 
            tdatetime = ?, 
            amount = ?, 
            vatpercentage = ?, 
            vat = ?, 
            package = ?, 
            description = ?, 
            price = ?, 
            discount = ?, 
            seller = ?, 
            payedamount = ?, 
            status = ?, 
            comment = ?, 
            transactiontype = ?, 
            newsales = ?, 
            TDATETIME2 = ?, 
            resell = ?, 
            resend = ?, 
            invoiced = ?, 
            returned = ?,
            vouchered = ?
            WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
            $this->_company, 
            $this->_tdatetime, 
            $this->_amount, 
            $this->_vatpercentage, 
            $this->_vat, 
            $this->_package, 
            $this->_description, 
            $this->_price, 
            $this->_discount, 
            $this->_seller, 
            $this->_payedamount, 
            $this->_status, 
            $this->_comment, 
            $this->_transactiontype, 
            $this->_newsales, 
            $this->_TDATETIME2, 
            $this->_resell, 
            $this->_resend, 
            $this->_invoiced, 
            $this->_returned,
            $this->_vouchered,
            $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM TRANSACTIONS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}





class INVOICES
{

protected $_myconn, $_id, $_idate, $_company, $_icode, $_description, $_amount, $_vatpercentage, $_vat, $_comment, $_discount, $_price, $_status, $_companyname, $_profession, $_address, $_zipcode, $_city, $_area, $_phone, $_afm, $_doy, $_series, $_accesstoken, $_headerid ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM INVOICES WHERE id=?"; 
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
        $this->_idate = $all_rows[0]['idate']; 
        $this->_company = $all_rows[0]['company']; 
        $this->_icode = $all_rows[0]['icode']; 
        $this->_description = $all_rows[0]['description']; 
        $this->_amount = $all_rows[0]['amount']; 
        $this->_vatpercentage = $all_rows[0]['vatpercentage']; 
        $this->_vat = $all_rows[0]['vat']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_discount = $all_rows[0]['discount']; 
        $this->_price = $all_rows[0]['price']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_companyname = $all_rows[0]['companyname']; 
        $this->_profession = $all_rows[0]['profession']; 
        $this->_address = $all_rows[0]['address']; 
        $this->_zipcode = $all_rows[0]['zipcode']; 
        $this->_city = $all_rows[0]['city']; 
        $this->_area = $all_rows[0]['area']; 
        $this->_phone = $all_rows[0]['phone']; 
        $this->_afm = $all_rows[0]['afm']; 
        $this->_doy = $all_rows[0]['doy']; 
        $this->_series = $all_rows[0]['series']; 
        $this->_accesstoken = $all_rows[0]['accesstoken']; 
        $this->_headerid = $all_rows[0]['headerid']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_idate() { 
    return $this->_idate; 
} 
public function set_idate($val) { 
    $this->_idate = $val; 
} 

public function get_company() { 
    return $this->_company; 
} 
public function set_company($val) { 
    $this->_company = $val; 
} 

public function get_icode() { 
    return $this->_icode; 
} 
public function set_icode($val) { 
    $this->_icode = $val; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_amount() { 
    return $this->_amount; 
} 
public function set_amount($val) { 
    $this->_amount = $val; 
} 

public function get_vatpercentage() { 
    return $this->_vatpercentage; 
} 
public function set_vatpercentage($val) { 
    $this->_vatpercentage = $val; 
} 

public function get_vat() { 
    return $this->_vat; 
} 
public function set_vat($val) { 
    $this->_vat = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
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

public function get_status() { 
    return $this->_status; 
} 
public function set_status($val) { 
    $this->_status = $val; 
} 

public function get_companyname() { 
    return $this->_companyname; 
} 
public function set_companyname($val) { 
    $this->_companyname = $val; 
} 

public function get_profession() { 
    return $this->_profession; 
} 
public function set_profession($val) { 
    $this->_profession = $val; 
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

public function get_city() { 
    return $this->_city; 
} 
public function set_city($val) { 
    $this->_city = $val; 
} 

public function get_area() { 
    return $this->_area; 
} 
public function set_area($val) { 
    $this->_area = $val; 
} 

public function get_phone() { 
    return $this->_phone; 
} 
public function set_phone($val) { 
    $this->_phone = $val; 
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

public function get_series() { 
    return $this->_series; 
} 
public function set_series($val) { 
    $this->_series = $val; 
} 

public function get_accesstoken() { 
    return $this->_accesstoken; 
} 
public function set_accesstoken($val) { 
    $this->_accesstoken = $val; 
} 

public function get_headerid() { 
    return $this->_headerid; 
} 
public function set_headerid($val) { 
    $this->_headerid = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO INVOICES ( 
    idate,
    company,
    icode,
    description,
    amount,
    vatpercentage,
    vat,
    comment,
    discount,
    price,
    status,
    companyname,
    profession,
    address,
    zipcode,
    city,
    area,
    phone,
    afm,
    doy,
    series,
    accesstoken,
    headerid
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_idate, 
        $this->_company, 
        $this->_icode, 
        $this->_description, 
        $this->_amount, 
        $this->_vatpercentage, 
        $this->_vat, 
        $this->_comment, 
        $this->_discount, 
        $this->_price, 
        $this->_status, 
        $this->_companyname, 
        $this->_profession, 
        $this->_address, 
        $this->_zipcode, 
        $this->_city, 
        $this->_area, 
        $this->_phone, 
        $this->_afm, 
        $this->_doy, 
        $this->_series, 
        $this->_accesstoken, 
        $this->_headerid)); 
    $ssql = $this->_myconn->getLastIDsql('INVOICES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE INVOICES set 
        idate = ?, 
        company = ?, 
        icode = ?, 
        description = ?, 
        amount = ?, 
        vatpercentage = ?, 
        vat = ?, 
        comment = ?, 
        discount = ?, 
        price = ?, 
        status = ?, 
        companyname = ?, 
        profession = ?, 
        address = ?, 
        zipcode = ?, 
        city = ?, 
        area = ?, 
        phone = ?, 
        afm = ?, 
        doy = ?, 
        series = ?, 
        accesstoken = ?, 
        headerid = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_idate, 
        $this->_company, 
        $this->_icode, 
        $this->_description, 
        $this->_amount, 
        $this->_vatpercentage, 
        $this->_vat, 
        $this->_comment, 
        $this->_discount, 
        $this->_price, 
        $this->_status, 
        $this->_companyname, 
        $this->_profession, 
        $this->_address, 
        $this->_zipcode, 
        $this->_city, 
        $this->_area, 
        $this->_phone, 
        $this->_afm, 
        $this->_doy, 
        $this->_series, 
        $this->_accesstoken, 
        $this->_headerid,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM INVOICES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 



}






class INVOICEHEADERS_BASE
{

protected $_myconn, $_rs, $_id, $_idate, $_company, $_icode, $_description, $_amount, $_vat, $_comment, $_status, $_companyname, $_profession, $_address, $_zipcode, $_city, $_area, $_phone, $_afm, $_doy, $_series, $_accesstoken, $_paymethod, $_timesread ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM INVOICEHEADERS WHERE id=?"; 
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
        $this->_idate = $all_rows[0]['idate']; 
        $this->_company = $all_rows[0]['company']; 
        $this->_icode = $all_rows[0]['icode']; 
        $this->_description = $all_rows[0]['description']; 
        $this->_amount = $all_rows[0]['amount']; 
        $this->_vat = $all_rows[0]['vat']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_companyname = $all_rows[0]['companyname']; 
        $this->_profession = $all_rows[0]['profession']; 
        $this->_address = $all_rows[0]['address']; 
        $this->_zipcode = $all_rows[0]['zipcode']; 
        $this->_city = $all_rows[0]['city']; 
        $this->_area = $all_rows[0]['area']; 
        $this->_phone = $all_rows[0]['phone']; 
        $this->_afm = $all_rows[0]['afm']; 
        $this->_doy = $all_rows[0]['doy']; 
        $this->_series = $all_rows[0]['series']; 
        $this->_accesstoken = $all_rows[0]['accesstoken']; 
        $this->_paymethod = $all_rows[0]['paymethod']; 
        $this->_timesread = $all_rows[0]['timesread']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_idate() { 
    return $this->_idate; 
} 
public function set_idate($val) { 
    $this->_idate = $val; 
} 

public function get_company() { 
    return $this->_company; 
} 
public function set_company($val) { 
    $this->_company = $val; 
} 

public function get_icode() { 
    return $this->_icode; 
} 
public function set_icode($val) { 
    $this->_icode = $val; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_amount() { 
    return $this->_amount; 
} 
public function set_amount($val) { 
    $this->_amount = $val; 
} 

public function get_vat() { 
    return $this->_vat; 
} 
public function set_vat($val) { 
    $this->_vat = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_status() { 
    return $this->_status; 
} 
public function set_status($val) { 
    $this->_status = $val; 
} 

public function get_companyname() { 
    return $this->_companyname; 
} 
public function set_companyname($val) { 
    $this->_companyname = $val; 
} 

public function get_profession() { 
    return $this->_profession; 
} 
public function set_profession($val) { 
    $this->_profession = $val; 
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

public function get_city() { 
    return $this->_city; 
} 
public function set_city($val) { 
    $this->_city = $val; 
} 

public function get_area() { 
    return $this->_area; 
} 
public function set_area($val) { 
    $this->_area = $val; 
} 

public function get_phone() { 
    return $this->_phone; 
} 
public function set_phone($val) { 
    $this->_phone = $val; 
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

public function get_series() { 
    return $this->_series; 
} 
public function set_series($val) { 
    $this->_series = $val; 
} 

public function get_accesstoken() { 
    return $this->_accesstoken; 
} 
public function set_accesstoken($val) { 
    $this->_accesstoken = $val; 
} 

public function get_paymethod() { 
    return $this->_paymethod; 
} 
public function set_paymethod($val) { 
    $this->_paymethod = $val; 
} 

public function get_timesread() { 
    return $this->_timesread; 
} 
public function set_timesread($val) { 
    $this->_timesread = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO INVOICEHEADERS ( 
    idate,
    company,
    icode,
    description,
    amount,
    vat,
    comment,
    status,
    companyname,
    profession,
    address,
    zipcode,
    city,
    area,
    phone,
    afm,
    doy,
    series,
    accesstoken,
    paymethod,
    timesread
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_idate, 
        $this->_company, 
        $this->_icode, 
        $this->_description, 
        $this->_amount, 
        $this->_vat, 
        $this->_comment, 
        $this->_status, 
        $this->_companyname, 
        $this->_profession, 
        $this->_address, 
        $this->_zipcode, 
        $this->_city, 
        $this->_area, 
        $this->_phone, 
        $this->_afm, 
        $this->_doy, 
        $this->_series, 
        $this->_accesstoken, 
        $this->_paymethod, 
        $this->_timesread)); 
    $ssql = $this->_myconn->getLastIDsql('INVOICEHEADERS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE INVOICEHEADERS set 
        idate = ?, 
        company = ?, 
        icode = ?, 
        description = ?, 
        amount = ?, 
        vat = ?, 
        comment = ?, 
        status = ?, 
        companyname = ?, 
        profession = ?, 
        address = ?, 
        zipcode = ?, 
        city = ?, 
        area = ?, 
        phone = ?, 
        afm = ?, 
        doy = ?, 
        series = ?, 
        accesstoken = ?, 
        paymethod = ?, 
        timesread = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_idate, 
        $this->_company, 
        $this->_icode, 
        $this->_description, 
        $this->_amount, 
        $this->_vat, 
        $this->_comment, 
        $this->_status, 
        $this->_companyname, 
        $this->_profession, 
        $this->_address, 
        $this->_zipcode, 
        $this->_city, 
        $this->_area, 
        $this->_phone, 
        $this->_afm, 
        $this->_doy, 
        $this->_series, 
        $this->_accesstoken, 
        $this->_paymethod, 
        $this->_timesread,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM INVOICEHEADERS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}



class INVOICEHEADERS extends INVOICEHEADERS_BASE
{
    protected $_dbo, $_id;
    
    public function __construct($dbo, $id, $rs = NULL, $sql = '') { 
        parent::__construct($dbo, $id, $rs, $sql);
        $this->_dbo = $dbo;
        $this->_id = $id;
    }
    
    public function get_seriesCode() {
        return func::vlookup("code", "INVOICESERIES", "id=". parent::get_series(), $this->_dbo);
    }
    
}








class VOUCHERS
{

protected $_myconn, $_rs, $_id, $_vcode, $_vdate, $_deliverydate, $_deliverynotes, $_deliverytime, $_courier, $_customer, $_description, $_comment, $_amount, $_courier_ok, $_courier_notes, $_courier_return, $_courier_delivery_date, $_courier_status, $_export_to_excel, $_exported_to_excel, $_vcode2, $_transactionids, $_second_note_for_courier, $_second_note_courier_sent, $_customername, $_lastcommnotes, $_lastcommdate, $_userid, $_vcode3, $_courier_note_archive, $_followup_date, $_followup_time ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM VOUCHERS WHERE id=?"; 
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
        $this->_vcode = $all_rows[0]['vcode']; 
        $this->_vdate = $all_rows[0]['vdate']; 
        $this->_deliverydate = $all_rows[0]['deliverydate']; 
        $this->_deliverynotes = $all_rows[0]['deliverynotes']; 
        $this->_deliverytime = $all_rows[0]['deliverytime']; 
        $this->_courier = $all_rows[0]['courier']; 
        $this->_customer = $all_rows[0]['customer']; 
        $this->_description = $all_rows[0]['description']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_amount = $all_rows[0]['amount']; 
        $this->_courier_ok = $all_rows[0]['courier_ok']; 
        $this->_courier_notes = $all_rows[0]['courier_notes']; 
        $this->_courier_return = $all_rows[0]['courier_return']; 
        $this->_courier_delivery_date = $all_rows[0]['courier_delivery_date']; 
        $this->_courier_status = $all_rows[0]['courier_status']; 
        $this->_export_to_excel = $all_rows[0]['export_to_excel']; 
        $this->_exported_to_excel = $all_rows[0]['exported_to_excel']; 
        $this->_vcode2 = $all_rows[0]['vcode2']; 
        $this->_transactionids = $all_rows[0]['transactionids']; 
        $this->_second_note_for_courier = $all_rows[0]['second_note_for_courier']; 
        $this->_second_note_courier_sent = $all_rows[0]['second_note_courier_sent']; 
        $this->_customername = $all_rows[0]['customername']; 
        $this->_lastcommnotes = $all_rows[0]['lastcommnotes']; 
        $this->_lastcommdate = $all_rows[0]['lastcommdate']; 
        $this->_userid = $all_rows[0]['userid']; 
        $this->_vcode3 = $all_rows[0]['vcode3']; 
        $this->_courier_note_archive = $all_rows[0]['courier_note_archive']; 
        $this->_followup_date = $all_rows[0]['followup_date']; 
        $this->_followup_time = $all_rows[0]['followup_time']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_vcode() { 
    return $this->_vcode; 
} 
public function set_vcode($val) { 
    $this->_vcode = $val; 
} 

public function get_vdate() { 
    return $this->_vdate; 
} 
public function set_vdate($val) { 
    $this->_vdate = $val; 
} 

public function get_deliverydate() { 
    return $this->_deliverydate; 
} 
public function set_deliverydate($val) { 
    $this->_deliverydate = $val; 
} 

public function get_deliverynotes() { 
    return $this->_deliverynotes; 
} 
public function set_deliverynotes($val) { 
    $this->_deliverynotes = $val; 
} 

public function get_deliverytime() { 
    return $this->_deliverytime; 
} 
public function set_deliverytime($val) { 
    $this->_deliverytime = $val; 
} 

public function get_courier() { 
    return $this->_courier; 
} 
public function set_courier($val) { 
    $this->_courier = $val; 
} 

public function get_customer() { 
    return $this->_customer; 
} 
public function set_customer($val) { 
    $this->_customer = $val; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_amount() { 
    return $this->_amount; 
} 
public function set_amount($val) { 
    $this->_amount = $val; 
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

public function get_export_to_excel() { 
    return $this->_export_to_excel; 
} 
public function set_export_to_excel($val) { 
    $this->_export_to_excel = $val; 
} 

public function get_exported_to_excel() { 
    return $this->_exported_to_excel; 
} 
public function set_exported_to_excel($val) { 
    $this->_exported_to_excel = $val; 
} 

public function get_vcode2() { 
    return $this->_vcode2; 
} 
public function set_vcode2($val) { 
    $this->_vcode2 = $val; 
} 

public function get_transactionids() { 
    return $this->_transactionids; 
} 
public function set_transactionids($val) { 
    $this->_transactionids = $val; 
} 

public function get_second_note_for_courier() { 
    return $this->_second_note_for_courier; 
} 
public function set_second_note_for_courier($val) { 
    $this->_second_note_for_courier = $val; 
} 

public function get_second_note_courier_sent() { 
    return $this->_second_note_courier_sent; 
} 
public function set_second_note_courier_sent($val) { 
    $this->_second_note_courier_sent = $val; 
} 

public function get_customername() { 
    return $this->_customername; 
} 
public function set_customername($val) { 
    $this->_customername = $val; 
} 

public function get_lastcommnotes() { 
    return $this->_lastcommnotes; 
} 
public function set_lastcommnotes($val) { 
    $this->_lastcommnotes = $val; 
} 

public function get_lastcommdate() { 
    return $this->_lastcommdate; 
} 
public function set_lastcommdate($val) { 
    $this->_lastcommdate = $val; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
} 

public function get_vcode3() { 
    return $this->_vcode3; 
} 
public function set_vcode3($val) { 
    $this->_vcode3 = $val; 
} 

public function get_courier_note_archive() { 
    return $this->_courier_note_archive; 
} 
public function set_courier_note_archive($val) { 
    $this->_courier_note_archive = $val; 
} 

public function get_followup_date() { 
    return $this->_followup_date; 
} 
public function set_followup_date($val) { 
    $this->_followup_date = $val; 
} 

public function get_followup_time() { 
    return $this->_followup_time; 
} 
public function set_followup_time($val) { 
    $this->_followup_time = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO VOUCHERS ( 
    vcode,
    vdate,
    deliverydate,
    deliverynotes,
    deliverytime,
    courier,
    customer,
    description,
    comment,
    amount,
    courier_ok,
    courier_notes,
    courier_return,
    courier_delivery_date,
    courier_status,
    export_to_excel,
    exported_to_excel,
    vcode2,
    transactionids,
    second_note_for_courier,
    second_note_courier_sent,
    customername,
    lastcommnotes,
    lastcommdate,
    userid,
    vcode3,
    courier_note_archive,
    followup_date,
    followup_time
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_vcode, 
        $this->_vdate, 
        $this->_deliverydate, 
        $this->_deliverynotes, 
        $this->_deliverytime, 
        $this->_courier, 
        $this->_customer, 
        $this->_description, 
        $this->_comment, 
        $this->_amount, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_export_to_excel, 
        $this->_exported_to_excel, 
        $this->_vcode2, 
        $this->_transactionids, 
        $this->_second_note_for_courier, 
        $this->_second_note_courier_sent, 
        $this->_customername, 
        $this->_lastcommnotes, 
        $this->_lastcommdate, 
        $this->_userid, 
        $this->_vcode3, 
        $this->_courier_note_archive, 
        $this->_followup_date, 
        $this->_followup_time)); 
    $ssql = $this->_myconn->getLastIDsql('VOUCHERS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE VOUCHERS set 
        vcode = ?, 
        vdate = ?, 
        deliverydate = ?, 
        deliverynotes = ?, 
        deliverytime = ?, 
        courier = ?, 
        customer = ?, 
        description = ?, 
        comment = ?, 
        amount = ?, 
        courier_ok = ?, 
        courier_notes = ?, 
        courier_return = ?, 
        courier_delivery_date = ?, 
        courier_status = ?, 
        export_to_excel = ?, 
        exported_to_excel = ?, 
        vcode2 = ?, 
        transactionids = ?, 
        second_note_for_courier = ?, 
        second_note_courier_sent = ?, 
        customername = ?, 
        lastcommnotes = ?, 
        lastcommdate = ?, 
        userid = ?, 
        vcode3 = ?, 
        courier_note_archive = ?, 
        followup_date = ?, 
        followup_time = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_vcode, 
        $this->_vdate, 
        $this->_deliverydate, 
        $this->_deliverynotes, 
        $this->_deliverytime, 
        $this->_courier, 
        $this->_customer, 
        $this->_description, 
        $this->_comment, 
        $this->_amount, 
        $this->_courier_ok, 
        $this->_courier_notes, 
        $this->_courier_return, 
        $this->_courier_delivery_date, 
        $this->_courier_status, 
        $this->_export_to_excel, 
        $this->_exported_to_excel, 
        $this->_vcode2, 
        $this->_transactionids, 
        $this->_second_note_for_courier, 
        $this->_second_note_courier_sent, 
        $this->_customername, 
        $this->_lastcommnotes, 
        $this->_lastcommdate, 
        $this->_userid, 
        $this->_vcode3, 
        $this->_courier_note_archive, 
        $this->_followup_date, 
        $this->_followup_time,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM VOUCHERS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}









class INVOICESERIES
{

protected $_myconn, $_id, $_code, $_counter ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM INVOICESERIES WHERE id=?"; 
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
        $this->_code = $all_rows[0]['code']; 
        $this->_counter = $all_rows[0]['counter']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_code() { 
    return $this->_code; 
} 
public function set_code($val) { 
    $this->_code = $val; 
} 

public function get_counter() { 
    return $this->_counter; 
} 
public function set_counter($val) { 
    $this->_counter = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO INVOICESERIES ( 
    code,
    counter
    ) VALUES (?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_code, 
        $this->_counter)); 
    $ssql = $this->_myconn->getLastIDsql('INVOICESERIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE INVOICESERIES set 
        code = ?, 
        counter = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_code, 
        $this->_counter,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM INVOICESERIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}



//csdatetime defaultvalue ***
//$this->_csdatetime = date("YmdHis"); // Savedata function
class COMPANIES_STATUS
{

protected $_myconn, $_rs, $_id, $_companyid, $_productcategory, $_status, $_userid, $_recalldate, $_recalltime, $_csdatetime, $_last_user5 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM COMPANIES_STATUS WHERE id=?"; 
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
        $this->_companyid = $all_rows[0]['companyid']; 
        $this->_productcategory = $all_rows[0]['productcategory']; 
        $this->_status = $all_rows[0]['status']; 
        $this->_userid = $all_rows[0]['userid']; 
        $this->_recalldate = $all_rows[0]['recalldate']; 
        $this->_recalltime = $all_rows[0]['recalltime']; 
        $this->_csdatetime = $all_rows[0]['csdatetime']; 
        $this->_last_user5 = $all_rows[0]['last_user5']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_companyid() { 
    return $this->_companyid; 
} 
public function set_companyid($val) { 
    $this->_companyid = $val; 
} 

public function get_productcategory() { 
    return $this->_productcategory; 
} 
public function set_productcategory($val) { 
    $this->_productcategory = $val; 
} 

public function get_status() { 
    return $this->_status; 
} 
public function set_status($val) { 
    $this->_status = $val; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
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

public function get_csdatetime() { 
    return $this->_csdatetime; 
} 
public function set_csdatetime($val) { 
    $this->_csdatetime = $val; 
} 

public function get_last_user5() { 
    return $this->_last_user5; 
} 
public function set_last_user5($val) { 
    $this->_last_user5 = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO COMPANIES_STATUS ( 
    companyid,
    productcategory,
    status,
    userid,
    recalldate,
    recalltime,
    csdatetime,
    last_user5
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyid, 
        $this->_productcategory, 
        $this->_status, 
        $this->_userid, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_csdatetime, 
        $this->_last_user5)); 
    $ssql = $this->_myconn->getLastIDsql('COMPANIES_STATUS');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE COMPANIES_STATUS set 
        companyid = ?, 
        productcategory = ?, 
        status = ?, 
        userid = ?, 
        recalldate = ?, 
        recalltime = ?, 
        csdatetime = ?, 
        last_user5 = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyid, 
        $this->_productcategory, 
        $this->_status, 
        $this->_userid, 
        $this->_recalldate, 
        $this->_recalltime, 
        $this->_csdatetime, 
        $this->_last_user5,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM COMPANIES_STATUS WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}






class PRODUCT_CATEGORIES
{

protected $_myconn, $_id, $_description, $_color ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM PRODUCT_CATEGORIES WHERE id=?"; 
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
        $this->_description = $all_rows[0]['description']; 
        $this->_color = $all_rows[0]['color']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_color() { 
    return $this->_color; 
} 
public function set_color($val) { 
    $this->_color = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO PRODUCT_CATEGORIES ( 
    description,
    color
    ) VALUES (?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_color)); 
    $ssql = $this->_myconn->getLastIDsql('PRODUCT_CATEGORIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE PRODUCT_CATEGORIES set 
        description = ?, 
        color = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_color,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM PRODUCT_CATEGORIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class EP_CITIES
{

protected $_myconn, $_id, $_description ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM EP_CITIES WHERE id=?"; 
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
        $this->_description = $all_rows[0]['description']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO EP_CITIES ( 
    description
    ) VALUES (?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description)); 
    $ssql = $this->_myconn->getLastIDsql('EP_CITIES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE EP_CITIES set 
        description = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM EP_CITIES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}






class COMPANY_CHANGES
{

protected $_myconn, $_id, $_companyid, $_fieldname, $_val1, $_val2, $_userid, $_cdatetime ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM COMPANY_CHANGES WHERE id=?"; 
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
        $this->_companyid = $all_rows[0]['companyid']; 
        $this->_fieldname = $all_rows[0]['fieldname']; 
        $this->_val1 = $all_rows[0]['val1']; 
        $this->_val2 = $all_rows[0]['val2']; 
        $this->_userid = $all_rows[0]['userid']; 
        $this->_cdatetime = $all_rows[0]['cdatetime']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_companyid() { 
    return $this->_companyid; 
} 
public function set_companyid($val) { 
    $this->_companyid = $val; 
} 

public function get_fieldname() { 
    return $this->_fieldname; 
} 
public function set_fieldname($val) { 
    $this->_fieldname = $val; 
} 

public function get_val1() { 
    return $this->_val1; 
} 
public function set_val1($val) { 
    $this->_val1 = $val; 
} 

public function get_val2() { 
    return $this->_val2; 
} 
public function set_val2($val) { 
    $this->_val2 = $val; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
} 

public function get_cdatetime() { 
    return $this->_cdatetime; 
} 
public function set_cdatetime($val) { 
    $this->_cdatetime = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO COMPANY_CHANGES ( 
    companyid,
    fieldname,
    val1,
    val2,
    userid,
    cdatetime
    ) VALUES (?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyid, 
        $this->_fieldname, 
        $this->_val1, 
        $this->_val2, 
        $this->_userid, 
        $this->_cdatetime)); 
    $ssql = $this->_myconn->getLastIDsql('COMPANY_CHANGES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE COMPANY_CHANGES set 
        companyid = ?, 
        fieldname = ?, 
        val1 = ?, 
        val2 = ?, 
        userid = ?, 
        cdatetime = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_companyid, 
        $this->_fieldname, 
        $this->_val1, 
        $this->_val2, 
        $this->_userid, 
        $this->_cdatetime,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM COMPANY_CHANGES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}





class USER_WORKDATA
{

protected $_myconn, $_rs, $_id, $_userid, $_mdate, $_timein, $_timeout, $_comments, $_dayoff, $_hoursoff ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM USER_WORKDATA WHERE id=?"; 
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
        $this->_userid = $all_rows[0]['userid']; 
        $this->_mdate = $all_rows[0]['mdate']; 
        $this->_timein = $all_rows[0]['timein']; 
        $this->_timeout = $all_rows[0]['timeout']; 
        $this->_comments = $all_rows[0]['comments']; 
        $this->_dayoff = $all_rows[0]['dayoff']; 
        $this->_hoursoff = $all_rows[0]['hoursoff']; 

        $this->_rs = $all_rows[0];

    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_rs() { 
    return $this->_rs; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
} 

public function get_mdate() { 
    return $this->_mdate; 
} 
public function set_mdate($val) { 
    $this->_mdate = $val; 
} 

public function get_timein() { 
    return $this->_timein; 
} 
public function set_timein($val) { 
    $this->_timein = $val; 
} 

public function get_timeout() { 
    return $this->_timeout; 
} 
public function set_timeout($val) { 
    $this->_timeout = $val; 
} 

public function get_comments() { 
    return $this->_comments; 
} 
public function set_comments($val) { 
    $this->_comments = $val; 
} 

public function get_dayoff() { 
    return $this->_dayoff; 
} 
public function set_dayoff($val) { 
    $this->_dayoff = $val; 
} 

public function get_hoursoff() { 
    return $this->_hoursoff; 
} 
public function set_hoursoff($val) { 
    $this->_hoursoff = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO USER_WORKDATA ( 
    userid,
    mdate,
    timein,
    timeout,
    comments,
    dayoff,
    hoursoff
    ) VALUES (?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_userid, 
        $this->_mdate, 
        $this->_timein, 
        $this->_timeout, 
        $this->_comments, 
        $this->_dayoff, 
        $this->_hoursoff)); 
    $ssql = $this->_myconn->getLastIDsql('USER_WORKDATA');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE USER_WORKDATA set 
        userid = ?, 
        mdate = ?, 
        timein = ?, 
        timeout = ?, 
        comments = ?, 
        dayoff = ?, 
        hoursoff = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_userid, 
        $this->_mdate, 
        $this->_timein, 
        $this->_timeout, 
        $this->_comments, 
        $this->_dayoff, 
        $this->_hoursoff,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM USER_WORKDATA WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}







class EMAIL_TEMPLATES
{

protected $_myconn, $_id, $_description, $_bodytext ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM EMAIL_TEMPLATES WHERE id=?"; 
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
        $this->_description = $all_rows[0]['description']; 
        $this->_bodytext = $all_rows[0]['bodytext']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_description() { 
    return $this->_description; 
} 
public function set_description($val) { 
    $this->_description = $val; 
} 

public function get_bodytext() { 
    return $this->_bodytext; 
} 
public function set_bodytext($val) { 
    $this->_bodytext = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO EMAIL_TEMPLATES ( 
    description,
    bodytext
    ) VALUES (?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_bodytext)); 
    $ssql = $this->_myconn->getLastIDsql('EMAIL_TEMPLATES');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE EMAIL_TEMPLATES set 
        description = ?, 
        bodytext = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_bodytext,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM EMAIL_TEMPLATES WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}