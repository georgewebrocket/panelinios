<?php

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$basiccat = $_GET['cat'];
if ($basiccat=="") { $basiccat=-1;}
//echo "Basic cat=".$basiccat;
$cSubcategory = new comboBox("cSubcategory", $db1, 
        "SELECT id, description FROM CATEGORIES WHERE parentid=".$basiccat, 
        "id","description",
        0,"");

echo $cSubcategory->comboBox_simple();

?>