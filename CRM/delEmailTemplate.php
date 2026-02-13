<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$emailTempl = new EMAIL_TEMPLATES($db1,$id);
$templateName = $emailTempl->get_description();

$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    if ($emailTempl->Delete()) {
        $msg = "Το template \"$templateName\" διαγράφτηκε<br/>";
        
    }
    else {
        $msg = "Error<br/>";
    }    
}

include "_thePopupHeader.php";

if (isset($_GET['confirm']) && $_GET['confirm']==1) { 
    
?>

<form action="" method="POST">
    <h1><?php echo $msg; ?></h1>        
    <div class="col-4"></div>
    <div class="col-8">
    <input onclick="window.parent.location.reload(false);" type="button" 
           value="Close and Update" />
    </div>
    <div style="clear: both"></div>
</form>
    
<?php } else { ?>

<form action="delEmailTemplate.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">
    <h1>Διαγραφή template "<?php echo $templateName; ?>";</h1>
    <div class="col-4"></div>
    <div class="col-8">
    <input type="submit" value="Confirm" /></div>
    <div style="clear: both"></div>
</form>

<?php 


}


include "_thePopupFooter.php";