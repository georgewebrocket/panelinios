<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("USER_PROFILES",$lang,$db1);

$id = $_GET['id'];
$userpofile = new USER_PROFILES($db1,$id);
$val = array(1=>0,0,0,0,0,0,0,0,0,0);
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $err = 0;
    if ($_POST['txtDescription'] == ""){
        $err = 1;
        $msg .= $l->l('blank_description')."</br>";
    }
    for ($i = 1; $i < count($val) + 1; $i++){
        if(isset($_POST['chkFlag'.$i])) {$val[$i] = 1;}else{$val[$i] = 0;        
        }
    }
    
    if ($err==0) {        
        $userpofile->set_description($_POST['txtDescription']);
        $userpofile->set_flag1($val[1]);
        $userpofile->set_flag2($val[2]);
        $userpofile->set_flag3($val[3]);
        $userpofile->set_flag4($val[4]);
        $userpofile->set_flag5($val[5]);
        $userpofile->set_flag6($val[6]);
        $userpofile->set_flag7($val[7]);
        $userpofile->set_flag8($val[8]);
        $userpofile->set_flag9($val[9]);
        $userpofile->set_flag10($val[10]);
                
        if ($userpofile->Savedata()) {
            $msg .= $lg->l('ok')."<br/>"; //...........
            $id = $userpofile->get_id();
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
                
        <h1><?php echo $l->l("form_userprofile"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        <form action="edituserprofile.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
            
            <?php 
            //fullname
            $txtDescription = new textbox("txtDescription", $l->l('list_description'),$userpofile->get_description(), $lg->l('required-field'));
            $txtDescription->get_Textbox();
            //flag1
            $chkFlag1 = new checkbox("chkFlag1", $l->l('list_flag1'), $userpofile->get_flag1());
            $chkFlag1->get_Checkbox();
            //flag2
            $chkFlag2 = new checkbox("chkFlag2", $l->l('list_flag2'), $userpofile->get_flag2());
            $chkFlag2->get_Checkbox();
            //flag3
            $chkFlag3 = new checkbox("chkFlag3", $l->l('list_flag3'), $userpofile->get_flag3());
            $chkFlag3->get_Checkbox();
            //flag4
            $chkFlag4 = new checkbox("chkFlag4", $l->l('list_flag4'), $userpofile->get_flag4());
            $chkFlag4->get_Checkbox();
            //flag5
            $chkFlag5 = new checkbox("chkFlag5", $l->l('list_flag5'), $userpofile->get_flag5());
            $chkFlag5->get_Checkbox();
            //flag6
            $chkFlag6 = new checkbox("chkFlag6", $l->l('list_flag6'), $userpofile->get_flag6());
            $chkFlag6->get_Checkbox();
            //flag7
            $chkFlag7 = new checkbox("chkFlag7", $l->l('list_flag7'), $userpofile->get_flag7());
            $chkFlag7->get_Checkbox();
            //flag8
            $chkFlag8 = new checkbox("chkFlag8", $l->l('list_flag8'), $userpofile->get_flag8());
            $chkFlag8->get_Checkbox();
            //flag9
            $chkFlag9 = new checkbox("chkFlag9", $l->l('list_flag9'), $userpofile->get_flag9());
            $chkFlag9->get_Checkbox();
            //flag10
            $chkFlag10 = new checkbox("chkFlag10", $l->l('list_flag10'), $userpofile->get_flag10());
            $chkFlag10->get_Checkbox();
            
            
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