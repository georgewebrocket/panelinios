<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_REQUEST['id'];
$conv = new CONVERSATIONS($db1, $id);
$conv->set_isread(1);
$conv->Savedata();

$sql = "UPDATE MESSAGES SET `read` = 1 WHERE conversation=?";
$res = $db1->execSQL($sql, array($id));

echo "OK";