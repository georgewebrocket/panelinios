<?php

//require_once 'db.php';
//require_once 'utils.php';


/*EXAMPLES
$db = new DB($connstr,$user,$pass);
$user = new USERS($db,$id);
$user = new USERS($db,$id,$rs);
$user = new USERS($db,$id,NULL,$sql);
*/
class companies
{

protected $_myconn, $_id, $_company_name_gr, $_company_name_en, $_short_description_gr, 
        $_short_description_en, $_full_description_gr, $_full_description_en, 
        $_address_gr, $_address_en, $_zip_code, $_phone, $_phone2, $_mobile_phone, 
        $_email, $_fax, $_website, $_facebook, $_twitter, $_linkedin, $_basic_category,
        $_basic_category_path,
        $_area, $_city_id, $_geo_x, $_geo_y, $_package, $_expires, $_username, $_password, 
        $_impressions_counter, $_clicks_counter, $_proposed, $_rotating_proposed_comp, 
        $_rotating_new_comp, $_rotating_other_prop_comp, $_active, $_keywords_gr, 
        $_keywords_en, $_url_rewrite_gr, $_url_rewrite_en, $_seo_page_title_gr, 
        $_seo_page_title_en, $_seo_page_description_gr, $_seo_page_description_en, 
        $_seo_manually_set, $_popularity, $_hasoffers, $_datecreated, $_profession, $_eponimia,
        $_googleplus, $_pinterest, $_sites, $_workinghours, $_workingmonths, $_rating, 
        $_rating_count, $_domain, $_showsite, $_p_id, $_p_username, $_p_password, 
        $_instagram, $_instagram_dm;

public static function get_allfields() {
    return "`id`,`company_name_gr`, `company_name_en`, `short_description_gr` ,
        `short_description_en`,`full_description_gr`,`full_description_en`,
        `address_gr`, `address_en`,`zip_code`,`phone`,`phone2`,`mobile_phone`, 
        `email`,`fax`, `website`,`facebook`,`twitter`,`linkedin`,`basic_category`,
        `basic_category_path`, `area`, `city_id`, `geo_x`,`geo_y`,`package`,`expires`, 
        `username`, `password`, `impressions_counter`, `clicks_counter`, `proposed`, 
        `rotating_new_comp`, `rotating_proposed_comp`, `rotating_other_prop_comp`, 
        `active`, `keywords_gr`, `keywords_en`, `url_rewrite_gr`, `url_rewrite_en`, 
        `seo_page_title_gr`, `seo_page_title_en`, `seo_page_description_gr`, 
        `seo_page_description_en`, `seo_manually_set`, `popularity`, `hasoffers`, 
        `datecreated`, `profession`, `eponimia`";
}


public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM companies WHERE id=?";
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
        $this->_company_name_gr = $all_rows[0]['company_name_gr'];
        $this->_company_name_en = $all_rows[0]['company_name_en'];
        $this->_short_description_gr = $all_rows[0]['short_description_gr'];
        $this->_short_description_en = $all_rows[0]['short_description_en'];
        $this->_full_description_gr = $all_rows[0]['full_description_gr'];
        $this->_full_description_en = $all_rows[0]['full_description_en'];
        $this->_address_gr = $all_rows[0]['address_gr'];
        $this->_address_en = $all_rows[0]['address_en'];
        $this->_zip_code = $all_rows[0]['zip_code'];
        $this->_phone = $all_rows[0]['phone'];
        $this->_phone2 = $all_rows[0]['phone2'];
        $this->_mobile_phone = $all_rows[0]['mobile_phone'];
        $this->_email = $all_rows[0]['email'];
        $this->_fax = $all_rows[0]['fax'];
        $this->_website = $all_rows[0]['website'];
        $this->_facebook = $all_rows[0]['facebook'];
        $this->_twitter = $all_rows[0]['twitter'];
        $this->_linkedin = $all_rows[0]['linkedin'];
        $this->_basic_category = $all_rows[0]['basic_category'];
        $this->_area = $all_rows[0]['area'];
        $this->_city_id = $all_rows[0]['city_id'];
        $this->_geo_x = $all_rows[0]['geo_x'];
        $this->_geo_y = $all_rows[0]['geo_y'];
        $this->_package = $all_rows[0]['package'];
        $this->_expires = $all_rows[0]['expires'];
        $this->_username = $all_rows[0]['username'];
        $this->_password = $all_rows[0]['password'];
        $this->_impressions_counter = $all_rows[0]['impressions_counter'];
        $this->_clicks_counter = $all_rows[0]['clicks_counter'];
        $this->_proposed = $all_rows[0]['proposed'];
        $this->_rotating_proposed_comp = $all_rows[0]['rotating_proposed_comp'];
        $this->_rotating_new_comp = $all_rows[0]['rotating_new_comp'];
        $this->_rotating_other_prop_comp = $all_rows[0]['rotating_other_prop_comp'];
        $this->_active = $all_rows[0]['active'];
        $this->_keywords_gr = $all_rows[0]['keywords_gr'];
        $this->_keywords_en = $all_rows[0]['keywords_en'];
        $this->_url_rewrite_gr = $all_rows[0]['url_rewrite_gr'];
        $this->_url_rewrite_en = $all_rows[0]['url_rewrite_en'];
        $this->_seo_page_title_gr = $all_rows[0]['seo_page_title_gr'];
        $this->_seo_page_title_en = $all_rows[0]['seo_page_title_en'];
        $this->_seo_page_description_gr = $all_rows[0]['seo_page_description_gr'];
        $this->_seo_page_description_en = $all_rows[0]['seo_page_description_en']; 
        $this->_seo_manually_set = $all_rows[0]['seo_manually_set']; 
        $this->_popularity = $all_rows[0]['popularity']; 
        $this->_hasoffers = $all_rows[0]['hasoffers']; 
        $this->_datecreated = $all_rows[0]['datecreated']; 
        $this->_profession = $all_rows[0]['profession'];
        $this->_eponimia = $all_rows[0]['eponimia'];
        
        $this->_googleplus = $all_rows[0]['googleplus']; 
        $this->_pinterest = $all_rows[0]['pinterest']; 
        $this->_sites = $all_rows[0]['sites']; 
        $this->_workinghours = $all_rows[0]['workinghours'];
        $this->_workingmonths = $all_rows[0]['workingmonths'];
        $this->_rating = $all_rows[0]['rating'];
        $this->_rating_count = $all_rows[0]['rating_count'];
        
        $this->_domain = $all_rows[0]['domain']; 
        $this->_showsite = $all_rows[0]['showsite']; 
        
        $this->_p_id = $all_rows[0]['p_id']; 
        $this->_p_username = $all_rows[0]['p_username']; 
        $this->_p_password = $all_rows[0]['p_password'];
        
        $this->_instagram = $all_rows[0]['instagram']; 
        $this->_instagram_dm = $all_rows[0]['instagram_dm']; 
        
        $this->_id = $all_rows[0]['id']; 
    }
}

public function get_id() {
    return $this->_id;
}

public function get_company_name_gr() {
    return stripslashes($this->_company_name_gr);
}
public function set_company_name_gr($val) {
    $this->_company_name_gr = $val;
}

public function get_company_name_en() {
    return stripslashes($this->_company_name_en);
}
public function set_company_name_en($val) {
    $this->_company_name_en = $val;
}

public function get_short_description_gr() {
    return stripslashes($this->_short_description_gr);
}
public function set_short_description_gr($val) {
    $this->_short_description_gr = $val;
}

public function get_short_description_en() {
    return stripslashes($this->_short_description_en);
}
public function set_short_description_en($val) {
    $this->_short_description_en = $val;
}

public function get_full_description_gr() {
    return stripslashes($this->_full_description_gr);
}
public function set_full_description_gr($val) {
    $this->_full_description_gr = $val;
}

public function get_full_description_en() {
    return stripslashes($this->_full_description_en);
}
public function set_full_description_en($val) {
    $this->_full_description_en = $val;
}

public function get_address_gr() {
    return stripslashes($this->_address_gr);
}
public function set_address_gr($val) {
    $this->_address_gr = $val;
}

public function get_address_en() {
    return stripslashes($this->_address_en);
}
public function set_address_en($val) {
    $this->_address_en = $val;
}

public function get_zip_code() {
    return $this->_zip_code;
}
public function set_zip_code($val) {
    $this->_zip_code = $val;
}

public function get_phone() {
    return $this->_phone;
}
public function set_phone($val) {
    $this->_phone = $val;
}

public function get_phone2() {
    return $this->_phone2;
}
public function set_phone2($val) {
    $this->_phone2 = $val;
}

public function get_mobile_phone() {
    return $this->_mobile_phone;
}
public function set_mobile_phone($val) {
    $this->_mobile_phone = $val;
}

public function get_email() {
    return $this->_email;
}
public function set_email($val) {
    $this->_email = $val;
}

public function get_fax() {
    return $this->_fax;
}
public function set_fax($val) {
    $this->_fax = $val;
}

public function get_website() {
    $URL = stripslashes($this->_website);
    if ($URL=='') {
    	return '';
    }
    if (strpos($URL, "http") === FALSE) {
        $URL = str_replace(" ", "", $URL);
        $URL = str_replace(",", ",http://", $URL);
        return "http://".$URL;
    }
    else {
        return $URL;
    }
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

public function get_linkedin() {
    return $this->_linkedin;
}
public function set_linkedin($val) {
    $this->_linkedin = $val;
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

public function get_basic_category() {
    return $this->_basic_category;
}
public function set_basic_category($val) {
    $this->_basic_category = $val;
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

public function get_package() {
    return $this->_package;
}
public function set_package($val) {
    $this->_package = $val;
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

public function get_impressions_counter() {
    return $this->_impressions_counter;
}
public function set_impressions_counter($val) {
    $this->_impressions_counter = $val;
}

public function get_clicks_counter() {
    return $this->_clicks_counter;
}
public function set_clicks_counter($val) {
    $this->_clicks_counter = $val;
}

public function get_proposed() {
    return $this->_proposed;
}
public function set_proposed($val) {
    $this->_proposed = $val;
}

public function get_rotating_proposed_comp() {
    return $this->_rotating_proposed_comp;
}
public function set_rotating_proposed_comp($val) {
    $this->_rotating_proposed_comp = $val;
}

public function get_rotating_new_comp() {
    return $this->_rotating_new_comp;
}
public function set_rotating_new_comp($val) {
    $this->_rotating_new_comp = $val;
}

public function get_rotating_other_prop_comp() {
    return $this->_rotating_other_prop_comp;
}
public function set_rotating_other_prop_comp($val) {
    $this->_rotating_other_prop_comp = $val;
}

public function get_active() {
    return $this->_active;
}
public function set_active($val) {
    $this->_active = $val;
}

public function get_keywords_gr() {
    return $this->_keywords_gr;
}
public function set_keywords_gr($val) {
    $this->_keywords_gr = $val;
}

public function get_keywords_en() {
    return $this->_keywords_en;
}
public function set_keywords_en($val) {
    $this->_keywords_en = $val;
}

public function get_url_rewrite_gr() {
    if ($this->_url_rewrite_gr!="") {
        return $this->_url_rewrite_gr;
    }
    else {
        return func::normURL($this->get_profession_descr() . "-".
    			$this->get_city_descr2(). "-" . trim($this->get_company_name_gr()));
    }
    
    
}
public function set_url_rewrite_gr($val) {
    $this->_url_rewrite_gr = $val;
}

public function get_url_rewrite_en() {
    return $this->_url_rewrite_en;
}
public function set_url_rewrite_en($val) {
    $this->_url_rewrite_en = $val;
}

public function get_seo_page_title_gr() {
    return $this->_seo_page_title_gr;
}
public function set_seo_page_title_gr($val) {
    $this->_seo_page_title_gr = $val;
}

public function get_seo_page_title_en() {
    return $this->_seo_page_title_en;
}
public function set_seo_page_title_en($val) {
    $this->_seo_page_title_en = $val;
}

public function get_seo_page_description_gr() {
    return $this->_seo_page_description_gr;
}
public function set_seo_page_description_gr($val) {
    $this->_seo_page_description_gr = $val;
}

public function get_seo_page_description_en() {
    return $this->_seo_page_description_en;
}
public function set_seo_page_description_en($val) {
    $this->_seo_page_description_en = $val;
} 


public function get_seo_manually_set() { 
    return $this->_seo_manually_set; 
} 
public function set_seo_manually_set($val) { 
    $this->_seo_manually_set = $val; 
} 

public function get_popularity() { 
    return $this->_popularity; 
} 
public function set_popularity($val) { 
    $this->_popularity = $val; 
} 

public function get_hasoffers() { 
    return $this->_hasoffers; 
} 
public function set_hasoffers($val) { 
    $this->_hasoffers = $val; 
} 

public function get_datecreated() { 
    return $this->_datecreated; 
} 
public function set_datecreated($val) { 
    $this->_datecreated = $val; 
} 

public function get_profession() {
	return $this->_profession;
}
public function set_profession($val) {
	$this->_profession = $val;
}

public function get_eponimia() {
	return $this->_eponimia;
}
public function set_eponimia($val) {
	$this->_eponimia = $val;
}

public function get_googleplus() {     
    return $this->_googleplus; 
} 
public function set_googleplus($val, $setDate = TRUE) { 
    if ($this->_googleplus != $val && $setDate) {
        $this->_googleplus_dm = date('YmdHis');
    }
    $this->_googleplus = $val; 
} 


public function get_pinterest() { 
    return $this->_pinterest; 
} 
public function set_pinterest($val, $setDate = TRUE) { 
    if ($this->_pinterest != $val && $setDate) {
        $this->_pinterest_dm = date('YmdHis');
    }
    $this->_pinterest = $val; 
} 


public function get_sites() { 
    return $this->_sites; 
} 
public function set_sites($val, $setDate = TRUE) { 
    if ($this->_sites != $val && $setDate) {
        $this->_sites_dm = date('YmdHis');
    }
    $this->_sites = $val; 
} 


public function get_workinghours() { 
    return $this->_workinghours; 
} 
public function set_workinghours($val, $setDate = TRUE) { 
    if ($this->_workinghours != $val && $setDate) {
        $this->_workinghours_dm = date('YmdHis');
    }
    $this->_workinghours = $val; 
} 


public function get_workingnonths() { 
    return $this->_workingmonths; 
}


public function get_rating() { 
    return $this->_rating; 
} 
public function set_rating($val) { 
    $this->_rating = $val; 
} 

public function get_rating_count() { 
    return $this->_rating_count; 
} 
public function set_rating_count($val) { 
    $this->_rating_count = $val; 
} 


public function get_domain() { 
    return $this->_domain; 
} 
public function set_domain($val) { 
    $this->_domain = $val; 
}

public function get_showsite() { 
    return $this->_showsite; 
} 
public function set_showsite($val) { 
    $this->_showsite = $val; 
} 



public function get_p_id() { 
    return $this->_p_id; 
} 
public function set_p_id($val) { 
    $this->_p_id = $val; 
} 

public function get_p_username() { 
    return $this->_p_username; 
} 
public function set_p_username($val) { 
    $this->_p_username = $val; 
} 

public function get_p_password() { 
    return $this->_p_password; 
} 
public function set_p_password($val) { 
    $this->_p_password = $val; 
} 






/* ======================== */
public function get_main_photo() {
    //echo $this->_id;

    define('CDNHOST', 'https://cdn.panelinios.gr');

    $imgId = func::vlookup("id", "company_photos", "company_id=".$this->_id.
            " AND main_img=1", $this->_myconn);
    if ($imgId!="") {
        $photopath = func::vlookup("photo_path", "company_photos", "company_id=".$this->_id.
                " AND main_img=1", $this->_myconn);
        
        $myPath = $photopath."/photo_big/".$imgId.".png";
        //echo "<!--MYLOGO=$myPath-->";
        if (file_exists($myPath)) {
            return CDNHOST . "/" . $myPath;
        }
        else {
            return CDNHOST . "/" . $photopath."/photo_big/".$imgId.".jpg";
        }
        
        //return $photopath."/photo_big/".$imgId.".jpg";
    }
    else {
        return "img/blankimage150.jpg";
    }
        
}

public function get_main_photo_caption() {
	//echo $this->_id;
	$imgId = func::vlookup("id", "company_photos", "company_id=".$this->_id.
			" AND main_img=1", $this->_myconn);
	if ($imgId!="") {
		$caption = func::vlookup("caption_gr", "company_photos", "company_id=".$this->_id.
				" AND main_img=1", $this->_myconn);

		return $caption;
	}
	else {
		return "";
	}

}




public function get_basic_category_descr() {
    return func::vlookup("description_gr", "categories", "id=".$this->_basic_category, $this->_myconn);
}

public function get_area_descr() {
    return func::vlookup("description_gr", "areas", "id=".$this->_area, $this->_myconn);
}
public function get_area_descr2() {
    return func::vlookup("description_en", "areas", "id=".$this->_area, $this->_myconn);
}

public function get_city_descr() {
    return func::vlookup("description", "cities", "id=".$this->_city_id, $this->_myconn);
}
public function get_city_descr2() {
	return func::vlookup("description2", "cities", "id=".$this->_city_id, $this->_myconn);
}


public function get_profession_descr() {
	return func::vlookup("description", "professions", "id=".$this->_profession, $this->_myconn);

}

public function AddImpression() {
    $this->AddClickOrImpression(FALSE, TRUE);
}

public function AddClick($clicksCount = 1, $clicksDate = 0) {
    $this->AddClickOrImpression(TRUE, FALSE, TRUE, $clicksCount, $clicksDate);
}

public function AddClickOrImpression($click, $impression, $save = TRUE, $clicksCount = 1, $clicksDate = 0) {
    $clicksCounter = 0; $impressionsCounter = 0;
    $clicksCounter2 = 0; $impressionsCounter2 = 0;
    if ($click) {
        $clicksCounter = $this->_clicks_counter;
        if ($clicksCounter=="") { $clicksCounter = 0; }
        $clicksCounter = $clicksCounter + $clicksCount;
        $this->_clicks_counter = $clicksCounter;
    }
    if ($impression) {
        $impressionsCounter = $this->_impressions_counter;
        if ($impressionsCounter=="") { $impressionsCounter = 0; }
        $impressionsCounter++;
        $this->_impressions_counter = $impressionsCounter;
    }
    if ($save) {
        $this->Savedata();
    }
    //
    if ($clicksDate==0) {
        $clicksDate = date("Ymd"). "000000"; //today
    }
    
    $id = $this->get_id();
    $sqlCA = "SELECT * FROM company_activity WHERE company=$id AND activity_date='$clicksDate'";
    $rsCA = $this->_myconn->getRS($sqlCA);
    if ($rsCA!==FALSE) {
        //echo "ID=".$rsCA[0]['id']."-";
        $CA = new company_activity($this->_myconn, $rsCA[0]['id'], $rsCA);
        
    }
    else {
        $CA = new company_activity($this->_myconn, 0);
        $CA->set_company($id);
        $CA->set_activity_date($clicksDate);
        $CA->set_clicks(0);
        $CA->set_impressions(0);
    }
    if ($click) {
        $clicksCounter2 = $CA->get_clicks();
        $clicksCounter2 = $clicksCounter2 + $clicksCount;
        $CA->set_clicks($clicksCounter2);
    }
    if ($impression) {
        $impressionsCounter2 = $CA->get_impressions();
        $impressionsCounter2++;
        $CA->set_impressions($impressionsCounter2);
    }
    $CA->Savedata();
    
}





public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO companies ( 
        company_name_gr,
        company_name_en,
        short_description_gr,
        short_description_en,
        full_description_gr,
        full_description_en,
        address_gr,
        address_en,
        zip_code,
        phone,
        phone2,
        mobile_phone,
        email,
        fax,
        website,
        facebook,
        twitter,
        linkedin,
        basic_category,
        basic_category_path,
        area,
        geo_x,
        geo_y,
        package,
        expires,
        username,
        password,
        impressions_counter,
        clicks_counter,
        proposed,
        rotating_new_comp,
        rotating_proposed_comp,
        rotating_other_prop_comp,
        active,
        keywords_gr,
        keywords_en,
        url_rewrite_gr,
        url_rewrite_en,
        seo_page_title_gr,
        seo_page_title_en,
        seo_page_description_gr,
        seo_page_description_en,
        seo_manually_set,
        popularity,
        hasoffers,
        datecreated,
        profession, 
        eponimia
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
    		?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
    		?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company_name_gr, 
        $this->_company_name_en, 
        $this->_short_description_gr, 
        $this->_short_description_en, 
        $this->_full_description_gr, 
        $this->_full_description_en, 
        $this->_address_gr, 
        $this->_address_en, 
        $this->_zip_code, 
        $this->_phone, 
        $this->_phone2, 
        $this->_mobile_phone, 
        $this->_email, 
        $this->_fax, 
        $this->_website, 
        $this->_facebook, 
        $this->_twitter, 
        $this->_linkedin, 
        $this->_basic_category, 
        $this->_basic_category_path, 
        $this->_area, 
        $this->_geo_x, 
        $this->_geo_y, 
        $this->_package, 
        $this->_expires, 
        $this->_username, 
        $this->_password, 
        $this->_impressions_counter, 
        $this->_clicks_counter, 
        $this->_proposed, 
        $this->_rotating_new_comp, 
        $this->_rotating_proposed_comp, 
        $this->_rotating_other_prop_comp, 
        $this->_active, 
        $this->_keywords_gr, 
        $this->_keywords_en, 
        $this->_url_rewrite_gr, 
        $this->_url_rewrite_en, 
        $this->_seo_page_title_gr, 
        $this->_seo_page_title_en, 
        $this->_seo_page_description_gr, 
        $this->_seo_page_description_en, 
        $this->_seo_manually_set, 
        $this->_popularity, 
        $this->_hasoffers, 
        $this->_datecreated,
    	$this->_profession,
    	$this->_eponimia
    )); 
    $ssql = $this->_myconn->getLastIDsql('companies');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE companies set 
	        company_name_gr = ?, 
	        company_name_en = ?, 
	        short_description_gr = ?, 
	        short_description_en = ?, 
	        full_description_gr = ?, 
	        full_description_en = ?, 
	        address_gr = ?, 
	        address_en = ?, 
	        zip_code = ?, 
	        phone = ?, 
	        phone2 = ?, 
	        mobile_phone = ?, 
	        email = ?, 
	        fax = ?, 
	        website = ?, 
	        facebook = ?, 
	        twitter = ?, 
	        linkedin = ?, 
	        basic_category = ?, 
	        basic_category_path = ?, 
	        area = ?, 
	        geo_x = ?, 
	        geo_y = ?, 
	        package = ?, 
	        expires = ?, 
	        username = ?, 
	        password = ?, 
	        impressions_counter = ?, 
	        clicks_counter = ?, 
	        proposed = ?, 
	        rotating_new_comp = ?, 
	        rotating_proposed_comp = ?, 
	        rotating_other_prop_comp = ?, 
	        active = ?, 
	        keywords_gr = ?, 
	        keywords_en = ?, 
	        url_rewrite_gr = ?, 
	        url_rewrite_en = ?, 
	        seo_page_title_gr = ?, 
	        seo_page_title_en = ?, 
	        seo_page_description_gr = ?, 
	        seo_page_description_en = ?, 
	        seo_manually_set = ?, 
	        popularity = ?, 
	        hasoffers = ?, 
	        datecreated = ?,
	        profession = ?,
                eponimia = ?,
                rating = ?,
                rating_count = ?
	        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
	        $this->_company_name_gr, 
	        $this->_company_name_en, 
	        $this->_short_description_gr, 
	        $this->_short_description_en, 
	        $this->_full_description_gr, 
	        $this->_full_description_en, 
	        $this->_address_gr, 
	        $this->_address_en, 
	        $this->_zip_code, 
	        $this->_phone, 
	        $this->_phone2, 
	        $this->_mobile_phone, 
	        $this->_email, 
	        $this->_fax, 
	        $this->_website, 
	        $this->_facebook, 
	        $this->_twitter, 
	        $this->_linkedin, 
	        $this->_basic_category, 
	        $this->_basic_category_path, 
	        $this->_area, 
	        $this->_geo_x, 
	        $this->_geo_y, 
	        $this->_package, 
	        $this->_expires, 
	        $this->_username, 
	        $this->_password, 
	        $this->_impressions_counter, 
	        $this->_clicks_counter, 
	        $this->_proposed, 
	        $this->_rotating_new_comp, 
	        $this->_rotating_proposed_comp, 
	        $this->_rotating_other_prop_comp, 
	        $this->_active, 
	        $this->_keywords_gr, 
	        $this->_keywords_en, 
	        $this->_url_rewrite_gr, 
	        $this->_url_rewrite_en, 
	        $this->_seo_page_title_gr, 
	        $this->_seo_page_title_en, 
	        $this->_seo_page_description_gr, 
	        $this->_seo_page_description_en, 
	        $this->_seo_manually_set, 
	        $this->_popularity, 
	        $this->_hasoffers, 
	        $this->_datecreated,
        	$this->_profession,
        	$this->_eponimia,
                $this->_rating,
                $this->_rating_count,
	        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 


public function Delete() {
    $ssql = "DELETE FROM companies WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class ads
{

protected $_myconn, $_id, $_description, $_adtext, $_active, $_impressions, $_clicks, $_datestart, $_datestop, $_imagepath, $_position, $_link ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM ads WHERE id=?";
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
        $this->_adtext = $all_rows[0]['adtext'];
        $this->_active = $all_rows[0]['active'];
        $this->_impressions = $all_rows[0]['impressions'];
        $this->_clicks = $all_rows[0]['clicks'];
        $this->_datestart = $all_rows[0]['datestart'];
        $this->_datestop = $all_rows[0]['datestop'];
        $this->_imagepath = $all_rows[0]['imagepath'];
        $this->_position = $all_rows[0]['position'];
        $this->_link = $all_rows[0]['link'];
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

public function get_adtext() {
    return $this->_adtext;
}
public function set_adtext($val) {
    $this->_adtext = $val;
}

public function get_active() {
    return $this->_active;
}
public function set_active($val) {
    $this->_active = $val;
}

public function get_impressions() {
    return $this->_impressions;
}
public function set_impressions($val) {
    $this->_impressions = $val;
}

public function get_clicks() {
    return $this->_clicks;
}
public function set_clicks($val) {
    $this->_clicks = $val;
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

public function get_imagepath() {
    return $this->_imagepath;
}
public function set_imagepath($val) {
    $this->_imagepath = $val;
}

public function get_position() {
    return $this->_position;
}
public function set_position($val) {
    $this->_position = $val;
}

public function get_link() {
    return $this->_link;
}
public function set_link($val) {
    $this->_link = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO ads (
    description,
    adtext,
    active,
    impressions,
    clicks,
    datestart,
    datestop,
    imagepath,
    position,
    link
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_adtext,
        $this->_active,
        $this->_impressions,
        $this->_clicks,
        $this->_datestart,
        $this->_datestop,
        $this->_imagepath,
        $this->_position,
        $this->_link));
    $ssql = $this->_myconn->getLastIDsql('ads');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE ads set
        description = ?,
        adtext = ?,
        active = ?,
        impressions = ?,
        clicks = ?,
        datestart = ?,
        datestop = ?,
        imagepath = ?,
        position = ?,
        link = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_adtext,
        $this->_active,
        $this->_impressions,
        $this->_clicks,
        $this->_datestart,
        $this->_datestop,
        $this->_imagepath,
        $this->_position,
        $this->_link,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM ads WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class company_events
{

protected $_myconn, $_id, $_company_id, $_title_gr, $_title_en, $_short_description_gr, $_short_description_en, $_full_description_gr, $_full_description_en, $_photo, $_date_start, $_date_stop, $_active, $_eventtype ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM company_events WHERE id=?"; 
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
        $this->_company_id = $all_rows[0]['company_id']; 
        $this->_title_gr = $all_rows[0]['title_gr']; 
        $this->_title_en = $all_rows[0]['title_en']; 
        $this->_short_description_gr = $all_rows[0]['short_description_gr']; 
        $this->_short_description_en = $all_rows[0]['short_description_en']; 
        $this->_full_description_gr = $all_rows[0]['full_description_gr']; 
        $this->_full_description_en = $all_rows[0]['full_description_en']; 
        $this->_photo = $all_rows[0]['photo']; 
        $this->_date_start = $all_rows[0]['date_start']; 
        $this->_date_stop = $all_rows[0]['date_stop']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_eventtype = $all_rows[0]['eventtype']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_company_id() { 
    return $this->_company_id; 
} 
public function set_company_id($val) { 
    $this->_company_id = $val; 
} 

public function get_title_gr() { 
    return $this->_title_gr; 
} 
public function set_title_gr($val) { 
    $this->_title_gr = $val; 
} 

public function get_title_en() { 
    return $this->_title_en; 
} 
public function set_title_en($val) { 
    $this->_title_en = $val; 
} 

public function get_short_description_gr() { 
    return $this->_short_description_gr; 
} 
public function set_short_description_gr($val) { 
    $this->_short_description_gr = $val; 
} 

public function get_short_description_en() { 
    return $this->_short_description_en; 
} 
public function set_short_description_en($val) { 
    $this->_short_description_en = $val; 
} 

public function get_full_description_gr() { 
    return $this->_full_description_gr; 
} 
public function set_full_description_gr($val) { 
    $this->_full_description_gr = $val; 
} 

public function get_full_description_en() { 
    return $this->_full_description_en; 
} 
public function set_full_description_en($val) { 
    $this->_full_description_en = $val; 
} 

public function get_photo() { 
    return $this->_photo; 
} 
public function set_photo($val) { 
    $this->_photo = $val; 
} 

public function get_date_start() { 
    return $this->_date_start; 
} 
public function set_date_start($val) { 
    $this->_date_start = $val; 
} 

public function get_date_stop() { 
    return $this->_date_stop; 
} 
public function set_date_stop($val) { 
    $this->_date_stop = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_eventtype() { 
    return $this->_eventtype; 
} 
public function set_eventtype($val) { 
    $this->_eventtype = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO company_events ( 
    company_id,
    title_gr,
    title_en,
    short_description_gr,
    short_description_en,
    full_description_gr,
    full_description_en,
    photo,
    date_start,
    date_stop,
    active,
    eventtype
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company_id, 
        $this->_title_gr, 
        $this->_title_en, 
        $this->_short_description_gr, 
        $this->_short_description_en, 
        $this->_full_description_gr, 
        $this->_full_description_en, 
        $this->_photo, 
        $this->_date_start, 
        $this->_date_stop, 
        $this->_active, 
        $this->_eventtype)); 
    $ssql = $this->_myconn->getLastIDsql('company_events');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE company_events set 
        company_id = ?, 
        title_gr = ?, 
        title_en = ?, 
        short_description_gr = ?, 
        short_description_en = ?, 
        full_description_gr = ?, 
        full_description_en = ?, 
        photo = ?, 
        date_start = ?, 
        date_stop = ?, 
        active = ?, 
        eventtype = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company_id, 
        $this->_title_gr, 
        $this->_title_en, 
        $this->_short_description_gr, 
        $this->_short_description_en, 
        $this->_full_description_gr, 
        $this->_full_description_en, 
        $this->_photo, 
        $this->_date_start, 
        $this->_date_stop, 
        $this->_active, 
        $this->_eventtype,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM company_events WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class company_offers_base
{

protected $_myconn, $_id, $_company_id, $_title_gr, $_title_en, $_short_description_gr, $_short_description_en, $_full_description_gr, $_full_description_en, $_photo, $_date_start, $_date_stop, $_active, $_price1, $_price2 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM company_offers WHERE id=?"; 
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
        $this->_company_id = $all_rows[0]['company_id']; 
        $this->_title_gr = $all_rows[0]['title_gr']; 
        $this->_title_en = $all_rows[0]['title_en']; 
        $this->_short_description_gr = $all_rows[0]['short_description_gr']; 
        $this->_short_description_en = $all_rows[0]['short_description_en']; 
        $this->_full_description_gr = $all_rows[0]['full_description_gr']; 
        $this->_full_description_en = $all_rows[0]['full_description_en']; 
        $this->_photo = $all_rows[0]['photo']; 
        $this->_date_start = $all_rows[0]['date_start']; 
        $this->_date_stop = $all_rows[0]['date_stop']; 
        $this->_active = $all_rows[0]['active']; 
        $this->_price1 = $all_rows[0]['price1']; 
        $this->_price2 = $all_rows[0]['price2']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_company_id() { 
    return $this->_company_id; 
} 
public function set_company_id($val) { 
    $this->_company_id = $val; 
} 

public function get_title_gr() { 
    return $this->_title_gr; 
} 
public function set_title_gr($val) { 
    $this->_title_gr = $val; 
} 

public function get_title_en() { 
    return $this->_title_en; 
} 
public function set_title_en($val) { 
    $this->_title_en = $val; 
} 

public function get_short_description_gr() { 
    return $this->_short_description_gr; 
} 
public function set_short_description_gr($val) { 
    $this->_short_description_gr = $val; 
} 

public function get_short_description_en() { 
    return $this->_short_description_en; 
} 
public function set_short_description_en($val) { 
    $this->_short_description_en = $val; 
} 

public function get_full_description_gr() { 
    return $this->_full_description_gr; 
} 
public function set_full_description_gr($val) { 
    $this->_full_description_gr = $val; 
} 

public function get_full_description_en() { 
    return $this->_full_description_en; 
} 
public function set_full_description_en($val) { 
    $this->_full_description_en = $val; 
} 

public function get_photo() { 
    return $this->_photo; 
} 
public function set_photo($val) { 
    $this->_photo = $val; 
} 

public function get_date_start() { 
    return $this->_date_start; 
} 
public function set_date_start($val) { 
    $this->_date_start = $val; 
} 

public function get_date_stop() { 
    return $this->_date_stop; 
} 
public function set_date_stop($val) { 
    $this->_date_stop = $val; 
} 

public function get_active() { 
    return $this->_active; 
} 
public function set_active($val) { 
    $this->_active = $val; 
} 

public function get_price1() { 
    return $this->_price1; 
} 
public function set_price1($val) { 
    $this->_price1 = $val; 
} 

public function get_price2() { 
    return $this->_price2; 
} 
public function set_price2($val) { 
    $this->_price2 = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO company_offers ( 
    company_id,
    title_gr,
    title_en,
    short_description_gr,
    short_description_en,
    full_description_gr,
    full_description_en,
    photo,
    date_start,
    date_stop,
    active,
    price1,
    price2
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company_id, 
        $this->_title_gr, 
        $this->_title_en, 
        $this->_short_description_gr, 
        $this->_short_description_en, 
        $this->_full_description_gr, 
        $this->_full_description_en, 
        $this->_photo, 
        $this->_date_start, 
        $this->_date_stop, 
        $this->_active, 
        $this->_price1, 
        $this->_price2)); 
    $ssql = $this->_myconn->getLastIDsql('company_offers');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE company_offers set 
        company_id = ?, 
        title_gr = ?, 
        title_en = ?, 
        short_description_gr = ?, 
        short_description_en = ?, 
        full_description_gr = ?, 
        full_description_en = ?, 
        photo = ?, 
        date_start = ?, 
        date_stop = ?, 
        active = ?, 
        price1 = ?, 
        price2 = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company_id, 
        $this->_title_gr, 
        $this->_title_en, 
        $this->_short_description_gr, 
        $this->_short_description_en, 
        $this->_full_description_gr, 
        $this->_full_description_en, 
        $this->_photo, 
        $this->_date_start, 
        $this->_date_stop, 
        $this->_active, 
        $this->_price1, 
        $this->_price2,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM company_offers WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}


class company_offers extends company_offers_base
{
    protected $_myconn, $_id;
    
    public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
        parent::__construct($myconn, $_id, $my_rows, $_ssql);
        $this->_myconn = $myconn;
        $this->_id = $_id;
    }
    
    public function get_company() {
        return func::vlookup("company_name_gr", "companies", "id=".$this->_company_id, $this->_myconn);
    }
    
}


class categories_view
{
    protected $_myconn, $_ssql, $_languege, $_countCompanie, $_class; 
    public function __construct($myconn, $languege, $ssql = '', $countCompanies = 0, $class = '') {
        $this->_myconn = $myconn;
        $this->__ssql = $ssql;
        $this->__languege = $languege;
        $this->__countCompanies = $countCompanies;
        $this->__class = $class;
        $this->__data = array();
        
        $rs = $this->_myconn->getRS($this->__ssql);
        
        for($i=0;$i<count($rs);$i++){
            $this->__data[$rs[$i]['id']] = $rs[$i];
        }        
        $this->__data = $this->mapTree($this->__data);
    }
    
    function view_cat($dataset, $lang, $class ) {
        foreach ($dataset as $menu) {
            $strCountCompanies = '';
            if($this->__countCompanies == 1 ){
                //$rs = $this->_myconn->getRS("SELECT DISTINCT company_id FROM company_categories WHERE path LIKE '%-".$menu['id']."-%'");
                $rs = $this->_myconn->getRS("SELECT DISTINCT company_id AS id FROM company_categories WHERE path LIKE '%-".$menu['id']."-%' "
                        . "UNION SELECT id FROM companies WHERE basic_category = ".$menu['id']);
                $strCountCompanies = "<span class=\"count-comp\">(".count($rs).")</span>";
            }
            
            //echo "<li><a href=\"category.php?Cat=1&l=".$lang."&id=".$menu['id']."\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            echo "<li><a href=\"category/".$menu['id']. "/" . $lang . "/" . func::normURL($menu['description_'.$lang]) . "\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            
            if(isset($menu['childs'])) {
                echo "<ul class=\"".$class."\">";
                $this->view_cat($menu['childs'], $lang, $class);
                echo '</ul>';
            }
            echo '</li>';
        }
    }

    function mapTree($dataset) {

        $tree = array();

        foreach ($dataset as $id=>&$node) {
            if (isset($node['parent_id'])){    
                if (!$node['parent_id']) {
                    $tree[$id] = &$node;
                }
                else {
                    $dataset[$node['parent_id']]['childs'][$id] = &$node;
                }
            }
        }
        return $tree;
    }
    
    function get_tree(){
        $cat = $this->view_cat($this->__data, $this->__languege, $this->__class);
    }
}


class categories
{

protected $_myconn, $_id, $_description_gr, $_description_en, $_parent_id, $_level, $_nodes, $_path, $_display_in_top_menu, $_top_menu_order, $_display_in_footer, $_footer_order, $_active, $_newid ;
protected $_seo_title, $_seo_description, $_seo_url, $_children;
protected $_rscount;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM categories WHERE id=?";
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
    $this->_rscount = $icount;

    if ($icount==1) {
        $this->_description_gr = $all_rows[0]['description_gr'];
        $this->_description_en = $all_rows[0]['description_en'];
        $this->_parent_id = $all_rows[0]['parent_id'];
        $this->_level = $all_rows[0]['level'];
        $this->_nodes = $all_rows[0]['nodes'];
        $this->_path = $all_rows[0]['path'];
        $this->_display_in_top_menu = $all_rows[0]['display_in_top_menu'];
        $this->_top_menu_order = $all_rows[0]['top_menu_order'];
        $this->_display_in_footer = $all_rows[0]['display_in_footer'];
        $this->_footer_order = $all_rows[0]['footer_order'];
        $this->_active = $all_rows[0]['active'];        
        $this->_seo_title = $all_rows[0]['seo_title'];
        $this->_seo_description = $all_rows[0]['seo_description'];
        $this->_seo_url = $all_rows[0]['seo_url'];
        $this->_children = $all_rows[0]['children']; 
        
    }
    $this->_newid = 0;
}

public function get_id() {
    return $this->_id;
}
public function set_newid($val) {
    $this->_newid = $val;
}

public function get_rscount() {
    return $this->_rscount;
}

public function get_description_gr() {
    return $this->_description_gr;
}
public function set_description_gr($val) {
    $this->_description_gr = $val;
}

public function get_description_en() {
    return $this->_description_en;
}
public function set_description_en($val) {
    $this->_description_en = $val;
}

public function get_parent_id() {
    return $this->_parent_id;
}
public function set_parent_id($val) {
    $this->_parent_id = $val;
}

public function get_level() {
    return $this->_level;
}
public function set_level($val) {
    $this->_level = $val;
}

public function get_nodes() {
    return $this->_nodes;
}
public function set_nodes($val) {
    $this->_nodes = $val;
}

public function get_path() {
    return $this->_path;
}
public function set_path($val) {
    $this->_path = $val;
}

public function get_display_in_top_menu() {
    return $this->_display_in_top_menu;
}
public function set_display_in_top_menu($val) {
    $this->_display_in_top_menu = $val;
}

public function get_top_menu_order() {
    return $this->_top_menu_order;
}
public function set_top_menu_order($val) {
    $this->_top_menu_order = $val;
}

public function get_display_in_footer() {
    return $this->_display_in_footer;
}
public function set_display_in_footer($val) {
    $this->_display_in_footer = $val;
}

public function get_footer_order() {
    return $this->_footer_order;
}
public function set_footer_order($val) {
    $this->_footer_order = $val;
}

public function get_active() {
    return $this->_active;
}
public function set_active($val) {
    $this->_active = $val;
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

public function get_children() {
    return $this->_children;
}
public function set_children($val) {
    $this->_children = $val;
} 



public function Savedata() {
    echo $this->_newid>0;
    if ($this->_newid>0) {
    $ssql = "INSERT INTO categories (
    description_gr,
    description_en,
    parent_id,
    `level`,
    `nodes`,
    `path`,
    display_in_top_menu,
    top_menu_order,
    display_in_footer,
    footer_order,
    `active`,
    seo_title,
    seo_description,
    seo_url,
    children,
    id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";
    //echo $ssql;
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description_gr,
        $this->_description_en,
        $this->_parent_id,
        $this->_level,
        $this->_nodes,
        $this->_path,
        $this->_display_in_top_menu,
        $this->_top_menu_order,
        $this->_display_in_footer,
        $this->_footer_order,
        $this->_active,
        $this->_seo_title,
        $this->_seo_description,
        $this->_seo_url,
        $this->_children,
        $this->_newid
        ));
    //$ssql = $this->_myconn->getLastIDsql('categories');
        $ssql = "SELECT * FROM categories WHERE id=".$this->_newid;
        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE categories set
            description_gr = ?,
            description_en = ?,
            parent_id = ?,
            `level` = ?,
            `nodes` = ?,
            `path` = ?,
            display_in_top_menu = ?,
            top_menu_order = ?,
            display_in_footer = ?,
            footer_order = ?,
            `active` = ?,
            seo_title = ?,
            seo_description = ?,
            seo_url = ?,
            children = ?
            WHERE id = ?";
        //echo $ssql;
        $result = $this->_myconn->execSQL($ssql, array(
            $this->_description_gr,
            $this->_description_en,
            $this->_parent_id,
            $this->_level,
            $this->_nodes,
            $this->_path,
            $this->_display_in_top_menu,
            $this->_top_menu_order,
            $this->_display_in_footer,
            $this->_footer_order,
            $this->_active,
            $this->_seo_title,
            $this->_seo_description,
            $this->_seo_url,
            $this->_children,
            $this->_id));
        //print_r($result) ;
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM categories WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 



/*class company_photos
{

protected $_myconn, $_id, $_company_id, $_photo_path, $_caption_gr, $_caption_en, $_main_img ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM company_photos WHERE id=?";
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
        $this->_company_id = $all_rows[0]['company_id'];
        $this->_photo_path = $all_rows[0]['photo_path'];
        $this->_caption_gr = $all_rows[0]['caption_gr'];
        $this->_caption_en = $all_rows[0]['caption_en'];
        $this->_main_img = $all_rows[0]['main_img'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_company_id() {
    return $this->_company_id;
}
public function set_company_id($val) {
    $this->_company_id = $val;
}

public function get_photo_path() {
    return $this->_photo_path;
}
public function set_photo_path($val) {
    $this->_photo_path = $val;
}

public function get_caption_gr() {
    return $this->_caption_gr;
}
public function set_caption_gr($val) {
    $this->_caption_gr = $val;
}

public function get_caption_en() {
    return $this->_caption_en;
}
public function set_caption_en($val) {
    $this->_caption_en = $val;
}

public function get_main_img() {
    return $this->_main_img;
}
public function set_main_img($val) {
    $this->_main_img = $val;
}

public function get_full_img_path($fileExtension="jpg") {
    return $this->_photo_path."/photo_big/".$this->_id.".".$fileExtension;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO company_photos (
    company_id,
    photo_path,
    caption_gr,
    caption_en,
    main_img
    ) VALUES (?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_photo_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_main_img));
    $ssql = $this->_myconn->getLastIDsql('company_photos');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE company_photos set
        company_id = ?,
        photo_path = ?,
        caption_gr = ?,
        caption_en = ?,
        main_img = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_photo_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_main_img,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM company_photos WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} */






class company_photos
{

protected $_myconn, $_id, $_company_id, $_photo_path, $_caption_gr, $_caption_en, $_main_img, $_p_order, $_imgpath_cdn, $_smallimgpath_cdn ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM company_photos WHERE id=?";
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
        $this->_company_id = $all_rows[0]['company_id'];
        $this->_photo_path = $all_rows[0]['photo_path'];
        $this->_caption_gr = $all_rows[0]['caption_gr'];
        $this->_caption_en = $all_rows[0]['caption_en'];
        $this->_main_img = $all_rows[0]['main_img'];
        $this->_p_order = $all_rows[0]['p_order'];
        $this->_imgpath_cdn = $all_rows[0]['imgpath_cdn'];
        $this->_smallimgpath_cdn = $all_rows[0]['smallimgpath_cdn'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_company_id() {
    return $this->_company_id;
}
public function set_company_id($val) {
    $this->_company_id = $val;
}

public function get_photo_path() {
    return $this->_photo_path;
}
public function set_photo_path($val) {
    $this->_photo_path = $val;
}

public function get_caption_gr() {
    return $this->_caption_gr;
}
public function set_caption_gr($val) {
    $this->_caption_gr = $val;
}

public function get_caption_en() {
    return $this->_caption_en;
}
public function set_caption_en($val) {
    $this->_caption_en = $val;
}

public function get_main_img() {
    return $this->_main_img;
}
public function set_main_img($val) {
    $this->_main_img = $val;
}

public function get_p_order() {
    return $this->_p_order;
}
public function set_p_order($val) {
    $this->_p_order = $val;
}

public function get_imgpath_cdn() {
    return $this->_imgpath_cdn;
}
public function set_imgpath_cdn($val) {
    $this->_imgpath_cdn = $val;
}

public function get_smallimgpath_cdn() {
    return $this->_smallimgpath_cdn;
}
public function set_smallimgpath_cdn($val) {
    $this->_smallimgpath_cdn = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO company_photos (
    company_id,
    photo_path,
    caption_gr,
    caption_en,
    main_img,
    p_order,
    imgpath_cdn,
    smallimgpath_cdn
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_photo_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_main_img,
        $this->_p_order,
        $this->_imgpath_cdn,
        $this->_smallimgpath_cdn));
    $ssql = $this->_myconn->getLastIDsql('company_photos');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE company_photos set
        company_id = ?,
        photo_path = ?,
        caption_gr = ?,
        caption_en = ?,
        main_img = ?,
        p_order = ?,
        imgpath_cdn = ?,
        smallimgpath_cdn = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_photo_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_main_img,
        $this->_p_order,
        $this->_imgpath_cdn,
        $this->_smallimgpath_cdn,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM company_photos WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}








class company_video
{

protected $_myconn, $_id, $_company_id, $_video_source, $_path, $_caption_gr, $_caption_en, $_type ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM company_video WHERE id=?";
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
        $this->_company_id = $all_rows[0]['company_id'];
        $this->_video_source = $all_rows[0]['video_source'];
        $this->_path = $all_rows[0]['path'];
        $this->_caption_gr = $all_rows[0]['caption_gr'];
        $this->_caption_en = $all_rows[0]['caption_en'];
        $this->_type = $all_rows[0]['type'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_company_id() {
    return $this->_company_id;
}
public function set_company_id($val) {
    $this->_company_id = $val;
}

public function get_video_source() {
    return $this->_video_source;
}
public function set_video_source($val) {
    $this->_video_source = $val;
}

public function get_path() {
    return $this->_path;
}
public function set_path($val) {
    $this->_path = $val;
}

public function get_caption_gr() {
    return $this->_caption_gr;
}
public function set_caption_gr($val) {
    $this->_caption_gr = $val;
}

public function get_caption_en() {
    return $this->_caption_en;
}
public function set_caption_en($val) {
    $this->_caption_en = $val;
}

public function get_type() {
    return $this->_type;
}
public function set_type($val) {
    $this->_type = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO company_video (
    company_id,
    video_source,
    path,
    caption_gr,
    caption_en,
    type
    ) VALUES (?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_video_source,
        $this->_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_type));
    $ssql = $this->_myconn->getLastIDsql('company_video');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE company_video set
        company_id = ?,
        video_source = ?,
        path = ?,
        caption_gr = ?,
        caption_en = ?,
        type = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_video_source,
        $this->_path,
        $this->_caption_gr,
        $this->_caption_en,
        $this->_type,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM company_video WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class packages
{

protected $_myconn, $_id, $_description, $_max_categories, $_max_photos, $_max_videos, $_max_offers, $_max_events ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM packages WHERE id=?";
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
        $this->_max_categories = $all_rows[0]['max_categories'];
        $this->_max_photos = $all_rows[0]['max_photos'];
        $this->_max_videos = $all_rows[0]['max_videos'];
        $this->_max_offers = $all_rows[0]['max_offers'];
        $this->_max_events = $all_rows[0]['max_events'];
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

public function get_max_categories() {
    return $this->_max_categories;
}
public function set_max_categories($val) {
    $this->_max_categories = $val;
}

public function get_max_photos() {
    return $this->_max_photos;
}
public function set_max_photos($val) {
    $this->_max_photos = $val;
}

public function get_max_videos() {
    return $this->_max_videos;
}
public function set_max_videos($val) {
    $this->_max_videos = $val;
}

public function get_max_offers() {
    return $this->_max_offers;
}
public function set_max_offers($val) {
    $this->_max_offers = $val;
}

public function get_max_events() {
    return $this->_max_events;
}
public function set_max_events($val) {
    $this->_max_events = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO packages (
    description,
    max_categories,
    max_photos,
    max_videos,
    max_offers,
    max_events
    ) VALUES (?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_max_categories,
        $this->_max_photos,
        $this->_max_videos,
        $this->_max_offers,
        $this->_max_events));
    $ssql = $this->_myconn->getLastIDsql('packages');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE packages set
        description = ?,
        max_categories = ?,
        max_photos = ?,
        max_videos = ?,
        max_offers = ?,
        max_events = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_max_categories,
        $this->_max_photos,
        $this->_max_videos,
        $this->_max_offers,
        $this->_max_events,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM packages WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class categories_view2
{
    protected $_myconn, $_ssql, $_languege, $_countCompanie, $_class; 
    public function __construct($myconn, $languege, $ssql = '', $countCompanies = 0, $class = '') {
        $this->_myconn = $myconn;
        $this->__ssql = $ssql;
        $this->__languege = $languege;
        $this->__countCompanies = $countCompanies;
        $this->__class = $class;
        $this->__data = array();
        
        $rs = $this->_myconn->getRS($this->__ssql);
        
        for($i=0;$i<count($rs);$i++){
            $this->__data[$rs[$i]['id']] = $rs[$i];
        }        
        $this->__data = $this->mapTree($this->__data);
    }
    
    function view_cat($dataset, $lang, $class ) {
        foreach ($dataset as $menu) {
            $strCountCompanies = '';
            if($this->__countCompanies == 1 ){
                //$rs = $this->_myconn->getRS("SELECT DISTINCT company_id FROM company_categories WHERE path LIKE '%-".$menu['id']."-%'");
                $rs = $this->_myconn->getRS("SELECT DISTINCT company_id AS id FROM company_categories WHERE path LIKE '%-".$menu['id']."-%' "
                        . "UNION SELECT id FROM companies WHERE basic_category = ".$menu['id']);

                $strCountCompanies = "<span class=\"count-comp\">(".count($rs).")</span>";
            }
            if(isset($menu['childs'])) {$divArrow = "<div id=\"".$menu['id']."\" class=\"arrow\"></div>";} else {$divArrow = "";}
            //echo "<li>".$divArrow."<a href=\"category.php?Cat=1&l=".$lang."&id=".$menu['id']."\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            echo "<li>".$divArrow."<a href=\"category/".$menu['id']. "/" . $lang . "/" . func::normURL($menu['description_'.$lang]) . "\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            
            if(isset($menu['childs'])) {
                echo "<ul id=\"ul".$menu['id']."\" class=\"".$class."\">";
                $this->view_cat($menu['childs'], $lang, $class);
                echo '</ul>';
            }
            echo '</li>';
        }
    }

    function mapTree($dataset) {

        $tree = array();

        foreach ($dataset as $id=>&$node) {
            if (isset($node['parent_id'])){    
                if (!$node['parent_id']) {
                    $tree[$id] = &$node;
                }
                else {
                    $dataset[$node['parent_id']]['childs'][$id] = &$node;
                }
            }
        }
        return $tree;
    }
    
    function get_tree(){
        $cat = $this->view_cat($this->__data, $this->__languege, $this->__class);
    }
}


class company_categories
{

protected $_myconn, $_id, $_company_id, $_category_id, $_path ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM company_categories WHERE id=?";
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
        $this->_company_id = $all_rows[0]['company_id'];
        $this->_category_id = $all_rows[0]['category_id'];
        $this->_path = $all_rows[0]['path'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_company_id() {
    return $this->_company_id;
}
public function set_company_id($val) {
    $this->_company_id = $val;
}

public function get_category_id() {
    return $this->_category_id;
}
public function set_category_id($val) {
    $this->_category_id = $val;
}

public function get_path() {
    return $this->_path;
}
public function set_path($val) {
    $this->_path = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO company_categories (
    company_id,
    category_id,
    path
    ) VALUES (?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_category_id,
        $this->_path));
    $ssql = $this->_myconn->getLastIDsql('company_categories');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE company_categories set
        company_id = ?,
        category_id = ?,
        path = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_company_id,
        $this->_category_id,
        $this->_path,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM company_categories WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}



} 


class categories_view3
{
    protected $_myconn, $_ssql, $_languege, $_countCompanie, $_class; 
    public function __construct($myconn, $languege, $ssql = '', $countCompanies = 0, $class = '') {
        $this->_myconn = $myconn;
        $this->__ssql = $ssql;
        $this->__languege = $languege;
        $this->__countCompanies = $countCompanies;
        $this->__class = $class;
        $this->__data = array();
        
        $rs = $this->_myconn->getRS($this->__ssql);
        
        for($i=0;$i<count($rs);$i++){
            $this->__data[$rs[$i]['id']] = $rs[$i];
        }        
        $this->__data = $this->mapTree($this->__data);
    }
    
    function view_cat($dataset, $lang, $class ) {
        foreach ($dataset as $menu) {
            $strCountCompanies = '';
            if($this->__countCompanies == 1 ){
                $rs = $this->_myconn->getRS("SELECT DISTINCT company_id FROM company_categories WHERE path LIKE '%-".$menu['id']."-%'");
                $strCountCompanies = "<span class=\"count-comp\">(".count($rs).")</span>";
            }
            
            //echo "<li><a href=\"category.php?Cat=1&l=".$lang."&id=".$menu['id']."\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            echo "<li><a href=\"category/".$menu['id']. "/" . $lang . "/" . func::normURL($menu['description_'.$lang]) . "\">".$menu['description_'.$lang].$strCountCompanies."</a>";
            
            if(isset($menu['childs'])) {
                echo "<ul class=\"".$class."\">";
                $this->view_cat($menu['childs'], $lang, $class);
                echo '</ul>';
            }
            echo '</li>';
        }
    }

    function mapTree($dataset) {

        $tree = array();

        foreach ($dataset as $id=>&$node) {
            if (isset($node['parent_id'])){    
//                if (!$node['parent_id']) {
                    $tree[$id] = &$node;
                }
                else {
                    $dataset[$node['parent_id']]['childs'][$id] = &$node;
                }
//            }
        }
        return $tree;
    }
    
    function get_tree(){
        $cat = $this->view_cat($this->__data, $this->__languege, $this->__class);
    }
}


class areas
{

protected $_myconn, $_id, $_description_gr, $_description_en, $_parent_id, $_level, $_nodes, $_path, $_active ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM areas WHERE id=?";
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
        $this->_description_gr = $all_rows[0]['description_gr'];
        $this->_description_en = $all_rows[0]['description_en'];
        $this->_parent_id = $all_rows[0]['parent_id'];
        $this->_level = $all_rows[0]['level'];
        $this->_nodes = $all_rows[0]['nodes'];
        $this->_path = $all_rows[0]['path'];
        $this->_active = $all_rows[0]['active'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_description_gr() {
    return $this->_description_gr;
}
public function set_description_gr($val) {
    $this->_description_gr = $val;
}

public function get_description_en() {
    return $this->_description_en;
}
public function set_description_en($val) {
    $this->_description_en = $val;
}

public function get_parent_id() {
    return $this->_parent_id;
}
public function set_parent_id($val) {
    $this->_parent_id = $val;
}

public function get_level() {
    return $this->_level;
}
public function set_level($val) {
    $this->_level = $val;
}

public function get_nodes() {
    return $this->_nodes;
}
public function set_nodes($val) {
    $this->_nodes = $val;
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

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO areas (
    description_gr,
    description_en,
    parent_id,
    level,
    nodes,
    path,
    active
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description_gr,
        $this->_description_en,
        $this->_parent_id,
        $this->_level,
        $this->_nodes,
        $this->_path,
        $this->_active));
    $ssql = $this->_myconn->getLastIDsql('areas');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE areas set
        description_gr = ?,
        description_en = ?,
        parent_id = ?,
        level = ?,
        nodes = ?,
        path = ?,
        active = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description_gr,
        $this->_description_en,
        $this->_parent_id,
        $this->_level,
        $this->_nodes,
        $this->_path,
        $this->_active,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM areas WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

} 


class company_activity
{

protected $_myconn, $_id, $_company, $_activity_date, $_impressions, $_clicks ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn;
    
    //initialize
    $this->_impressions = 0; //!!!
    $this->_clicks = 0; //!!!
    
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM company_activity WHERE id=?"; 
        $all_rows = $this->_myconn->getRS($ssql, array($_id)); 
        } 
    else if ($_ssql!='') { 
        $ssql = $_ssql; 
        $all_rows = $this->_myconn->getRS($ssql); 
        } 
    else { 
        $rows = $my_rows; 
        if ($this->_id>0) {
            $all_rows = arrayfunctions::filter_by_value($rows, 'id', $this->_id); 
        }
    }
    $icount = count($all_rows); 

    if ($icount==1) { 
        $this->_company = $all_rows[0]['company']; 
        $this->_activity_date = $all_rows[0]['activity_date']; 
        $this->_impressions = $all_rows[0]['impressions']; 
        $this->_clicks = $all_rows[0]['clicks']; 
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

public function get_activity_date() { 
    return $this->_activity_date; 
} 
public function set_activity_date($val) { 
    $this->_activity_date = $val; 
} 

public function get_impressions() { 
    return $this->_impressions; 
} 
public function set_impressions($val) { 
    $this->_impressions = $val; 
} 

public function get_clicks() { 
    return $this->_clicks; 
} 
public function set_clicks($val) { 
    $this->_clicks = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO company_activity ( 
    company,
    activity_date,
    impressions,
    clicks
    ) VALUES (?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_activity_date, 
        $this->_impressions, 
        $this->_clicks)); 
    $ssql = $this->_myconn->getLastIDsql('company_activity');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE company_activity set 
        company = ?, 
        activity_date = ?, 
        impressions = ?, 
        clicks = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_company, 
        $this->_activity_date, 
        $this->_impressions, 
        $this->_clicks,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM company_activity WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}


class cities
{

protected $_myconn, $_id, $_description, $_description2 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM cities WHERE id=?"; 
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
        $this->_description2 = $all_rows[0]['description2']; 
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

public function get_description2() { 
    return $this->_description2; 
} 
public function set_description2($val) { 
    $this->_description2 = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO cities ( 
    description,
    description2
    ) VALUES (?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_description2)); 
    $ssql = $this->_myconn->getLastIDsql('cities');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE cities set 
        description = ?, 
        description2 = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_description2,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM cities WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}


class areascities
{

protected $_myconn, $_id, $_description, $_description2, $_areaorcity, $_id2 ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM areascities WHERE id=?"; 
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
        $this->_description2 = $all_rows[0]['description2']; 
        $this->_areaorcity = $all_rows[0]['areaorcity']; 
        $this->_id2 = $all_rows[0]['id2']; 
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

public function get_description2() { 
    return $this->_description2; 
} 
public function set_description2($val) { 
    $this->_description2 = $val; 
} 

public function get_areaorcity() { 
    return $this->_areaorcity; 
} 
public function set_areaorcity($val) { 
    $this->_areaorcity = $val; 
} 

public function get_id2() { 
    return $this->_id2; 
} 
public function set_id2($val) { 
    $this->_id2 = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO areascities ( 
    description,
    description2,
    areaorcity,
    id2
    ) VALUES (?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_description2, 
        $this->_areaorcity, 
        $this->_id2)); 
    $ssql = $this->_myconn->getLastIDsql('areascities');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE areascities set 
        description = ?, 
        description2 = ?, 
        areaorcity = ?, 
        id2 = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_description, 
        $this->_description2, 
        $this->_areaorcity, 
        $this->_id2,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM areascities WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}



class company_ratings
{

protected $_myconn, $_id, $_useremail, $_username, $_rating, $_companyid, $_comment, $_rdatetime, $_approved, $_userid ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') { 
    $all_rows = NULL; 
    $this->_id = $_id; 
    $this->_myconn = $myconn; 
    if ($my_rows==NULL) { 
        $ssql = "SELECT * FROM company_ratings WHERE id=?"; 
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
        $this->_useremail = $all_rows[0]['useremail']; 
        $this->_username = $all_rows[0]['username']; 
        $this->_rating = $all_rows[0]['rating']; 
        $this->_companyid = $all_rows[0]['companyid']; 
        $this->_comment = $all_rows[0]['comment']; 
        $this->_rdatetime = $all_rows[0]['rdatetime']; 
        $this->_approved = $all_rows[0]['approved']; 
        $this->_userid = $all_rows[0]['userid']; 
    } 
} 

public function get_id() { 
    return $this->_id; 
} 

public function get_useremail() { 
    return $this->_useremail; 
} 
public function set_useremail($val) { 
    $this->_useremail = $val; 
} 

public function get_username() { 
    return $this->_username; 
} 
public function set_username($val) { 
    $this->_username = $val; 
} 

public function get_rating() { 
    return $this->_rating; 
} 
public function set_rating($val) { 
    $this->_rating = $val; 
} 

public function get_companyid() { 
    return $this->_companyid; 
} 
public function set_companyid($val) { 
    $this->_companyid = $val; 
} 

public function get_comment() { 
    return $this->_comment; 
} 
public function set_comment($val) { 
    $this->_comment = $val; 
} 

public function get_rdatetime() { 
    return $this->_rdatetime; 
} 
public function set_rdatetime($val) { 
    $this->_rdatetime = $val; 
} 

public function get_approved() { 
    return $this->_approved; 
} 
public function set_approved($val) { 
    $this->_approved = $val; 
} 

public function get_userid() { 
    return $this->_userid; 
} 
public function set_userid($val) { 
    $this->_userid = $val; 
} 

public function Savedata() { 
    if ($this->_id==0) { 
    $ssql = "INSERT INTO company_ratings ( 
    useremail,
    username,
    rating,
    companyid,
    comment,
    rdatetime,
    approved,
    userid
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; 
    $result = $this->_myconn->execSQL($ssql, array( 
        $this->_useremail, 
        $this->_username, 
        $this->_rating, 
        $this->_companyid, 
        $this->_comment, 
        $this->_rdatetime, 
        $this->_approved, 
        $this->_userid)); 
    $ssql = $this->_myconn->getLastIDsql('company_ratings');

        $newrows = $this->_myconn->getRS($ssql); 
        $this->_id = $newrows[0]['id']; 
    } 
    else { 
        $ssql = "UPDATE company_ratings set 
        useremail = ?, 
        username = ?, 
        rating = ?, 
        companyid = ?, 
        comment = ?, 
        rdatetime = ?, 
        approved = ?, 
        userid = ?
        WHERE id = ?"; 
        $result = $this->_myconn->execSQL($ssql, array( 
        $this->_useremail, 
        $this->_username, 
        $this->_rating, 
        $this->_companyid, 
        $this->_comment, 
        $this->_rdatetime, 
        $this->_approved, 
        $this->_userid,
        $this->_id));
    } 
    if ($result===false) { 
        return false; 
    } 
    return true; 
} 

public function Delete() { 
    $ssql = "DELETE FROM company_ratings WHERE id=?"; 
    $result = $this->_myconn->execSQL($ssql, array($this->_id)); 
    if ($result===false) { 
        return false; 
    } 
else { 
    return true; 
}
} 

}




class tags
{

protected $_myconn, $_id, $_description, $_alt_description, $_norm_descriptions, $_seo_url ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM tags WHERE id=?";
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
        $this->_alt_description = $all_rows[0]['alt_description'];
        $this->_norm_descriptions = $all_rows[0]['norm_descriptions'];
        $this->_seo_url = $all_rows[0]['seo_url'];
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

public function get_alt_description() {
    return $this->_alt_description;
}
public function set_alt_description($val) {
    $this->_alt_description = $val;
}

public function get_norm_descriptions() {
    return $this->_norm_descriptions;
}
public function set_norm_descriptions($val) {
    $this->_norm_descriptions = $val;
}

public function get_seo_url() {
    return $this->_seo_url;
}
public function set_seo_url($val) {
    $this->_seo_url = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO tags (
    description,
    alt_description,
    norm_descriptions,
    seo_url
    ) VALUES (?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_alt_description,
        $this->_norm_descriptions,
        $this->_seo_url));
    $ssql = $this->_myconn->getLastIDsql('tags');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE tags set
        description = ?,
        alt_description = ?,
        norm_descriptions = ?,
        seo_url = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_description,
        $this->_alt_description,
        $this->_norm_descriptions,
        $this->_seo_url,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM tags WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}








class company_tags
{

protected $_myconn, $_id, $_tag_id, $_company_id ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM company_tags WHERE id=?";
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
        $this->_tag_id = $all_rows[0]['tag_id'];
        $this->_company_id = $all_rows[0]['company_id'];
    }
}

public function get_id() {
    return $this->_id;
}

public function get_tag_id() {
    return $this->_tag_id;
}
public function set_tag_id($val) {
    $this->_tag_id = $val;
}

public function get_company_id() {
    return $this->_company_id;
}
public function set_company_id($val) {
    $this->_company_id = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO company_tags (
    tag_id,
    company_id
    ) VALUES (?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_tag_id,
        $this->_company_id));
    $ssql = $this->_myconn->getLastIDsql('company_tags');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE company_tags set
        tag_id = ?,
        company_id = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_tag_id,
        $this->_company_id,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM company_tags WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}