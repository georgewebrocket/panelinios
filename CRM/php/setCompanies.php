<?php

require_once('../php/session.php');
require_once('../php/dataobjects.php');
require_once('../php/controls.php');
require_once('../inc.php');

$id = $_GET['id'];
$catalogueid = $_GET['catalogueid'];
//$fields = $_GET['fields'];
//$values = $_GET['values'];
//
//$arFields = explode("///",$fields);
//$arValues = explode("///",$values);
$arFields = array();
$arValues = array();
$arrFieldsValues = $_SESSION['FieldsValues'];
foreach($arrFieldsValues as $values){
    array_push($arFields, $values[0]);
    array_push($arValues, $values[1]);
}
$vals = "";
$rs = "";

if ($id==0) {
    $sql = "INSERT INTO COMPANIES (";
    for ($i=0;$i<count($arFields);$i++) {
        $sql .= $arFields[$i];
        $vals .= "?";
        if ($i<count($arFields)-1) {
            $sql .= ", ";
            $vals .= ", ";
        }
    }
    $sql .= ") VALUES (".$vals.")";
    $res = $db1->execSQL($sql, $arValues);
    if($res > 0){
        $err = "ok";
        $id = $res; 
    }else{
        $err = "err";
        $id = 0;  
    }
}
else {
    $sql = "UPDATE COMPANIES SET ";
    for ($i=0;$i<count($arFields);$i++) {
        $sql .= $arFields[$i]."=?";
        if ($i<count($arFields)-1) {
            $sql .= ", ";
        }        
    }
    $sql .= " WHERE id=".$id;
    $res = $db1->execSQL($sql, $arValues);
    if($res === FALSE){
        $err = "err";        
    }else{
        $err = "ok";            
    }
}
header('Location: ../editCompanyListing.php?companyListing='.$err.'&catalogueid='.$catalogueid.'&id='.$id);



?>