<?php

require_once('session.php');
require_once('dataobjects.php');
require_once('controls.php');
require_once('../inc.php');

$id = $_GET['id'];
$catalogueid = $_GET['catalogueid'];

$arFields = array();
$arValues = array();
$arVal2 = array();

$arrFieldsValues = $_SESSION['FieldsValues'];
foreach($arrFieldsValues as $values){
    array_push($arFields, $values[0]);
    array_push($arValues, $values[1]);
    $arVal2[$values[0]] = $values[1];
}
$vals = "";
$rs = "";

$company = new companies($db1, $id);

if (array_key_exists('companyname', $arVal2)) {
    $company->set_companyname($arVal2['companyname']);
    $company->set_companyname_dm($arVal2['companyname_dm']);
}
if (array_key_exists('address', $arVal2)) {
    $company->set_address($arVal2['address']);
    $company->set_address_dm($arVal2['address_dm']);
}
if (array_key_exists('phone1', $arVal2)) {
    $company->set_phone1($arVal2['phone1']);
    $company->set_phone1_dm($arVal2['phone1_dm']);
}
if (array_key_exists('phone2', $arVal2)) {
    $company->set_phone2($arVal2['phone2']);
    $company->set_phone2_dm($arVal2['phone2_dm']);
}
if (array_key_exists('mobilephone', $arVal2)) {
    $company->set_mobilephone($arVal2['mobilephone']);
    $company->set_mobile_dm($arVal2['mobile_dm']);
}
if (array_key_exists('fax', $arVal2)) {
    $company->set_fax($arVal2['fax']);
    $company->set_fax_dm($arVal2['fax_dm']);
}
if (array_key_exists('facebook', $arVal2)) {
    $company->set_facebook($arVal2['facebook']);
    $company->set_facebook_dm($arVal2['facebook_dm']);
}
if (array_key_exists('twitter', $arVal2)) {
    $company->set_twitter($arVal2['twitter']);
    $company->set_twitter_dm($arVal2['twitter_dm']);
}
if (array_key_exists('email', $arVal2)) {
    $company->set_email($arVal2['email']);
    $company->set_email_dm($arVal2['email_dm']);
}
if (array_key_exists('website', $arVal2)) {
    $company->set_website($arVal2['website']);
    $company->set_website_dm($arVal2['website_dm']);
}
if (array_key_exists('zipcode', $arVal2)) {
    $company->set_zipcode($arVal2['zipcode']);
    $company->set_zipcode_dm($arVal2['zipcode_dm']);
}
if (array_key_exists('ShortDescription', $arVal2)) {
    $company->set_ShortDescription($arVal2['ShortDescription']);
    $company->set_shortdescr_dm($arVal2['shortdescr_dm']);
}
if (array_key_exists('FullDescription', $arVal2)) {
    $company->set_FullDescription($arVal2['FullDescription']);
    $company->set_fulldescr_dm($arVal2['fulldescr_dm']);
}
if (array_key_exists('basiccategory', $arVal2)) {
    $company->set_basiccategory($arVal2['basiccategory']);
    $company->set_basiccat_dm($arVal2['basiccat_dm']);
}
if (array_key_exists('area', $arVal2)) {
    $company->set_area($arVal2['area']);
    $company->set_area_dm($arVal2['area_dm']);
}
if (array_key_exists('vn_keywords', $arVal2)) {
    $company->set_vn_keywords($arVal2['vn_keywords']);
    $company->set_keywords_dm($arVal2['keywords_dm']);
}
if (array_key_exists('geo_x', $arVal2)) {
    $company->set_geo_x($arVal2['geo_x']);
    $company->set_geox_dm($arVal2['geox_dm']);
}
if (array_key_exists('geo_y', $arVal2)) {
    $company->set_geo_y($arVal2['geo_y']);
    $company->set_geoy_dm($arVal2['geoy_dm']);
}

//mono update oxi insert new sto crm apo edw

$res = $company->Savedata();

if($res === FALSE){
    $err = "err";        
}else{
    $err = "ok";            
}

/*
if ($id==0) {
    $sql = "INSERT INTO COMPANIES (";
    for ($i=0;$i<count($arFields);$i++) {
        $sql .= $arFields[$i];
        $vals .= "?";
        if ($i<count($arFields)-1) {
            $sql .= ", ";
            $vals .= ", ";
        }
    }
    $sql .= ") VALUES (".$vals.")";
    $res = $db1->execSQL($sql, $arValues);
    if($res > 0){
        $err = "ok";
        $id = $res; 
    }else{
        $err = "err";
        $id = 0;  
    }
}
else {
    $sql = "UPDATE COMPANIES SET ";
    for ($i=0;$i<count($arFields);$i++) {
        $sql .= $arFields[$i]."=?";
        if ($i<count($arFields)-1) {
            $sql .= ", ";
        }        
    }
    $sql .= " WHERE id=".$id;
    $res = $db1->execSQL($sql, $arValues);
    if($res === FALSE){
        $err = "err";        
    }else{
        $err = "ok";            
    }
}
*/

header('Location: ../editCompanyNewSite.php?company='.$err.'&catalogueid='.$catalogueid.'&id='.$id);



?>