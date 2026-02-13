<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

class connSite
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
}

$dbSite = new DB(connSite::$connstr, connSite::$username, connSite::$password);

$templateid = $_REQUEST['templateid'];
$customerid = $_REQUEST['customerid'];

$emailTemplate = new EMAIL_TEMPLATES($db1, $templateid);

$customer = new COMPANIES($db1, $customerid);

$str = $emailTemplate->get_bodytext();

$str = str_replace("[ONLINEID]", func::getCompanyPId($customer), $str);

if (strpos($str, "[KEYWORDS]")!=FALSE) {
  $tags = "";
  $onlineId = $customer->get_catalogueid();
  $sql = "SELECT tags.description FROM tags INNER JOIN company_tags ON tags.id = company_tags.tag_id WHERE company_tags.company_id= $onlineId";
  $rsTags = $dbSite->getRS($sql);
  if ($rsTags) {
    for ($i=0;$i<count($rsTags);$i++) {
      $tags .= $rsTags[$i]['description'];
      if ($i<count($rsTags)-1) {
        $tags .= ", ";
      }
    }
  }
  $str = str_replace("[KEYWORDS]", $tags, $str);
}

$basic_category_id = $customer->get_basiccategory();
$basic_category = new categories($db1, $basic_category_id);
$basic_category_title = $basic_category->get_panel_description();
$basic_category_slug = $basic_category->get_panel_url();
$str = str_replace("[BASIC-CATEGORY-TITLE]", $basic_category_title, $str);
$str = str_replace("[BASIC-CATEGORY-LINK]", "https://www.panelinios.gr/category/" . $basic_category_id . "/" . $basic_category_slug, $str);



//str = str_replace("[KEYWORDS]", $customer->get_vn_keywords(), $str);
$str = str_replace("[USERNAME]", $customer->get_username(), $str);
$str = str_replace("[PASSWORD]", $customer->get_password(), $str);
$str = str_replace("[DOMAIN]", $customer->get_domain_name(), $str);

$finalprice0 = $customer->get_price() + $customer->get_price2() 
        + $customer->get_fb_price() + $customer->get_ga_price();
$finalprice = func::nrToCurrency($customer->get_price() + $customer->get_price2() 
        + $customer->get_fb_price() + $customer->get_ga_price());
$vatPercentage = func::vlookup("value", "VAT", "zone=1", $db1);
$vat = $finalprice0 * $vatPercentage / 100;
$finalpriceWithVat = func::nrToCurrency($finalprice0 + $vat);
$str = str_replace("[FINALPRICE]", $finalprice, $str);
$str = str_replace("[FINALPRICEWITHVAT]", $finalpriceWithVat, $str);
$str = str_replace("[CONTACTNAME]", $customer->get_contactperson(), $str);

$str = str_replace("[CUSTOMER_EMAIL]", $customer->get_email(), $str);

$str = str_replace("[AGENT]", $_SESSION['user_fullname'], $str);

$user_photo = strpos($_SESSION['user_photo'], "https")!==false? $_SESSION['user_photo']: "https://crm.panelinios.gr/" . $_SESSION['user_photo'];
$str = str_replace("[AGENT-PHOTO]", $user_photo, $str);

$user_sign = strpos($_SESSION['user_sign'], "https")!==false? $_SESSION['user_sign']: "https://crm.panelinios.gr/" . $_SESSION['user_sign'];
$str = str_replace("[AGENT-SIGN]", $user_sign, $str);

echo $str;