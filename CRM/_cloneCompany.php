<?php


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$parentId = $_REQUEST['id'];

$child = new COMPANIES($db1, $parentId);

//set id=0 to insert new record
$child->set_id(0);

$child->set_log_code("");
$child->set_parent_record($parentId);

//....

$child->Savedata();
$newId = $child->get_id();

$parent = new COMPANIES($db1, $parentId);
$parent->set_child_record($newId);
$parent->Savedata();

echo "<h2>Record was cloned</h2>";
echo "<a class=\"button\" href=\"editcompany.php?id=$newId\">Open new record</a>";