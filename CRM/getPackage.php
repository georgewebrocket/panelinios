<?php

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$package = new PACKAGES($db1,$_GET['id']);

echo $package->get_price();
echo "//";
echo $package->get_duration();

?>