<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


$sql = "SELECT * FROM EMAIL_TEMPLATES";
$rs = $db1->getRS($sql);
if ($rs) {
    $grid = new datagrid("grid", $db1, "", 
            array("id", "description"), 
            array("ID", "ΠΕΡΙΓΡΑΦΗ"), 
            $ltoken);
    $grid->set_rs($rs);
    $grid->set_edit("editEmailTemplate.php");
    $grid->set_del("delEmailTemplate.php");
}

$myStyle = <<<EOT
<style>
        
    #grid {
        max-width: 800px;
    }
        
</style>
EOT;


include '_theHeader.php';


echo "<h1>Email templates</h1>";

if ($rs) {
    
    $grid->get_datagrid();
    
}

echo "<div class=\"spacer-20\"></div>";

echo "<a class=\"button fancybox\" href=\"editEmailTemplate.php?id=0\">Προσθήκη template</a>";


include './_theFooter.php';


?>
