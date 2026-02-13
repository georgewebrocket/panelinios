<?php

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$l = new mlng("COMPANY",$lang,$db1);

$id = $_GET['id'];

$action = new ACTIONS($db1,$id);



$err = 0;

$msg = "";



if (isset($_GET['confirm']) && $_GET['confirm']==1) {

    $delAction = new ACTIONS_DELETED($db1, 0);
    $delAction->set_adatetime($action->get_adatetime());
    $delAction->set_atimestamp($action->get_atimestamp());
    $delAction->set_comment($action->get_comment());
    $delAction->set_company($action->get_company());
    $delAction->set_product_categories($action->get_product_categories());
    $delAction->set_status1($action->get_status1());
    $delAction->set_status2($action->get_status2());
    $delAction->set_user($action->get_user());
    $delAction->set_userDel($_SESSION['user_id']);
    $delAction->Savedata();

    if ($action->Delete()) {
        $msg = $l->l("action-deleted")."<br/>";
    }
    else {
        $msg = $lg->l("error")."<br/>";
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



        <form action="historydel.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">

            <h1>

                <?php echo $l->l("delete-action") . ' : ' . $action->get_id();  ?>

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