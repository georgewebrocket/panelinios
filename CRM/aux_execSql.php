<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$start = 0;
$end = 0;
$rs = FALSE;
$stop = 750000;
$step = 20000;


if (isset($_REQUEST['t_start'])) {
    
    $start = $_REQUEST['t_start'];
    $end = $_REQUEST['t_end'];
    
    if ($start>$stop) {
        die ("FINISHED ALL");
    }
    
    /*$sql = "UPDATE `COMPANIES_EPAG11` 
        SET `companyname_dm` = '20180517000000',
        `address_dm`= '20180517000000',
        `phone1_dm`= '20180517000000',
        `phone2_dm`= '20180517000000',
        `fax_dm`= '20180517000000',
        `email_dm`= '20180517000000',
        `mobile_dm`= '20180517000000',
        `website_dm`= '20180517000000',
        `geox_dm`= '20180517000000',
        `geoy_dm`= '20180517000000',
        `zipcode_dm`= '20180517000000',
        `facebook_dm`= '20180517000000',
        `twitter_dm`= '20180517000000',
        `linkedin_dm` =  '20180517000000',
        `shortdescr_dm`= '20180517000000',
        `fulldescr_dm`= '20180517000000',
        `basiccat_dm`= '20180517000000',
        `area_dm`= '20180517000000',
        `keywords_dm`= '20180517000000',
        `cityid_dm`= '20180517000000',
        `profession_dm`= '20180517000000',
        `password_dm`= '20180517000000',
        `expires_dm`= '20180517000000',
        `googleplus_dm`='20180517000000', 
        `pinterest_dm`='20180517000000',
        `reference` = 15,
        `status` = 1,
        `active`=1,
        phone1digits = digits(phone1),
        phone2digits = digits(phone2),
        faxdigits = digits(fax),
        mobiledigits = digits(mobilephone) 
        WHERE id>=? AND id<?";*/
    
    /*$sql = "UPDATE `COMPANIES_EPAG11` 
        SET `allphonesdigits` = CONCAT(phone1digits, '-', phone2digits, '-', faxdigits, '-', mobiledigits)
        WHERE id>=? AND id<?";*/
    
    
    /*$sql = "UPDATE COMPANIES_EPAG11
            SET BUSINESSKIND = UPPER(BUSINESSKIND),
            BUSINESSSTATE = UPPER(BUSINESSSTATE)
            WHERE id>=? AND id<? ";*/
    
    /*$sql = "UPDATE COMPANIES_EPAG11
            SET BUSINESSKIND = REPLACE(BUSINESSKIND,'Ώ','Ω'),
            BUSINESSSTATE = REPLACE(BUSINESSSTATE,'Ώ','Ω')
            WHERE id>=? AND id<? ";*/
    
    
    /*$sql = "UPDATE COMPANIES_EPAG11 
            SET COMPANIES_EPAG11.city_id = 
            (SELECT EP_CITIES.id 
            FROM EP_CITIES
            WHERE EP_CITIES.description LIKE COMPANIES_EPAG11.BUSINESSCITY2)
            WHERE id>=? AND id<? ";*/
    
    /*$sql = "UPDATE COMPANIES_EPAG11 
            SET COMPANIES_EPAG11.area = 
            (SELECT EP_AREAS.id 
            FROM EP_AREAS
            WHERE EP_AREAS.description LIKE COMPANIES_EPAG11.BUSINESSSTATE)
            WHERE id>=? AND id<? ";*/
    
    /*$sql = "UPDATE COMPANIES_EPAG11 
            SET COMPANIES_EPAG11.basiccategory = 
            (SELECT EP_CATEGORIES.id 
            FROM EP_CATEGORIES
            WHERE EP_CATEGORIES.description LIKE COMPANIES_EPAG11.BUSINESSKIND)
            WHERE id>=? AND id<? ";  */
    
    $sql = "INSERT INTO `COMPANIES`
        (`id`, `companyname`, `phone1`, `phone2`, `fax`, `mobilephone`, `contactperson`, `basiccategory`, `reference`, `area`, `geo_x`, `geo_y`, `address`, `zipcode`, `email`, `website`, `facebook`, `twitter`, `expires`, `status`, `comment`, `history`, `LinkedIn`, `linkedin_dm`, `ShortDescription`, `FullDescription`, `afm`, `doy`, `eponimia`, `onlinestatus`, `onlinedatetime`, `phonecode`, `company_type`, `city_id`, `phone1digits`, `phone2digits`, `faxdigits`, `mobiledigits`, `companyname_dm`, `address_dm`, `phone1_dm`, `phone2_dm`, `fax_dm`, `email_dm`, `mobile_dm`, `website_dm`, `geox_dm`, `geoy_dm`, `zipcode_dm`, `facebook_dm`, `twitter_dm`, `shortdescr_dm`, `fulldescr_dm`, `basiccat_dm`, `area_dm`, `keywords_dm`, `cityid_dm`, `CUSID`, `profession_dm`, `password_dm`, `expires_dm`, `active`, `googleplus_dm`, `pinterest_dm`, `allphonesdigits`, `workingmonths`)

        SELECT  
        `id`, `companyname`, `phone1`, `phone2`, `fax`, `mobilephone`, `contactperson`, `basiccategory`, `reference`, `area`, `geo_x`, `geo_y`, `address`, `zipcode`, `email`, `website`, `facebook`, `twitter`, `expires`, `status`, `comment`, `history`, `LinkedIn`, `linkedin_dm`, `ShortDescription`, `FullDescription`, `afm`, `doy`, `eponimia`, `onlinestatus`, `onlinedatetime`, `phonecode`, `company_type`, `city_id`, `phone1digits`, `phone2digits`, `faxdigits`, `mobiledigits`, `companyname_dm`, `address_dm`, `phone1_dm`, `phone2_dm`, `fax_dm`, `email_dm`, `mobile_dm`, `website_dm`, `geox_dm`, `geoy_dm`, `zipcode_dm`, `facebook_dm`, `twitter_dm`, `shortdescr_dm`, `fulldescr_dm`, `basiccat_dm`, `area_dm`, `keywords_dm`, `cityid_dm`, `CUSID`, `profession_dm`, `password_dm`, `expires_dm`, `active`, `googleplus_dm`, `pinterest_dm`, `allphonesdigits`, `workingmonths`
        FROM `COMPANIES_EPAG11` WHERE `PHONE1EXISTS` = 0 AND `PHONE2EXISTS` = 0
        AND id>=? AND id<?";
    

    $rs = $db1->execSQL($sql, array($start, $end));
    
}



?>
<html>
<head>
    <title>Check import</title>
</head>
<body>
    
<form action="aux_execSql.php" method="GET">

    START ID <input type="text" name="t_start" value="<?php echo $start; ?>" /><br/><br/>
    STOP ID <input type="text" name="t_end" value="<?php echo $end; ?>" /><br/><br/>
    <input type="submit" value="GO" /><br/><br/><br/>
</form>
    
    
<?php

var_dump($rs);

if ($rs!==FALSE) {

    $start =$end;
    $end = $start + $step; 

    echo <<<EOT

<script>

setTimeout(function(){ window.location.href = "aux_execSql.php?t_start=$start&t_end=$end"; }, 3000);    

</script>

EOT;
    }
    else {
    echo <<<EOT
    <h2>FINISHED!</h2>
    <audio autoplay>
      <source src="tada.ogg" type="audio/ogg">
      <source src="tada.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
    </audio>
    
EOT;
}
    
?>
    
</body>
</html>