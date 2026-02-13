<?php

header('Content-Type: text/html; charset=utf-8');
require_once('php/db.php');

$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

$term = $_GET['term'];

if ($term=="***") {
   $term = ""; 
}

$table = $_GET['table'];
$idfield = $_GET['idfield'];
$descrfield = $_GET['descrfield'];

if ($table=="COMPANIES") {
    $sql = "SELECT * FROM $table WHERE companyname LIKE CONCAT('%',?,'%') OR eponimia LIKE CONCAT('%',?,'%') ORDER BY $descrfield";
    $rs = $db1->getRS($sql, array($term, $term));
}
else if ($table=="CATEGORIES") {
    $sql = "SELECT c.id AS id, c.panel_description AS panel_description FROM CATEGORIES c LEFT JOIN CATEGORIES c2 ON c.id = c2.parentid 
    WHERE c2.id IS NULL AND c.panel_active=1 AND c.panel_description LIKE CONCAT('%',?,'%') ";
    $rs = $db1->getRS($sql, array($term));
}
else {
    $sql = "SELECT * FROM $table WHERE $descrfield LIKE CONCAT('%',?,'%') ORDER BY $descrfield";
    $rs = $db1->getRS($sql, array($term));
}


echo "[";
for ($i=0;$i<count($rs);$i++) {
    echo "{\"id\":\"".$rs[$i][$idfield]."\",";
    echo "\"label\":\"".$rs[$i][$descrfield]."\"}";
    if ($i<count($rs)-1) { echo ","; }
}
echo "]";

//echo '[{"id":"0","label":"'.$term.'"},{"id":"1","label":"Αθήνα"},{"id":"2","label":"Θεσσαλονίκη"},{"id":"3","label":"Λάρισα"},{"id":"4","label":"Καβάλα"}]';

?>