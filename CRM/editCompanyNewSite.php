<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);


require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');

function getHlDate($date1, $date2) {
    if ($date1==$date2) {
        return "hldate";
    }
    elseif ($date1<$date2) {
        return "hldateRed";
    }
    else {
        return "hldateGreen";
    }
}

//require_once('inc.php');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);
$ltoken = "l=gr"; $lang = "gr"; $locale = "GR";
$myHost = "http://epagelmatias.gr/crm";  //!!!!!!!!!!!!!!!!!!!!!!!!!
$lg = new mlng("GLOBAL",$lang,$db1);

require_once('php/companiesNewSiteArr.php');   //xxxxxxx

//session_start(); // 26/07/2014 10:51
$userid = $_SESSION['user_id'];

$l = new mlng("COMPANIES",$lang,$db1);
$id = $_GET['id'];
$catalogueid = $_GET['catalogueid'];
if(isset($_GET['company'])){$company = $_GET['company'];}

$ssql = "SELECT * FROM COMPANIES WHERE id=?";
$arrComp = $db1->getRS($ssql,array($id));
//if this new company
foreach ($arrCompanyCRM[0] as $key => $value) {        
    $arrCompanyCRM[0][$key] = $arrComp[0][$key];
}
//$arrCompanyCRM[0]['companyname_dm'] = $arrComp[0]['companyname_dm'];
//....all dm fields

//site get table companies
$html = file_get_contents('http://www.epagelmatias.gr/interface-new-site/getCompany.php?id='.$catalogueid);


$onlinedata = explode("<br/>", $html);
$onlinedataCompany = $onlinedata;
$i=0;
if(count($onlinedata) > 1){
    foreach ($arrCompany as $key => $value) {        
        $arrCompany[$key] = $onlinedata[$i];
        $i++;
    }  
}

//site get table categories
if ($arrCompany['basic_category'] > 0){
    $html = file_get_contents('http://www.epagelmatias.gr/interface-new-site/getCategories.php?id='.$arrCompany['basic_category']);

    $onlinedata = explode("<br/>", $html);
    $i=0;
    if(count($onlinedata) > 1){$companyCatDesc = $onlinedata[1]; }else{$companyCatDesc = '';}
}
else{
    $companyCatDesc = '';    
}

//site get table packages
if ($arrCompany['package'] > 0){
    $html = file_get_contents('http://www.epagelmatias.gr/interface-new-site/getPackages.php?id='.
            $arrCompany['package']);

    $onlinedata = explode("<br/>", $html);
    $i=0;
    if(count($onlinedata) > 1){$companyPackage = $onlinedata[1]; }else{$companyPackage = '';}
}
else{
    $companyPackage = '';    
}

//site get table area
if ($arrCompany['area'] > 0){
    $html = file_get_contents('http://www.epagelmatias.gr/interface-new-site/getAreas.php?id='.
            $arrCompany['area']);

    $onlinedata = explode("<br/>", $html);
    $i=0;
    if(count($onlinedata) > 1){$companyArea = $onlinedata[1]; }else{$companyArea = '';}
}
else{
    $companyArea = '';    
}

$companyCity = "";
if ($onlinedataCompany[47] > 0){
    $companyCity = func::vlookup("description", "EP_CITIES", "id=".$onlinedataCompany[47], $db1);
}



$arrFieldsValues = array();                

//Metafora dedomenwn apo CRM => Site
if($company == 1){
    //if checkboxes is checked
    if(isset($_POST['chkListing_fields'])){
        $arrCompany_fields = $_POST['chkListing_fields'];
        
        array_push($arrCompany_fields, "username|||".$arrCompanyCRM[0]['username']);
        array_push($arrCompany_fields, "password|||".$arrCompanyCRM[0]['password']);
        array_push($arrCompany_fields, "active|||1");
        ///
        array_push($arrCompany_fields, "companyname_dm|||".$arrComp[0]['companyname_dm']);
        array_push($arrCompany_fields, "address_dm|||".$arrComp[0]['address_dm']);
        array_push($arrCompany_fields, "phone1_dm|||".$arrComp[0]['phone1_dm']);
        array_push($arrCompany_fields, "phone2_dm|||".$arrComp[0]['phone2_dm']);
        array_push($arrCompany_fields, "fax_dm|||".$arrComp[0]['fax_dm']);
        array_push($arrCompany_fields, "email_dm|||".$arrComp[0]['email_dm']);
        array_push($arrCompany_fields, "mobile_dm|||".$arrComp[0]['mobile_dm']);
        array_push($arrCompany_fields, "website_dm|||".$arrComp[0]['website_dm']);
        array_push($arrCompany_fields, "geox_dm|||".$arrComp[0]['geox_dm']);
        array_push($arrCompany_fields, "geoy_dm|||".$arrComp[0]['geoy_dm']);
        array_push($arrCompany_fields, "zipcode_dm|||".$arrComp[0]['zipcode_dm']);
        array_push($arrCompany_fields, "facebook_dm|||".$arrComp[0]['facebook_dm']);
        array_push($arrCompany_fields, "twitter_dm|||".$arrComp[0]['twitter_dm']);
        array_push($arrCompany_fields, "shortdescr_dm|||".$arrComp[0]['shortdescr_dm']);
        array_push($arrCompany_fields, "fulldescr_dm|||".$arrComp[0]['fulldescr_dm']);
        array_push($arrCompany_fields, "basiccat_dm|||".$arrComp[0]['basiccat_dm']);
        array_push($arrCompany_fields, "area_dm|||".$arrComp[0]['area_dm']);
        array_push($arrCompany_fields, "keywords_dm|||".$arrComp[0]['keywords_dm']);
        //....all dm fields
        array_push($arrCompany_fields, "city_id|||".$arrComp[0]['city_id']);
        array_push($arrCompany_fields, "cityid_dm|||".$arrComp[0]['cityid_dm']);
        ///
        
        if ($arrCompanyCRM[0]['basiccategory'] <> '0'){
            $strCompanyCategory = '';        
            if(in_array("basic_category|||basiccategory", $arrCompany_fields)){
                //*********************listing_category *********************//
                $arrCompanyCategory = array ('id' => '0','company_id' =>  $arrCompany['id'],
                    'category_id' =>  $arrCompanyCRM[0]['basiccategory'],
                    'path' =>  "-".$arrCompanyCRM[0]['basiccategory']."-");
                //get sub category and sub category path
                if($arrCompanyCRM[0]['subcategory'] > 0){
                    $subcategoryPath = func::get_category_path($arrCompanyCRM[0]['subcategory'], $db1);
                    $arrCompanyCategory = array ('id' => '0',
                        'company_id' =>  $arrCompany['id'],
                        'category_id' =>  $arrCompanyCRM[0]['subcategory'],
                        'path' =>  $subcategoryPath);
                }
                
                foreach ($arrCompanyCategory as $key => $value) {
                    $arrCompanyCategory[$key] = $key."|||".$value;
                }
                $strCompanyCategory = implode("####", $arrCompanyCategory);
                //*********************listing_category *********************//
            }
        }
        for($i=0;$i<count($arrCompany_fields);$i++) {
            $key = substr(strpbrk($arrCompany_fields[$i], '|||'),3);
            if(isset($arrCompanyCRM[0][$key])){
                if(array_key_exists($key, $arrCompanyCRM[0])){
                    switch ($key) {
                        case 'package':
                            if ($arrCompanyCRM[0][$key] > 0){
                                $arrCompany_fields[$i] = str_replace("|||".$key, "|||".
                                    func::vlookup("online_package","PACKAGES",
                                    "id=".$arrCompanyCRM[0][$key],$db1), $arrCompany_fields[$i]);
                            }
                            break;
                        default :
                            $arrCompany_fields[$i] = str_replace("|||".$key, 
                                    "|||".$arrCompanyCRM[0][$key], 
                                    $arrCompany_fields[$i]);
                            break;
                    }
                }
            }            
        }
        
        //var_dump($arrCompany_fields);
        
        $arrCompanyContent = array();
        $strCompany = implode("####", $arrCompany_fields);
        
        $arrCompanyContent['strCompany'] = $strCompany;
        if(isset($strCompanyCategory)){$arrCompanyContent['strCompanyCategories'] = $strCompanyCategory;}
                
        $arrContent = $arrCompanyContent;
        
    
        //*****************************************************************************************
        $url = 'http://www.epagelmatias.gr/if/setCompany2.php?catalogueid='.$catalogueid;
        
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($arrContent),
                 
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
       
        $arrResult = explode("<br/>", $result);
        
        $err = $arrResult[0];
        $catalogueid = $arrResult[1];
               
        $now = func::dateTo14str(date("d-m-Y"));
        $rs = $db1->execSQL("UPDATE COMPANIES SET catalogueid=?, onlinestatus=1, onlinedatetime=? WHERE id=?", 
                array($catalogueid, $now, $id));
        
        header('Location: '.$myHost.'/editCompanyNewSite.php?company='.$err.'&catalogueid='.$catalogueid.'&id='.$id); 
        exit();
        //********************************************************************************************************
//        var_dump($catalogueid);
//        var_dump($arrCompany_fields);
//            var_dump($arrCompanyCategory);
    }
}
//Metafora dedomenwn apo Site => CRM 
elseif($company == 2) {
    //if checkboxes is checked
    if(isset($_POST['chkCompany_fields'])){
        $arrCompany_fields = $_POST['chkCompany_fields'];
        
        foreach($arrCompany_fields as $values){
            array_push($arrFieldsValues, explode("|||", $values));                 
        }
             
        
        for($i=0;$i<count($arrFieldsValues);$i++){            
            if (array_key_exists($arrFieldsValues[$i][1], $arrCompany)){
                $arrFieldsValues[$i][1] = $arrCompany[$arrFieldsValues[$i][1]];
            }            
        }
        
        array_push($arrFieldsValues, array('companyname_dm', $onlinedataCompany[29]));
        array_push($arrFieldsValues, array('address_dm', $onlinedataCompany[30]));
        array_push($arrFieldsValues, array('phone1_dm', $onlinedataCompany[31]));
        array_push($arrFieldsValues, array('phone2_dm', $onlinedataCompany[32]));
        array_push($arrFieldsValues, array('fax_dm', $onlinedataCompany[33]));
        array_push($arrFieldsValues, array('email_dm', $onlinedataCompany[34]));
        array_push($arrFieldsValues, array('mobile_dm', $onlinedataCompany[35]));
        array_push($arrFieldsValues, array('website_dm', $onlinedataCompany[36]));
        array_push($arrFieldsValues, array('geox_dm', $onlinedataCompany[37]));
        array_push($arrFieldsValues, array('geoy_dm', $onlinedataCompany[38]));
        array_push($arrFieldsValues, array('zipcode_dm', $onlinedataCompany[39]));
        array_push($arrFieldsValues, array('facebook_dm', $onlinedataCompany[40]));
        array_push($arrFieldsValues, array('twitter_dm', $onlinedataCompany[41]));
        array_push($arrFieldsValues, array('shortdescr_dm', $onlinedataCompany[42]));
        array_push($arrFieldsValues, array('fulldescr_dm', $onlinedataCompany[43]));
        array_push($arrFieldsValues, array('basiccat_dm', $onlinedataCompany[44]));
        array_push($arrFieldsValues, array('area_dm', $onlinedataCompany[45]));
        array_push($arrFieldsValues, array('keywords_dm', $onlinedataCompany[46]));
        //all date fields
        array_push($arrFieldsValues, array('city_id', $onlinedataCompany[47]));
        array_push($arrFieldsValues, array('cityid_dm', $onlinedataCompany[48]));
        
        $_SESSION['FieldsValues'] = $arrFieldsValues;
        header('Location: php/setCompaniesNewSite.php?catalogueid='.$catalogueid.'&id='.$id);        
    }
} 



//var_dump($id);
//var_dump($catalogueid);
//var_dump($arrCompany);
//var_dump($_SESSION['FieldsValues']);
//var_dump($arrCompanyCRM);
//var_dump($_SESSION['FieldsValuesSeller']);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    <script>
        $(document).ready(function() {	
                $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1100, 'height' : 450 });	
        });

        $(document).ready(function() {	
                $("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });	
        });
        
        $(document).ready(function() {
            $("#form1-check").click(function() {
                //alert($("#form1-check").html());
                if ($("#form1-check").html()=="check") {
                    $('#form1 input:checkbox').prop('checked', true);
                    $("#form1-check").html("un-check");
                }
                else {
                    $('#form1 input:checkbox').prop('checked', false);
                    $("#form1-check").html("check");
                }
            });
            
            $("#form2-check").click(function() {
                if ($("#form2-check").html()=="check") {
                    $('#form2 input:checkbox').prop('checked', true);
                    $("#form2-check").html("un-check");
                }
                else {
                    $('#form2 input:checkbox').prop('checked', false);
                    $("#form2-check").html("check");
                }
            });
            
        });

    </script>
    
    <style>
        body {
            min-height: 400px;
        }
        .hldate {
            background-color: rgb(200,200,200);
            padding:3px 10px;
        }
        .hldateGreen {
            background-color: rgb(150,255,150);
            padding:3px 10px;
        }
        .hldateRed {
            background-color: rgb(255,150,150);
            padding:3px 10px;
        }
    </style>
    
    </head>
    
    
    <body>
        
        <h1><?php echo $lg->l("sync-data");?></h1>
        <?php if($company == "ok"){echo '<h2>'.$lg->l('sync-ok').'</h2>';}elseif($company == "err"){echo '<h2>'.$lg->l('sync-failed').'</h2>';} ?>
        
        <hr></hr>
        
        <div class="col-6">
            <form id="form1" method="POST" action="editCompanyNewSite.php?company=1&catalogueid=<?php echo $catalogueid; ?>&id=<?php echo $id; ?>">
                 
                <h2>Local data</h2>
                
                <div class="button" id="form1-check" style="display: inline-block">un-check</div>
                <br/><br/>

                Id: <?php echo $arrCompanyCRM[0]['id']; ?>  
                <br/><br/>
                <input id="company_name_gr" type="checkbox" name="chkListing_fields[]" value="company_name_gr|||companyname" checked="checked" /> 
                <label for="company_name_gr">
                    <?php 
                    $hldate = getHlDate($arrComp[0]['companyname_dm'],$onlinedataCompany[29]);
                    echo $l->l("company-name").': '.
                        func::shortDescription($arrCompanyCRM[0]['companyname'],50) .
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['companyname_dm']). 
                            "</span>"; 
                ?>
                </label> 
                <br/>
                <input id="address_gr" type="checkbox" name="chkListing_fields[]" value="address_gr|||address" checked="checked" /> 
                <label for="address_gr">
                    <?php 
                    $hldate = getHlDate($arrComp[0]['address_dm'],$onlinedataCompany[30]);
                    echo $l->l("address").': '. 
                        func::shortDescription($arrCompanyCRM[0]['address'],50) .
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['address_dm']). 
                            "</span>"; 
                    ?>
                </label> 
                <br/>
                <input id="phone" type="checkbox" name="chkListing_fields[]" value="phone|||phone1" checked="checked" /> 
                <label for="phone"><?php 
                $hldate = getHlDate($arrComp[0]['phone1_dm'],$onlinedataCompany[31]);
                echo $l->l("phone-1").': '. $arrCompanyCRM[0]['phone1'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['phone1_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="phone2" type="checkbox" name="chkListing_fields[]" value="phone2|||phone2" checked="checked" /> 
                <label for="phone2"><?php 
                $hldate = getHlDate($arrComp[0]['phone2_dm'],$onlinedataCompany[32]);
                echo $l->l("phone-2").': '. $arrCompanyCRM[0]['phone2'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['phone2_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="fax" type="checkbox" name="chkListing_fields[]" value="fax|||fax" checked="checked" /> 
                <label for="fax"><?php 
                $hldate = getHlDate($arrComp[0]['fax_dm'],$onlinedataCompany[33]);
                echo $l->l("Fax").': '. $arrCompanyCRM[0]['fax'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['fax_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="email" type="checkbox" name="chkListing_fields[]" value="email|||email" checked="checked" /> 
                <label for="email"><?php 
                $hldate = getHlDate($arrComp[0]['email_dm'],$onlinedataCompany[34]);
                echo $l->l("email").': '. $arrCompanyCRM[0]['email'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['email_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="website" type="checkbox" name="chkListing_fields[]" value="website|||website" checked="checked" />
                <label for="website"><?php 
                $hldate = getHlDate($arrComp[0]['website_dm'],$onlinedataCompany[36]);
                echo $l->l("website").': '. $arrCompanyCRM[0]['website'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['website_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="geo_x" type="checkbox" name="chkListing_fields[]" value="geo_x|||geo_x" checked="checked" />
                <label for="geo_x"><?php 
                $hldate = getHlDate($arrComp[0]['geox_dm'],$onlinedataCompany[37]);
                echo $l->l("geo_x").': '. $arrCompanyCRM[0]['geo_x'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['geox_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="geo_y" type="checkbox" name="chkListing_fields[]" value="geo_y|||geo_y" checked="checked" />
                <label for="geo_y"><?php 
                $hldate = getHlDate($arrComp[0]['geoy_dm'],$onlinedataCompany[38]);
                echo $l->l("geo_y").': '. $arrCompanyCRM[0]['geo_y'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['geoy_dm']). 
                            "</span>"; ?></label> 
                <br/>
                <input id="zip_code" type="checkbox" name="chkListing_fields[]" value="zip_code|||zipcode" checked="checked" />
                <label for="zip_code"><?php 
                $hldate = getHlDate($arrComp[0]['zipcode_dm'],$onlinedataCompany[39]);
                echo $l->l("zipcode").': '. $arrCompanyCRM[0]['zipcode'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['zipcode_dm']). 
                            "</span>"; ?></label> 
                <br/>                
                <input id="facebook" type="checkbox" name="chkListing_fields[]" value="facebook|||facebook" checked="checked" />
                <label for="facebook"><?php 
                $hldate = getHlDate($arrComp[0]['facebook_dm'],$onlinedataCompany[40]);
                echo $l->l("facebook").': '. 
                        func::shortDescription($arrCompanyCRM[0]['facebook'],40).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['facebook_dm']). 
                            "</span>"; ?></label> 
                <br/>                
                <input id="twitter" type="checkbox" name="chkListing_fields[]" value="twitter|||twitter" checked="checked" />
                <label for="twitter"><?php 
                $hldate = getHlDate($arrComp[0]['twitter_dm'],$onlinedataCompany[41]);
                echo $l->l("twitter").': '. 
                        func::shortDescription($arrCompanyCRM[0]['twitter'],40).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['twitter_dm']). 
                            "</span>"; ?></label> 
                <br/>                
                <input id="short_description_gr" type="checkbox" name="chkListing_fields[]" value="short_description_gr|||ShortDescription" checked="checked" />
                <label for="short_description_gr"><?php 
                $hldate = getHlDate($arrComp[0]['shortdescr_dm'],$onlinedataCompany[42]);
                echo $l->l("ShortDescription").': '. 
                        func::shortDescription(strip_tags($arrCompanyCRM[0]['ShortDescription']),50).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['shortdescr_dm']). 
                            "</span>"; ?></label> 
                <br/>                
                <input id="full_description_gr" type="checkbox" name="chkListing_fields[]" value="full_description_gr|||FullDescription" checked="checked" />
                <label for="full_description_gr"><?php 
                $hldate = getHlDate($arrComp[0]['fulldescr_dm'],$onlinedataCompany[43]);
                echo $l->l("FullDescription").': '. 
                        func::shortDescription(strip_tags($arrCompanyCRM[0]['FullDescription']),50).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['fulldescr_dm']). 
                            "</span>"; ?></label> 
                <br/>                
                <input id="expires" type="checkbox" name="chkListing_fields[]" value="expires|||expires" checked="checked" />
                <label for="expires"><?php echo $l->l("expires").': '. 
                        func::str14toDate($arrCompanyCRM[0]['expires']); ?></label> 
                <br/>                
                <input id="basic_category" type="checkbox" name="chkListing_fields[]" value="basic_category|||basiccategory" checked="checked" />
                <label for="basic_category"><?php 
                $hldate = getHlDate($arrComp[0]['basiccat_dm'],$onlinedataCompany[44]);
                echo $l->l("category").': '. 
                        func::vlookup("description","CATEGORIES","id=".$arrCompanyCRM[0]['basiccategory'],$db1).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['basiccat_dm']). 
                            "</span>"; ?></label> 
                <br/>
                
                <input id="area" type="checkbox" name="chkListing_fields[]" value="area|||area" checked="checked" />
                <label for="area"><?php 
                $hldate = getHlDate($arrComp[0]['area_dm'],$onlinedataCompany[45]);
                echo $l->l("area").': '. 
                        func::vlookup("description","AREAS","id=".$arrCompanyCRM[0]['area'],$db1).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['area_dm']). 
                            "</span>"; ?></label> 
                <br/>
                
                <input id="city_id" type="checkbox" name="chkListing_fields[]" value="city_id|||city_id" checked="checked" />
                <label for="city_id"><?php 
                $hldate = getHlDate($arrComp[0]['cityid_dm'],$onlinedataCompany[48]);                
                echo $l->l("ΠΟΛΗ").': '. 
                        func::vlookup("description","EP_CITIES","id=".$arrComp[0]['city_id'],$db1).
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['cityid_dm']). 
                            "</span>"; ?></label> 
                <br/>
                
                <input id="package" type="checkbox" name="chkListing_fields[]" value="package|||package" checked="checked" />
                <label for="package"><?php echo $l->l("package").': '. func::vlookup("description","PACKAGES","id=".$arrCompanyCRM[0]['package'],$db1); ?></label> 
                
                <br/>
                <input id="keywords_gr" type="checkbox" name="chkListing_fields[]" value="keywords_gr|||vn_keywords" checked="checked" />
                <label for="keywords_gr"><?php 
                $hldate = getHlDate($arrComp[0]['keywords_dm'],$onlinedataCompany[46]);
                echo 'KEYWORDS: '.$arrCompanyCRM[0]['vn_keywords'].
                            " <span class=\" $hldate \">". 
                            func::str14toDate($arrComp[0]['keywords_dm']). 
                            "</span>"; ?></label> 
                
                
                <br/><br/>

                <input type="submit" value="<?php echo $l->l("update-online-catalogue"); ?>" />
            
            </form>
        </div>
        
        <div class="col-6">
            <form id="form2" method="POST" action="editCompanyNewSite.php?company=2&catalogueid=<?php echo $catalogueid; ?>&id=<?php echo $id; ?>">
                <h2>Online data</h2>
                
                <div class="button" id="form2-check" style="display: inline-block">un-check</div>
                <br/><br/>

                Id: <?php echo $arrCompany['id']; ?>  
                <br/><br/>
                <input id="companyname" type="checkbox" name="chkCompany_fields[]" value="companyname|||company_name_gr" checked="checked" /> 
                <label for="companyname">
                    <?php 
                    $hldate = getHlDate($onlinedataCompany[29],$arrComp[0]['companyname_dm']);
                    echo $l->l("company-name").': ' . 
                        func::shortDescription($arrCompany['company_name_gr'],50) .
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[29])."</span>"; ?></label>
                <br/>
                <input id="address" type="checkbox" name="chkCompany_fields[]" value="address|||address_gr" checked="checked" /> 
                <label for="address"><?php 
                $hldate = getHlDate($onlinedataCompany[30],$arrComp[0]['address_dm']);
                echo $l->l("address").': '. 
                        func::shortDescription($arrCompany['address_gr'],50).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[30])."</span>"; ?></label>
                <br/>
                <input id="phone1" type="checkbox" name="chkCompany_fields[]" value="phone1|||phone" checked="checked" /> 
                <label for="phone1"><?php 
                $hldate = getHlDate($onlinedataCompany[31],$arrComp[0]['phone1_dm']);
                echo  $l->l("phone").': '. $arrCompany['phone'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[31])."</span>"; ?></label>
                <br/>
                <input id="phone2C" type="checkbox" name="chkCompany_fields[]" value="phone2|||phone2" checked="checked" /> 
                <label for="phone2C"><?php 
                $hldate = getHlDate($onlinedataCompany[32],$arrComp[0]['phone2_dm']);
                echo  $l->l("phone-2").': '. $arrCompany['phone2'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[32])."</span>"; ?></label>
                <br/>
                <input id="faxC" type="checkbox" name="chkCompany_fields[]" value="fax|||fax" checked="checked" /> 
                <label for="faxC"><?php 
                $hldate = getHlDate($onlinedataCompany[33],$arrComp[0]['phone2_dm']);
                echo  $l->l("Fax").': '. $arrCompany['fax'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[33])."</span>"; ?></label>
                <br/>
                <input id="email" type="checkbox" name="chkCompany_fields[]" value="email|||email" checked="checked" /> 
                <label for="email"><?php 
                $hldate = getHlDate($onlinedataCompany[34],$arrComp[0]['email_dm']);
                echo $l->l("email").': '. $arrCompany['email'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[34])."</span>"; ?></label>
                <br/>
                <input id="website" type="checkbox" name="chkCompany_fields[]" value="website|||website" checked="checked" />
                <label for="website"><?php 
                $hldate = getHlDate($onlinedataCompany[36],$arrComp[0]['website_dm']);
                echo $l->l("website").': '. $arrCompany['website'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[36])."</span>"; ?></label>
                <br/>
                <input id="geo_x" type="checkbox" name="chkCompany_fields[]" value="geo_x|||geo_x" checked="checked" />
                <label for="geo_x"><?php 
                $hldate = getHlDate($onlinedataCompany[37],$arrComp[0]['geox_dm']);
                echo $l->l("geo_x").': '. $arrCompany['geo_x'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[37])."</span>"; ?></label>
                <br/>
                <input id="geo_y" type="checkbox" name="chkCompany_fields[]" value="geo_y|||geo_y" checked="checked" />
                <label for="geo_y"><?php 
                $hldate = getHlDate($onlinedataCompany[38],$arrComp[0]['geoy_dm']);
                echo $l->l("geo_y").': '. $arrCompany['geo_y'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[38])."</span>"; ?></label>
                <br/>
                <input id="zipcode" type="checkbox" name="chkCompany_fields[]" value="zipcode|||zip_code" checked="checked" />
                <label for="zipcode"><?php 
                $hldate = getHlDate($onlinedataCompany[39],$arrComp[0]['zipcode_dm']);
                echo $l->l("zipcode").': '. $arrCompany['zip_code'].
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[39])."</span>"; ?></label>
                <br/>                
                <input id="facebook" type="checkbox" name="chkCompany_fields[]" value="facebook|||facebook" checked="checked" />
                <label for="facebook"><?php 
                $hldate = getHlDate($onlinedataCompany[40],$arrComp[0]['facebook_dm']);
                echo $l->l("facebook").': '. 
                        func::shortDescription($arrCompany['facebook'],40).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[40])."</span>"; ?></label>
                <br/>                
                <input id="twitter" type="checkbox" name="chkCompany_fields[]" value="twitter|||twitter" checked="checked" />
                <label for="twitter"><?php 
                $hldate = getHlDate($onlinedataCompany[41],$arrComp[0]['twitter_dm']);
                echo $l->l("twitter").': '. 
                        func::shortDescription($arrCompany['twitter'],40).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[41])."</span>"; ?></label>
                <br/>                
                <input id="ShortDescription" type="checkbox" name="chkCompany_fields[]" value="ShortDescription|||short_description_gr" checked="checked" />
                <label for="ShortDescription"><?php 
                $hldate = getHlDate($onlinedataCompany[42],$arrComp[0]['shortdescr_dm']);
                echo $l->l("ShortDescription").': '. 
                        func::shortDescription(strip_tags($arrCompany['short_description_gr']),50).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[42])."</span>"; ?></label>
                <br/>                
                <input id="FullDescription" type="checkbox" name="chkCompany_fields[]" value="FullDescription|||full_description_gr" checked="checked" />
                <label for="FullDescription"><?php 
                $hldate = getHlDate($onlinedataCompany[43],$arrComp[0]['fulldescr_dm']);
                echo $l->l("FullDescription").': '
                        . func::shortDescription(strip_tags($arrCompany['full_description_gr']),50).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[43])."</span>"; ?></label>
                <br/>                
                <input id="expires" type="checkbox" name="chkCompany_fields[]" value="expires|||expires" checked="checked" />
                <label for="expires"><?php echo $l->l("expires").': '. 
                        func::str14toDate($arrCompany['expires']); ?></label>
                <br/>                
                <input id="basiccategory" type="checkbox" name="chkCompany_fields[]" value="basiccategory|||basic_category" checked="checked" />
                <label for="basiccategory"><?php 
                $hldate = getHlDate($onlinedataCompany[44],$arrComp[0]['basiccat_dm']);
                echo $l->l("category").': '. 
                        $companyCatDesc .
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[44])."</span>"; ?></label>
                <br/>                
                
                <input id="area" type="checkbox" name="chkCompany_fields[]" value="area|||area" checked="checked" />
                <label for="area"><?php 
                $hldate = getHlDate($onlinedataCompany[45],$arrComp[0]['area_dm']);
                echo $l->l("area").': '. $companyArea.
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[45])."</span>"; ?></label>
                <br/>
                
                <input id="city_id" type="checkbox" name="chkCompany_fields[]" value="city_id|||city_id" checked="checked" />
                <label for="city_id"><?php 
                $hldate = getHlDate($onlinedataCompany[48],$arrComp[0]['cityid_dm']);
                
                echo $l->l("ΠΟΛΗ").': '. $companyCity.
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[48])."</span>"; ?></label>
                <br/>
                
                <input id="package" type="checkbox" name="chkCompany_fields[]" value="package|||package" checked="checked" />
                <label for="package"><?php echo $l->l("package").': '. $companyPackage; ?></label>
                <br/>                
                <input id="vn_keywords" type="checkbox" name="chkCompany_fields[]" value="vn_keywords|||keywords_gr" checked="checked" />
                <label for="vn_keywords"><?php 
                $hldate = getHlDate($onlinedataCompany[46],$arrComp[0]['keywords_dm']);
                echo 'SEO-KEYWORDS: '. 
                        func::shortDescription($arrCompany['keywords_gr'],40).
                        " <span class=\" $hldate \">".
                        func::str14toDate($onlinedataCompany[46])."</span>"; ?></label>
                
                
                
                <br/><br/>
                
                <?php if($catalogueid != 0){ ?> 
                    <input type="submit" value="<?php echo $l->l("update-crm"); ?>" />                
                <?php } ?>
            </form>
        </div>
        
        <div style="clear: both"></div>
        
        <form>        
            
                <a class="button" href="editcompany.php?id=<?php echo $id; ?>"><?php echo $l->l("Go back to company") ?></a>
                <?php
                $btnCloseUpdate = new button("btnCloseUpdate", $lg->l("close-update"), "close-update");
                $btnCloseUpdate->set_method("UnlockCompany($id)");
                $btnCloseUpdate->get_button_simple();
                ?>
            
        </form>
        
        
<!--        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>-->
        
        
    </body>
    
</html>