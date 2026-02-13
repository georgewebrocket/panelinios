<?php
//header('Content-Type: text/html; charset=utf-8');

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("LOGIN",$lang,$db1);



if (isset($_POST['BtnOK']) && $_POST['BtnOK']=="Login") {
	$username = $_POST['TxtUsername'];
	$password = $_POST['TxtPassword'];
	//validate
	$err = 0;
	if ($username=="") {
		$err = 1;	
	}
	if ($password=="") {
		$err = 1;	
	}
	
	if ($err==0) {
		$sql = "SELECT * FROM USERS WHERE active=1 AND  username=? AND password=?";
		$users = $db1->getRS($sql,array($username,$password));
		if ($users) {
                    $user = new USERS($db1,$users[0]['id'],$users);
                    
                    $nowDay = date("N");
                    if (!strpos($user->get_working_days(), $nowDay)) {
                        die("<h1>ΚΑΛΟ ΣΑΒΒΑΤΟΚΥΡΙΑΚΟ. ΘΑ ΤΑ ΠΟΥΜΕ ΤΗΝ ΔΕΥΤΕΡΑ.</h1>");
                    }
                    
                    if ($user->get_time_start()!=0) {
                        
                        //check time
                        $nowHour = date("H");
                        $nowMin = date("i");
                        $nowHM = $nowHour + $nowMin / 60;
                        
                        $userStart = explode(":",func::vlookup("description", 
                                "TIMES", "id=".$user->get_time_start(), 
                                $db1));
                        $userStartHM = (int)$userStart[0] + (int)$userStart[1]/60;
                        
                        $userStop = explode(":",func::vlookup("description", 
                                "TIMES", "id=".$user->get_time_stop(), 
                                $db1));
                        $userStopHM = (int)$userStop[0] + (int)$userStop[1]/60;
                        
                        if ($userStartHM<=$nowHM && $userStopHM>=$nowHM) {
                            //ok
                        }
                        else {
                            die("<h1>ΚΑΛΗ ΞΕΚΟΥΡΑΣΗ. ΘΑ ΤΑ ΠΟΥΜΕ ΑΥΡΙΟ.<h1>");
                        }
                        
                        
                    }
                    
                    
                    session_start(); 
                    $_SESSION['authorized'] = 1;
                    $_SESSION['user_id'] = $user->get_id();
                    $_SESSION['user_fullname'] = $user->get_fullname();
                    $_SESSION['user_profile'] = $user->get_userprofile();
                    $_SESSION['user_access'] = $user->get_useraccess();
                    $_SESSION['user_photo'] = $user->get_photo();
                    $_SESSION['user_sign'] = $user->get_sign();
                    $_SESSION['PANCRM2024_authorized'] = 1;
                    
                    $_SESSION['start'] = time(); // taking now logged in time
                    
                    fn::ulog("Login", $db1);
                    
                    
                    if(!isset($_SESSION['expire'])){
                        $_SESSION['expire'] = $_SESSION['start'] + (1 * 12 * 60 * 60) ; // ending a session in 30 seconds
                    }
                    if (strpos($_SESSION['user_access'],"[1]")!==false) {                   
                            header('Location: home.php');
                    }
                    elseif (strpos($_SESSION['user_access'],"[4]")!==false) {
                            header('Location: courier.php');
                    }
		}
                else
                {
                    $err = $l->l("invalid_data");
                }
	}
	
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS CRM</title>
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />

<style>

* {
    box-sizing: border-box;
}

form.login {
	width:400px;
	/*margin:auto;
	margin-top:100px;*/
	padding:0px;
	border:1px solid rgb(200,200,200);
	border-radius:0px;
	
}

input {
    
    width:100% !important;
    margin-bottom:20px !important;
}



</style>


</head>

<body>
    
    <div style="position:fixed; top:0px; left:0px; right:0px;  padding:15px; background-color: #fff; border-bottom:1px solid #ccc; font-size: 20px; font-weight: 700;">
        PANELINIOS CRM
    </div>
    
    <div style="display:flex; justify-content: center;   align-items: center; height: 100vh; background-color: #ecf2f6;">
        
        <div>
            
            <form style="background-color: #fff;" class="login" action="index.php?login=1" method="post">

                <h1 style="padding: 20px; border-bottom: 1px solid #ccc;margin: 0px; text-align: center; font-weight: 400;">Login to CRM</h1>
                
                <div style="padding:20px 20px 0px">
                    
                    <div class="col-12">Username</div>
                    <div class="col-12">
                    <input name="TxtUsername" type="text" />
                    </div>
                    
                    <div class="col-12">Password</div>
                    <div class="col-12">
                    <input name="TxtPassword" type="password" />
                    </div>
                    
                    <div class="col-12"></div>
                    <div class="col-12">
                    <input style="padding:15px; " name="BtnOK" type="submit" value="Login" />
                    </div>
                    
                    <div style="clear:both"></div>
                    
                </div>
                
            
            </form>
            
        </div>
        
    </div>




</body>
</html>
