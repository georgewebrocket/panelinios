<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("CATEGORIES",$lang,$db1);

$id = $_GET['id'];
$category = new CATEGORIES($db1,$id);
$msg = "";


function UpdateCatSite($category) {
    $url = "https://www.epagelmatias.gr/interface-new-site/setCategory.php";
    $url .= "?id=".$category->get_id();
    $url .= "&description_gr=".urlencode($category->get_description()) ;
    $url .= "&description_en=".urlencode($category->get_description_en());
    //echo $category->get_parentid();
    $url .= "&parentid=".urlencode($category->get_parentid());
    $url .= "&level=".urlencode($category->get_level());
    $url .= "&path=".urlencode($category->get_path());
    $url .= "&active=".urlencode($category->get_active());
    $url .= "&nodes=".urlencode($category->get_nodes());
    $url .= "&seotitle=".urlencode($category->get_seo_title());
    $url .= "&seodescription=".urlencode($category->get_seo_description());
    $url .= "&seourl=".urlencode($category->get_seo_url());
    $url .= "&photo=".urlencode($category->get_photo());
    $url .= "&icon=".urlencode($category->get_icon());
    $url .= "&misspellings=".urlencode($category->get_misspellings());
    $url .= "&panel_photo=".urlencode($category->get_panel_photo());
    $url .= "&panel_active=".urlencode($category->get_panel_active());
    $url .= "&panel_description=".urlencode($category->get_panel_description());
    $url .= "&panel_url=".urlencode($category->get_panel_url());
    $url .= "&panel_comment=".urlencode($category->get_panel_comment());

    //echo $url;

    $result = "";
    $result = file_get_contents($url);
    //echo "RESULT=".$result;
    if ($result=="OK") { 
        return TRUE;             
    } else {
        echo $result;
        return FALSE;
    }
}



if(isset($_GET['ParentId'])){
    $category->set_parentid($_GET['ParentId']);
}

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtDescription'] == ""){
        $err = 1;
        $msg .= $l->l('blank_category')."</br>";
    }
    $parentId = $_POST['cParentId'];
    
    if ($err==0) {        
        $category->set_description($_POST['txtDescription']);
        $category->set_description_en($_POST['t_description_en']);
        $category->set_parentid($_POST['cParentId']);  
        $sql = "SELECT level, nodes, path FROM CATEGORIES WHERE id=".$parentId;
        $res = $db1->getRS($sql);
        $level = 1;
        $path = "";
        if (is_array($res) && count($res)>0){
            if($res[0]['level'] != ""){
                $level = $res[0]['level'] + 1;
            }                     
            $rs = $db1->execSQL("UPDATE CATEGORIES SET nodes=? WHERE id=?", array(1,$parentId));
            $path = $res[0]['path'];
        }
        $category->set_level($level);
        
        $category->set_seo_title($_POST['t_seo_title']);
        $category->set_seo_description($_POST['t_seo_description']);
        $category->set_seo_url($_POST['t_seo_url']);
        
        $category->set_photo($_POST['t_photo']);
        $category->set_icon($_POST['t_icon']);
        
        $category->set_panel_photo($_POST['t_panel_photo']);
        $category->set_panel_active(checkbox::getVal2($_POST, 'chk_panel_active'));
        $category->set_panel_description($_POST['t_panel_description']);
        $category->set_panel_url($_POST['t_panel_url']);
        $category->set_panel_comment($_POST['t_panel_comment']);
        
        
        //****************************
        if ($id==0) {
            $category->set_active(1);
            $category->set_nodes(0);
            $category->set_path("");
        }
        
        if ($category->Savedata()) {            
            $id = $category->get_id();  
            //if($path != ""){$path .= ",".$id; }else{$path = $id;}
            if($path != ""){$path = trim($path) . trim($id)."-"; }else{$path = "-".$id."-";}
            $category->set_path($path);
            
            
            
            $category->Savedata();
            $msg .= $lg->l('ok')."<br/>"; //...........
            
            if (UpdateCatSite($category)) {
                $msg .= "<br/>Website updated successfully.<br/>";
            }
            else {
                $msg .= "<br/>Error in Website update.<br/>";
            }
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


<style>
    
    #cParentId {
        max-width:95%;
    }
    
    #t_description_en {
        height: 150px;
    }
    
    
</style>


</head>

<body class="form">
    <div class="form-container">
                
        <h1><?php echo $l->l("form_category"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="editCategory.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //description
            $txtDescription = new textbox("txtDescription", "NAME",$category->get_description(), $lg->l('required-field'));
            $txtDescription->get_Textbox();
            
            //full description //uses t_description_en field           
            $t_description_en = new textbox("t_description_en", $l->l('FULL DESCRIPTION'),$category->get_description_en(), $lg->l('required-field'));
            $t_description_en->set_multiline();
            $t_description_en->get_Textbox();
            
            
            //parentid
            $cParentId = new comboBox("cParentId", $db1, "SELECT id, description FROM CATEGORIES", 
                    "id","description",
                    $category->get_parentid(),
                    $l->l('form_parent_category'));
            $cParentId->get_comboBox();
            
            
            //seo_title
            $t_seo_title = new textbox("t_seo_title", "SEO-TITLE",$category->get_seo_title(), "");
            $t_seo_title->get_Textbox();
            
            //seo_description
            $t_seo_description = new textbox("t_seo_description", "SEO-DESCRIPTION", 
                    $category->get_seo_description(), "");
            $t_seo_description->set_multiline();
            $t_seo_description->get_Textbox();
            
            //seo_title
            $t_seo_url = new textbox("t_seo_url", "SEO-URL", $category->get_seo_url(), "");
            $t_seo_url->get_Textbox();
            
            //photo
            $t_photo = new textbox("t_photo", "PHOTO", $category->get_photo(), "");
            $t_photo->get_Textbox();
            
            if ($category->get_photo()!="") {
                $photo = $category->get_photo();
                echo "<div class=\"col-4\"></div><div class=\"col-8\"><img src=\"$photo\" style=\"height:200px; width:auto\" /></div>";
                echo "<div class=\"spacer-20\"></div>";
            }
            
            //icon
            $t_icon = new textbox("t_icon", "ICON", $category->get_icon(), "");
            $t_icon->get_Textbox();
            
            echo "<div class=\"spacer-50\"></div>";
            echo "<hr/>";
            echo "<div class=\"spacer-50\"></div>";
            
            echo "<h2 style=\"padding-left:0px\">PANELINIOS</h2>";
            echo "<div class=\"spacer-20\"></div>";
            
            
            //panelinios description
            $t_panel_description = new textbox("t_panel_description", "Description", $category->get_panel_description());
            $t_panel_description->get_Textbox();
            
            
            //panelinios SEO URL
            $t_panel_url = new textbox("t_panel_url", "SEO URL", $category->get_panel_url());
            $t_panel_url->get_Textbox();
            
            
            //panelphoto
            $t_panel_photo = new textbox("t_panel_photo", "Φωτο ", $category->get_panel_photo(), "");
            $t_panel_photo->get_Textbox();
            if ($category->get_panel_photo()!="") {
                $photo = $category->get_panel_photo();
                echo "<div class=\"col-4\"></div><div class=\"col-8\"><img src=\"$photo\" style=\"height:200px; width:auto\" /></div>";
                echo "<div class=\"spacer-20\"></div>";
            }
            
            //panelinios active
            $chk_panel_active = new checkbox("chk_panel_active", "Active", $category->get_panel_active());
            $chk_panel_active->get_Checkbox();
            
            
            
            //panelinios text
            $t_panel_comment = new textbox("t_panel_comment", "Comment", $category->get_panel_comment());
            $t_panel_comment->set_multiline();
            $t_panel_comment->get_Textbox();
            
            
            
            //submit
            $btnOK = new button("BtnOk", $lg->l('save'));            
            
            echo "<div class=\"col-4\"></div><div class=\"col-8\">";
            $btnOK->get_button_simple();
            $btnCloseUpdate = new button("button", $lg->l("close-update"), "close-update");
            echo "&nbsp;";
            $btnCloseUpdate->get_button_simple();
            echo "</div>";
            
            ?> 
            
            <div style="clear: both;height: 50px"></div>
            
        </form>
        
    </div>    
</body>
</html>