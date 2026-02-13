<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('inc.php');

$sql = "UPDATE COMPANIES SET lockedbyuser=0, lockuser=0";
$db1->execSQL($sql);

echo "<h1>ALL COMPANIES UNLOCKED!</h1>";

