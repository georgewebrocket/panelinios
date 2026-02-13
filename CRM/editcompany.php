<?php

//panelinios

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("COMPANIES",$lang,$db1);

date_default_timezone_set('Europe/Athens');


function normPhone($phone) {
    $phone = str_replace("+30", "", $phone);
    $phone = preg_replace("/[^0-9]/", "", $phone);
    return $phone;

}

function updateCompanySites($db, $companyId, $sitesVal, $delimiterRows = "///", $delimiterColumns = "||") {
    $companyId = intval($companyId);
    $db->execSQL("DELETE FROM COMPANY_SITES WHERE company_id=?", array($companyId));

    if (trim($sitesVal)=="") {
        return;
    }

    $rows = explode($delimiterRows, $sitesVal);
    for ($i=0; $i<count($rows); $i++) {
        $row = trim($rows[$i]);
        if ($row=="") {
            continue;
        }

        $cols = explode($delimiterColumns, $row);
        $address = isset($cols[0]) ? trim($cols[0]) : "";
        $phone = isset($cols[1]) ? trim($cols[1]) : "";
        $mapX = isset($cols[2]) ? trim($cols[2]) : "";
        $mapY = isset($cols[3]) ? trim($cols[3]) : "";
        $cityId = isset($cols[4]) && trim($cols[4])!="" ? intval($cols[4]) : 0;
        $areaId = isset($cols[5]) && trim($cols[5])!="" ? intval($cols[5]) : 0;

        if ($address=="" && $phone=="" && $mapX=="" && $mapY=="" && $cityId==0 && $areaId==0) {
            continue;
        }

        $sql = "INSERT INTO COMPANY_SITES (company_id, address, phone, map_x, map_y, city_id, area_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $db->execSQL($sql, array($companyId, $address, $phone, $mapX, $mapY, $cityId, $areaId));
    }
}

$noCacheHash = "&chash=" . date("YmdHis");

$userid = $_SESSION['user_id'];

$adminpass = func::vlookup("keyvalue", "SETTINGS", "keycode='admin-pass'", $db1);
$hideHistory = func::vlookup("keyvalue", "SETTINGS", "keycode='AGENTS-HIDE-HISTORY'", $db1);
$hideHistory = $hideHistory==1? TRUE: FALSE;

$id = $_GET['id'];
$company = new COMPANIES($db1, $id);


/*EPAGELMATIAS*/
include_once "php/configEpag.php";
$dboEpag = new DB(conn_epag::$connstr, conn_epag::$username, conn_epag::$password);
// $sql = "SELECT * FROM COMPANIES WHERE epag_id=?";
$epagId = $company->get_epag_id();
//echo "<!--EPAGID=$epagId-->";





$pid = 0;
if ($company->get_catalogueid()!="" && $company->get_catalogueid()!=0) {  
    $pid = func::getCompanyPId($company);
}

$url_online = "";


if ($company->get_catalogueid()!="" && $company->get_catalogueid()!=0) {  
    $companyURL = file_get_contents("http://www.panelinios.gr/ws/_getCompanyURL.php?pid=$pid");
    // echo "###{$companyURL}###";
    $url_online = $companyURL;
    
} 



$voucherid = 0;
if (isset($_REQUEST['voucherid'])) {
    $voucherid = $_REQUEST['voucherid'];
    $voucher = new VOUCHERS($db1, $voucherid);
}


$lockmsg = "";
if ($company->get_lockedbyuser()==1 && $company->get_lockuser()!=$_SESSION['user_id']) {
    $userfullname = func::vlookup("fullname", "USERS", "id=".$company->get_lockuser(), $db1);
    $lockmsg = "Αυτή η καταχώρηση είναι κλειδωμένη από το χρήστη <br/><strong>".$userfullname."</strong>";
}

if ($id>0 && $company->get_lockedbyuser()==0) {
    $company->set_lockedbyuser(1);
    $company->set_lockuser($_SESSION['user_id']);
    $company->Savedata();
}

$msg = "";

function GetNr($string) {
    return preg_replace("/[^0-9]/","",$string);
}

if ($id==0) {
    $company->set_workingmonths("[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12]");
    $company->set_mydata_companytype(1);
}

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $oldSitesVal = $company->get_sites();
    $err = 0;
    if ($_POST['txtCompanyname'] == ""){
        $err = 1;
        $msg .= $l->l('blank_companyname')."</br>";
    }
    if ($_POST['txtPhone1'] == ""){
        $err = 1;
        $msg .= $l->l('blank_phone_1')."</br>";
    }
    
    if ($id==0) {
        //check phones
        $myPhone = GetNr($_POST['txtPhone1']);
        $chkPhone1 = func::vlookup("id", "COMPANIES", "allphonesdigits LIKE '%".$myPhone."%'", $db1);
        if ($chkPhone1>0) {
            $err=1;
            $msg = $l->l("company-phone1-exists");
        }
        if ($err==0 && trim($_POST['txtPhone2'])!="") {
            $myPhone = GetNr($_POST['txtPhone2']);
            $chkPhone2 = func::vlookup("id", "COMPANIES", "allphonesdigits LIKE '%".$myPhone."%'", $db1);
            if ($chkPhone2>0) {
                $err=1;
                $msg = $l->l("company-phone2-exists");
            }    
        }
        if ($err==0 && trim($_POST['txtMobilephone'])!="") {
            $myPhone = GetNr($_POST['txtMobilephone']);
            $chkMobile = func::vlookup("id", "COMPANIES", "allphonesdigits LIKE '%".$myPhone."%'", $db1);
            if ($chkMobile>0) {
                $err=1;
                $msg = $l->l("company-phonemobile-exists");
            }    
        }
        if ($err==0 && trim($_POST['txtFax'])!="") {
            $myPhone = GetNr($_POST['txtFax']);
            $chkFax = func::vlookup("id", "COMPANIES", "allphonesdigits LIKE '%".$myPhone."%'", $db1);
            if ($chkFax>0) {
                $err=1;
                $msg = $l->l("company-fax-exists");
            }    
        }
        
        
        //allphonesdigits
        /*$chkPhone1 = func::vlookup("id", "COMPANIES", "digits(phone1) LIKE '%".$phone1."%'", $db1);
        if ($chkPhone1>0) {
            $err=1;
            $msg = $l->l("company-phone-exists");
        }
        if ($err==0) {
            $chkPhone2 = func::vlookup("id", "COMPANIES", "digits(phone2) LIKE '%".$phone1."%'", $db1);
            if ($chkPhone2>0) {
                $err=1;
                $msg = $l->l("company-phone-exists");
            }
        }
        if ($err==0) {
            $chkPhone3 = func::vlookup("id", "COMPANIES", "digits(mobilephone) LIKE '%".$phone1."%'", $db1);
            if ($chkPhone3>0) {
                $err=1;
                $msg = $l->l("company-phone-exists");
            }
        }
        if ($err==0) {
            $chkPhone4 = func::vlookup("id", "COMPANIES", "digits(fax) LIKE '%".$phone1."%'", $db1);
            if ($chkPhone4>0) {
                $err=1;
                $msg = $l->l("company-phone-exists");
            }
        }*/
        
        
        
        $company->set_active(1);
        
    }
    
    if ($err==0) {
        
        /*TRACK CHANGES*/
        $companyChange = new company_change($id, $userid, $db1);
        $companyChange->addChange("companyname", $company->get_companyname(), 
                trim($_POST['txtCompanyname']));
        $companyChange->addChange("phone1", $company->get_phone1(), 
                trim($_POST['txtPhone1']));
        $companyChange->addChange("phone2", $company->get_phone2(), 
                trim($_POST['txtPhone2']));
        $companyChange->addChange("fax", $company->get_fax(), 
                trim($_POST['txtFax']));
        $companyChange->addChange("mobilephone", $company->get_mobilephone(), 
                trim($_POST['txtMobilephone']));
        $companyChange->addChange("contactperson", $company->get_contactperson(), 
                trim($_POST['txtContactperson']));

        if ($company->get_lock_category()==0) {
                $companyChange->addChange("basiccategory", $company->get_basiccategory(), 
                $_POST['cCategory']);
        }
        $companyChange->addChange("subcategory", $company->get_subcategory(), 
                $_POST['cSubcategory']);
        $companyChange->addChange("reference", $company->get_reference(), 
                $_POST['cReference']);
        $companyChange->addChange("area", $company->get_area(), 
                $_POST['cArea']);
        $companyChange->addChange("city_id", $company->get_city_id(), 
                $_POST['c_city_id']);
        $companyChange->addChange("geo_x", $company->get_geo_x(), 
                trim($_POST['txtGeoX']));
        $companyChange->addChange("geo_y", $company->get_geo_y(), 
                trim($_POST['txtGeoY']));
        $companyChange->addChange("address", $company->get_address(), 
                trim($_POST['txtAddress']));
        $companyChange->addChange("zipcode", $company->get_zipcode(), 
                trim($_POST['txtZipcode']));
        $companyChange->addChange("email", $company->get_email(), 
                trim($_POST['txtEmail']));
        $companyChange->addChange("website", $company->get_website(), 
                trim($_POST['txtWebsite']));
        $companyChange->addChange("facebook", $company->get_facebook(), 
                trim($_POST['txtFacebook']));
        $companyChange->addChange("twitter", $company->get_twitter(), 
                trim($_POST['txtTwitter']));
        $companyChange->addChange("LinkedIn", $company->get_LinkedIn(), 
                trim($_POST['txtLinkedin']));
        $companyChange->addChange("ShortDescription", $company->get_ShortDescription(), 
                trim($_POST['txtShortDescr']));
        $companyChange->addChange("FullDescription", $company->get_FullDescription(), 
                trim($_POST['txtFullDescr']));
        $companyChange->addChange("company_type", $company->get_company_type(), 
                $_POST['cCompanyType']);        
        $companyChange->addChange("package", $company->get_package(), 
                $_POST['cPackage']);
        $companyChange->addChange("discount", $company->get_discount(), 
                $_POST['cDiscount']);
        $companyChange->addChange("package2", $company->get_package2(), 
                $_POST['cPackage2']);
        $companyChange->addChange("discount2", $company->get_discount2(), 
                $_POST['cDiscount2']);        
        $companyChange->addChange("expires", $company->get_expires(), 
                textbox::getDate($_POST['txtExpires'],$locale));
        $companyChange->addChange("afm", $company->get_afm(), 
                trim($_POST['t_afm']));
        $companyChange->addChange("doy", $company->get_doy(), 
                trim($_POST['t_doy']));
        $companyChange->addChange("eponimia", $company->get_eponimia(), 
                trim($_POST['t_eponimia']));
        $companyChange->addChange("profession", $company->get_profession(), 
                $_POST['c_profession']);
        $companyChange->addChange("googleplus", $company->get_googleplus(), 
                trim($_POST['txtGoogleplus']));
        $companyChange->addChange("pinterest", $company->get_pinterest(), 
                trim($_POST['txtPinterest']));
        $companyChange->addChange("sites", $company->get_sites(), 
                trim($_POST['t_sites-val']));
        $companyChange->addChange("workinghours", $company->get_workinghours(), 
                trim($_POST['t_workinghours-val']));
        $companyChange->addChange("workingmonths", $company->get_workingmonths(), 
                selectList::getVal("l_workingmonths", $_POST));
        $companyChange->addChange("domain_name", $company->get_domain_name(), 
                trim($_POST['t_domain_name']));
        $companyChange->addChange("domain_expires", $company->get_domain_expires(), 
                textbox::getDate($_POST['t_domain_expires'],$locale));
                
        $companyChange->addChange("mydata_companytype", $company->get_mydata_companytype(), 
                $_POST['c_mydata_companytype']);
        
        
        //....
        
        $company->set_companyname(trim($_POST['txtCompanyname']));

        if ($id>0) {
            if ($company->get_phone1()!=trim($_POST['txtPhone1']) 
            || $company->get_phone2()!=trim($_POST['txtPhone2'])
            || $company->get_mobilephone()!=trim($_POST['txtMobilephone'])
            ) {
                $sql = "DELETE FROM PHONES WHERE company_id=?";
                $myRes = $db1->execSQL($sql, [$id]);

                $phones = [];

                $phones1 = explode(",", $_POST['txtPhone1']);
                foreach ($phones1 as $phone1) {
                    $phones[] = $phone1;
                }

                $phones2 = explode(",", $_POST['txtPhone2']);
                foreach ($phones2 as $phone2) {
                    $phones[] = $phone2;
                }

                $mobilephones = explode(",", $_POST['txtMobilephone']);
                foreach ($mobilephones as $mobilephone) {
                    $phones[] = $mobilephone;
                }

                //insert phones
                foreach ($phones as $phone) {
                    if (trim($phone)!="") {
                        $sql = "INSERT INTO PHONES (company_id, phone) VALUES (?, ?)";
                        $ret = $db1->execSQL($sql, [$id, normPhone($phone)]);
                        //echo $ret . " - " . $rs[$i]['id'] . " - $phone" . "<br/>";
                    }        
                }
            

            }
        }





        $company->set_phone1(trim($_POST['txtPhone1']));
        $company->set_phone2(trim($_POST['txtPhone2']));
        $company->set_fax(trim($_POST['txtFax']));
        $company->set_mobilephone(trim($_POST['txtMobilephone']));
        $company->set_contactperson(trim($_POST['txtContactperson']));
        if ($company->get_lock_category()==0) {
            $company->set_basiccategory($_POST['cCategory']);
        }
        $company->set_subcategory($_POST['cSubcategory']);
        $company->set_reference($_POST['cReference']);
        $company->set_area($_POST['cArea']);
        $company->set_city_id($_POST['c_city_id']);
        $company->set_geo_x(trim($_POST['txtGeoX'])!=""?trim($_POST['txtGeoX']):0);
        $company->set_geo_y(trim($_POST['txtGeoY'])!=""? trim($_POST['txtGeoY']): 0);
        $company->set_address(trim($_POST['txtAddress']));
        $company->set_zipcode(trim($_POST['txtZipcode']));
        
        $company->set_region($_POST['t_region']);
        
        $company->set_email(trim($_POST['txtEmail']));
        $company->set_website(trim($_POST['txtWebsite']));
        $company->set_facebook(trim($_POST['txtFacebook']));
        $company->set_instagram($_POST['txtInstagram']);
        $company->set_twitter(trim($_POST['txtTwitter']));
        $company->set_LinkedIn(trim($_POST['txtLinkedin']));
        $company->set_ShortDescription(trim($_POST['txtShortDescr']));
        $company->set_FullDescription(trim($_POST['txtFullDescr']));
        $company->set_company_type($_POST['cCompanyType']);
        
        $company->set_package($_POST['cPackage']);
        $company->set_discount($_POST['cDiscount']);        
        $price = func::vlookup("price", "PACKAGES", "id=".$company->get_package(), $db1);
        if ($price>0) {
            $discount = func::vlookup("discount", "DISCOUNTS", "id=".$company->get_discount(), $db1);
            if ($discount=='') {
                $discount = 0;
            }
            $finalprice = $price * (1-$discount/100); //echo "FP=".$finalprice;
            $company->set_price($finalprice);
        }
        else {
            $finalprice = textbox::getCurrency($_POST['txtPrice'], $locale);
            $company->set_price($finalprice);
        }
        
        $company->set_package2($_POST['cPackage2']);
        $company->set_discount2($_POST['cDiscount2']);
        $price2 = func::vlookup("price", "PACKAGES", "id=".$company->get_package2(), $db1);
        if ($price2>0) {
            $discount2 = func::vlookup("discount", "DISCOUNTS", "id=".$company->get_discount2(), $db1);;
            $finalprice2 = $price2 * (1-$discount2/100); //echo "FP=".$finalprice;
            $company->set_price2($finalprice2);
        }
        else {
            $finalprice2 = textbox::getCurrency($_POST['txtPrice2'], $locale);
            $company->set_price2($finalprice2);
        }
        
        
        // $company->set_catalogueid($_POST['txtCatalogueid']!=""?$_POST['txtCatalogueid']: 0);
        if (($_SESSION['user_profile']>0)) {
            $myCatalogueId = $_POST['txtCatalogueid']==""? 0: $_POST['txtCatalogueid'];
            $myCatalogueId = $myCatalogueId == 0? 0: func::getCatalogueidFromPid($_POST['txtCatalogueid']);
            $company->set_catalogueid($myCatalogueId);
            //$pid = $company->get_catalogueid() * 2 + 7128;
            //echo "updating txtCatalogueid...";
            $pid = $_POST['txtCatalogueid'];
        }


        $company->set_expires(textbox::getDate($_POST['txtExpires'],$locale));
		if (($_SESSION['user_profile']>1)) {
			$company->set_username($_POST['txtUsername']);
			$company->set_password($_POST['txtPassword']);
		}
        
        if (isset($_POST['cUser'])) {
            $company->set_user($_POST['cUser']);
        }
        
        if (isset($_POST['cUserdataentry'])) {
            $company->set_userdataentry($_POST['cUserdataentry']);
        }
        
        //$company->set_recalldate(textbox::getDate($_POST['txtRecalldate'],$locale));
        //$company->set_recalltime($_POST['cRecalltime']);
        
        if ($company->get_id()>0) {            
            if (($_SESSION['user_profile']>1)) {
                $company->set_status($_POST['cStatus']);
            }            
        }
        else {
            $company->set_status(1); //new
        }
        
        $company->set_courier($_POST['c_courier']);    
        $company->set_DeliveryDate(textbox::getDate($_POST['txtDeliveryDate'],$locale));
        $company->set_DeliveryTime($_POST['txtDeliveryTime']);
        $company->set_DeliveryNotes(mb_substr ($_POST['txtDeliveryNotes'],0,105));
        
        
        $myAddress = $_POST['t_courier_address'];
        $myZipcode = $_POST['t_courier_zipcode'];
        $myCity = $_POST['c_courier_city'];
        //$myNotes = $_POST['t_courier_notes'];
        $myPhone = $_POST['t_courier_phone'];
        $myRegion = $_POST['t_courier_region'];
        
        $company->set_courier_address($myAddress);
        $company->set_courier_zipcode($myZipcode);
        $company->set_courier_city($myCity);
        //$company->set_courier_notes($myNotes);
        $company->set_courier_region($myRegion);
        $company->set_courier_phone($myPhone);
        
        
                
        if ($id==0) {
            $company->set_userdataentry($_SESSION['user_id']);
            $company->set_status(1);
            $company->set_courier_status(1);
        }
        
        //....
        $company->set_afm($_POST['t_afm']);
        $company->set_doy($_POST['t_doy']);
        if ($_SESSION['user_profile']==3) {
            if (isset($_POST['chk_invoiceprinted'])) {
                $company->set_invoiceprinted(1);
            }
            else {
                $company->set_invoiceprinted(0);
            }
        }
        
        $company->set_eponimia($_POST['t_eponimia']);
             
        if (($_SESSION['user_profile']!=1)) {
            $company->set_onlinestatus(checkbox::getVal($_POST['chk_onlinestatus']));
            $company->set_onlinedatetime(textbox::getDate($_POST['t_onlinedatetime'], $locale));
        }
        else {
            if ($id==0) {
                $company->set_onlinestatus(0);
            }            
        }
        
        $company->set_vn_keywords($_POST['t_vn_keywords']); 
        
        if ($id!=0) {
            $lastactiondate = func::vlookup("DATE_FORMAT(MAX(atimestamp),\"%Y%m%d000000\")", 
                "ACTIONS", "company=".$company->get_id(), $db1);
            $company->set_lastactiondate($lastactiondate);
        }
		
        $company->set_haswebsite(checkbox::getVal2($_POST, 'chk_haswebsite'));
        if ($_POST['s-profession']!='') {
                $company->set_profession($_POST['c_profession']);
        }
        else {
                $company->set_profession(0);
        }

        if ($_SESSION['user_profile']==3) {
          $company->set_active(checkbox::getVal2($_POST, 'chk_active'));
        }

        if ($_SESSION['user_profile']==3) {
          $company->set_lock_category(checkbox::getVal2($_POST, 'chk_lock_category'));
        }

        $company->set_googleplus($_POST['txtGoogleplus']);
        $company->set_pinterest($_POST['txtPinterest']);
        $company->set_sites($_POST['t_sites-val']);
        $company->set_workinghours($_POST['t_workinghours-val']);
        $company->set_workingmonths(selectList::getVal("l_workingmonths", $_POST));
        
        $company->set_domain(checkbox::getVal2($_POST, "chk_domain"));
        $company->set_domain_name($_POST['t_domain_name']);
        $company->set_domain_expires(textbox::getDate($_POST['t_domain_expires'], $locale));
        
        
        
        $company->set_fb_package($_POST['c_fb_package']);
        $company->set_fb_discount($_POST['c_fb_discount']);
        $company->set_fb_months($_POST['c_fb_months']);
        $company->set_fb_price(textbox::getCurrency($_POST['t_fb_price'], $locale));
        $company->set_fb_expires(textbox::getDate($_POST['t_fb_expires'], $locale));
        $company->set_fb_page($_POST['t_fb_page']);
        $company->set_fb_comments($_POST['t_fb_comments']);
        $company->set_fb_ok(checkbox::getVal2($_POST, "chk_fb_ok"));
        
        $company->set_ga_package($_POST['c_ga_package']);
        $company->set_ga_discount($_POST['c_ga_discount']);
        $company->set_ga_months($_POST['c_ga_months']);
        $company->set_ga_price(textbox::getCurrency($_POST['t_ga_price'], $locale));
        $company->set_ga_expires(textbox::getDate($_POST['t_ga_expires'], $locale));
        $company->set_ga_page($_POST['t_ga_page']);
        $company->set_ga_keywords($_POST['t_ga_keywords']);
        $company->set_ga_comments($_POST['t_ga_comments']);
        $company->set_ga_ok(checkbox::getVal2($_POST, "chk_ga_ok"));
        
        $company->set_srv_app(checkbox::getVal2($_POST, "t_srv_app"));
        $company->set_srv_services(selectList::getVal("t_srv_services", $_POST));
        $company->set_srv_date(textbox::getDate($_POST['t_srv_date'], $locale));
        $company->set_srv_field1($_POST['t_srv_field1']); //time
        $company->set_srv_comments($_POST['t_srv_comments']);
        $company->set_srv_status($_POST['t_srv_status']);
        $company->set_srv_salesman($_POST['t_srv_salesman']);
        $company->set_srv_result($_POST['t_srv_result']);
        $company->set_srv_price($_POST['t_srv_price']);
        
        $company->set_online_url($url_online);
        
        $company->set_mydata_companytype($_POST['c_mydata_companytype']);
        
        $company->set_old_tax_data($_POST['t_old_tax_data']);
        
        if ($company->Savedata()) {
            $msg .= "Τα δεδομένα αποθηκεύτηκαν"."<br/>"; //...........
            $id = $company->get_id();
            
            //$printPanelId = 1;
            //include 'updatePanelCompany.php';
            
            $msg .= "<span style=\"font-size:16px; line-height:30px\">" . $panelMsg . "</span>";

            if (trim($oldSitesVal) != trim($_POST['t_sites-val'])) {
                updateCompanySites($db1, $id, $_POST['t_sites-val']);
            }
            
            $companyChange->commitChanges();
            
        }
        else {
            $msg .= $lg->l('error')."<br/>".$lg->l('try-again')."<br/>"; //...........
        }
        
        
    }
}

//$userid = $_SESSION['user_id'];
/*$sql = "SELECT * FROM MESSAGES WHERE companyid=$id AND "
        . "((`sender`=$userid) OR (`receiver`=$userid) OR (`receiver`=43)) "
        . "ORDER BY id DESC";*/

$messages = NULL;

if ($id>0) {
    $sql = "SELECT * FROM MESSAGES WHERE companyid=$id ORDER BY id DESC";
    $messages = $db1->getRS($sql);
}


$msg_gindex = "";
if (isset($_REQUEST['gindex']) && $_REQUEST['gindex']==1) {
    // 1) Configure these values:
    $endpoint   = 'https://gindex.epagelmatias.gr/index.php';  // Your actual endpoint
    $apiKey     = 'dtranq24!@';                   // Must match indexing.php’s API_KEY
    $urlToIndex = $url_online;   // The URL you’re pushing to Google
    // echo $urlToIndex;

    // 2) Call the function
    $result = func::sendIndexingRequest($endpoint, $apiKey, $urlToIndex);

    ob_start();

    // 3) Check for low-level errors first
    if ($result['error'] !== null) {
        echo "❌ Request failed: " . $result['error'] . "...\n";
        exit;
    }

    // 4) Inspect HTTP status
    echo "HTTP Status: " . $result['httpStatus'] . "\n";

    // 5) Examine the decoded PHP array in 'response'
    $responseData = $result['response'];

    if ($result['httpStatus'] >= 200 && $result['httpStatus'] < 300) {
        // Assuming a 2xx means the webservice itself ran and returned JSON
        if (isset($responseData['success']) && $responseData['success'] === true) {
            echo "✅ Indexing succeeded. ";
            echo $responseData['data']['url'];
            //print_r($responseData['data']);
        } else {
            echo "❌ Indexing reported failure: ";
            if (isset($responseData['error'])) {
                echo "Error message: " . $responseData['error'] . " ";
            } else {
                echo "Unexpected response format: ";
                print_r($responseData);
            }
        }
    } else {
        // Non‐2xx HTTP status (e.g., 400, 401, 403, 502, 500)
        echo "❌ HTTP error (status " . $result['httpStatus'] . "):\n";
        print_r($responseData);
    }

    $msg_gindex = ob_get_clean();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title><?php echo $id . " - " . $company->get_companyname() ?></title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    <!--<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />-->
    <script src="https://kit.fontawesome.com/8282565b47.js" crossorigin="anonymous"></script>
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    <script>
    
        $(document).ready(function() {	
                $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 450 });
                $("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });
                $("a.fancybox700").fancybox({'type' : 'iframe', 'width' : 700, 'height' : 450 });
                //$("a.fancybox7000").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 450 });

                $('#btnGetStats').click(function(){ GetStats(); });
                
        });
    
        function CreatePassword(id) {
            var pusername = id + 3118;
            $("#txtUsername").val(pusername);
            mypass = makeid(8);
            $("#txtPassword").val(mypass);
        }
	
        function makeid(myLen) {
                var text = "";
                var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                for( var i=0; i < myLen; i++ )
                        text += possible.charAt(Math.floor(Math.random() * possible.length));

                return text;
        }
        
        function GetStats() {
            var company = <?php echo $company->get_catalogueid(); ?>;
            var datestart = $('#txtStatsStart').val();
            var ar1 = datestart.split("/");
            datestart = ar1[2]+ar1[1]+ar1[0]+"000000";
            var datestop = $('#txtStatsStop').val();
            var ar2 = datestop.split("/");
            datestop = ar2[2]+ar2[1]+ar2[0]+"000000";
            var myUrl = "https://www.panelinios.gr/ws/ws-stats.php?id="+company;
            myUrl += "&datestart="+datestart;
            myUrl += "&datestop="+datestop;
            
            $.ajax({url: myUrl, 
		success:function(result){
                $('#stats').html(result);
                $('#stats-heading').html("Στατιστικά");
                
            }});
        
        }

    </script>

    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);            
            $("#cCategory").change(function() {ShowSubCat();});
            $("#cPackage").change(function() {UpdatePackage();});
            $("#cDiscount").change(function() {UpdatePackage();});
            $("#cPackage2").change(function() {UpdatePackage2();});
            $("#cDiscount2").change(function() {UpdatePackage2();});
            
            
            $("#c_fb_package").change(function() {UpdatePackageFB();});
            $("#c_fb_discount").change(function() {UpdatePackageFB();});
            $("#c_fb_months").change(function() {UpdatePackageFB();});
            
            //UpdatePackageGA
            $("#c_ga_package").change(function() {UpdatePackageGA();});
            $("#c_ga_discount").change(function() {UpdatePackageGA();});
            $("#c_ga_months").change(function() {UpdatePackageGA();});
            
        });
            
            
            
        $(function() {
            var dNow = new Date();
            $( "#txtDeliveryDate" ).change(function() {
                var myDate = $( "#txtDeliveryDate" ).val();
                var ar = myDate.split("/");
                var strDate = ar[2]+"/"+ar[1]+"/"+ar[0];
                var d = new Date(strDate);
                if (d<dNow) {
                    alert("Μη επιτρεπτή ημερομηνία παράδοσης");
                    $( "#txtDeliveryDate" ).val("");
                }
            });
        });
   
            
            
//        $(document).on("keypress", 'form', function (e) {
//           var code = e.keyCode || e.which;
//           if (code == 13) {
//               e.preventDefault();
//               return false;
//           }
//           });
           
        $(document).ready(function() {
            $("form input").keypress(function (e) {
               var code = e.keyCode || e.which;
               if (code == 13) {
                   e.preventDefault();
                   return false;
               }
               });
            });

            
        function ShowSubCat() {
            //var theCat = $('#cCategory :selected').val();
            var theCat = $('#cCategory').val();
            var myURL = 'getSubCategories.php?cat=' + theCat;
            $.ajax({
               url: myURL,
               success: function(data) {
                     $('#subcategory').html(data);                      
               }
               });
        }

        function UpdatePackage() {
            var thePackage = $('#cPackage :selected').val();
            var theDiscount = $('#cDiscount :selected').text();
            var discount = theDiscount == "..."? 0: parseInt(theDiscount);
            var myURL = "getPackage.php?id="+thePackage;
            $.ajax({
               url: myURL,
               success: function(data) {                               
                   var ardata = data.split("//");
                   var price = ardata[0] * (100 - discount)/100 ;
                   var duration = ardata[1];
                   var expDate = addDays(new Date(), duration);                   
                   var expDateF = getMyDate(expDate);
                   $('#txtExpires').val(expDateF);
                   $('#txtPrice').val(price); 
               }
               });
        }
        
        function UpdatePackage2() {
            var thePackage2 = $('#cPackage2 :selected').val();
            var theDiscount2 = $('#cDiscount2 :selected').text();
            var discount2 = theDiscount2 == "..."? 0: parseInt(theDiscount2);
            var myURL = "getPackage.php?id="+thePackage2;
            $.ajax({
               url: myURL,
               success: function(data) {                               
                   var ardata = data.split("//");
                   var price = ardata[0] * (100 - discount2)/100 ;
                   var duration = ardata[1];
                   var expDate = addDays(new Date(), duration);                   
                   var expDateF = getMyDate(expDate);
                   $('#t_domain_expires').val(expDateF);
                   $('#txtPrice2').val(price); 
               }
               });
        }
        
        
        
        function UpdatePackageFB() {
            var thePackage = $('#c_fb_package :selected').val();
            var theDiscount = $('#c_fb_discount :selected').text();
            var theMonths = $('#c_fb_months :selected').val();
            var discount = theDiscount == "..."? 0: parseInt(theDiscount);
            var myURL = "getPackage.php?id="+thePackage;
            $.ajax({
               url: myURL,
               success: function(data) {                               
                   var ardata = data.split("//");
                   var price = (ardata[0] * (100 - discount)/100) * theMonths ;
                   var duration = ardata[1] * theMonths; ///
                   var expDate = addDays(new Date(), duration);                   
                   var expDateF = getMyDate(expDate);
                   $('#t_fb_expires').val(expDateF);
                   $('#t_fb_price').val(price); 
               }
               });
        }
        
        
        function UpdatePackageGA() {
            var thePackage = $('#c_ga_package :selected').val();
            var theDiscount = $('#c_ga_discount :selected').text();
            var theMonths = $('#c_ga_months :selected').val();
            var discount = theDiscount == "..."? 0: parseInt(theDiscount);
            var myURL = "getPackage.php?id="+thePackage;
            $.ajax({
               url: myURL,
               success: function(data) {                               
                   var ardata = data.split("//");
                   var price = (ardata[0] * (100 - discount)/100) * theMonths ;
                   var duration = ardata[1] * theMonths; ///
                   var expDate = addDays(new Date(), duration);                   
                   var expDateF = getMyDate(expDate);
                   $('#t_ga_expires').val(expDateF);
                   $('#t_ga_price').val(price); 
               }
               });
        }
        
        
        
        
        
        function getMyDate(expDate) {
            var dd = '0' + expDate.getDate();
            dd = dd.substr(dd.length - 2);
            var mm = expDate.getMonth() + 1;
            mm = '0' + mm;
            mm = mm.substr(mm.length - 2);
            var y = expDate.getFullYear();
            var expDateF = dd + '/'+ mm + '/'+ y;
            return expDateF;
        }
         
        function addDays(theDate, days) {
            return new Date(theDate.getTime() + days*24*60*60*1000);
        }
        
        $(function() {
            $('#selectworkinghours').change(function() {
                var id = $('#selectworkinghours').val();
                $.post( "getWorkingHours.php", 
                    {id : id},
                    function( data ) {
                        $( "#workinghours" ).html( data );
                    });
                    
            });
        });
        
        $(function() {
            $('#chk_domain').change(function() {
                if ($('#chk_domain').is(':checked')) {
                    var now = new Date();
                    var dd = pad(now.getDate(),2);
                    var mm = pad(now.getMonth()+1,2); //January is 0!
                    var yyyy = now.getFullYear() + 1; //next year
                    $('#t_domain_expires').val(dd+'/'+mm+'/'+yyyy);
                }
                else {
                    $('#t_domain_expires').val('');
                }
            });

        });
    
        $(function(){
            $('#save2').click(function() {
                $('#mainform').submit();
            });
        });
        
        function pad(num, size) {
            var s = num+"";
            while (s.length < size) s = "0" + s;
            return s;
        }

        $(function() {
            
            $("#toggle-xreoseis").click(function() {
                if ($("#xreoseis-panelinios").css('display')=='none') {
                    $("#xreoseis-panelinios").css('display', 'block');
                    $("#xreoseis-title").html('Χρεώσεις Panelinios');
                    $("#xreoseis-epagelmatias").css('display', 'none');
                    $(this).html('Epagelmatias >');
                }
                else {
                    $("#xreoseis-panelinios").css('display', 'none');
                    $("#xreoseis-epagelmatias").css('display', 'block');
                    $("#xreoseis-title").html('Χρεώσεις Epagelmatias');
                    $(this).html('Panelinios >');
                }
            });

        });


        $(function() {
            $("#btn-send-courier-email").click(function() {
                var sendRed = $("#chk_send_courier_red").is(':checked') ? 1 : 0;
                var sendAcs = $("#chk_send_courier_acs").is(':checked') ? 1 : 0;
                var sendText = $("#t_courier_email_text").val();
                var voucher = $(this).data("voucher");
                
                $.post("sendCourierEmail.php", 
                {
                    sendred: sendRed,
                    sendacs: sendAcs,
                    sendtext: sendText,
                    companyid: <?php echo $company->get_id(); ?>,
                    voucher: voucher,
                    user: <?php echo $_SESSION['user_id']; ?>
                }, function(data){
                    if (data!="OK") {
                        alert("Σφάλμα κατά την αποστολή του email: " + data);
                        return;
                    }
                    else {
                        //all good
                        alert("Το email στάλθηκε στον courier.");
                    }
                    
                });
            });
        });
    </script>
    
    
    <!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
    <script src="//cdn.tinymce.com/4.1/tinymce.min.js"></script>-->
    <!-- <script src="https://cdn.tiny.cloud/1/fiq8lm63smdu8mc2fhs375nntdcf27e6r8gdeb3c5zqzllnl/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
    <script src="js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector:'#txtFullDescr',
            menubar : false,
            relative_urls : false,
            remove_script_host : false,
            entities: 'raw',
            entity_encoding: 'raw',
            convert_urls : true,
            paste_as_text: true,

            plugins: 'code, link, lists, image, paste',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code',
            menubar: 'edit format view',
            image_advtab: true

        }); 

    </script>
    
    

    <style>
        
        #l_workingmonths {
            -webkit-column-count: 3; /* Chrome, Safari, Opera */
            -moz-column-count: 3; /* Firefox */
            column-count: 3;            
        }
        
        #BtnOk {
            font-weight: bold;
            width:100%;
            background-color: #090;
            color:#fff;
            border:none;
        }
        
        #datagridStatus {
            width:100%;
        }
        
        form input[type="submit"], form input[type="button"], form input[type="reset"] {
            padding: 0.5em 1em;
            vertical-align: middle;
        }
        
        #t_afm, #t_doy, #t_eponimia, #cCompanyType, #c_mydata_companytype {
            background-color:rgb(255,255,220);
        }
        
        .tel {
            color: rgb(0,150,200);
            font-size: 20px;
            margin-right: 5px;
            margin-left: 5px;
        }
        
        .hl {
            /*color:rgb(100,100,255);*/
            background-color: rgb(255,255,150);
            padding:3px 0px;
        }
        
        .voucher-info {
            background-color: rgb(250,250,200);
            padding: 30px;
            margin: 1em;
            line-height: 30px;
            
        }
        
        b {
            font-weight: bold;
        }
        
        #voucher-status {
            font-size: 30px;
        }
        
        .note-for-courier,
        .note-for-courier-email {
            padding: 1px 5px;
            background-color: rgb(200,250,200);
            cursor: pointer;
        }
        .note-for-courier:hover,
        .note-for-courier-email:hover {
            background-color: rgb(250,200,200);
        }
        
        div.header .button {
            display:block;
            margin:0px 0px 5px !important;
            padding:10px !important;
        }
        
        .form-container {
            width:calc(100% - 220px);
            float:left;
        }
        
        .mycol {
            padding:0px 70px 0px 0px;
        }
        @media screen and (max-width:1360px) {
        .mycol {
            padding:0px 30px 0px 0px;
        }    
        }

        #t_srv_comments {
            height:350px;
        }
        
        
    </style>

    </head>

    <body class="form">
        
        
        <?php include "blocks/menu.php"; ?>
        
        
        <div id="left-menu" class="header" style="position:absolute; top:0px; left:0px; transition:all 0.2s">
    
            <div class="spacer-50"></div>
            <div class="spacer-10"></div>
            
            <div style="padding:10px; background-color:#fff; font-size: 20px; color:#222; margin-bottom: 30px;">
                <?php echo $company->get_id(); ?>
                <hr/>
                <?php echo $company->get_companyname(); ?>
            </div>
            
            
            
            <div class="" style="width:180px; position: relative;">
                    
                <?php
                $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=?";
                $rs = $db1->getRS($sql, array($id));
                $myStatusRecall = FALSE;
                $myCommUser = 0;
                /*for ($i = 0; $i < count($rs); $i++) {
                    if ($rs[$i]['status']==3) { //recall
                        $myStatusRecall = TRUE;
                        $myCommUser = $rs[$i]['userid'];
                    }
                }*/
                if ((!$myStatusRecall) 
                        || ($_SESSION['user_id'] == $myCommUser) 
                        || ($_SESSION['user_profile']>1)) {
                if ($id!=0) {
                    $statusLink1 = "companyChangeStatus3.php?company=".$id."&status1=".$company->get_status();       
                    $statusLink = $statusLink1."&status2=2";
                ?>
                <a class="button fancybox7000" style="background-color: #999;" href="<?php echo $statusLink; ?>">ΔΕΝ ΑΠΑΝΤΑ</a>
                
                <?php $statusLink = $statusLink1."&status2=3"; ?>
                <a class="button fancybox7000" style="background-color: #f48221;" href="<?php echo $statusLink; ?>">RECALL</a>
                
                <?php $statusLink = $statusLink1."&status2=12"; ?>
                <a class="button fancybox7000" style="background-color: #999;" href="<?php echo $statusLink; ?>">ΛΑΘΟΣ ΤΗΛ.</a>
                
                <?php $statusLink = $statusLink1."&status2=5"; ?>
                <a class="button fancybox7000" style="background-color: #6c6;" href="<?php echo $statusLink; ?>">ΣΥΜΦΩΝΗΣΕ</a>
                
                <?php $statusLink = $statusLink1."&status2=4"; ?>
                <a class="button fancybox7000" style="background-color: #7552e0;" href="<?php echo $statusLink; ?>">ΑΡΝΗΤΙΚΟΣ</a>
                
                <?php $statusLink = $statusLink1."&status2=15"; ?>
                <a class="button fancybox7000" style="background-color: #7552e0;" href="<?php echo $statusLink; ?>">ΑΡΝΗΣΗ ΑΝΑΝ.</a>
                
                <?php $statusLink = $statusLink1."&status2=17"; ?>
                <a class="button fancybox7000" style="background-color: #999;" href="<?php echo $statusLink; ?>">ΣΥΝΕΧ. ΕΠΙΣΤΡ.</a>
                
                
                <?php if ($_SESSION['user_profile']>1) { ?>
                    <?php $statusLink = $statusLink1."&status2=9"; ?>
                    <a class="button fancybox7000" href="<?php echo $statusLink; ?>">ΠΛΗΡΩΣΕ</a>                           
                    <?php $statusLink = $statusLink1."&status2=8"; ?>
                    <a class="button fancybox7000" style="background-color: #c66;" href="<?php echo $statusLink; ?>">ΕΠΙΣΤΡΟΦΗ</a>
                    <?php $statusLink = $statusLink1."&status2=16"; ?>
                    <a class="button fancybox7000" style="background-color: #c66;" href="<?php echo $statusLink; ?>">ΑΚΥΡΩΣΗ</a>
                <?php } ?>
                
                <?php $statusLink = $statusLink1."&status2=14"; ?>
                <a class="button fancybox7000" href="<?php echo $statusLink; ?>">ΕΠΙΚΟΙΝΩΝΙΑ</a>
                
                <!--<a class="button" href="sendoffer.php?id=<?php echo $company->get_id(); ?>">Προσφορά</a>
                <a class="button" href="sendCodes.php?id=<?php echo $company->get_id(); ?>">Αποστ. κωδικών</a> -->
                <a class="button fancybox" href="mailclient/sendEmail.php?account=0&customer=<?php echo $company->get_id(); ?>">Αποστ. email</a>

                <a class="button" href="companyPrint.php?id=<?php echo $company->get_id(); ?>">ΕΚΤΥΠΩΣΗ</a>
                
                
                
                
                
                <div id="left-menu-more-btn" style="font-size: 30px; margin-bottom: 20px;"><span style="background: #999; padding:5px 15px; border-radius: 20px; cursor:pointer" class="fa fa-angle-right"></span></div>
                
                <div id="left-menu-less-btn" style="font-size: 30px; display:none; margin-bottom: 20px;"><span style="background: #999; padding:5px 15px; border-radius: 20px; cursor:pointer" class="fa fa-angle-left"></span></div>
                
                <div id="left-menu-more" style="display:none; position:absolute; top:0px; left:200px; z-index: 1000; padding:0px 20px 20px 0px; width: 190px;">
                    
                    <a target="_blank" class="button" href="mailclient/index.php?h_ac_customer=<?php echo $company->get_id(); ?>">EMAILS</a>          
                
                    <?php
                    if ($company->get_profession()>0 && $company->get_city_id()>0 && $company->get_companyname()!="") {
                    
                    if ($company->get_catalogueid()!="") {  ?>
                        <a class="button" href="syncCompany.php?companyid=<?php echo $id; ?>">Sync Online</a>
                        
                        <?php if (($_SESSION['user_profile']>1)) { ?>
                            <a target="_blank" class="button" href="https://partners.panelinios.gr/login.php?TxtUsername=<?php echo $company->get_catalogueid(); ?>&TxtPassword=ep932KZ$!&BtnOK=Login">Edit online</a>
                        <?php } else { ?>
                            <a target="_blank" class="button" href="https://partners.panelinios.gr/login.php?TxtUsername=<?php echo $company->get_catalogueid(); ?>&TxtPassword=<?php echo $adminpass; ?>&BtnOK=Login">Edit online</a> 

                        <?php } ?>
                            
                    <?php } else {   ?>
    
                        <a class="button" href="syncCompany.php?companyid=<?php echo $id; ?>">Add online</a>
                    <?php } } else { echo "<a title=\"Επωνυμία, Επάγγελμα ή Πόλη είναι κενό\" class=\"button\" style=\"background-color:#ccc; cursor:default\">Συγχρ. CRM<->ONLINE</a>"; }  ?>
                        
                    <?php if ($company->get_catalogueid()!="") {  ?>
                    <a target="_blank" title="Ανοιγμα καταχώρησης της εταιρείας στο panelinios.gr" class="button" href="<?php echo $url_online; ?>">View online</a>
                    
                    <?php } ?>
                    
                    <a title="Ανανέωση δεδομένων φόρμας" class="button" href="editcompany.php?id=<?php echo $company->get_id(); ?>">Refresh</a>
                    
                    
                    
                    <?php 
                    }
                    //submit
                    $btnOK = new button("BtnOk", $lg->l('save'));
                    //$btnOK->get_button_simple();
                    
                    //echo "&nbsp;";
                    
                    //echo $_SESSION['user_id'];
                    if ($company->get_lockedbyuser()==1 && $company->get_lockuser()==$_SESSION['user_id']) {
                        $btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "");
                        $btnCloseUpdate->set_method("UnlockCompany($id)");
                    }
                    else {
                        $btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "close-update");
                    }
                    //$btnCloseUpdate->get_button_simple();
                    
                    ?>
                    
                    <?php } ?>
                    
                    
                        
                    
                    
                    <iframe src="formCompanyPostSosuite.php?id=<?php echo $id ?>" style="width: 100%; height: 70px; vertical-align: middle; margin-bottom: 5px;" scrolling="no" ></iframe>
                    <a target="_blank" href="https://sosuite.gr/app/posts.php?id=66&customField1=<?php echo $id ?>" class="button">Facebook posts</a>
                    <a target="_blank" href="https://sosuite.gr/app/posts.php?id=74&customField1=<?php echo $id ?>" class="button">Twitter posts</a>


                    <a class="button change-tax-data" style="display:block">Αλλαγή φορολογικών στοιχείων</a>

                    <a class="button btn-get-company-data-afm" style="display:block">Ενημέρωση στοιχείων από ΑΑΔΕ</a>

                    <a class="button" style="display:block" href="editcompany.php?id=<?php echo $id ?>&gindex=1">Google Index</a>


                    <?php if ($company->get_catalogueid()!="") {  ?>
                    <a class="button" style="display:block" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($url_online); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    style="text-decoration: none; padding: 8px 12px; background-color: #4267B2; color: white; border-radius: 4px; display: inline-block;">
                    Share on Facebook
                    </a>
                    <?php } ?>

                    
                    
                    <div style="height:20px"></div>

                    <a class="button" style="display:block" href="company-ai-content.php?id=<?php echo $id ?>">AI content</a>
                    
                    <?php if ($_SESSION['user_profile']==3) { ?>
                    <a class="button fancybox7000" style="background-color: #c00;"  href="delCompany.php?id=<?php echo $id; ?>">DELETE</a>
                    <?php } ?>
                    
                    <!--GP 29/5/2019-->
                    <!--<?php if ($id>0 && $_SESSION['user_profile']>1) { ?>                    
                    <a href="_cloneCompany.php?id=<?php echo $id ?>" class="button" style="background-color: #f90;">Clone</a>                   
                    <?php } ?>-->
                    
                    
                </div>
                
                
                
                <?php 
                if ($company->get_parent_record()>0) { 
                    $parentId = $company->get_parent_record();
                    echo "<p style=\"line-height:18px\">Αυτή η καρτέλα προέρχεται από την <a href=\"editcompany.php?id=$parentId\">$parentId</a></p>";
                } 
                if ($company->get_child_record()>0) { 
                    $childId = $company->get_child_record();
                    echo "<p style=\"line-height:18px\">Αυτή η καρτέλα μετασχηματίστηκε στην <a href=\"editcompany.php?id=$childId\">$childId</a></p>";
                } 
                ?>
                <!--GP 29/5/2019 END-->   
                
                <div style="clear: both; height: 30px;"></div>
                
                
            </div>
            
            
            
            
            
        </div>
            
        
        
        
        <div id="form-container" class="form-container" style="margin-left: 220px; background-color: #eee;">
            
            <div style="height: 60px;" id="top"></div>   
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            <?php if ($lockmsg!="") { echo "<h2 style=\"color:red\" class=\"msg\">".$lockmsg."</h2>";} ?>

            <?php if ($msg_gindex!="") { echo "<p class=\"msg\" style=\"padding:0px 40px\">".$msg_gindex."</p>";} ?>
            
            
            
            <?php
            
            if (isset($_GET['save'])) {
                //check area + zipcode
                $area = "";
                if ($company->get_courier_zipcode()!="") {                    
                    $zipcode = $company->get_courier_zipcode();
                    
                    if ($company->get_courier_region()!="") {
                        $area = $company->get_courier_region();
                    }
                    else {
                        $area = $company->get_courier_city_descr();
                    }
                    
                    $checkareaClass = "red-accept-courier-region";
                }
                else {
                    $zipcode = $company->get_zipcode();
                    
                    if ($company->get_region()!="") {
                        $area = $company->get_region();
                    }
                    else {
                        $area = $company->get_city();
                    }
                    
                    $checkareaClass = "red-accept-region"; 
                }
                
                $area = trim($area);
                $zipcode = str_replace(" ", "", $zipcode);
                $zipcode = trim($zipcode);
                
                if ($company->get_region()=='') {
                    include("_checkarea.php");
                }
                                
            }
            
            ?>
            
            
            <?php
            if ($voucherid>0) {
                echo "<div class=\"voucher-info\">";
                
                echo "<div class=\"col-6\">";
                echo "<b>Voucher ID:</b> $voucherid / " . func::nrToCurrency($voucher->get_amount()) . " €<br/>";
                echo "<b>Ημερ/Ώρα παράδοσης:</b> " . func::str14toDate($voucher->get_deliverydate()) . 
                        " " . $voucher->get_deliverytime() . "<br/>";
                echo "<b>Σχόλια παράδοσης:</b> " . $voucher->get_deliverynotes() . "<br/>";
                echo "<b>Σχόλια courier:</b> " . $voucher->get_courier_notes() . "<br/>";   
                echo "<b>Σημείωση για τον Courier:</b><br/>";
                
                echo "<div class=\"col-8\">";
                $t_second_note_for_courier = new textbox("t_second_note_for_courier", "", 
                        $voucher->get_second_note_for_courier());
                $t_second_note_for_courier->set_multiline();
                echo $t_second_note_for_courier->textboxSimple();
                echo "</div>";
                echo "<div class=\"col-4\">";
                echo "<a id=\"btn-second-note-for-courier-ok\" data-voucher=\"$voucherid\" class=\"button\" style=\"margin-left:10px\">ΟΚ</a>";
                echo "</div>";
                echo "<div class=\"clear\"></div>";
                
                echo "<span class=\"note-for-courier\">Νέο ραντεβού / ημερ. ..... / ώρα ...</span> &nbsp";
                echo "<span class=\"note-for-courier\">Επιστροφή φακέλου</span> &nbsp";
                echo "<span class=\"note-for-courier\">Θα περάσει ο πελάτης από τα γραφεία της εταιρείας courier</span> &nbsp";
                echo "<span class=\"note-for-courier\">Να γίνει εκ νέου επικοινωνία</span> &nbsp";
                echo "<span class=\"note-for-courier\">Να κρατηθει ο φάκελος μέχρι τις</span> &nbsp";
                
                echo "<div class=\"clear\" style=\"height:20px\"></div>";
                
                echo "<p>";
                echo $voucher->get_courier_note_archive();
                echo "</p>";

                echo "<hr style=\"width: calc(100% - 30px);margin:0px 0px 30px;\"/>";
                $textarea_text = "Αγαπητέ συνεργάτη,\n\nΣας στέλνουμε οδηγίες για το ακόλουθο Voucher:";
                $textarea_text .= "Κωδ. RED {$voucher->get_vcode2()}\n";
                if ($voucher->get_vcode3()!="") {
                    $textarea_text .= " / ACS {$voucher->get_vcode3()}";
                }
                echo "<textarea id=\"t_courier_email_text\" rows=\"8\" cols=\"30\" style=\"width: calc(100% - 30px);\">{$textarea_text}</textarea>";
                echo "<br/>";

                echo "<span class=\"note-for-courier-email\">Νέο ραντεβού / ημερ. ..... / ώρα ...</span> &nbsp";
                echo "<span class=\"note-for-courier-email\">Επιστροφή φακέλου</span> &nbsp";
                echo "<span class=\"note-for-courier-email\">Θα περάσει ο πελάτης από τα γραφεία της εταιρείας courier</span> &nbsp";
                echo "<span class=\"note-for-courier-email\">Να γίνει εκ νέου επικοινωνία</span> &nbsp";
                echo "<span class=\"note-for-courier-email\">Να κρατηθει ο φάκελος μέχρι τις</span> &nbsp";
                echo "<br/><br/>";
                echo "<input type=\"checkbox\" id=\"chk_send_courier_red\" checked/> RED";
                echo "<br/>";
                $checked = $voucher->get_vcode3()!="" ? "checked" : "";
                echo "<input type=\"checkbox\" id=\"chk_send_courier_acs\" {$checked}/> ACS";
                echo "<br/>";
                echo "<a id=\"btn-send-courier-email\" data-voucher=\"$voucherid\" class=\"button\" style=\"margin-top:10px\">Αποστολή email στον courier</a>";
                
                echo "</div>";
                
                echo "<div class=\"col-6\">";
                echo "<h3>Επικοινωνία με τον πελάτη</h3>";
                $sql = "SELECT * FROM ACTIONS WHERE voucherid=? ORDER BY atimestamp DESC";
                $rsVoucherActions = $db1->getRS($sql, array($voucherid));                
                echo "<div id=\"voucher-comm-history\">";
                if ($rsVoucherActions) {                    
                    for ($i = 0; $i < count($rsVoucherActions); $i++) {
                        //$myDate = $rsVoucherActions[$i]["atimestamp"];
                        $myComment = $rsVoucherActions[$i]["comment"];
                        
                        $myDate = new DateTime($rsVoucherActions[$i]['atimestamp']);
                        $myDate = $myDate->format("d/m/Y");
                        
                        echo "<div class=\"col-3\" style=\"font-weight:bold\">$myDate</div>";
                        echo "<div class=\"col-9\">$myComment</div>"; 
                        echo "<div class=\"clear\"></div>";
                    }
                    echo "<div class=\"clear\" style=\"height:20px\"></div>";
                }
                echo "</div>";
                
                echo "<div class=\"col-12\">";
                echo "<form style=\"margin:0px\">";
                //echo "<textarea name=\"t_vouchercomm\" id=\"t_vouchercomm\" rows=\"4\" cols=\"20\"></textarea>";
                
                $t_vouchercomm = new textbox("t_vouchercomm", "Επικοινωνία", "");
                $t_vouchercomm->set_multiline();
                $t_vouchercomm->get_Textbox();
                
                echo "<hr/>";
                
                $t_voucher_followup_date = new textbox("t_voucher_followup_date", 
                        "Ημερ. followup", $voucher->get_followup_date());
                $t_voucher_followup_date->set_format("DATE");
                $t_voucher_followup_date->set_locale($locale);
                $t_voucher_followup_date->get_Textbox();
                
                echo "<div class=\"clear\"></div>";
                
                $t_voucher_followup_time = new comboBox("t_voucher_followup_time", 
                    $db1, 
                    "SELECT id, description FROM TIMES ORDER BY description", 
                    "id","description",
                    $voucher->get_followup_time(),
                    "Ώρα followup");
                $t_voucher_followup_time->get_comboBox();
                
                echo "<div class=\"spacer-20\"></div>";                
                
                echo "<a id=\"btn-voucher-comment-ok\" data-voucher=\"$voucherid\" data-company=\"$id\" class=\"button\">OK</a>";
                
                echo "<div class=\"clear\"></div>";
                
                echo "</form>";
                
                
                echo "</div>";
                
                echo "<div class=\"clear\"></div>";
                
                echo "</div>";
                
                echo "<div class=\"clear\" style=\"height:1px\"></div>";
                
                echo "</div>";
                
            }           
            
            ?>
            
            
            
            
            
            <form id="mainform" style="border:none; background-color: #eee;" action="editcompany.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
                
                
                <div class="col-6 col-md-12 mycol">
                    
                    <?php
                    //Id
                    $txtId = new textbox("txtId", $l->l('id'),$company->get_id(), $lg->l('auto'));
                    $txtId->set_disabled();
                    $txtId->get_Textbox();

                    //companyname
                    $txtCompanyname = new textbox("txtCompanyname", $l->l('companyname'),$company->get_companyname(), "*");
                    $txtCompanyname->get_Textbox();

                    if ($_SESSION['user_profile']==3) {
                        $chk_active = new checkbox("chk_active", "Visible", $company->get_active());
                        $chk_active->get_Checkbox();
                    }

                    $ac_profession = new autocomplete("s-profession", "PROFESSIONS", 
                        $company->get_profession(), $db1);
                    $ac_profession->set_label("Επάγγελμα");
                    $ac_profession->set_hiddenid("c_profession");
                    $ac_profession->getAutocomplete();

                    //phone1
                    $txtPhone1 = new textbox("txtPhone1", $l->l('phone-1'),$company->get_phone1(), "*");
                    echo "<div class=\"col-4\">Τηλ. 1</div><div class=\"col-6\" style=\"\">";
                    echo $txtPhone1->textboxSimple();
                    echo "</div>";
                    echo "<div class=\"col-2\">";                    
                    $arPhone1 = explode(",", $company->get_phone1());
                    if ($arPhone1[0]!="") {
                        for ($p = 0; $p < count($arPhone1); $p++) {
                            $tel = $arPhone1[$p];
                            echo "<a class=\"tel\" title=\"$tel\" href=\"tel:$tel\"><span class=\"fa fa-phone-square\"></span></a>";    
                        }                        
                    }
                    echo "</div>";

                    //phone2
                    $txtPhone2 = new textbox("txtPhone2", $l->l('phone-2'),$company->get_phone2(), "");
                    echo "<div class=\"col-4\">Τηλ. 2</div><div class=\"col-6\" style=\"\">";
                    echo $txtPhone2->textboxSimple();
                    echo "</div>";
                    echo "<div class=\"col-2\">";
                    $arPhone2 = explode(",", $company->get_phone2());
                    if ($arPhone2[0]!="") {
                        for ($p = 0; $p < count($arPhone2); $p++) {
                            $tel = $arPhone2[$p];
                            echo "<a class=\"tel\" title=\"$tel\" href=\"tel:$tel\"><span class=\"fa fa-phone-square\"></span></a>";    
                        }                        
                    }
                    echo "</div>";

                    //fax
                    $txtFax = new textbox("txtFax", $l->l('Fax'),$company->get_fax(), "");
                    echo "<div class=\"col-4\">Fax</div><div class=\"col-6\" style=\"\">";
                    echo $txtFax->textboxSimple();
                    echo "</div>";
                    
                    
                    //mobile-phone
                    $txtMobilephone = new textbox("txtMobilephone", $l->l('mobile-phone'),$company->get_mobilephone(), "");
                    echo "<div class=\"col-4\">Κινητό</div><div class=\"col-6\" style=\"\">";
                    echo $txtMobilephone->textboxSimple();
                    echo "</div>";
                    echo "<div class=\"col-2\">";
                    $arPhoneM = explode(",", $company->get_mobilephone());
                    if ($arPhoneM[0]!="") {
                        for ($p = 0; $p < count($arPhoneM); $p++) {
                            $tel = $arPhoneM[$p];
                            echo "<a class=\"tel\" title=\"$tel\" href=\"tel:$tel\"><span class=\"fa fa-phone-square\"></span></a>";    
                        }
                        echo "&nbsp;";
                        for ($p = 0; $p < count($arPhoneM); $p++) {
                            $tel = $arPhoneM[$p];
                            echo "<a class=\"sms fancybox500\" title=\"SMS $tel\" href=\"sendsms.php?phone=$tel&customer_id=$id\"><img src=\"img/sms.svg\" style=\"width:20px\" /></a>";    
                        }
                    }
                    echo "</div>";
                    

                    //contactperson
                    $txtContactperson = new textbox("txtContactperson", $l->l('contact-person'),$company->get_contactperson(), "");
                    $txtContactperson->get_Textbox();

                    
                    if ($company->get_lock_category()==0) {
                    $ac_category = new autocomplete("s-category", "CATEGORIES", 
                            $company->get_basiccategory(), $db1);
                    $ac_category->set_label("Επιλ. κατηγορίας");
					$ac_category->set_descr_field('panel_description');
                    $ac_category->set_hiddenid("cCategory");
                    $ac_category->getAutocomplete();
                    }
                    else {
                        echo "<div class=\"col-4\">Κατηγορία</div>";
                        $category_descr = func::vlookup("panel_description", "CATEGORIES", "id=". $company->get_basiccategory(), $db1);
                        echo "<div class=\"col-8\">{$category_descr}</div>";
                        echo "<div style=\"clear:both;height:20px;\"></div>";

                    }

                    if ($_SESSION['user_profile']==3) {                        
                        $chk_lock_category = new checkbox("chk_lock_category", "Κλείδωμα κατηγορίας 🔒", $company->get_lock_category());
                        $chk_lock_category->get_Checkbox();
                        echo "<div style=\"clear:both;height:15px;\"></div>";
                    }

                    //sub-category
                    $basiccat = $company->get_basiccategory();
                    if ($basiccat=="") { $basiccat=-1;}
                    //echo "Basic cat=".$basiccat;
                    $cSubcategory = new comboBox("cSubcategory", $db1, 
                            "SELECT id, description FROM CATEGORIES WHERE parentid=".$basiccat, 
                            "id","description",
                            $company->get_subcategory(),$l->l("sub-category"));
                    echo "<div class=\"col-4\">".$l->l("sub-category")."</div><div class=\"col-8\"><div id=\"subcategory\">";
                    echo $cSubcategory->comboBox_simple();
                    echo "</div></div>";

                    $t_vn_category = new textbox("t_vn_category", "VN-CATEGORY", $company->get_vn_category());
                    $t_vn_category->set_disabled();
                    $t_vn_category->get_Textbox();


                    //reference
                    $cReference = new comboBox("cReference", $db1, 
                            "SELECT id, description FROM REFERENCE", 
                            "id","description",
                            $company->get_reference(),
                            $l->l("reference"));
                    $cReference->get_comboBox();



                    $cArea = new comboBox("cArea", $db1, 
                        "SELECT id, description FROM AREAS", 
                        "id","description",
                        $company->get_area(),$l->l("area"));
                    $cArea->get_comboBox(); 
                    
                    $c_city_id = new comboBox("c_city_id", $db1,
                    		"SELECT EP_CITIES.id, CONCAT(EP_CITIES.description, ' (', AREAS.description, ')') AS CITY_AREA FROM EP_CITIES INNER JOIN AREAS ON EP_CITIES.area_id = AREAS.id",
                            "id","CITY_AREA",
                    		$company->get_city_id(),"ΠΟΛΗ");
                    $c_city_id->get_comboBox();
                    
                    //geo-x
                    $txtGeoX = new textbox("txtGeoX", $l->l('geo-x'),$company->get_geo_x(), "00.000000");
                    $txtGeoX->get_Textbox();

                    //geo-y
                    $txtGeoY = new textbox("txtGeoY", $l->l('geo-y'),$company->get_geo_y(), "00.000000");
                    $txtGeoY->get_Textbox();

                    //address
                    $txtAddress = new textbox("txtAddress", $l->l('address'),$company->get_address(), "");
                    $txtAddress->get_Textbox();

                    //zipcode
                    $txtZipcode = new textbox("txtZipcode", $l->l('zipcode'),$company->get_zipcode(), "");
                    $txtZipcode->get_Textbox();
                    
                    //region
                    $t_region = new textbox("t_region", "Περιοχή RED", $company->get_region());
                    $t_region->get_Textbox();

                    //email
                    $txtEmail = new textbox("txtEmail", $l->l('email'),$company->get_email(), "");
                    $txtEmail->get_Textbox();
					
                    //haswebsite
                    $chk_haswebsite = new checkbox("chk_haswebsite", "ΕΧΕΙ WEBSITE", 
                            $company->get_haswebsite());
                    $chk_haswebsite->get_Checkbox();

                    //website
                    $txtWebsite = new textbox("txtWebsite", $l->l('website'),$company->get_website(), "");
                    $txtWebsite->get_Textbox();

                    //facebook
                    $txtFacebook = new textbox("txtFacebook", $l->l('facebook'),$company->get_facebook(), "");
                    $txtFacebook->get_Textbox();
                    
                    //instagram
                    $txtInstagram = new textbox("txtInstagram", "Instagram",$company->get_instagram(), "");
                    $txtInstagram->get_Textbox();

                    //twitter
                    $txtTwitter = new textbox("txtTwitter", $l->l('twitter'),$company->get_twitter(), "");
                    $txtTwitter->get_Textbox();
                    
                    //linked-in
                    $txtLinkedin = new textbox("txtLinkedin", $l->l('linked-in'),$company->get_LinkedIn(), "");
                    $txtLinkedin->get_Textbox();
                    
                    //Googleplus
                    $txtGoogleplus = new textbox("txtGoogleplus", "Google +",$company->get_googleplus(), "");
                    $txtGoogleplus->get_Textbox();
                    
                    //Pinterest
                    $txtPinterest = new textbox("txtPinterest", "Pinterest",$company->get_pinterest(), "");
                    $txtPinterest->get_Textbox();
                    
                    //echo "<div style=\"clear:both\"></div>";
                    
                    //echo "<div style=\"background-color:rgb(220,220,220);padding-top:10px\">";
                    $t_afm = new textbox("t_afm", $l->l("AFM"), $company->get_afm());
                    $t_afm->get_Textbox();

                    $t_doy = new textbox("t_doy", $l->l("DOY"), $company->get_doy());
                    $t_doy->get_Textbox();

                    $t_eponimia = new textbox("t_eponimia", $l->l("Eponymia"), $company->get_eponimia());
                    $t_eponimia->get_Textbox();

                    $cCompanyType = new comboBox("cCompanyType", $db1, 
                        "SELECT id, description FROM COMPANY_TYPES", 
                        "id","description",
                        $company->get_company_type(),$l->l("company_type"));
                    $cCompanyType->get_comboBox();
                    //echo "<div style=\"clear:both\"></div>";
                    //echo "</div>";
                    
                    
                    
                    $c_mydata_companytype = new comboBox("c_mydata_companytype", $db1,
                        "SELECT id, description FROM mydata_companytypes",
                        "id","description",
                        $company->get_mydata_companytype(), "Τύπος πελάτη (My data)");
    
                    $c_mydata_companytype->get_comboBox();

                    $t_old_tax_data = new textbox("t_old_tax_data", "Παλιά φορολογικά στοιχεία", $company->get_old_tax_data());
                    $t_old_tax_data->set_multiline();
                    $t_old_tax_data->set_disabled();
                    $t_old_tax_data->get_Textbox();

                    

                    ?> 
                    
                    <div class="col-12 col-sm-0" style="clear: both; height: 1em"></div>
                    
                    
                </div>
                
                <div class="col-6 col-md-12 mycol">
                    
                    <?php
                    
                    //short descr
                    $txtShortDescr = new textbox("txtShortDescr", $l->l('short-description'),$company->get_ShortDescription(), ""); 
                    $txtShortDescr->set_multiline();
                    $txtShortDescr->get_Textbox();
                    
                    echo '<div style="clear:both; height:1em"></div>';
                    
                    //full descr
                    //$txtFullDescr = new textbox("txtFullDescr", $l->l('full-description'),$company->get_ShortDescription(), ""); 
                    $txtFullDescr = new textbox("txtFullDescr", $l->l('full-description'),htmlspecialchars($company->get_FullDescription()), "");
                     
                    $txtFullDescr->set_multiline();
                    // $txtFullDescr->get_Textbox();
                    echo "<label>Πλήρης περιγραφή</label>";
                    echo $txtFullDescr->textboxSimple();
                    
                    echo '<div style="clear:both; height:1em"></div>';
                    
                    echo "<div style=\"opacity:0px;height:0px;overflow:hidden\">";
                    $t_vn_keywords = new textbox("t_vn_keywords", "KEYWORDS", $company->get_vn_keywords());
                    $t_vn_keywords->set_multiline();
                    $t_vn_keywords->get_Textbox();
                    echo "</div>";
                    
                    // echo "<div class=\"clear\"></div>";
                    // echo "<br/>ΥΠΟΚΑΤΑΣΤΗΜΑΤΑ";
                    // $t_sites = new arrayControl("t_sites", $company->get_sites(), 4);
                    // $t_sites->setColumnNames(array("ΔΝΣΗ", "ΤΗΛ", "Χ", "Υ"));
                    // $t_sites->setColumnWidths(array(30,20,15,15));
                    // $t_sites->getControl();
                    // $t_sites->getScriptGetArray();
                    
                    echo "<div class=\"clear\"></div>";
                    echo "<br/>ΩΡΕΣ ΛΕΙΤΟΥΡΓΙΑΣ";
                    
                    $str = <<<EOT
                            <select id="selectworkinghours">
                            <option value="0">...</option>
                            <option value="1">ΔΕ,ΤΕ 9-15/ΤΡ,ΠΕ,ΠΑ 9-14+17.30-21/ΣΑ 9-15</option>
                            <option value="2">ΔΕ-ΠΑ 9-21/ΣΑ 9-20</option>
                            <option value="3">ΔΕ-ΠΑ 9-17</option>
                            <option value="4">24-ΩΡΟ</option>
                            </select>
EOT;
                    echo $str;
                    
                    echo "<div id=\"workinghours\">";
                    $t_workinghours = new arrayControl("t_workinghours", $company->get_workinghours(), 7);
                    $t_workinghours->setColumnNames(array("ΔΕ", "ΤΡ", "ΤΕ", "ΠΕ", "ΠΑ", "ΣΑ", "ΚΥ"));
                    $t_workinghours->setColumnWidths(array(10,10,10,10,10,10,10));
                    $t_workinghours->getControl();
                    echo "</div>";
                    
                    $l_workingmonths = new selectList("l_workingmonths", "MONTHS", 
                            $company->get_workingmonths(), $db1);
                    $l_workingmonths->set_descrField("shortdescr");
                    $l_workingmonths->set_orderby("id");
                    echo "ΕΡΓΑΣΙΜΟΙ ΜΗΝΕΣ<br/><br/>";
                    echo $l_workingmonths->getSimpleList();
                    

                    ?>
                    
                    <div style="clear:both; height:30px"></div>
                    
                    <?php

                    if ($company->get_catalogueid()>0) {
                        //statistika site
                        $stats = "";
                        
                        echo '<div style="clear:both"></div>';
                        echo "<br/><h2 id=\"stats-heading\" style=\"padding-left:0px\">Στατιστικά επισκεψιμότητας</h2>";                        
                        echo "<div id=\"stats\">$stats</div><br/>";

                        echo "<div class=\"col-4\">";
                        $txtStatsStart = new textbox("txtStatsStart", "ΑΠΟ", "", "ΗΗ/ΜΜ/ΕΕΕΕ");
                        $txtStatsStart->set_format("DATE");
                        $txtStatsStart->set_locale($locale);
                        echo $txtStatsStart->textboxSimple();
                        echo "</div><div class=\"col-4\">";
                        $txtStatsStop = new textbox("txtStatsStop", "ΑΠΟ", "", "ΗΗ/ΜΜ/ΕΕΕΕ");
                        $txtStatsStop->set_format("DATE");
                        $txtStatsStop->set_locale($locale);
                        echo $txtStatsStop->textboxSimple();
                        echo "</div><div class=\"col-4\">";
                        $btnGetStats = new button("btnGetStats", "OK", "custom");
                        $btnGetStats->get_button_simple();
                        echo "</div>";
                    }
                    
                    ?>
                    
                    <div style="clear:both; height:30px"></div>
                    <h3>Tags </h3>
                    
                    <?php
                    if ($company->get_catalogueid()>0) {
                        $myOnlineId = $company->get_catalogueid();
                    ?>
                    <iframe src="https://partners.panelinios.gr/edit-tags.php?id=<?php echo $myOnlineId ?>&readonly=0" width="100%" height="330" style="margin-bottom: 20px"></iframe>
                    <?php } ?>
                    
                        
                        
                </div>
                
                
                <div style="clear: both"></div>

                <?php

                echo "<div class=\"clear\"></div>";
                echo "<br/>ΥΠΟΚΑΤΑΣΤΗΜΑΤΑ";
                $t_sites = new arrayControl("t_sites", $company->get_sites(), 6);
                $t_sites->setColumnNames(array("ΔΝΣΗ", "ΤΗΛ", "Χ", "Υ", "ΠΟΛΗ", "ΝΟΜΟΣ"));
                $t_sites->setColumnWidths(array(20,15,10,10,15,15));
                $t_sites->setColumnTypes(array(
                    "text",
                    "text",
                    "text",
                    "text",
                    array(
                        "type" => "combobox",
                        "conn" => $db1,
                        "sql" => "SELECT EP_CITIES.id, CONCAT(EP_CITIES.description, ' (', AREAS.description, ')') AS description FROM EP_CITIES INNER JOIN AREAS ON EP_CITIES.area_id = AREAS.id",
                        "table" => "EP_CITIES",
                        "idField" => "id",
                        "descField" => "description",
                        "where" => "id>0",
                        "orderBy" => "description"
                    ),
                    array(
                        "type" => "combobox",
                        "conn" => $db1,
                        "table" => "AREAS",
                        "idField" => "id",
                        "descField" => "description",
                        "where" => "parentid>0",
                        "orderBy" => "description"
                    )),

                );
                $t_sites->getControl();
                $t_sites->getScriptGetArray();
                echo "<div class=\"clear\"></div>";

                ?>
                
                <div id="products" class="col-6 col-md-12 mycol">
                    <?php
                    //$colorPackage = "#fff";
                    $colorPackage = func::vlookup("color", "PRODUCT_CATEGORIES", "id=1", $db1);
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorPackage\">ΚΑΤΑΧΩΡΗΣΗ</div>";
                    
                    $myPackage = $company->get_package();
                    $cPackage = new comboBox("cPackage", $db1, "SELECT id, CONCAT(description,'-',price,' €') as descprice FROM PACKAGES WHERE (active=1 AND basic=1) OR id=$myPackage ORDER BY iorder", "id","descprice", $company->get_package(),$l->l("package"));
                    $cPackage->get_comboBox();
                                        
                    $cDiscount = new comboBox("cDiscount", $db1, "SELECT id, CONCAT(discount,' %') as MyDiscount FROM DISCOUNTS WHERE active=1", "id","MyDiscount", $company->get_discount(),$l->l("discount"));
                    $cDiscount->get_comboBox();
                    
                    //price
                    $txtPrice = new textbox("txtPrice", $l->l('price'),$company->get_price(), "");
                    $txtPrice->set_format("CURRENCY");
                    $txtPrice->set_locale($locale);
                    $txtPrice->get_Textbox();
                                                            
                    
                    $txtCatalogueid = new textbox("txtCatalogueid", $l->l('catalogueid'),$pid, "");
                    if (($_SESSION['user_profile']==0)) {
                        $txtCatalogueid->set_disabled();                        
                    }
                    $txtCatalogueid->get_Textbox();
                    
                    $txtExpires = new textbox("txtExpires", $l->l('expires'),$company->get_expires(), "ΗΗ/ΜΜ/ΕΕΕΕ");
                    $txtExpires->set_format("DATE");
                    $txtExpires->set_locale($locale);
                    $txtExpires->get_Textbox();
                    
                    $txtUsername = new textbox("txtUsername", $l->l('username'),$company->get_username(), "");
                    if (($_SESSION['user_profile']==1)) {
                        $txtUsername->set_disabled();
                    } 
                    $txtUsername->get_Textbox();
                    
                    $txtPassword = new textbox("txtPassword", $l->l('password'),$company->get_password(), "");
                    if (($_SESSION['user_profile']==1)) {
                        $txtPassword->set_disabled();
                    } 
                    $txtPassword->get_Textbox();
                    
                    if (($_SESSION['user_profile']!=1)) {
                        echo "<div class=\"col-4\"></div>";
                        echo "<div class=\"col-8\">";
                        $btnCreatePassword = new button("btnCreatePassword", 
                                "Δημιουργία κωδικού", "");
                        $btnCreatePassword->set_method("CreatePassword($id)");
                        $btnCreatePassword->get_button_simple();
                        echo "</div>";
                    }
                    
                    ?>
                    
                    <div style="clear: both; height: 1em"></div>
                    
                </div>
                
                <div class="col-6 col-md-12 mycol">
                    <?php
                    
                    $colorDomain = func::vlookup("color", "PRODUCT_CATEGORIES", "id=2", $db1);
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorDomain\">DOMAIN</div>";
                    
                    $myPackage2 = $company->get_package2();
                    $cPackage2 = new comboBox("cPackage2", $db1, "SELECT id, CONCAT(description,'-',price,' €') as descprice FROM PACKAGES WHERE (active=1 AND product_category=2) OR id=$myPackage2 ORDER BY iorder", "id","descprice", $company->get_package2(),$l->l("package"));
                    $cPackage2->get_comboBox();
                                        
                    $cDiscount2 = new comboBox("cDiscount2", $db1, "SELECT id, CONCAT(discount,' %') as MyDiscount FROM DISCOUNTS WHERE active=1", "id","MyDiscount", $company->get_discount2(),$l->l("discount"));
                    $cDiscount2->get_comboBox();
                    
                    //price
                    $txtPrice2 = new textbox("txtPrice2", $l->l('price'),$company->get_price2(), "");
                    $txtPrice2->set_format("CURRENCY");
                    $txtPrice2->set_locale($locale);
                    $txtPrice2->get_Textbox();
                                        
                    $t_domain_name = new textbox("t_domain_name", "Domain name", 
                            $company->get_domain_name());
                    $t_domain_name->get_Textbox();
                    
                    $chk_domain = new checkbox("chk_domain", "Domain OK", $company->get_domain());
                    $chk_domain->get_Checkbox();
                    
                    $t_domain_expires = new textbox("t_domain_expires", "Λήξη domain", 
                            $company->get_domain_expires());
                    $t_domain_expires->set_format("DATE");
                    $t_domain_expires->set_locale($locale);
                    $t_domain_expires->get_Textbox();
                    
                    
                    ?>
                    
                    <div style="clear: both; height: 1em"></div>
                    
                </div>
                
                
                
                
                
                <div style="clear: both"></div>
                
                
                
                <!--FACEBOOK-->
                <div class="col-6 col-md-12 mycol">
                    
                    <?php
                    //product facebook // 4
                    $colorDomain = func::vlookup("color", "PRODUCT_CATEGORIES", "id=4", $db1);
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorDomain; color:#222\">FACEBOOK</div>";
                    
                    $fb_package = $company->get_fb_package()>0? $company->get_fb_package(): 0;
                    $c_fb_package = new comboBox("c_fb_package", $db1, 
                            "SELECT id, CONCAT(description,'-',price,' €') as descprice "
                            . "FROM PACKAGES WHERE (active=1 AND product_category=4) "
                            . "OR id=$fb_package ORDER BY iorder", 
                            "id","descprice", $company->get_fb_package(), "Πακέτο");
                    $c_fb_package->get_comboBox();
                                        
                    $c_fb_discount = new comboBox("c_fb_discount", $db1, 
                            "SELECT id, CONCAT(discount,' %') as MyDiscount "
                            . "FROM DISCOUNTS WHERE package=$fb_package AND active=1", 
                            "id","MyDiscount", $company->get_fb_discount(), "Έκπτωση");
                    $c_fb_discount->get_comboBox();                    
                    
                    
                    $c_fb_months = new comboBox("c_fb_months", $db1, 
                            "SELECT tvalue, description FROM PACKAGE_TIME "
                            . "WHERE product_category = 4 AND active=1 ORDER BY id", 
                            "tvalue","description", $company->get_fb_months(), "Διάρκεια (μήνες)");
                    $c_fb_months->get_comboBox();
                    
                    //price
                    $t_fb_price = new textbox("t_fb_price", $l->l('price'), 
                            $company->get_fb_price(), "");
                    $t_fb_price->set_format("CURRENCY");
                    $t_fb_price->get_Textbox();
                    
                    
                    $t_fb_expires = new textbox("t_fb_expires", "Λήξη Facebook", 
                            $company->get_fb_expires());
                    $t_fb_expires->set_format("DATE");
                    $t_fb_expires->get_Textbox(); 
                                        
                    $t_fb_page = new textbox("t_fb_page", "Σελίδα Facebook", 
                            $company->get_fb_page());
                    $t_fb_page->get_Textbox();
                    
                    $t_fb_comments = new textbox("t_fb_comments", "Σχόλια", 
                            $company->get_fb_comments());
                    $t_fb_comments->set_multiline();
                    $t_fb_comments->get_Textbox();
                    
                    $chk_fb_ok = new checkbox("chk_fb_ok", "Facebook OK", $company->get_fb_ok());
                    $chk_fb_ok->get_Checkbox();
                    
                    ?>
                    
                    <div style="clear: both; height: 1em"></div>
                    
                    
                </div>
                
                
                
<!--#########################################################################-->                
                <!--GOOGLE ADWORDS-->
<!--#########################################################################-->                
                <div class="col-6 col-md-12 mycol">
                    
                    <?php
                    //product google adwords // 4
                    $colorDomain = func::vlookup("color", "PRODUCT_CATEGORIES", "id=5", $db1);
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorDomain; color:#222\">Google Adwords</div>";
                    
                    $ga_package = $company->get_ga_package()>0? $company->get_ga_package(): 0;
                    $c_ga_package = new comboBox("c_ga_package", $db1, 
                            "SELECT id, CONCAT(description,'-',price,' €') as descprice "
                            . "FROM PACKAGES WHERE (active=1 AND product_category=5) "
                            . "OR id=$ga_package ORDER BY iorder", 
                            "id","descprice", $company->get_ga_package(), "Πακέτο");
                    $c_ga_package->get_comboBox();
                                        
                    $c_ga_discount = new comboBox("c_ga_discount", $db1, 
                            "SELECT id, CONCAT(discount,' %') as MyDiscount "
                            . "FROM DISCOUNTS WHERE package = $ga_package AND active=1", 
                            "id","MyDiscount", $company->get_ga_discount(), "Έκπτωση");
                    $c_ga_discount->get_comboBox();
                                        
                    $c_ga_months = new comboBox("c_ga_months", $db1, 
                            "SELECT tvalue, description FROM PACKAGE_TIME "
                            . "WHERE product_category = 5 AND active=1 ORDER BY id", 
                            "tvalue","description", $company->get_ga_months(), "Διάρκεια (μήνες)");
                    $c_ga_months->get_comboBox();
                    
                    //price
                    $t_ga_price = new textbox("t_ga_price", $l->l('price'), 
                            $company->get_ga_price(), "");
                    $t_ga_price->set_format("CURRENCY");
                    $t_ga_price->get_Textbox();
                    
                    
                    $t_ga_expires = new textbox("t_ga_expires", "Λήξη Google adwords", 
                            $company->get_ga_expires());
                    $t_ga_expires->set_format("DATE");
                    $t_ga_expires->get_Textbox(); 
                                        
                    $t_ga_page = new textbox("t_ga_page", "Ιστοσελίδα", 
                            $company->get_ga_page());
                    $t_ga_page->get_Textbox();
                    
                    $t_ga_keywords = new textbox("t_ga_keywords", "Keywords", 
                            $company->get_ga_keywords());
                    $t_ga_keywords->get_Textbox();
                    
                    $t_ga_comments = new textbox("t_ga_comments", "Σχόλια", 
                            $company->get_ga_comments());
                    $t_ga_comments->set_multiline();
                    $t_ga_comments->get_Textbox();
                    
                    $chk_ga_ok = new checkbox("chk_ga_ok", "Google ads OK", $company->get_ga_ok());
                    $chk_ga_ok->get_Checkbox();
                    
                    ?>
                    
                    <div style="clear: both; height: 1em"></div>
                    
                    
                </div>
                
                
                
                <div style="clear: both"></div>
                
                
                
<!--#########################################################################-->                
                <!--SERVICES/APPOINTMENT-->
<!--#########################################################################-->                    
                    <div class="col-6 col-md-12 mycol invisible">
                        
                        <?php
                        $prodCatSrv = new PRODUCT_CATEGORIES($db1, 7);
                        $colorSrv = $prodCatSrv->get_color();
                        $descrSrv = $prodCatSrv->get_description();
                        echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorSrv; color:#222\">$descrSrv</div>";
                        
                        $t_srv_app = new checkbox("t_srv_app", "Ραντεβού", $company->get_srv_app());
                        $t_srv_app->get_Checkbox();
                        
                        $t_srv_services = new selectList("t_srv_services", "SRV_SERVICES", $company->get_srv_services(), $db1);
                        $t_srv_services->set_label("Είδος Ασφάλειας");
                        $t_srv_services->getList();
                        
                        $t_srv_date = new textbox("t_srv_date", "Ημερ.", $company->get_srv_date());
                        $t_srv_date->set_format("DATE");
                        $t_srv_date->set_locale($locale);
                        $t_srv_date->get_Textbox();
                        
                        $t_srv_field1 = new comboBox("t_srv_field1", $db1, 
                                "SELECT * FROM TIMES", 
                                "id", "description", 
                                $company->get_srv_field1(), "Ώρα");
                        $t_srv_field1->get_comboBox();
                        
                        //comments
                        
                        $t_srv_status = new comboBox("t_srv_status", $db1, 
                                "SELECT * FROM SRV_APPSTATUS", 
                                "id", "description", 
                                $company->get_srv_status(), "Status");
                        $t_srv_status->get_comboBox();
                        
                        $t_srv_salesman = new comboBox("t_srv_salesman", $db1, 
                                "SELECT * FROM USERS", 
                                "id", "fullname", 
                                $company->get_srv_salesman(), "Πωλητής");
                        $t_srv_salesman->get_comboBox();
                        
                        $t_srv_result = new comboBox("t_srv_result", $db1, 
                                "SELECT * FROM SRV_RESULTS", 
                                "id", "description", 
                                $company->get_srv_result(), "Αποτέλεσμα");
                        $t_srv_result->get_comboBox();                        
                        
                        ?>


                    </div>

                    <div class="col-6 col-md-12 mycol invisible">
                            <?php
                            echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff; border-bottom:2px solid $colorSrv; color:#222\">$descrSrv</div>";

                            $t_srv_comments = new textbox("t_srv_comments", "Σχόλια", $company->get_srv_comments());
                            $t_srv_comments->set_multiline();
                            $t_srv_comments->get_Textbox();
                            ?>
                        </div>
                
                
                
                

                <div style="clear: both"></div>
                
                
                
                
                
                <div id="courier" class="col-6 col-md-12 mycol">
                    <?php
                    
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff\">COURIER</div>";
                    
                    //COURIER
                    $c_courier = new comboBox("c_courier", $db1, 
                            "SELECT id, description FROM COURIER WHERE active=1", 
                            "id","description",
                            $company->get_courier(),"Courier");                                   
                    $c_courier->get_comboBox();
                    
                    //DeliveryDate                    
                    $txtDeliveryDate = new textbox("txtDeliveryDate", $l->l('delivery-date'), $company->get_DeliveryDate(), "ΗΗ/ΜΜ/ΕΕΕΕ");
                    $txtDeliveryDate->set_format("DATE");
                    $txtDeliveryDate->set_locale($locale);
                    $txtDeliveryDate->get_Textbox();
                    
                    //delivery time
                    $txtDeliveryTime = new textbox("txtDeliveryTime", $l->l('delivery-time'),$company->get_DeliveryTime(), "");                 
                    $txtDeliveryTime->get_Textbox();
                    
                    //delivery notes
                    $txtDeliveryNotes = new textbox("txtDeliveryNotes", $l->l('delivery-notes'),$company->get_DeliveryNotes(), ""); 
                    $txtDeliveryNotes->set_multiline();
                    $txtDeliveryNotes->get_Textbox();
                    
                    
                    //COURIER ADDRESS
                    $t_courier_address = new textbox("t_courier_address", "ΔΙΕΥΘΥΝΣΗ", 
                            $company->get_courier_address(), ""); 
                    $t_courier_address->get_Textbox();
                    
                    //COURIER ZIPCODE
                    $t_courier_zipcode = new textbox("t_courier_zipcode", "ΤΚ", 
                            $company->get_courier_zipcode(), ""); 
                    $t_courier_zipcode->get_Textbox();
                    
                    //COURIER CITY
                    $c_courier_city = new comboBox("c_courier_city", $db1,
                            "SELECT id, description FROM EP_CITIES", 
                            "id", "description", 
                            $company->get_courier_city(), "ΠΟΛΗ"); 
                    $c_courier_city->get_comboBox();
                    
                    //COURIER region
                    $t_courier_region = new textbox("t_courier_region", "Περιοχή RED", 
                            $company->get_courier_region());
                    $t_courier_region->get_Textbox();
                    
                    
                    //COURIER NOTES // AREA
                    /*$t_courier_notes = new textbox("t_courier_notes", "ΠΕΡΙΟΧΗ", 
                            $company->get_courier_notes(), ""); 
                    $t_courier_notes->get_Textbox();*/
                    
                    
                    //COURIER PHONE
                    $defaultPhone = fn::noSpaces($company->get_mobilephone()) . " " . fn::noSpaces($company->get_phone1()) . " " . fn::noSpaces($company->get_phone2());
                    $t_courier_phone = new textbox("t_courier_phone", "ΤΗΛ", 
                            $company->get_courier_phone(), ""); 
                    $t_courier_phone->get_Textbox();
                    
                    
                    
                    
                    //voucherid
                    $t_voucherid = new textbox("t_voucherid", $l->l('Voucher'),$company->get_voucherid(), $lg->l('auto'));
                    $t_voucherid->set_disabled();
                    $t_voucherid->get_Textbox();
                    
                    
                    ?>
                    
                    <div style="clear:both; height:1em"></div>
                    
                </div>
                
                <div id="other" class="col-6 col-md-12 mycol">
                    
                    <?php
                    
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff\">ΑΛΛΑ ΣΤΟΙΧΕΙΑ</div>";
                    
                    $cUserdataentry = new comboBox("cUserdataentry", $db1, 
                            "SELECT id, fullname FROM USERS", 
                            "id","fullname",
                            $company->get_userdataentry(),$l->l("user-dataentry"));
                    if (($_SESSION['user_profile']==1)) {
                        $cUserdataentry->set_disabled();
                    }                     
                    $cUserdataentry->get_comboBox();
                    
                    
                    //USER RIGHTS!!!
                    $cStatus = new comboBox("cStatus", $db1, 
                            "SELECT id, description FROM STATUS2 ", 
                            "id","description",
                            $company->get_status(),$l->l("status"));
                    
                    if (($_SESSION['user_profile']==1)) {
                        $cStatus->set_disabled();
                    }                    
                    $cStatus->get_comboBox();
                    
                    
                    $chk_onlinestatus = new checkbox("chk_onlinestatus", "ON-LINE", $company->get_onlinestatus());
                    if (($_SESSION['user_profile']==1)) {
                        $chk_onlinestatus->set_disabled();
                    }
                    $chk_onlinestatus->get_Checkbox();
                    
                    $t_onlinedatetime = new textbox("t_onlinedatetime", "Ημερ. On line", $company->get_onlinedatetime());
                    if (($_SESSION['user_profile']==1)) {
                        $t_onlinedatetime->set_disabled();
                    }
                    $t_onlinedatetime->set_format("DATE");
                    $t_onlinedatetime->set_locale($locale);
                    $t_onlinedatetime->get_Textbox();
                    
                    
                    if ($_SESSION['user_profile']==3) {
                        $chk_invoiceprinted = new checkbox("chk_invoiceprinted", $l->l("invoiceprinted"), $company->get_invoiceprinted());
                        $chk_invoiceprinted->get_Checkbox();
                    }
                    
                    
                    
                    ?>
                    
                    
                </div>              
                
                
                <div style="clear: both"></div>
                
                               
                
                <div id="messages" class="col-6 col-md-12 mycol">
                    
                    <!--MESSAGES-->

                    <?php
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff\">ΜΗΝΥΜΑΤΑ</div>";
                    ?>
                    
                    <iframe src="messages.php?showlist=0&companyid=<?php echo $id; ?>&popupurl=0&receiver=0<?php echo $noCacheHash; ?>" width="100%" height="250" frameborder="0"></iframe>
                    
                    <?php
                
                    $txtComment = new textbox("txtComment", $l->l('comment'),$company->get_comment(), ""); 
                    $txtComment->set_multiline();
                    $txtComment->set_disabled();
                    echo $txtComment->textboxSimple();

                    ?>
                    
                
                    <div style="clear: both; height: 1em"></div>
                
                    <!--MESSAGES-->
                    
                </div>
        
                <div class="col-6 col-md-12 mycol">

                    <?php	
                        
                    echo "<div style=\"clear:both; width:100%; padding:10px 1%; font-weight:bold; margin-bottom:10px; background-color:#fff\">&nbsp;</div>";
                    
                    $strSql = "SELECT * FROM USERS";
                    $rsUsers = $db1->getRS($strSql);
                    
                    for ($i=0;$i<count($messages);$i++) {
                        $curmessage = new MESSAGES($db1,$messages[$i]['id'],$messages);
                        $sendername = func::vlookupRS("fullname", $rsUsers, 
                            $curmessage->get_sender());
                        $receivername = func::vlookupRS("fullname", $rsUsers, 
                            $curmessage->get_receiver());
                    ?>

                    <div class="message" style="margin-bottom:10px">
                        <div style="margin-bottom:5px">
                            <strong><?php echo $sendername . " -> " . $receivername; ?></strong>
                        </div>
                        <div style="margin-bottom:5px"><?php echo strip_tags($curmessage->get_message()); ?></div>
                        <div style="padding-bottom:5px; border-bottom: 1px dashed rgb(200,200,200)"><?php echo $curmessage->get_mdatetime(); ?></div>
                    </div>

                    <?php } ?>


                </div>
        
        
                <div style="clear: both"></div>
                
                <?php
                    //$hr = new hr(); $hr->get_hr();  
                ?>
        
                
                <div id="status" class="col-12 mycol">
                    
                    <?php
                    
                    //$hr = new hr(); $hr->get_hr();
                    
                    /*NEW*/
                    echo "<div style=\"clear:both;height:20px\"></div>";
                    echo "<h2 style=\"padding-left:0px\">STATUS ΕΠΙΚΟΙΝΩΝΙΑΣ</h2>";
                    $sql = "SELECT * FROM COMPANIES_STATUS WHERE companyid=$id";
                    $rs = $db1->getRS($sql);
                    
                    $strSql = "SELECT * FROM STATUS";
                    $rsStatus = $db1->getRS($strSql);
                    $strSql = "SELECT * FROM STATUS2";
                    $rsStatus2 = $db1->getRS($strSql);
                    $strSql = "SELECT * FROM PRODUCT_CATEGORIES";
                    $rsProdCat = $db1->getRS($strSql);
                    $strSql = "SELECT * FROM TIMES";
                    $rsTimes = $db1->getRS($strSql);
                    
                    for ($i = 0; $i < count($rs); $i++) {
                        $rs[$i]['status'] = func::vlookupRS("description", $rsStatus, 
                                $rs[$i]['status']);
                        $rs[$i]['productcategory'] = func::vlookupRS("description", $rsProdCat, 
                                $rs[$i]['productcategory']);
                        $rs[$i]['userid'] = func::vlookupRS("fullname", $rsUsers, 
                                $rs[$i]['userid']);
                        $rs[$i]['recalltime'] = func::vlookupRS("description", $rsTimes, 
                                $rs[$i]['recalltime']);
                    }
                    $datagridStatus = new datagrid("datagridStatus", $db1, "", 
                            array("productcategory", "status", "userid", "csdatetime", "recalldate", "recalltime"), 
                            array("Κατηγορία προϊόντος", "Status", "Χρήστης","Ημερ.", "Recall", "Time"));
                    $datagridStatus->set_rs($rs);
                    $datagridStatus->set_colsFormat(array("", "", "", "DATE","DATE", ""));
                    if (($_SESSION['user_profile']==3)) {
                        $datagridStatus->set_edit("editCompanyStatus.php");
                        $datagridStatus->set_del("delCompanyStatus.php");
                    }
                    $datagridStatus->get_datagrid();
                     /*NEW*/
                    
                    $hr = new hr(); $hr->get_hr();
                    
                    //SHOW STATUS HISTORY.....
                    
                    echo '<h2 id="history" style="margin:0px;padding:0px">'.$l->l("history").'</h2><br/>';
                    
                    if ($id!=0) {                    
                        
                        $sql = "SELECT *, '' AS color, '' AS mydate, '' AS mytime FROM ACTIONS WHERE company=? ORDER BY id DESC";
                        $rs = $db1->getRS($sql, array($id));
                        $rsActions = array();
                        $rsPCat = $db1->getRS("SELECT * FROM PRODUCT_CATEGORIES");
                        for ($i = 0; $i < count($rs); $i++) {
                            $productCategories = str_replace(array("[", "]"), "", $rs[$i]['product_categories']);
                            $productCategoriesIds = explode(",", $productCategories);
                            for ($k = 0; $k < count($productCategoriesIds); $k++) {
                                $pcat = new PRODUCT_CATEGORIES($db1, $productCategoriesIds[$k], $rsPCat);
                                $color = $pcat->get_color();
                                $rs[$i]['color'] .= "<div style=\"height:10px;width:20px;background-color:$color\"></div>";                            
                            }
                            $myDate = new DateTime($rs[$i]['atimestamp']);
                            $rs[$i]['mydate'] = $myDate->format("d/m/Y");
                            $rs[$i]['mytime'] = $myDate->format("H:i");
                            
                            $rsActions[$i]['id'] = $rs[$i]['id'];
                            $rsActions[$i]['color'] = $rs[$i]['color'];
                            $rsActions[$i]['product_categories'] = 
                                    $rs[$i]['product_categories'];
                            $rsActions[$i]['status2'] = $rs[$i]['status2'];
                            $rsActions[$i]['comment'] = $rs[$i]['comment'];
                            $rsActions[$i]['mydate'] = $rs[$i]['mydate'];
                            $rsActions[$i]['mytime'] = $rs[$i]['mytime'];
                            $rsActions[$i]['user'] = $rs[$i]['user'];
                            
                            if ($rs[$i]['status2']==4 /*arnitikos*/
                                    && $_SESSION['user_profile']==1
                                    && $hideHistory) {
                                break;
                            }
                            
                            
                        }
                        
                        $gridHistory = new datagrid("gridHistory", $db1, "", 
                                array("color","id","product_categories", "status2", "comment", "mydate", "mytime", "user"), 
                                    
                                array("", "ID","Κ.Π.", "Ενέργεια", "Σχόλια", "Ημερ.", "Ώρα", "Χρήστης"));
                        
                        $gridHistory->set_rs($rsActions);
                        
                                                
                        //$gridHistory->col_vlookup("status1", "status1", "STATUS", "description", $db1);
                        $gridHistory->col_vlookupRS("status2", "status2", $rsStatus, "code");
                        $gridHistory->col_vlookupRS("user", "user", $rsUsers, "fullname"); 
                        $gridHistory->GetItemsFromIdsRS("product_categories", $rsProdCat, "shortdescription");
                        
                        if ($_SESSION['user_profile']==3) {
                            $gridHistory->set_del("historydel.php", "DEL");
                            $gridHistory->set_edit("editHistory.php", "EDIT");
                        }
                        echo "<div style=\"width:100%; max-height:600px; overflow:scroll\">";
                        $gridHistory->get_datagrid();

                        echo "</div>";

                    }

                    
                    
                    ?>
                    
                        
                    
                    
                </div>
                
                <div style="clear: both"></div>
                
                <?php
                    //$hr = new hr(); $hr->get_hr();  
                ?>
                
                <div class="col-12 mycol">
                    <div style="padding-right: 0px;">
                    <?php
                    
                    $hr = new hr(); $hr->get_hr();
                                                       
                    echo '<h2 style="margin:0px;padding:0px;position:relative"><span id="xreoseis-title">ΧΡΕΩΣΕΙΣ</span> <span id="toggle-xreoseis" style="position:absolute;top:50px;right:5px;font-size:15px;cursor:pointer;padding:5px;background-color:#cfc;border-radius:5px;">Epagelmatias ></span></h2><br/>';
                    
                    $strSql = "SELECT * FROM PACKAGES";
                    $rsPackages = $db1->getRS($strSql);
                    
                    $sql = "SELECT *, amount + vat AS total FROM TRANSACTIONS WHERE company=? AND transactiontype=1";
                    $rs = $db1->getRS($sql, array($id));
                    for ($i=0;$i<count($rs);$i++) {
                        if ($rs[$i]['package']!=0 && $rs[$i]['package']!="") {
                            $rs[$i]['description'] = func::vlookupRS("description", 
                                $rsPackages, $rs[$i]['package']);
                        }
                    }
                    
                    $strSql = "SELECT * FROM TRANSACTION_STATUS";
                    $rsTransStatus = $db1->getRS($strSql);
                    
                    $dg = new datagrid("dg_xrewseis", $db1, "", 
                            array("id","tdatetime", "description","amount","vat","total", "seller","status"), 
                            array("ID","ΗΜΕΡ.","ΠΕΡΙΓΡΑΦΗ","ΠΟΣΟ","ΦΠΑ","ΣΥΝΟΛΟ", "ΠΩΛΗΤΗΣ","STATUS"), 
                            $ltoken);
                    $dg->set_rs($rs);
                    $dg->col_vlookupRS("seller", "seller", $rsUsers, "fullname");
                    $dg->col_vlookupRS("status", "status", $rsTransStatus, "description");
                    $dg->set_colsFormat(array("","DATE","","CURRENCY","CURRENCY","CURRENCY","","",""));
                    if (($_SESSION['user_profile']>1)) {
                        $dg->set_edit("editTransaction.php", "EDIT");
                        $dg->set_del("delTransaction.php", "DEL");
                    }

                    echo "<div id=\"xreoseis-panelinios\">";
                    $dg->get_datagrid();
                    echo "</div>";

                    include "_xreoseis_epagelmatias.php";
                    
                    ?>
                    <br/><br/>
                    <a class="fancybox button" href="editTransaction.php?id=0&company=<?php echo $id; ?>&type=1">Προσθήκη χρέωσης</a>
                    <br/><br/>
                    
                    </div>
                    
                    
                </div>
                
                <div class="col-12 mycol">
                    <?php $hr = new hr(); $hr->get_hr(); ?>
                </div>
                
                
                <div class="col-6 mycol">
                    <div style="padding-right: 10px;">
                        <?php
                        //$hr = new hr(); $hr->get_hr();                                      
                        echo '<h2 style="margin:0px;padding:0px">ΕΙΣΠΡΑΞΕΙΣ</h2><br/>';

                        $sql = "SELECT * FROM TRANSACTIONS WHERE company = $id AND transactiontype = 2";
                        //$rs = $db1->getRS($sql, array($id));
                        $rs = $db1->getRS($sql);
                        for ($i=0;$i<count($rs);$i++) {
                            if ($rs[$i]['package']!=0 && $rs[$i]['package']!="") {
                                $rs[$i]['description'] = func::vlookupRS("description", 
                                    $rsPackages, $rs[$i]['package']);
                            }
                        }

                        $dg = new datagrid("dg_xrewseis", $db1, $sql, 
                                array("id","tdatetime","amount"), 
                                array("ID","ΗΜΕΡ.","ΠΟΣΟ"), 
                                $ltoken);
                        $dg->set_rs($rs);                    
                        $dg->set_colsFormat(array("","DATE","CURRENCY",""));
                        if (($_SESSION['user_profile']>1)) {
                            $dg->set_edit("editTransaction.php", "EDIT");
                            $dg->set_del("delTransaction.php", "DEL");
                        }
                        $dg->get_datagrid();

                        ?>
                        
                        <?php if (($_SESSION['user_profile']>1)) { ?>
                        <br/><br/>
                        <a class="fancybox button" href="editTransaction.php?id=0&company=<?php echo $id; ?>&type=2">Προσθήκη είσπραξης</a>
                        <br/><br/>
                        <?php } ?>
                        
                    </div>
                    
                    
                </div>
                
                <div class="col-6 mycol">
                    <?php
                    //$hr = new hr(); $hr->get_hr();                                      
                    echo '<h2 style="margin:0px;padding:0px">ΠΙΣΤΩΣΕΙΣ</h2><br/>';
                    
                    $sql = "SELECT * FROM TRANSACTIONS WHERE transactiontype = 3 AND company = $id ";
                    $rs = $db1->getRS($sql);
                    for ($i=0;$i<count($rs);$i++) {
                        if ($rs[$i]['package']!=0 && $rs[$i]['package']!="") {
                            $rs[$i]['description'] = func::vlookupRS("description", 
                                $rsPackages, $rs[$i]['package']);
                        }
                    }
                    
                    $dg = new datagrid("dg_xrewseis", $db1, $sql, 
                            array("id","tdatetime","amount"), 
                            array("ID","ΗΜΕΡ.","ΠΟΣΟ"), 
                            $ltoken);
                    $dg->set_rs($rs);                    
                    $dg->set_colsFormat(array("","DATE","CURRENCY",""));
                    if (($_SESSION['user_profile']>1)) {
                        $dg->set_edit("editTransaction.php", "EDIT");
                        $dg->set_del("delTransaction.php", "DEL");
                    }
                    $dg->get_datagrid();
                    
                    ?>
                    
                    <?php if (($_SESSION['user_profile']>1)) { ?>
                    <br/><br/>
                    <a class="fancybox button" href="editTransaction.php?id=0&company=<?php echo $id; ?>&type=3">Προσθήκη πίστωσης</a>
                    <br/><br/>
                    <?php } ?>
                    
                    
                </div>
                
                
                <div style="clear: both"></div>
                
                <div class="col-12 mycol">
                    <?php $hr = new hr(); $hr->get_hr(); ?>
                </div>
                
                
                <div class="col-6 mycol">
                    <div style="padding-right: 10px;">
                        <?php
                        echo '<h2 id="invoices" style="margin:0px;padding:0px">ΤΙΜΟΛΟΓΙΑ</h2><br/>';

                        $sql = "SELECT * FROM INVOICEHEADERS WHERE company=$id";

                        $dg = new datagrid("dg_invoices", $db1, $sql, 
                                array("id","icode","idate","amount", "vat", "status" ), 
                                array("ID", "ΚΩΔ.", "ΗΜΕΡ.","ΠΟΣΟ", "ΦΠΑ", "ST."), 
                                $ltoken);
                        
                        $strSql = "SELECT * FROM INVOICE_STATUS";
                        $rsInvoiceStatus = $db1->getRS($strSql);
                        
                        $dg->set_colsFormat(array("","", "DATE","CURRENCY","CURRENCY", ""));
                        $dg->col_vlookupRS("status", "status", $rsInvoiceStatus, "shortdescr");
                        if (($_SESSION['user_profile']>1)) {
                            $dg->set_edit("editInvoice3.php", "EDIT");
                            $dg->set_del("delInvoice2.php", "DEL");
                        }
                        $dg->get_datagrid();

                        ?>
                        
                        <?php if (($_SESSION['user_profile']>1)) { ?>
                        <br/><br/>
                        <a class="fancybox button" href="editInvoice3.php?id=0&company=<?php echo $id; ?>">Προσθήκη τιμολογίου</a>
                        <br/><br/>
                        <?php } ?>
                        
                    </div>
                
                </div>
                
                <div class="col-6 mycol">
                    <?php
                    echo '<h2 style="margin:0px;padding:0px">VOUCHERS</h2><br/>';
                    
                    $sql = "SELECT * FROM VOUCHERS WHERE customer=?";
                    $rsV = $db1->getRS($sql, array($id));
                    
                    $strSql = "SELECT * FROM COURIER";
                    $rsCourier = $db1->getRS($strSql);
                    
                    for ($i = 0; $i < count($rsV); $i++) {
                        $rsV[$i]['courier'] = func::vlookupRS("description", $rsCourier, $rsV[$i]['courier']);
                        $vcode2 = $rsV[$i]['vcode2'];
                        if ($vcode2!="") {
                            $rsV[$i]['courier'] = "<div class=\"hl\"><a target=\"_blank\"  href=\"https://redcourier.gr/track-and-trace/?voucher=$vcode2\">".$rsV[$i]['courier']."</a></div>";
                        }
                            
                    }


                    //echo $sql;
                    $dg = new datagrid("dg_vouchers", $db1, "", 
                            array("id","vcode","vdate","amount", "courier", "deliverydate" ), 
                            array("ID", "ΚΩΔ.", "ΗΜΕΡ.","ΠΟΣΟ", "COURIER", "ΗΜ.ΠΑΡ."), 
                            $ltoken);
                    $dg->set_rs($rsV);
                    
                    $strSql = "SELECT * FROM COURIER";
                    $rsCourier = $db1->getRS($strSql);
                    
                    //$dg->col_vlookupRS("courier", "courier", $rsCourier, "description") ;                  
                    $dg->set_colsFormat(array("","", "DATE","CURRENCY","","DATE"));
                    if (($_SESSION['user_profile']>1)) {
                        $dg->set_edit("editVoucher.php", "EDIT");
                        $dg->set_del("delVoucher.php", "DEL");
                    }
                    $dg->get_datagrid();
                    
                    ?>
                    
                    <?php if (($_SESSION['user_profile']>1)) { ?>
                    <br/><br/>
                    <a class="fancybox button" href="editVoucher.php?id=0&company=<?php echo $id; ?>">Προσθήκη voucher</a>
                    <br/><br/>
                    <?php } ?>
                
                </div>
                
                <div style="clear: both; height: 3em"></div>
                
                
            
            
            </form>


        </div>
        
        <div style="position: fixed; bottom:0px; right:0px; padding: 10px 10px 20px;">
            
            <a class="button" href="#top">TOP</a>
            <a class="button" href="#products">ΠΡΟΪΟΝΤΑ</a>&nbsp;
            <a class="button" href="#courier">COURIER</a>&nbsp;
            <a class="button" href="#other">OTHER</a>&nbsp;
            <a class="button" href="#messages">ΜΗΝΥΜΑΤΑ</a>&nbsp;
            <a class="button" href="#status">STATUS</a>&nbsp;            
            <a class="button" href="#history">ΙΣΤΟΡΙΚΟ</a>&nbsp;
            <a class="button" href="#dg_xrewseis">ΠΑΡΑΣΤΑΤΙΚΑ</a>&nbsp;
            <?php
            if ((!$myStatusRecall) 
                || ($_SESSION['user_id'] == $myCommUser) 
                || ($_SESSION['user_profile']>1)) {
            ?>
            
            <a id="save2" class="button" href="#">SAVE</a>
            <?php } ?>
            <a class="button" href="editcompany.php?id=<?php echo $id; ?>">REFRESH</a>
            <?php if ($_SESSION['user_profile']>1) { ?>
            <a class="button fancybox" href="getCompanyChanges.php?companyid=<?php echo $id; ?>">View Changes</a>
            <?php } ?>
        </div>
        <div class="new-tax-data-container" style="justify-content: center;align-items:center;position:fixed;top:0px;left:0px;width:100%;height:100vh;display:none;background-color:#00000044;z-index:9999">
            <div style="max-width:800px; background-color:#fff;padding:50px;border:1px solid #ccc;display:grid;grid-template-columns:1fr 1fr;gap:20px;">                
            
                <div>
                    <h2>Παλιά στοιχεία</h2>
                    <br/>
                    <p>ΑΦΜ <?php echo $company->get_afm(); ?></p>
                    <br/>
                    <p>Επωνυμία <?php echo $company->get_eponimia(); ?></p>
                    <br/>
                    <p>Διεύθυνση <?php echo $company->get_address(); ?></p>
                    <br/>
                    <p>ΤΚ <?php echo $company->get_zipcode(); ?></p>
                    <br/>
                    <p>ΔΟΥ <?php echo $company->get_doy(); ?></p>
                </div>

                <div>
                    <h2>Νέα στοιχεία</h2>
                    <label>ΑΦΜ</label>
                    <input type="text" id="new_afm" />

                    <label>Επωνυμία</label>
                    <input type="text" id="new_eponimia" />

                    <!-- <label>Επάγγελμα</label>
                    <input type="text" id="new_epagelma" /> -->

                    <label>Διεύθυνση</label>
                    <input type="text" id="new_address" />

                    <label>ΤΚ</label>
                    <input type="text" id="new_zipcode" />

                    <label>ΔΟΥ</label>
                    <input type="text" id="new_doy" />

                    <input type="button" value="Αποθήκευση" class="new-tax-data-save-btn" />
                </div>               


            </div>

            <div class="close-new-tax-data" style="position:absolute;top:100px;right:30px;background-color:#000;color:#fff;padding:10px 20px;cursor:pointer;">Κλείσιμο</div>
        </div>

        
        
        <script>
        
        $(function() {
            
            $("#btn-voucher-delivered").click(function() {
                var voucherid = $(this).data("voucher");
                $.post("_voucherChangeStatus.php", 
                    {voucherid: voucherid,
                    courierstatus: 5}, 
                    function(data) {
                        $("#btn-voucher-delivered").hide();
                        $("#voucher-status").html("Παραδόθηκε");
                        
                    }
                );
            }); 
           
            $("#btn-voucher-comment-ok").click(function() {
                var voucherid = $(this).data("voucher");
                var companyid = $(this).data("company");
                var comment = $("#t_vouchercomm").val();
                var followup_date = $("#t_voucher_followup_date").val();
                var followup_time = $("#t_voucher_followup_time").val();
                
                if (comment.trim()!=="") {
                    $.post("_setCompanyCommAction.php",
                        {
                            voucherid:voucherid,
                            companyid:companyid,
                            comment:comment
                        },
                        function(data) {
                            if (data==="ok") {
                                $("#t_vouchercomm").val("");
                                $("#t_vouchercomm").css("background-color","$fff");
                                $.post("_getVoucherCommHistory.php",
                                {voucherid: voucherid}, 
                                function(dataHistory) {
                                    $("#voucher-comm-history").html(dataHistory);
                                });
                            }
                        }
                        );
                }                
                //second post for followup....
                if (followup_date!=="") {
                    $.post("_setVoucherFollowUp.php",
                    {
                        voucherid:voucherid,
                        followup_date:followup_date,
                        followup_time:followup_time
                    },
                    function(data) {
                        alert(data);
                    });
                }
            });
            
            
            $("#btn-second-note-for-courier-ok").click(function() {
                var voucherid = $(this).data("voucher");
                var comment = $("#t_second_note_for_courier").val();
                
                $.post("_setVoucher2ndCourierNote.php",
                    {
                        voucherid:voucherid,
                        comment:comment
                    },
                function(data) {
                    $("#t_second_note_for_courier").css("background-color","rgb(200,250,200)");
                });
            });
            
            
            $("#t_second_note_for_courier").change(function() {
                $(this).css("background-color","rgb(250,200,200)");
            });
            $("#t_vouchercomm").change(function() {
                $(this).css("background-color","rgb(250,200,200)");
            });
            
            $(".note-for-courier").click(function() {
                var comment = $(this).html();
                $("#t_second_note_for_courier").val(comment);
                $("#t_second_note_for_courier").css("background-color","rgb(250,200,200)");
            });

            $(".note-for-courier-email").click(function() {
                var comment = $(this).html();
                var prev_comment = $("#t_courier_email_text").val();
                $("#t_courier_email_text").val(prev_comment + '\n' + comment);
                $("#t_courier_email_text").css("background-color","rgb(250,200,200)");
            });
            
            
            
        });
        
        </script>

        
        <script>
        
        
        $(function() {
            
            $(".red-accept-region").click(function() {
                
                var area = $(this).data("area");
                var zipcode = $(this).data("zipcode");
                
                $("#t_region").val(area);
                $("#txtZipcode").val(zipcode);
                
                alert("ΠΑΡΑΚΑΛΩ ΑΠΟΘΗΚΕΥΣΤΕ ΤΗΝ ΚΑΡΤΕΛΑ");
                
            });
            
            $(".red-accept-courier-region").click(function() {
                
                var area = $(this).data("area");
                var zipcode = $(this).data("zipcode");
                
                $("#t_courier_region").val(area);
                $("#t_courier_zipcode").val(zipcode);
                
                alert("ΠΑΡΑΚΑΛΩ ΑΠΟΘΗΚΕΥΣΤΕ ΤΗΝ ΚΑΡΤΕΛΑ");
                
            });
            
        });
        
        
        
        //smooth scrolling
        $(function() {
            $('a[href^="#"]').on('click', function(event) {	
                var target = $( $(this).attr('href') );
                var time = 1000;
                if (target.offset().top == 0) {
                    time = 2000;
                }
        
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
        
            });
        
        });
        
        
        $(function() {
            
            $("#left-menu-more-btn").click(function() {
                $("#left-menu-more").show();
                $("#left-menu").css("width","410px");
                $("#form-container").css("margin-left", "410px");
                $("#left-menu-less-btn").show();
                $(this).hide();
            });
            
            $("#left-menu-less-btn").click(function() {
                $("#left-menu-more").hide();
                $("#left-menu").css("width","220px");
                $("#form-container").css("margin-left", "220px");
                $("#left-menu-more-btn").show();
                $(this).hide();
            });
            
            $('#txtDeliveryNotes').keyup(function() {
                var tLength = $(this).val().length;
                if (tLength>105) {
                    alert("Προσοχή! Μέγιστος αριθμός χαρακτήρων = 105!");
                }
            });

            $(".change-tax-data").click(function() {
                $(".new-tax-data-container").css("display", "flex");
            });

            $(".new-tax-data-save-btn").click(function() {
                let old_afm = $("#t_afm").val();
                let old_doy = $("#t_doy").val();
                let old_eponimia = $("#t_eponimia").val();
                let old_address = $("#txtAddress").val();
                let old_zipcode = $("#txtZipcode").val();

                let old_tax_data = $("#t_old_tax_data").val();
                $("#t_old_tax_data").val(old_tax_data + "\n====\n" + old_afm + "\n" + old_doy + "\n" + old_eponimia + "\n" + old_address + "\n" + old_zipcode);


                let new_afm = $("#new_afm").val();
                let new_eponimia = $("#new_eponimia").val();
                // let new_epagelma = $("#new_epagelma").val();
                let new_address = $("#new_address").val();
                let new_zipcode = $("#new_zipcode").val();
                let new_doy = $("#new_doy").val();

                $("#t_afm").val(new_afm);
                $("#t_doy").val(new_doy);
                $("#t_eponimia").val(new_eponimia);
                $("#txtAddress").val(new_address);
                $("#txtZipcode").val(new_zipcode);

                $("#save2").trigger('click');

            })

            $(".close-new-tax-data").click(function() {
                $(".new-tax-data-container").css('display', 'none');
            });
            
        });



        $(function() {

        $(".btn-get-company-data-afm").click(function() {
            var afm = $("#t_afm").val();
            if (afm=='') {
                alert('Παρακαλώ συμπληρώστε πρώτα το ΑΦΜ');
                return;
            }
            $.post(
                "_getCompanyDataAfmAade.php",
                {
                    afm: afm
                },
                function(data) {
                    console.log(data);
                    var alldata = JSON.parse(data);
                    var company_data = alldata.result.rg_ws_public2_result_rtType.basic_rec;
                    console.log(alldata);

                    if ($("#txtCompanyname").val()=="") {
                        $("#txtCompanyname").val(company_data['onomasia']);
                    }                            
                    $("#t_eponimia").val(company_data['onomasia']);

                    /*$("#txtAddress").val(company_data['postal_address'] 
                    + " " + company_data['postal_address_no'] 
                    + " " + company_data['postal_area_description']);*/

                    //$("#txtZipcode").val(company_data['postal_zip_code']);
                    
                    //$("#area").val(company_data['postal_area_description']);
                    //$("#city").val(company_data['postal_area_description']);
                    
                    $("#t_doy").val(company_data['doy_descr']);

                    var extra_data = alldata.result.rg_ws_public2_result_rtType.firm_act_tab;
                    //alert(extra_data.item[0].firm_act_descr);
                    //$("#profession").val(extra_data.item[0].firm_act_descr);

                    alert('Τα δεδομένα ενημερώθηκαν. Πατήστε SAVE για αποθήκευση.');
                    
                }
            );
        });
            
            
        });
                
        
        
        </script>
        
        

    </body>
</html>