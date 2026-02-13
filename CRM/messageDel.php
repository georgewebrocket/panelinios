<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_REQUEST['id'];
$message = new MESSAGES($db1, $id);
$message->Delete();

echo "OK";