<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$userid = $_SESSION['user_id'];
$sql = "SELECT  COUNT(DISTINCT conversation) AS MYCOUNT FROM MESSAGES WHERE (sender=? OR receiver=?) "
        . "AND `read`=0 AND (`receiver`<>43)";
$rs = $db1->getRS($sql, array($userid,$userid));
echo $rs[0]['MYCOUNT'];