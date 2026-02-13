<?php


class companies
{

	protected $_myconn, $_id, $_company_name_gr, $_company_name_en, $_short_description_gr, $_short_description_en, $_full_description_gr, $_full_description_en, $_address_gr, $_address_en, $_zip_code, $_phone, $_phone2, $_mobile_phone, $_email, $_fax, $_website, $_facebook, $_twitter, $_linkedin, $_basic_category, $_basic_category_path, $_area, $_city_id, $_geo_x, $_geo_y, $_package, $_expires, $_username, $_password, $_impressions_counter, $_clicks_counter, $_proposed, $_rotating_new_comp, $_rotating_proposed_comp, $_rotating_other_prop_comp, $_active, $_keywords_gr, $_keywords_en, $_url_rewrite_gr, $_url_rewrite_en, $_seo_page_title_gr, $_seo_page_title_en, $_seo_page_description_gr, $_seo_page_description_en, $_seo_manually_set, $_popularity, $_hasoffers, $_datecreated, $_companyname_dm, $_address_dm, $_phone1_dm, $_phone2_dm, $_fax_dm, $_email_dm, $_mobile_dm, $_website_dm, $_geox_dm, $_geoy_dm, $_zipcode_dm, $_facebook_dm, $_twitter_dm, $_shortdescr_dm, $_fulldescr_dm, $_basiccat_dm, $_area_dm, $_keywords_dm, $_cityid_dm, $_profession, $_profession_dm, $_eponimia ;

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
			$this->_basic_category_path = $all_rows[0]['basic_category_path'];
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
			$this->_rotating_new_comp = $all_rows[0]['rotating_new_comp'];
			$this->_rotating_proposed_comp = $all_rows[0]['rotating_proposed_comp'];
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
			$this->_profession = $all_rows[0]['profession'];
			$this->_profession_dm = $all_rows[0]['profession_dm'];
			$this->_eponimia = $all_rows[0]['eponimia'];
		}
	}

	public function get_id() {
		return $this->_id;
	}

	public function get_company_name_gr() {
		return $this->_company_name_gr;
	}
	public function set_company_name_gr($val) {
		$this->_company_name_gr = $val;
	}

	public function get_company_name_en() {
		return $this->_company_name_en;
	}
	public function set_company_name_en($val) {
		$this->_company_name_en = $val;
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

	public function get_address_gr() {
		return $this->_address_gr;
	}
	public function set_address_gr($val) {
		$this->_address_gr = $val;
	}

	public function get_address_en() {
		return $this->_address_en;
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

	public function get_linkedin() {
		return $this->_linkedin;
	}
	public function set_linkedin($val) {
		$this->_linkedin = $val;
	}

	public function get_basic_category() {
		return $this->_basic_category;
	}
	public function set_basic_category($val) {
		$this->_basic_category = $val;
	}

	public function get_basic_category_path() {
		return $this->_basic_category_path;
	}
	public function set_basic_category_path($val) {
		$this->_basic_category_path = $val;
	}

	public function get_area() {
		return $this->_area;
	}
	public function set_area($val) {
		$this->_area = $val;
	}

	public function get_city_id() {
		return $this->_city_id;
	}
	public function set_city_id($val) {
		$this->_city_id = $val;
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

	public function get_rotating_new_comp() {
		return $this->_rotating_new_comp;
	}
	public function set_rotating_new_comp($val) {
		$this->_rotating_new_comp = $val;
	}

	public function get_rotating_proposed_comp() {
		return $this->_rotating_proposed_comp;
	}
	public function set_rotating_proposed_comp($val) {
		$this->_rotating_proposed_comp = $val;
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
		return $this->_url_rewrite_gr;
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

	public function get_eponimia() {
		return $this->_eponimia;
	}
	public function set_eponimia($val) {
		$this->_eponimia = $val;
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
    city_id,
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
    profession,
    profession_dm,
    eponimia
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
					$this->_city_id,
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
					$this->_profession,
					$this->_profession_dm,
					$this->_eponimia));
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
        city_id = ?,
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
        profession = ?,
        profession_dm = ?,
        eponimia = ?
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
					$this->_city_id,
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
					$this->_profession,
					$this->_profession_dm,
					$this->_eponimia,
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




?>