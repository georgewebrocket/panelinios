<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("AREAS",$lang,$db1);

$id = $_GET['id'];
$area = new AREAS($db1,$id);
$msg = "";

if(isset($_GET['ParentId'])){
    $area->set_parentid($_GET['ParentId']);
}

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtDescription'] == ""){
        $err = 1;
        $msg .= $l->l('blank_area')."</br>";
    }
    $parentId = $_POST['cParentId'];
    
    if ($err==0) {        
        $area->set_description($_POST['txtDescription']);
        $area->set_parentid($_POST['cParentId']);  
        $sql = "SELECT level, nodes FROM AREAS WHERE id=".$parentId;
        $res = $db1->getRS($sql);
        $level = 1;
        if (is_array($res) && count($res)>0){
            if($res[0]['level'] != ""){
                $level = $res[0]['level'] + 1;
            }                     
            $rs = $db1->execSQL("UPDATE AREAS SET nodes=? WHERE id=?", array(1,$parentId));                  
        }
        $area->set_level($level);
        //****************************
        if ($id==0) {
            $area->set_active(1);
            $area->set_nodes(0);
        }
        
        if ($area->Savedata()) {            
            $id = $area->get_id();            
            $msg .= $lg->l('ok')."<br/>"; //...........
        }
        else {
            $msg .= $lg->l('error')."<br/>".$lg->l('try-again')."<br/>"; //...........
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="form-container">
                
        <h1><?php echo $l->l("form_area"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editArea.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //description
            $txtDescription = new textbox("txtDescription", $l->l('form_area'),$area->get_description(), $lg->l('required-field'));
            $txtDescription->get_Textbox();
            //parentid
            $cParentId = new comboBox("cParentId", $db1, "SELECT id, description FROM AREAS", 
                    "id","description",
                    $area->get_parentid(),
                    $l->l('form_parent_area'));
            $cParentId->get_comboBox();
            
            //submit
            $btnOK = new button("BtnOk", $lg->l('save'));            
            
            echo "<div class=\"col-4\"></div><div class=\"col-8\">";
            $btnOK->get_button_simple();
            $btnCloseUpdate = new button("button", $lg->l("close-update"), "close-update");
            echo "&nbsp;";
            $btnCloseUpdate->get_button_simple();
            echo "</div>";
            
            ?> 
            
            <div style="clear: both;"></div>
            
        </form>
        
    </div>    
</body>
</html>