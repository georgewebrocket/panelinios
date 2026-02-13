<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('php/session.php');
require_once('php/config.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

//session_start();

$_SESSION['authorized'] = 0;
$_SESSION['EPCR186_authorized'] = 0;
//EPCR186_authorized

fn::ulog("Logout", $db1);

//session_unset();
//session_destroy();
header("Location: index.php");