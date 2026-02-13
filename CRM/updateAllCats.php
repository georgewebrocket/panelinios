<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

function UpdateCatSite($category) {
    $url = "http://www.epagelmatias.gr/interface-new-site/setCategory.php";
    $url .= "?id=".$category->get_id();
    $url .= "&description_gr=".urlencode($category->get_description()) ;
    $url .= "&description_en=".urlencode($category->get_description_en());
    $url .= "&parentid=".urlencode($category->get_parentid());
    $url .= "&level=".urlencode($category->get_level());
    $url .= "&path=".urlencode($category->get_path());
    $url .= "&active=".urlencode($category->get_active());
    $url .= "&nodes=".urlencode($category->get_nodes());
    $url .= "&seotitle=".urlencode($category->get_seo_title());
    $url .= "&seodescription=".urlencode($category->get_seo_description());
    $url .= "&seourl=".urlencode($category->get_seo_url());

    //echo $url;

    $result = "";
    $result = file_get_contents($url);
    //echo "RESULT=".$result;
    if ($result=="OK") { 
        return TRUE;             
    } else {
        echo $result;
        return FALSE;
    }
}


$sql = "SELECT * FROM CATEGORIES";
$rs = $db1->getRS($sql);

for ($i=0;$i<count($rs);$i++) {
    $category = new CATEGORIES($db1, $rs[$i]['id']);
    $categoryId = $rs[$i]['id'];
    if (UpdateCatSite($category)) {
        echo "$categoryId - OK.<br/>";
    }
    else {
        echo "$categoryId - ERROR.<br/>";
    }
}


