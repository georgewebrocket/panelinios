<?php


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/mydata.php');
require_once('inc.php');


//myData_SendInvoices("16893", $db1);
myData_SendInvoices("16893,18562", $db1);