<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT * FROM FOOTABLE";
$rs = $db1->getRS($sql);

var_dump($rs);