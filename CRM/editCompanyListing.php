<?php
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
require_once('php/companiesArr.php');

//session_start(); // 26/07/2014 10:51
$userid = $_SESSION['user_id'];

$l = new mlng("COMPANIES",$lang,$db1);
$id = $_GET['id'];
$catalogueid = $_GET['catalogueid'];
if(isset($_GET['companyListing'])){$companyListing = $_GET['companyListing'];}

$ssql = "SELECT * FROM COMPANIES WHERE id=?";
$arrComp = $db1->getRS($ssql,array($id));
//if this new company
foreach ($arrCompany[0] as $key => $value) {        
    $arrCompany[0][$key] = $arrComp[0][$key];
}

//site get table listing
$html = file_get_contents('http://www.epagelmatias.gr/interface/getListing.php?id='.$catalogueid);

//local site on my pc
//$html = file_get_contents('http://localhost/epangelmatias-crm/site/getListing.php?id='.$catalogueid); //SPITI
//$html = file_get_contents('http://localhost/epangelmatias/site/getListing.php?id='.$catalogueid);

$onlinedata = explode("<br/>", $html);
$i=0;
if(count($onlinedata) > 1){
    foreach ($arrListing as $key => $value) {        
        $arrListing[$key] = $onlinedata[$i];
        $i++;
    }  
}

//site get table seller
$html = file_get_contents('http://www.epagelmatias.gr/interface/getSeller.php?id='.$arrListing['listing_seller']);

//local site on my pc
//$html = file_get_contents('http://localhost/epangelmatias-crm/site/getSeller.php?id='.$arrListing['listing_seller']); //SPITI
//$html = file_get_contents('http://localhost/epangelmatias/site/getSeller.php?id='.$arrListing['listing_seller']);

$onlinedataSeller = explode("<br/>", $html);
$i=0;
if(count($onlinedataSeller) > 1){
    foreach ($arrSeller as $key => $value) {        
        $arrSeller[$key] = $onlinedataSeller[$i];
        $i++;
    }  
}

$arrFieldsValues = array();

//Metafora dedomenwn apo CRM => Site
if($companyListing == 1){
    //if checkboxes is checked
    if(isset($_POST['chkListing_fields'])){
        $arrListing_fields = $_POST['chkListing_fields'];
        if(in_array("listing_location|||area", $arrListing_fields)){
            //add field and value 'listing_location_path'
            $areaPath = trim(iconv("UTF-8", "ASCII//TRANSLIT", func::vlookup("path", "AREAS", "id=".$arrCompany[0]['area'], $db1)));
            array_push($arrListing_fields, "listing_location_path|||".$areaPath);
        }
        
        $strListingCategory = '';        
        if(in_array("listing_category|||basiccategory", $arrListing_fields)){
            //add field and value 'listing_category_path'
            
            $categoryPath = trim(iconv("UTF-8", "ASCII//TRANSLIT", func::vlookup("path", "CATEGORIES", "id=".$arrCompany[0]['subcategory'], $db1)));
            array_push($arrListing_fields, "listing_category_path|||".$categoryPath);
            
            //set listing_category
            $arrListingCat = explode("-", $categoryPath);
            //var_dump($arrListingCat);
            $key = array_search('listing_category|||basiccategory', $arrListing_fields);
            $listing_category = $arrListingCat[count($arrListingCat)-2];
            $arrListing_fields[$key] = 'listing_category|||'.$listing_category;
            
            //*********************listing_category *********************//
            
            $arrListingCategory = array ('category_id' => '0','category_listing' =>  $arrListing['listing_id'],'category_value' =>  $listing_category,
                'category_path' =>  $categoryPath,'category_status' =>  'approved');
            
            //site get table listing_category
            
            $html = file_get_contents('http://www.epagelmatias.gr/interface/getListing_category.php?id='.$arrListing['listing_id']);

            //local site on my pc
            //$html = file_get_contents('http://localhost/epangelmatias-crm/site/getListing_category.php?id='.$arrListingCategory); //SPITI
            //$html = file_get_contents('http://localhost/epangelmatias/site/getListing_category.php?id='.$arrListingCategory);

            $onlinedataSeller = explode("<br/>", $html);
            $i=0;
            if(count($onlinedataSeller) > 1){
                foreach ($arrListingCategory as $key => $value) {        
                    $arrListingCategory[$key] = $onlinedataSeller[$i];
                    $i++;
                }  
            }
            
            
            foreach ($arrListingCategory as $key => $value) {
                $arrListingCategory[$key] = $key."|||".$value;
            }
            $strListingCategory = implode("####", $arrListingCategory);
            //*********************listing_category *********************//
        }
        
        for($i=0;$i<count($arrListing_fields);$i++) {
            $key = substr(strpbrk($arrListing_fields[$i], '|||'),3);
            if(isset($arrCompany[0][$key])){
                if(array_key_exists($key, $arrCompany[0])){
                    $arrListing_fields[$i] = str_replace("|||".$key, "|||".$arrCompany[0][$key], $arrListing_fields[$i]);
                }
            }            
        }
        
        $strListing = implode("####", $arrListing_fields);
        
        //echo $strListing; ///
        
        /*****************SELLER*****************************/
        $arrSeller['seller_username'] = $arrCompany[0]['username'];
        $arrSeller['seller_password'] = $arrCompany[0]['password'];
        $arrSeller['seller_firstname'] = $arrCompany[0]['companyname'];
        $arrSeller['seller_expire_date'] = $arrCompany[0]['expires'];
        $arrSeller['seller_company'] = $arrCompany[0]['companyname'];
        
        $arrSeller['seller_phone'] = $arrCompany[0]['phone1'];
        $arrSeller['seller_email'] = $arrCompany[0]['email'];
        $arrSeller['seller_address'] = $arrCompany[0]['address'];
        $arrSeller['seller_zip'] = $arrCompany[0]['zipcode'];
        $arrSeller['seller_mobile'] = $arrCompany[0]['mobilephone'];
        $arrSeller['seller_website'] = $arrCompany[0]['website'];
        
        $arrSeller['seller_package'] = 2;
        
        foreach ($arrSeller as $key => $value) {
            $arrSeller[$key] = $key."|||".$value;
        }
        $strSeller = implode("####", $arrSeller);
                
        $arrContent = array('strListing' => $strListing, 'strListingCategory' => $strListingCategory, 'strSeller' => $strSeller);
        
        //*****************************************************************************************
        $url = 'http://www.epagelmatias.gr/interface/setListing.php?catalogueid='.$catalogueid;

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
        $rs = $db1->execSQL("UPDATE COMPANIES SET catalogueid=?, status=10 WHERE id=?", array($catalogueid,$id));
        $rsAction = $db1->execSQL("INSERT INTO ACTIONS (company, user, status1, status2) VALUES (?,?,?,?)", 
                array($id, $userid, $arrComp[0]['status'], 10));
        header('Location: '.$myHost.'/editCompanyListing.php?companyListing='.$err.'&catalogueid='.$catalogueid.'&id='.$id);
        //header('Location: '.$myHost.'/editCompanyListing.php?companyListing='.$err.'&catalogueid='.$catalogueid.'&id='.$id);
        exit();
        //********************************************************************************************************
//        var_dump($catalogueid);
//        var_dump($arrListing_fields);
//            var_dump($arrListingCategory);
//            var_dump($arrSeller);
    }
}
//Metafora dedomenwn apo Site => CRM 
elseif($companyListing == 2) {
    //if checkboxes is checked
    if(isset($_POST['chkCompany_fields'])){
        $arrListing_fields = $_POST['chkCompany_fields'];
        if($arrSeller['seller_id'] > 0){
            array_push($arrListing_fields, "username|||".$arrSeller['seller_username']);
            array_push($arrListing_fields, "password|||".$arrSeller['seller_password']);
            array_push($arrListing_fields, "contactperson|||".$arrSeller['seller_firstname']);
            //array_push($arrListing_fields, "expires|||".$arrSeller['seller_expire_date']);
            //array_push($arrListing_fields, "companyname|||".$arrSeller['seller_company']);
        }

        foreach($arrListing_fields as $values){
            array_push($arrFieldsValues, explode("|||", $values));                 
        }
        
        for($i=0;$i<count($arrFieldsValues);$i++){            
            if (array_key_exists($arrFieldsValues[$i][1], $arrListing)){
                if($arrFieldsValues[$i][1] == 'listing_expire'){
                    $date = date_create($arrListing[$arrFieldsValues[$i][1]]);
                    $arrFieldsValues[$i][1] = func::dateTo14str(date_format($date, 'd-m-Y'), array("-","",":"));                    
                }else{
                    $arrFieldsValues[$i][1] = $arrListing[$arrFieldsValues[$i][1]];
                }
            }            
        }
        
        $_SESSION['FieldsValues'] = $arrFieldsValues;
        header('Location: php/setCompanies.php?catalogueid='.$catalogueid.'&id='.$id);        
    }
} 



//var_dump($id);
//var_dump($catalogueid);
//var_dump($arrListing);
//var_dump($_SESSION['FieldsValues']);
//var_dump($arrCompany);
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
    </style>
    
    </head>
    
    
    <body>
        
        <h1><?php echo $lg->l("sync-data");?></h1>
        <?php if($companyListing == "ok"){echo '<h2>'.$lg->l('sync-ok').'</h2>';}elseif($companyListing == "err"){echo '<h2>'.$lg->l('sync-failed').'</h2>';} ?>
        
        <hr></hr>
        
        <div class="col-6">
            <form id="form1" method="POST" action="editCompanyListing.php?companyListing=1&catalogueid=<?php echo $catalogueid; ?>&id=<?php echo $id; ?>">
                 
                <h2>Local data</h2>
                
                <div class="button" id="form1-check" style="display: inline-block">un-check</div>
                <br/><br/>

                Id: <?php echo $arrCompany[0]['id']; ?>  
                <br/><br/>
                <input id="listing_title_1" type="checkbox" name="chkListing_fields[]" value="listing_title_7|||companyname" checked="checked" /> 
                <label for="listing_title_1"><?php echo $l->l("company-name").': '.func::shortDescription($arrCompany[0]['companyname'],50); ?></label> 
                <br/>
                <input id="listing_address" type="checkbox" name="chkListing_fields[]" value="listing_address|||address" checked="checked" /> 
                <label for="listing_address"><?php echo $l->l("address").': '. func::shortDescription($arrCompany[0]['address'],50); ?></label> 
                <br/>
                <input id="listing_phone" type="checkbox" name="chkListing_fields[]" value="listing_phone|||phone1" checked="checked" /> 
                <label for="listing_phone"><?php echo $l->l("phone-1").': '. $arrCompany[0]['phone1']; ?></label> 
                <br/>
                <input id="listing_email" type="checkbox" name="chkListing_fields[]" value="listing_email|||email" checked="checked" /> 
                <label for="listing_email"><?php echo $l->l("email").': '. $arrCompany[0]['email']; ?></label> 
                <br/>
                <input id="listing_website" type="checkbox" name="chkListing_fields[]" value="listing_website|||website" checked="checked" />
                <label for="listing_website"><?php echo $l->l("website").': '. $arrCompany[0]['website']; ?></label> 
                <br/>
                <input id="listing_zip_lat" type="checkbox" name="chkListing_fields[]" value="listing_posted_latitude|||geo_x" checked="checked" />
                <label for="listing_zip_lat"><?php echo $l->l("geo_x").': '. $arrCompany[0]['geo_x']; ?></label> 
                <br/>
                <input id="listing_zip_lon" type="checkbox" name="chkListing_fields[]" value="listing_posted_longitude|||geo_y" checked="checked" />
                <label for="listing_zip_lon"><?php echo $l->l("geo_y").': '. $arrCompany[0]['geo_y']; ?></label> 
                <br/>
                <input id="listing_zip" type="checkbox" name="chkListing_fields[]" value="listing_zip|||zipcode" checked="checked" />
                <label for="listing_zip"><?php echo $l->l("zipcode").': '. $arrCompany[0]['zipcode']; ?></label> 
                <br/>
                
                <input id="listing_facebook" type="checkbox" name="chkListing_fields[]" value="listing_facebook|||facebook" checked="checked" />
                <label for="listing_facebook"><?php echo $l->l("facebook").': '. func::shortDescription($arrCompany[0]['facebook'],40); ?></label> 
                <br/>
                
                <input id="listing_twitter" type="checkbox" name="chkListing_fields[]" value="listing_twitter|||twitter" checked="checked" />
                <label for="listing_twitter"><?php echo $l->l("twitter").': '. func::shortDescription($arrCompany[0]['twitter'],40); ?></label> 
                <br/>
                
                <input id="listing_descbrief_7" type="checkbox" name="chkListing_fields[]" value="listing_descbrief_7|||ShortDescription" checked="checked" />
                <label for="listing_descbrief_7"><?php echo $l->l("ShortDescription").': '. func::shortDescription(strip_tags($arrCompany[0]['ShortDescription']),50); ?></label> 
                <br/>
                
                <input id="listing_descfull_7" type="checkbox" name="chkListing_fields[]" value="listing_descfull_7|||FullDescription" checked="checked" />
                <label for="listing_descfull_7"><?php echo $l->l("FullDescription").': '. func::shortDescription(strip_tags($arrCompany[0]['FullDescription']),50); ?></label> 
                <br/>
                
                <input id="listing_expire" type="checkbox" name="chkListing_fields[]" value="listing_expire|||expires" checked="checked" />
                <label for="listing_expire"><?php echo $l->l("expires").': '. func::str14toDate($arrCompany[0]['expires']); ?></label> 
                <br/>
                
                <input id="listing_category" type="checkbox" name="chkListing_fields[]" value="listing_category|||basiccategory" checked="checked" />
                <label for="listing_category"><?php echo $l->l("category").': '. func::vlookup("description","CATEGORIES","id=".$arrCompany[0]['basiccategory'],$db1); ?></label> 
                <br/>
                <input id="listing_location" type="checkbox" name="chkListing_fields[]" value="listing_location|||area" checked="checked" />
                <label for="listing_location"><?php echo $l->l("area").': '. func::vlookup("description","AREAS","id=".$arrCompany[0]['area'],$db1); ?></label> 
                <br/>
                <input id="listing_package" type="checkbox" name="chkListing_fields[]" value="listing_package|||package" checked="checked" />
                <label for="listing_package"><?php echo $l->l("package").': '. func::vlookup("description","PACKAGES","id=".$arrCompany[0]['package'],$db1); ?></label> 
                                
                <br/><br/>

                <input type="submit" value="<?php echo $l->l("update-online-catalogue"); ?>" />
            
            </form>
        </div>
        
        <div class="col-6">
            <form id="form2" method="POST" action="editCompanyListing.php?companyListing=2&catalogueid=<?php echo $catalogueid; ?>&id=<?php echo $id; ?>">
                <h2>Online data</h2>
                
                <div class="button" id="form2-check" style="display: inline-block">un-check</div>
                <br/><br/>

                Id: <?php echo $arrListing['listing_id']; ?>  
                <br/><br/>
                <input id="companyname" type="checkbox" name="chkCompany_fields[]" value="companyname|||listing_title_7" checked="checked" /> 
                <label for="companyname"><?php echo $l->l("company-name").': ' . func::shortDescription($arrListing['listing_title_7'],50); ?></label>
                <br/>
                <input id="address" type="checkbox" name="chkCompany_fields[]" value="address|||listing_address" checked="checked" /> 
                <label for="address"><?php echo $l->l("address").': '. func::shortDescription($arrListing['listing_address'],50); ?></label>
                <br/>
                <input id="phone1" type="checkbox" name="chkCompany_fields[]" value="phone1|||listing_phone" checked="checked" /> 
                <label for="phone1"><?php echo  $l->l("phone").': '. $arrListing['listing_phone']; ?></label>
                <br/>
                <input id="email" type="checkbox" name="chkCompany_fields[]" value="email|||listing_email" checked="checked" /> 
                <label for="email"><?php echo $l->l("email").': '. $arrListing['listing_email']; ?></label>
                <br/>
                <input id="website" type="checkbox" name="chkCompany_fields[]" value="website|||listing_website" checked="checked" />
                <label for="website"><?php echo $l->l("website").': '. $arrListing['listing_website']; ?></label>
                <br/>
                <input id="geo_x" type="checkbox" name="chkCompany_fields[]" value="geo_x|||listing_posted_latitude" checked="checked" />
                <label for="geo_x"><?php echo $l->l("geo_x").': '. $arrListing['listing_posted_latitude']; ?></label>
                <br/>
                <input id="geo_y" type="checkbox" name="chkCompany_fields[]" value="geo_y|||listing_posted_longitude" checked="checked" />
                <label for="geo_y"><?php echo $l->l("geo_y").': '. $arrListing['listing_posted_longitude']; ?></label>
                <br/>
                <input id="zipcode" type="checkbox" name="chkCompany_fields[]" value="zipcode|||listing_zip" checked="checked" />
                <label for="zipcode"><?php echo $l->l("zipcode").': '. $arrListing['listing_zip']; ?></label>
                <br/>
                
                <input id="facebook" type="checkbox" name="chkCompany_fields[]" value="facebook|||listing_facebook" checked="checked" />
                <label for="facebook"><?php echo $l->l("facebook").': '. func::shortDescription($arrListing['listing_facebook'],40); ?></label>
                <br/>
                
                <input id="twitter" type="checkbox" name="chkCompany_fields[]" value="twitter|||listing_twitter" checked="checked" />
                <label for="twitter"><?php echo $l->l("twitter").': '. func::shortDescription($arrListing['listing_twitter'],40); ?></label>
                <br/>
                
                <input id="ShortDescription" type="checkbox" name="chkCompany_fields[]" value="ShortDescription|||listing_descbrief_7" checked="checked" />
                <label for="ShortDescription"><?php echo $l->l("ShortDescription").': '. func::shortDescription(strip_tags($arrListing['listing_descbrief_7']),50); ?></label>
                <br/>
                
                <input id="FullDescription" type="checkbox" name="chkCompany_fields[]" value="FullDescription|||listing_descfull_7" checked="checked" />
                <label for="FullDescription"><?php echo $l->l("FullDescription").': '. func::shortDescription(strip_tags($arrListing['listing_descfull_7']),50); ?></label>
                <br/>
                
                <input id="expires" type="checkbox" name="chkCompany_fields[]" value="expires|||listing_expire" checked="checked" />
                <label for="expires"><?php $date = date_create($arrListing['listing_expire']); echo $l->l("expires").': '. date_format($date, 'd-m-Y'); ?></label>
                <br/>
                
                <input id="basiccategory" type="checkbox" name="chkCompany_fields[]" value="basiccategory|||listing_category" checked="checked" />
                <label for="basiccategory"><?php echo $l->l("category").': '. $arrListing['listing_category']; ?></label>
                <br/>
                
                <input id="area" type="checkbox" name="chkCompany_fields[]" value="area|||listing_location" checked="checked" />
                <label for="area"><?php echo $l->l("area").': '. $arrListing['listing_location']; ?></label>
                <br/>
                
                <input id="area" type="checkbox" name="chkCompany_fields[]" value="package|||listing_package" checked="checked" />
                <label for="package"><?php echo $l->l("package").': '. $arrListing['listing_package']; ?></label>
                
                <br/><br/>
                
                <?php if($catalogueid != 0){ ?> 
                    <input type="submit" value="<?php echo $l->l("update-crm"); ?>" />                
                <?php } ?>
            </form>
        </div>
        
        <div style="clear: both"></div>
        
        
<!--        <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>-->
        
        
    </body>
    
</html>

