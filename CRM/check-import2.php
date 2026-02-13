<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

function GetNr($string) {
    return preg_replace("/[^0-9]/","",$string);
}

$mytable = "";
$start = 0;
$end = "";
$myField = "";
$myCheckField = "";
$rs = FALSE;

if (isset($_REQUEST['t_table'])) {
    $mytable = $_REQUEST['t_table'];
    $start = $_REQUEST['t_start'];
    $end = $_REQUEST['t_end'];
    $myField = $_REQUEST['t_field'];
    $myCheckField = $_REQUEST['t_checkfield'];

    $sql = "SELECT * FROM `$mytable` WHERE id>=$start AND id<$end";
	//echo $sql;
    $rs = $db1->getRS($sql);
    echo count($rs);
}

?>
<html>
<head>
    <title>Check import</title>
</head>
<body>
        
<form action="check-import2.php" method="GET">
    
    TABLE <input type="text" name="t_table" value="<?php echo $mytable; ?>" /><br/><br/>
    START ID <input type="text" name="t_start" value="<?php echo $start; ?>" /><br/><br/>
    STOP ID <input type="text" name="t_end" value="<?php echo $end; ?>" /><br/><br/>
    FIELD <input type="text" name="t_field" value="<?php echo $myField; ?>" /><br/><br/>
    CHECK-FIELD <input type="text" name="t_checkfield" value="<?php echo $myCheckField; ?>" /><br/><br/>
    <input type="submit" value="GO" /><br/><br/><br/>
</form>
        
<?php
if ($rs) {
    echo count($rs);

    echo "<table>";

    for ($i=0;$i<count($rs);$i++) {
        if ($rs[$i][$myField]!="") {
            $phone = $rs[$i][$myField];
            $chkPhone1 = func::vlookup("id", "COMPANIES", "phone1digits LIKE '".$phone."'", $db1);
            if ($chkPhone1>0) {
                echo "<tr><td>".$rs[$i]['id']."</td></tr>";
                if ($myCheckField != '') {
                    $sql = "UPDATE `$mytable` SET $myCheckField = 1 WHERE id = ". $rs[$i]['id'];
                    $res = $db1->getRS($sql);
                }				
            }
            else {
                $chkPhone1 = func::vlookup("id", "COMPANIES", "phone2digits LIKE '".$phone."'", $db1);
                if ($chkPhone1>0) {
                    echo "<tr><td>".$rs[$i]['id']."</td></tr>";
                        if ($myCheckField != '') {
                            $sql = "UPDATE `$mytable` SET $myCheckField = 1 WHERE id = ". $rs[$i]['id'];
                            $res = $db1->getRS($sql);
                        }
                }
                else {
                    $chkPhone1 = func::vlookup("id", "COMPANIES", "mobiledigits LIKE '".$phone."'", $db1);
                    if ($chkPhone1>0) {
                        echo "<tr><td>".$rs[$i]['id']."</td></tr>";
                            if ($myCheckField != '') {
                                $sql = "UPDATE `$mytable` SET $myCheckField = 1 WHERE id = ". $rs[$i]['id'];
                                $res = $db1->getRS($sql);
                            }
                    }					
                    else {
                        $chkPhone1 = func::vlookup("id", "COMPANIES", "faxdigits LIKE '".$phone."'", $db1);
                        if ($chkPhone1>0) {
                            echo "<tr><td>".$rs[$i]['id']."</td></tr>";
                            if ($myCheckField != '') {
                                $sql = "UPDATE `$mytable` SET $myCheckField = 1 WHERE id = ". $rs[$i]['id'];
                                $res = $db1->getRS($sql);
                            }							
                        }
                    }				
                }
            }
        }
    }

    echo "</table>";
    $start =$end;
    $end = $start + 3000; 

    echo <<<EOT
        
    <script>
    
    setTimeout(function(){ window.location.href = "check-import2.php?t_table=$mytable&t_start=$start&t_end=$end&t_field=$myField&t_checkfield=$myCheckField"; }, 3000);
    

    </script>


EOT;
        
    
}
else {
    echo <<<EOT
    <h2>FINISHED!</h2>
    <audio autoplay>
      <source src="tada.ogg" type="audio/ogg">
      <source src="tada.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
    </audio>
    
EOT;
}
        
?>


</body>
</html>
