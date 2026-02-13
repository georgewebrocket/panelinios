<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("CATEGORIES",$lang,$db1);

$id = $_GET['id'];
$category = new CATEGORIES($db1,$id);

$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    $sql = "SELECT COUNT(*) AS MyCategory FROM CATEGORIES WHERE parentid=".$id;
    $res = $db1->getRS($sql);

    if ($res[0]['MyCategory']>0) {
        $err = 1;
        $msg .= $l->l("this-category-has-subcategories")."<br/>";
    }

    if ($err==1) {
        $msg .= $l->l("cannot-del-category").' : '.$category->get_description()."<br/>";
    }
    else {
        if ($category->Delete()) {
            $msg = $l->l("category-deleted")."<br/>";
        }
        else {
            $msg = $lg->l("error")."<br/>";
        }
    }
    
    //update site 
    // $url = "http://www.epagelmatias.gr/interface-new-site/setCategory.php";
    // $url .= "?id=".$id;
    // $url .= "&del=1";
    
    // $result = "";
    // $result = file_get_contents($url);
    // if ($result=="OK") { 
    //     $msg .= "Website update successfully";             
    // } else {
    //     $msg .= "Error in website update";
    // }
    
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="form-container">
            
        <?php if (isset($_GET['confirm']) && $_GET['confirm']==1) { ?>            

        <form action="" method="POST">
        <h1><?php echo $msg; ?></h1>        
        <div class="col-4"></div>
        <div class="col-8">
        <input onclick="window.parent.location.reload(false);" type="button" value="<?php echo $lg->l("close-update"); ?>" /></div>
        <div style="clear: both"></div>
        </form>

        <?php } else { ?>

        <form action="delCategory.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">
            <h1>
                <?php echo $l->l("delete-category") . ' : ' . $category->get_description(); ?>
            </h1>
        <div class="col-4"></div>
        <div class="col-8">
        <input type="submit" value="<?php echo $lg->l("confirm"); ?>" /></div>
        <div style="clear: both"></div>
        </form>

        <?php } ?>
    </div>
</body>
</html>