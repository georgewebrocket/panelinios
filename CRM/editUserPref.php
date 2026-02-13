<?php

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/session.php');
require_once 'php/db.php';
require_once 'php/utils.php';
require_once('php/dataobjects.v2.php');
require_once('php/controls.v3.php');
require_once('php/start.v2.php');

$dbo = $db1;
$myHost = "https://crm.panelinios.gr/";

$id = $_GET['id'];
$item = new USERS($dbo, $id);

$canSave = TRUE;
$canDelete = FALSE;

$itemControl = new ITEMCONTROL($dbo, $item, 
        array("id", "fullname", "photo", "sign"), 
        array("ID", "text", "filecontrol", "filecontrol"), 
        array("ID", "Όνομα / Επώνυμο", "Φωτογραφία", "Υπογραφή"),
        "editUserPref.php", 
        $canSave, $canDelete);

$myFolder = "USER-".$id;
if (!file_exists("uploads/$myFolder")) {
    mkdir("uploads/$myFolder", 0777, true);
}
$itemControl->setFieldAttr("photo", array(
"HOST" => $myHost,
"FOLDER" => $myFolder
));        
        
$itemControl->setFieldAttr("sign", array(
  "HOST" => $myHost,
  "FOLDER" => $myFolder
  ));        

$saveRes = $itemControl->SaveItem($_POST);
$delRes = $itemControl->DeleteItem($_GET);

$id = $id==0? $save: $id;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>

<script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />


<style>
    
</style>

</head>

<body>
        
      <div class="padding-20">
          
        <?php $itemControl->ViewItem($saveRes, $delRes); ?>
          
      </div>
      
      <?php include "blocks/modal.php"; ?>
    
</body>

</html>