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
$eDate = $_REQUEST['eDate'];
$eDate = func::dateTo14str($eDate);

$user = new USERS($db1, $employeeId);
$userFullName = $user->get_fullname();

$sql = "SELECT * FROM USER_WORKDATA WHERE mdate=? AND userid=?";
$rs = $db1->getRS($sql, array($eDate, $employeeId));
if ($rs) {
    $userWorkData = new USER_WORKDATA($db1, $rs[0]['id'], $rs);
    
    
    
    $obj = new stdClass();
    $obj->hour_in = floor($userWorkData->get_timein());
    $obj->min_in = $userWorkData->get_timein() - $obj->hour_in;
    $obj->hour_out = floor($userWorkData->get_timeout());
    $obj->min_out = $userWorkData->get_timeout() - $obj->hour_out;
    
    //convert to ids (tables: TIME_HOUR, TIME_MIN)
    if ($obj->hour_in>0) {
        $obj->hour_in = $obj->hour_in + 1;
        $obj->min_in = round($obj->min_in * 12, 2) + 1;
    }
    else {
        $obj->hour_in = 0;
        $obj->min_in = 0;
    }
    if ($obj->hour_out>0) {
        $obj->hour_out = $obj->hour_out + 1;
        $obj->min_out = round($obj->min_out * 12, 2) + 1;
    }
    else {
        $obj->hour_out = 0;
        $obj->min_out = 0;
    }
    
    $obj->username = $userFullName;
    $obj->comments = $userWorkData->get_comments();
    
    $obj->dayoff = $userWorkData->get_dayoff();
    $obj->hoursoff = $userWorkData->get_hoursoff();
    
    $obj->user_role = $userWorkData->get_user_role();
    
    echo json_encode($obj);
    
}
else {
    $obj = new stdClass();
    $obj->hour_in = 0;
    $obj->min_in = 0;
    $obj->hour_out = 0;
    $obj->min_out = 0;
    $obj->username = $userFullName;
    $obj->comments = "";
    
    $obj->dayoff = 0;
    $obj->hoursoff = 0;
    
    $obj->user_role = 0;
    
    echo json_encode($obj);
}