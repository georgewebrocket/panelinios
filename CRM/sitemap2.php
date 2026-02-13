<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

header('Content-type: application/xml');

require_once 'php/config.php';
require_once 'php/utils.php';
require_once 'php/db.php';
require_once 'php/dataobjects.php';
require_once 'php/controls.php';
require_once 'php/wp.php';
require_once 'php/start.php';

//$start = $_REQUEST['start'];
//$stop = $_REQUEST['stop'];
$start = 0;
$stop = 1000000;


$sql = "SELECT * FROM categories_professions";
$rsCat = $db1->getRS($sql);

$sql2 = "SELECT companies.id AS id, companies.url_rewrite_gr AS url_rewrite_gr, 
    companies.company_name_gr AS company_name_gr,
cities.description2 AS city_description2, professions.description AS profession_description
FROM companies 
INNER JOIN cities ON companies.city_id = cities.id
INNER JOIN professions ON companies.profession = professions.id
WHERE package>1 AND companies.id>=$start AND companies.id<$stop  ORDER BY companies.id";
//echo $sql2;

$rsCompanies = $db1->getRS($sql2);




if ($start==1) {
$output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$output .= '<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';


echo $output;





?>

<url>
  <loc>http://www.epagelmatias.gr/</loc>
  <changefreq>daily</changefreq>
</url>


<?php 
for($i = 0; $i < count($rsCat); $i++) { 
    //$cat = new categories_p($db1, $rsCat[$i]['id'], $rsCat);
    //$catDescr = $cat->get_description_gr();
    //$cat_seo_url = $cat->get_seo_url();
    //if ($cat_seo_url == "") {
    //    $cat_seo_url = func::normURL($catDescr);
    //}
    $catid = $rsCat[$i]['id'];
    $catDescr = $rsCat[$i]['description'];
    $cat_seo_url = func::normURL($catDescr);
    $catURL = "https://www.epagelmatias.gr/αναζητηση/".
        $catid."/0//".
        $cat_seo_url."//alpha/0";
    
?>
<url>
  <loc><?php echo $catURL; ?></loc>
  <changefreq>daily</changefreq>
</url>
<?php 

}

}
?>


<?php 
for($i = 0; $i < count($rsCompanies); $i++) { 
    $company = new companies($db1, $rsCompanies[$i]['id'], $rsCompanies);
    $companyId = $rsCompanies[$i]['id'];
        
    if ($rsCompanies[$i]['url_rewrite_gr']!="") {
        $companyUrl = $rsCompanies[$i]['url_rewrite_gr'];
    }
    else {        
        $companyUrl = func::normURL($rsCompanies[$i]['profession_description'] . "-".
            $rsCompanies[$i]['city_description2']. "-" . 
            trim($rsCompanies[$i]['company_name_gr']));
    }
    
    $companyLink = "https://www.epagelmatias.gr/εταιρεια/$companyId/$companyUrl";
    
?>
<url>
  <loc><?php echo $companyLink; ?></loc>
  <changefreq>daily</changefreq>
</url>
<?php } ?>

<?php if (isset($_GET['end'])) { ?>
</urlset>
<?php } ?>



