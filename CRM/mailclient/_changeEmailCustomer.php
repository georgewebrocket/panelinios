<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('../php/session.php');
require_once('../php/config.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');

$id = $_REQUEST['email'];
$email = new EMAILS($db1, $id);

$company = $_REQUEST['customer'];

$email->set_company($company);

$email->Savedata();

echo "OK";