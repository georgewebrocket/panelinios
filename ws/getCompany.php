<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'php/config.php';
require_once 'php/db.php';
require_once 'php/utils.php';

header('Content-Type: text/html; charset=utf-8');

$dbo = new DB(conn1::$connstr, conn1::$username, conn1::$password);

$companyid = $_REQUEST['companyid'];
$rs = $dbo->getRS("SELECT * FROM companies WHERE id=?", array($companyid));

if ($rs) {
    $rs0 = $rs[0];
    //creating object of SimpleXMLElement
    $xml_company = new SimpleXMLElement("<?xml version=\"1.0\"?><company></company>");

    //function call to convert array to xml
    array_to_xml($rs0,$xml_company);

    //saving generated xml file
    //$xml_file = $xml_company->asXML('users.xml');

    print $xml_company->asXML();
}


//function definition to convert array to xml
function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                $subnode = $xml_user_info->addChild("item$key");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}



?>