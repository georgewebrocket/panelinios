<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$company = new COMPANIES($db1,$id);

if ($id>0) {
    $company->set_lockedbyuser(0);
    $company->set_lockuser(0);
    $company->Savedata();
}