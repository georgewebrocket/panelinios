<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$mytable = "CHECKIMPORT";
$myField = "phone";

$sql = "SELECT * FROM `$mytable`";
$rs = $db1->getRS($sql);

echo "<table>";

for ($i=0;$i<count($rs);$i++) {
    echo "<tr>";
    echo "<td>".$rs[$i]['id']."</td>";
    echo "<td>";
    if ($rs[$i][$myField]!="") {
        $phone = str_replace(" ","",$rs[$i][$myField]);
        $phone = str_replace("-","",$phone);
        $phone = str_replace("/","",$phone);
        $chkPhone1 = func::vlookup("id", "COMPANIES", "phone1 LIKE '".$phone."'", $db1);
        if ($chkPhone1>0) {
            //echo $rs[$i]['id']."<br/>";
            echo "X";
        }
        else {
            $chkPhone1 = func::vlookup("id", "COMPANIES", "phone2 LIKE '".$phone."'", $db1);
            if ($chkPhone1>0) {
                //echo $rs[$i]['id']."<br/>";
                echo "X";
            }
            else {
                $chkPhone1 = func::vlookup("id", "COMPANIES", "mobilephone LIKE '".$phone."'", $db1);
                if ($chkPhone1>0) {
                    //echo $rs[$i]['id']."<br/>";
                    echo "X";
                }
            }
        }
        
    }
    echo "</td></tr>";
}

echo "</table>";
