<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("CATEGORIES",$lang,$db1);

$refid = $_GET['id'];

$refcategories = new CATEGORIES($db1, $refid);

echo "<h1><a href=\"editCategory.php?id=0&ParentId=".$refid."\">".$l->l("add-category-child")." (".$refcategories->get_description().".##) "."</a></h1><br/>";

if($refcategories->get_level() > 1){
    $parentCategory = func::vlookup("description","CATEGORIES","id=".$refcategories->get_parentid(),$db1);
    echo "<h1><a href=\"editCategory.php?id=0&ParentId=".$refcategories->get_parentid()."\">".$l->l("add-category-same-level")." (".$parentCategory.".##) "."</a></h1><br/>";
}
else{
    echo "<h1><a href=\"editCategory.php?id=0&ParentId=".$refcategories->get_parentid()."\">".$l->l("add-category-same-level")." (##) "."</a></h1><br/>";
}


?>
