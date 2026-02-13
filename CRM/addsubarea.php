<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("AREAS",$lang,$db1);

$refid = $_GET['id'];

$refarea = new AREAS($db1, $refid);

echo "<h1><a href=\"editArea.php?id=0&ParentId=".$refid."\">".$l->l("add-area-child")." (".$refarea->get_description().".##) "."</a></h1><br/>";

if($refarea->get_level() > 1){
    $parentArea = func::vlookup("description","AREAS","id=".$refarea->get_parentid(),$db1);
    echo "<h1><a href=\"editArea.php?id=0&ParentId=".$refarea->get_parentid()."\">".$l->l("add-area-same-level")." (".$parentArea.".##) "."</a></h1><br/>";
}
else{
    echo "<h1><a href=\"editArea.php?id=0&ParentId=".$refarea->get_parentid()."\">".$l->l("add-area-same-level")." (##) "."</a></h1><br/>";
}


?>
