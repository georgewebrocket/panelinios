<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_GET['id'];
$city = new EP_CITIES($db1,$id);
$cityName = $city->get_description();

$url = "https://www.epagelmatias.gr/interface/crud.php";
define("DELIM", "[/$$/]");

$err = 0;
$msg = "";

if (isset($_GET['confirm']) && $_GET['confirm']==1) {
    if ($city->Delete()) {
        $msg = "Η πόλη $cityName διαγράφτηκε<br/>";
        //cities
        $data = array('table' => "cities",
            'mode' => "D",
            'fields' => "id",
            'values' => $id);
        $res = func::postData($url, $data);
        //....
        $data = array('table' => "areascities",
            'mode' => "D",
            'fields' => "id2",
            'values' => $id);
        $res = func::postData($url, $data); 
    }
    else {
        $msg = "Error<br/>";
    }    
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<?php include "blocks/head.php"; ?>


</head>
    
<body class="form">
<div class="form-container">
    
<?php if (isset($_GET['confirm']) && $_GET['confirm']==1) { ?>

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

<form action="delCity.php?id=<?php echo $id; ?>&confirm=1&<?php echo $ltoken; ?>" method="POST">
    <h1>Διαγραφή πόλης "<?php echo $cityName; ?>";</h1>
    <div class="col-4"></div>
    <div class="col-8">
    <input type="submit" value="Confirm" /></div>
    <div style="clear: both"></div>
</form>

<?php } ?>    
    
    
</div>
</body>
</html>