<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$companyStatus = new COMPANIES_STATUS($db1,$id);
$companyid = $companyStatus->get_companyid();
$msg = "";

if (isset($_GET['save']) && $_GET['save'] == 1) {
    $companyStatus->set_productcategory($_POST['c_productcategory']);
    $companyStatus->set_status($_POST['c_status']);
    $companyStatus->set_recalldate(textbox::getDate($_POST['t_recalldate'], $locale));
    $companyStatus->set_recalltime($_POST['c_recalltime']);
    $companyStatus->set_userid($_POST['c_userid']);
    $companyStatus->set_csdatetime(textbox::getDate($_POST['t_csdatetime'], $locale));
    
    if ($companyStatus->Savedata()) {
        $msg = "Τα στοιχεία αποθηκεύτηκαν";
        $company = new COMPANIES($db1, $companyStatus->get_companyid());
        include "updateCompanyData.php";
    }
    else {
        $msg = "Παρουσιάσθηκε σφάλμα κατά την αποθήκευση";
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
    <title>PANELINIOS- CRM</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />
    
    <link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>        
    <script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
    <script type="text/javascript" src="js/code.js"></script>
    
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

    <script>        
        $(function() {
            $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);            
            
            
        });
            
        $("form input").keypress(function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });
            
    </script>
    
    </head>
    
    <body class="form">
        
        <div class="form-container">
            
            <h1>Company Status</h1>
            
            <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
            
            <form action="editCompanyStatus.php?id=<?php echo $id; ?>&save=1&<?php echo $ltoken; ?>" method="POST">
                
                <?php
                
                $c_productcategory = new comboBox("c_productcategory", $db1, 
                        "SELECT * FROM PRODUCT_CATEGORIES", "id",
                        "description", $companyStatus->get_productcategory(), "Κατηγ. προϊόντος");
                
                $c_productcategory->get_comboBox();
                
                $c_status = new comboBox("c_status", $db1, 
                        "SELECT * FROM STATUS", "id",
                        "description", $companyStatus->get_status(), "Status");
                $c_status->get_comboBox();
                
                $t_recalldate = new textbox("t_recalldate", "Recall date", 
                        $companyStatus->get_recalldate());
                $t_recalldate->set_format("DATE");
                $t_recalldate->set_locale($locale);
                $t_recalldate->get_Textbox();
                
                $c_recalltime = new comboBox("c_recalltime", $db1, 
                        "SELECT * FROM TIMES", "id",
                        "description", $companyStatus->get_recalltime(), "Recall time");
                $c_recalltime->get_comboBox();
                
                $c_userid = new comboBox("c_userid", $db1, 
                        "SELECT * FROM USERS", "id",
                        "fullname", $companyStatus->get_userid(), "Χρήστης");
                $c_userid->get_comboBox();
                
                $t_csdatetime = new textbox("t_csdatetime", "Ημερ.", 
                        $companyStatus->get_csdatetime());
                $t_csdatetime->set_format("DATE");
                $t_csdatetime->set_locale($locale);
                $t_csdatetime->get_Textbox();
                
                echo '<div style="clear: both;height:10px"></div>';
                $btnOK = new button("btnOK", "ΑΠΟΘΗΚΕΥΣΗ");
                echo $btnOK->get_button_simple();
                
                ?>
                
                <input onclick="window.parent.location.href = 'editcompany.php?id=<?php echo $companyid ?>#invoices';" type="button"                      
               value="CLOSE &amp; UPDATE" />
                
                <div style="clear: both"></div>
                
            </form>            
            
        </div>
        
        
    </body>
    
</html>