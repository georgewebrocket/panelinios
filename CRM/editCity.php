<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);


require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$city = new EP_CITIES($db1,$id);
$description = "";
$msg = "";
$url = "https://www.epagelmatias.gr/interface/crud.php";
define("DELIM", "[/$$/]");

$onlineid = 0;
$description1 = "";
$description2 = "";
$seo_url = "";

if ($id>0) {
    $data = array('table' => "cities",
        'mode' => "R",
        'id' => $id);
        
    $data['username'] = conn1::$username;
    $data['password'] = conn1::$password; 
    
    //var_dump($data);
        
    $result = func::postDataWithCurl($url, $data);
    $obj = json_decode($result);
    $rsWeb = (array) $obj[0];
    //var_dump($rsWeb);
    if ($rsWeb!=="false") {
        $onlineid = $rsWeb['id'];
        $description1 = $rsWeb['description'];
        $description2 = $rsWeb['description2'];
        $seo_url = $rsWeb['seo_url'];
    }
}


if ($_POST) {
    $description = $_POST['t_description'];
    $description1 = $_POST['t_description1'];
    $description2 = $_POST['t_description2'];
    $seo_url = $_POST['t_seo_url'];
    $city->set_description($description);
    if ($city->Savedata()) {
        //update tables in website
        if ($onlineid==0) { //insert records in website
            //echo "inserting...";
            //cities
            $data = array("table" => "cities",
                "mode" => "I",
                "fields" => "id".DELIM."description".DELIM."description2".DELIM."seo_url",
                "values" => $city->get_id().DELIM.$description1.DELIM.$description2.DELIM.$seo_url);
                
            $data['username'] = conn1::$username;
            $data['password'] = conn1::$password; 
                
            $res = func::postDataWithCurl($url, $data);
            var_dump($res);
            //areascities
            $data = array("table" => "areascities",
                "mode" => "I",
                "fields" => "description".DELIM."description2".DELIM."areaorcity".DELIM."id2",
                "values" => $description1.DELIM.$description2.DELIM."2".DELIM.$city->get_id());
                
            $data['username'] = conn1::$username;
            $data['password'] = conn1::$password;                 
                
            $res = func::postDataWithCurl($url, $data);
            //var_dump($res);
                      
        }
        else { //update records in website
            //cities
            $data = array("table" => "cities",
                "mode" => "U",
                "idValue" => $city->get_id(),
                "fields" => "description".DELIM."description2".DELIM."seo_url",
                "values" => $description1.DELIM.$description2.DELIM.$seo_url);
            
            $data['username'] = conn1::$username;
            $data['password'] = conn1::$password; 
            
            $res = func::postDataWithCurl($url, $data);
            //var_dump($res);
            
            //areascities
            $data = array("table" => "areascities",
                "mode" => "U",
                "idField" => "id2",
                "idValue" => $city->get_id(),
                "fields" => "description".DELIM."description2",
                "values" => $description1.DELIM.$description2);
            $res = func::postDataWithCurl($url, $data);
            //var_dump($res);
        }
        
        $id = $city->get_id();
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
    
    <h1>Edit City</h1>   
    <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
    
    <form action="editCity.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
        
        <?php
        
        $t_id = new textbox("t_id", "ID", $city->get_id());
        $t_id->set_disabled();
        $t_id->get_Textbox();
        
        $t_description = new textbox("t_description", "ΟΝΟΜΑΣΙΑ", $city->get_description());
        $t_description->get_Textbox();
        
        $t_description1 = new textbox("t_description1", "ΟΝΟΜΑΣΙΑ 1", 
                $description1);
        $t_description1->get_Textbox();
        
        $t_description2 = new textbox("t_description2", "ΟΝΟΜΑΣΙΑ 2", 
                $description2);
        $t_description2->get_Textbox();
        
        $t_seo_url = new textbox("t_seo_url", "SEO-URL", 
                $seo_url);
        $t_seo_url->get_Textbox();
        
        $btnOK = new button("BtnOk", "ΑΠΟΘΗΚΕΥΣΗ");
        $btnOK->get_button();
        
        ?>
        
        <div class="clear"></div>
        
    </form>
    
</div>
</body>
</html>    