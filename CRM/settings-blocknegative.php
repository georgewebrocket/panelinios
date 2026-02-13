<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('inc.php');

$blockstatusnegative = func::vlookup("keyvalue", "SETTINGS", "keycode='BLOCK-STATUS-NEGATIVE'", $db1);

if (isset($_POST['btnchange'])) {
	$newval = $blockstatusnegative==1? 0: 1;
	$sql = "UPDATE SETTINGS SET keyvalue='$newval' WHERE keycode='BLOCK-STATUS-NEGATIVE'";
	//echo $sql;
	$db1->execSQL($sql);
}

$blockstatusnegative = func::vlookup("keyvalue", "SETTINGS", "keycode='BLOCK-STATUS-NEGATIVE'", $db1);

?>
<html>
<head>
<title></title>
</head>
<body>

<div style="text-align:center; padding-top:50px">

<?php
if ($blockstatusnegative==1) {
	echo "<h1 style=\"color:red\">Οι καταχωρήσεις με status αρνητικός είναι κλειδωμένες για τους απλούς χρήστες</h1>";
} else {
	echo "<h1 style=\"color:blue\">Οι καταχωρήσεις με status αρνητικός είναι ανοιχτές για όλους τους χρήστες</h1>";
}
?>

<form name="?" method="post" action="settings-blocknegative.php?commit=1" enctype="application/x-www-form-urlencoded">
	<input name="btnchange" id="btnchange" type="submit" value="ΑΛΛΑΓΗ" />

</form>

</div>

</body>
</html>