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

if (isset($_REQUEST['spam'])) {
  $email->set_spam($_REQUEST['spam']);
}

if (isset($_REQUEST['trash'])) {
  $email->set_trash($_REQUEST['trash']);
}

if (isset($_REQUEST['mark'])) {
  $email->set_mark($_REQUEST['mark']);
}

$email->Savedata();

echo "OK";

