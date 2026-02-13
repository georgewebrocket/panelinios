<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_REQUEST['id'];
$hours = "";
switch ($id) {
    case 1:
        $hours = "09.00||09.00||09.00||09.00||09.00||09.00||///"
            . "15.00||14.00||15.00||14.00||14.00||15.00||///"
            . "||17.30||||17.30||17.30||||///"
            . "||21.00||||21.00||21.00||||";
        break;
    case 2:
        $hours = "09.00||09.00||09.00||09.00||09.00||09.00||///"
            . "21.00||21.00||21.00||21.00||21.00||20.00||";
        break;
    case 3:
        $hours = "09.00||09.00||09.00||09.00||09.00||||///"
            . "17.00||17.00||17.00||17.00||17.00||||";
        break;
    case 4:
        $hours = "00.00||00.00||00.00||00.00||00.00||00.00||00.00///"
            . "24.00||24.00||24.00||24.00||24.00||24.00||24.00";
        break;
        
}

$t_workinghours = new arrayControl("t_workinghours", $hours, 7);
$t_workinghours->setColumnNames(array("ΔΕ", "ΤΡ", "ΤΕ", "ΠΕ", "ΠΑ", "ΣΑ", "ΚΥ"));
$t_workinghours->setColumnWidths(array(10,10,10,10,10,10,10));
$t_workinghours->getControl();

?>