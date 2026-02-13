<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("USER_PROFILES",$lang,$db1);

$id = $_GET['id'];
$userprofile = new USER_PROFILES($db1,$id);

$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    $sql = "SELECT COUNT(*) AS MyUsers FROM USERS WHERE userprofile=".$id;
    $res = $db1->getRS($sql);

    if ($res[0]['MyUsers']>0) {
        $err = 1;
        $msg .= $l->l("this-userprofile-has-users"). ' : ' . $userprofile->get_description()."<br/>";
    }

    if ($err==1) {
        $msg .= $l->l("cannot-del-userprofile")."<br/>";
    }
    else{    
        if ($userprofile->Delete()) {
            $msg = $l->l("userprofile-deleted"). ' : ' . $userprofile->get_description()."<br/>";
        }
        else {
            $msg = $lg->l("error")."<br/>";
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
            
        <?php if (isset($_GET['confirm']) && $_GET['confirm']==1) { ?>            

        <form action="" method="POST">
        <h1><?php echo $msg; ?></h1>        
        <div class="col-4"></div>
        <div class="col-8">
        <input onclick="window.parent.location.reload(false);" type="button" value="<?php echo $lg->l("close-update"); ?>" /></div>
        <div style="clear: both"></div>
        </form>

        <?php } else { ?>

        <form action="deluserprofile.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">
            <h1>
                <?php echo $l->l("delete-userprofile"). ' : ' . $userprofile->get_description();  ?>
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