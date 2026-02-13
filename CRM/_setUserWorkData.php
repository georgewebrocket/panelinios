<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$employeeId = $_REQUEST['employeeId'];
if ($employeeId==0) {die ("Please select a user");}

$eDate = $_REQUEST['eDate'];
if ($eDate=="" || strlen($eDate)!=10) {die ("Please select a date");}

$eDate = func::dateTo14str($eDate);
$hourIn = $_REQUEST['hourIn'];
$minIn = $_REQUEST['minIn'];
if ($hourIn>0 && $minIn>0) {
    $timeIn = ($hourIn-1) + ($minIn-1) * 5 / 60;
}
else {
    $timeIn = 0;
}
$hourOut = $_REQUEST['hourOut'];
$minOut = $_REQUEST['minOut'];
if ($hourOut>0 && $minOut>0) {
    $timeOut = ($hourOut-1) + ($minOut-1) * 5 / 60;
}
else {
    $timeOut = 0;
}
$comments = $_REQUEST['comments'];

$dayoff = $_REQUEST['dayoff'];
$hoursoff = $_REQUEST['hoursoff'];

$user_role = $_REQUEST['userrole'];
echo $user_role . "/";

$user = new USERS($db1, $employeeId);
$userFullName = $user->get_fullname();

$sql = "SELECT * FROM USER_WORKDATA WHERE mdate=? AND userid=?";
$rs = $db1->getRS($sql, array($eDate, $employeeId));
if ($rs) {
    $userWorkData = new USER_WORKDATA($db1, $rs[0]['id'], $rs);
}
else {
    $userWorkData = new USER_WORKDATA($db1, 0);
    $userWorkData->set_userid($employeeId);
    $userWorkData->set_mdate($eDate);
}

$userWorkData->set_timein($timeIn);
$userWorkData->set_timeout($timeOut);
$userWorkData->set_comments($comments);

$userWorkData->set_dayoff($dayoff);
$userWorkData->set_hoursoff($hoursoff);

$userWorkData->set_user_role($user_role);

$userWorkData->Savedata();

if ($userWorkData->get_id()>0) {
    echo "$userFullName / Saved";
}
else {
    echo "$userFullName / Error";
}