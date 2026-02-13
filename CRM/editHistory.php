<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$action = new ACTIONS($db1, $id);
$msg = "";

if (isset($_GET['save'])) {
    $action->set_comment($_POST['t_comment']);
    
    if ($action->Savedata()) {
        $msg = "DATA WAS SAVED";
    }
    else {
        $msg = "ERROR";
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
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />

    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
            });
            
        

    </script>
    
    </head>
    
    <body class="form">
        
        <div class="form-container">
            
            <h1>Edit action #<?php echo $id; ?></h1>
            
            <form action="editHistory.php?id=<?php echo $id; ?>&save=1" method="POST">
                
                <?php
                
                $t_comment = new textbox("t_comment", "ΣΧΟΛΙΑ", 
                        $action->get_comment(), ""); 
                $t_comment->set_multiline();
                $t_comment->get_Textbox();
                
                $btnOK = new button("btnOK", "ΑΠΟΘΗΚΕΥΣΗ");
                echo $btnOK->get_button_simple();
                
                echo "&nbsp;";
                
                $btnCloseUpdate = new button("btnCloseUpdate", "CLOSE &amp; UPDATE", "close-update");
                echo $btnCloseUpdate->get_button_simple();
                
                ?>   
                
                <div class="clear"></div>
            
            </form>
            
        </div>
        
    </body>
    
</html>