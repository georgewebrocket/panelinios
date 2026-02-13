<?php

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$company = new COMPANIES($db1, $id);

$text = $company->get_FullDescription();
$text = str_replace("<p>", "", $text);
//$text = str_replace("<p", "", $text);
$text = str_replace("</p>", "\n", $text);
$text = str_replace("<ul>", "", $text);
$text = str_replace("</ul>", "\n", $text);
$text = str_replace("<li>", "• ", $text);
$text = str_replace("</li>", "", $text);
$text = str_replace("<br/>", "\n", $text);
$text = strip_tags($text);

$sql = "SELECT id, url_rewrite_gr FROM companies WHERE id=?";
$rsOnline = $dbSite->getRS($sql, array($company->get_catalogueid()));
if ($rsOnline) {
    if ($rsOnline[0]['url_rewrite_gr']!='') {
        $url_online = "https://www.epagelmatias.gr/" . $rsOnline[0]['url_rewrite_gr'];
    }
    else {
        $url_online = "https://www.epagelmatias.gr/εταιρεια/" . $rsOnline[0]['id'];
    }
    $text .= "\n\n" .  $url_online;
}

$sql = "SELECT * FROM company_photos WHERE company_id=?";
$rsPhotos = $dbSite->getRS($sql, array($company->get_catalogueid()));
$photos = "";
if ($rsPhotos) {
    $photo = $rsPhotos[0]['photo_path'];
    for ($i = 0; $i < count($rsPhotos); $i++) {
        $photos .= $rsPhotos[$i]['photo_path'];
        if ($i<count($rsPhotos)-1) {
            $photos .= ",";
        }
    }
}
else {
    $photo = "";
    
}

$emails = explode(",", $company->get_email());
$email = trim($emails[0]);


?>
<html>
    <head>
        <title>title</title>
        <style>
            body {
                margin: 0px;
            }
            
            .btn {
                background-color: #269af1;
                cursor: pointer;
                padding: 7px 10px;
                border-radius: 3px;
                border:none;
                width:100%;
            }
        </style>
    </head>
    <body>

        <form action="https://sosuite.gr/app/addPost.php" method="post" target="_blank" style="margin-bottom:5px">
            <input type="hidden" name="account" value="" />
            <input type="hidden" name="password" value="" />
            <input type="hidden" name="page" value="66" />
            <input type="hidden" name="text" value="<?php echo $text ?>" />
            <input type="hidden" name="photo" value="<?php echo $photo ?>" />
            <input type="hidden" name="photos" value="<?php echo $photos ?>" />
            <input type="hidden" name="link" value="" />
            <input type="hidden" name="customerid" value="<?php echo $id ?>" />
            <input type="hidden" name="customername" value="<?php echo $company->get_companyname() ?>" />
            <input type="hidden" name="customeremail" value="<?php echo $email ?>" />
            <input class="btn" type="submit" value="Post to Facebook" style="color:#fff; font-size:15px; text-align: left;" />

        </form>
        
        <form action="https://sosuite.gr/app/addPost.php" method="post" target="_blank" style="margin-bottom:5px">
            <input type="hidden" name="account" value="" />
            <input type="hidden" name="password" value="" />
            <input type="hidden" name="page" value="74" />
            <input type="hidden" name="text" value="<?php echo $text ?>" />
            <input type="hidden" name="photo" value="<?php echo $photo ?>" />
            <input type="hidden" name="photos" value="<?php echo $photos ?>" />
            <input type="hidden" name="link" value="" />
            <input type="hidden" name="customerid" value="<?php echo $id ?>" />
            <input type="hidden" name="customername" value="<?php echo $company->get_companyname() ?>" />
            <input type="hidden" name="customeremail" value="<?php echo $email ?>" />
            <input class="btn" type="submit" value="Post to Twitter" style="color:#fff; font-size:15px; text-align: left;" />

        </form>
        
        
    </body>
</html>
