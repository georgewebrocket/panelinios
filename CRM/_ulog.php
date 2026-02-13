<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/config.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$action = $_POST['action'];
fn::ulog($action, $db1);

echo "User action logged";