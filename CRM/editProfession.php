<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$profession = new PROFESSIONS($db1, $id);

$url = "https://www.epagelmatias.gr/interface/crud.php";
define("DELIM", "[/$$/]");

$idOnline = 0;
$description = "";
$msg = "";

if ($id>0 && FALSE) {
    $data = array('table' => "professions",
        'mode' => "R", /*READ*/
        'id' => $id);
    $rsWeb = func::curlPostData($url, $data); 
    //var_dump($rsWeb);
    if ($rsWeb) {
        $idOnline = $rsWeb['id'];
        $description = $rsWeb['description'];
    }
    else {
        echo "Online record N/A";
    }
}

if ($_POST) {
    
    $description = $_POST['t_description'];
    
    $profession->set_description($description);
    
    if ($profession->Savedata()) {
        //update table in website
        //echo "idOnline=$idOnline";
        if ($idOnline==0) { //insert record
            $data = array(
                "username" => conn1::$username,
                "password" => conn1::$password,
                "table" => "professions",
                "mode" => "I",
                "fields" => "id".DELIM."description",
                "values" => $profession->get_id().DELIM.$description
            );
            //var_dump($data);
            $res = func::postDataWithCurl($url, $data);
            //var_dump($res);
        }
        else { //update record
            $data = array("table" => "professions",
                "mode" => "U",
                "idValue" => $profession->get_id(),
                "fields" => "description",
                "values" => $description);
            $res = func::postData($url, $data);
            //echo $res;
        }
        
        $id = $profession->get_id();
        if ($id>0) {
            $msg = "Τα δεδομένα αποθηκεύτηκαν";
        }
        else {
            $msg = "Παρουσιάσθηκε σφάλμα";
        }
    }
    else {
        $msg = "Παρουσιάσθηκε σφάλμα";
    }
    
    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<?php include "blocks/head.php"; ?>

</head>
<body class="form">
<div class="form-container">
    
    <h1>Edit Profession</h1>   
    <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
    
    <form action="editProfession.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
        
        <?php
        
        $t_id = new textbox("t_id", "ID", $profession->get_id());
        $t_id->set_disabled();
        $t_id->get_Textbox();
        
        $t_description = new textbox("t_description", "ΠΕΡΙΓΡΑΦΗ", $profession->get_description());
        $t_description->get_Textbox();
        
        
        
        $btnOK = new button("BtnOk", "ΑΠΟΘΗΚΕΥΣΗ");
        $btnOK->get_button();
        
        ?>
        
        <div class="clear"></div>
        
    </form>
    
</div>
</body>
</html>  