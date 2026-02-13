<?php
header('Content-Type: text/html; charset=utf-8');
$companyonlineid = $_REQUEST['id'];
$simple = file_get_contents("http://www.epagelmatias.gr/ws/getCompany.php?companyid=$companyonlineid");

/*
$p = xml_parser_create();
xml_parse_into_struct($p, $simple, $vals, $index);
xml_parser_free($p);

echo "Index array\n";
print_r($index);

echo "\r\n\r\n";


echo "\nVals array\n";
print_r($vals);
*/

/*
$xml = simplexml_load_string($simple, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
print_r($array);*/


function XML2Array(SimpleXMLElement $parent)
{
	$array = array();

	foreach ($parent as $name => $element) {
		($node = & $array[$name])
		&& (1 === count($node) ? $node = array($node) : 1)
		&& $node = & $node[];

		$node = $element->count() ? XML2Array($element) : trim($element);
	}

	return $array;
}

$xml   = simplexml_load_string($simple);
$array = XML2Array($xml);
$array = array($xml->getName() => $array);
print_r($array);

?>