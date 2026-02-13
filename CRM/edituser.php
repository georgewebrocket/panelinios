<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("USERS",$lang,$db1);

$id = $_GET['id'];
$user = new USERS($db1,$id);
$val = 0;
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtFullname'] == ""){
        $err = 1;
        $msg .= $l->l('blank_fullname')."</br>";
    }
    if ($_POST['txtUsername'] == ""){
        $err = 1;
        $msg .= $l->l('blank_username')."</br>";
    }
    if ($_POST['txtPassword'] == ""){
        $err = 1;
        $msg .= $l->l('blank_password')."</br>";
    }
    if(isset($_POST['chkActive'])) {
        $val = 1;
    }
    else{
        $val = 0;
    } 
    
    $is_agent = isset($_POST['chk_is_agent'])? 1: 0;
    
    if ($err==0) {        
        $user->set_fullname($_POST['txtFullname']);
        $user->set_username($_POST['txtUsername']);
        $user->set_password($_POST['txtPassword']);
        $user->set_userprofile($_POST['cProfile']); 
		$user->set_useraccess($_POST['txtUseraccess']);
        $user->set_active($val);
        $user->set_is_agent($is_agent);
        
        //t_costperhour
        $user->set_costperhour(textbox::getCurrency($_POST['t_costperhour'], $locale));
        
        $user->set_time_start($_POST['t_time_start']);
        $user->set_time_stop($_POST['t_time_stop']);
        
        $user->set_working_days(selectList::getVal('t_working_days', $_POST));
        
        
        if ($id == 0){
            //new user
            $user->set_active(1);  
            $user->set_photo("");  
            $user->set_sign("");            
        } 
        
        if ($user->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $user->get_id();
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
    
    #t_working_days .col-10,
    #t_working_days .col-2 {
        border-top:1px dotted rgb(220,220,220);
        padding-top:5px;
    }
    
    
</style>


</head>

<body class="form">
    <div class="form-container">
                
        <h1><?php echo $l->l("form_user"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="edituser.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //fullname
            $txtFullname = new textbox("txtFullname", $l->l('list_fullname'),$user->get_fullname(), $lg->l('required-field'));
            $txtFullname->get_Textbox();
            //username
            $txtUsername = new textbox("txtUsername", $l->l('list_username'),$user->get_username(), $lg->l('required-field'));
            $txtUsername->get_Textbox();
            //username
            $txtPassword = new textbox("txtPassword", $l->l('list_password'),$user->get_password(), $lg->l('required-field'));
            $txtPassword->set_type("password");
            $txtPassword->get_Textbox();
            //active
            $chkActive = new checkbox("chkActive", $l->l('list_active'), $user->get_active());
            $chkActive->get_Checkbox();
            
            //is agent
            $chk_is_agent = new checkbox("chk_is_agent", "Agent", $user->get_is_agent());
            $chk_is_agent->get_Checkbox();
            
            
            //profile
            $cProfile = new comboBox("cProfile", $db1, "SELECT id, description FROM USER_PROFILES", 
                    "id","description",
                    $user->get_userprofile(),
                    $l->l('list_profile'));
            $cProfile->get_comboBox();
			
			//txtUseraccess
            $txtUseraccess = new textbox("txtUseraccess", $l->l('user-access'),$user->get_useraccess(), $lg->l('p.x. [1],[5]'));
            $txtUseraccess->get_Textbox();
            
            $t_costperhour = new textbox("t_costperhour", "Ωρομίσθιο", $user->get_costperhour());
            $t_costperhour->set_format("CURRENCY");
            $t_costperhour->get_Textbox();
            
            
            
            $t_working_days = new selectList("t_working_days", "WEEK_DAYS", $user->get_working_days(), $db1);
            //$c_listUsers->set_criteria("active=1 AND is_agent=1");
            $t_working_days->set_descrField("description");
            $t_working_days->set_orderby("id");
            echo "<div style=\"clear:both;height:10px\"></div>";
            echo "<div class=\"col-4\">ΕΡΓΑΣΙΜΕΣ ΗΜΕΡΕΣ</div>"
            . "<div class=\"col-8\">";
            echo $t_working_days->getSimpleList();
            echo "</div>";
            
            
            
            
            $t_time_start = new comboBox("t_time_start", $db1, 
                    "SELECT id, description FROM TIMES",
                    "id", "description",
                    $user->get_time_start(), 
                    "ΠΡΟΣΒΑΣΗ ΑΠΟ");
            $t_time_start->get_comboBox();
            
            $t_time_stop = new comboBox("t_time_stop", $db1, 
                    "SELECT id, description FROM TIMES",
                    "id", "description",
                    $user->get_time_stop(), 
                    "ΕΩΣ");
            $t_time_stop->get_comboBox();
            
            
            
            
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