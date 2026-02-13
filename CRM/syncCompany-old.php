<?php

//require_once('php/session.php');
require_once('php/db.php');
require_once('php/utils.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');

header('Access-Control-Allow-Origin: *');

$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

$companyid = $_REQUEST['companyid'];
$crm = new COMPANIES($db1, $companyid);
if (isset($_REQUEST['newonlineid'])) {
	$crm->set_catalogueid($_REQUEST['newonlineid']);
	$crm->set_onlinestatus(1);
	$crm->Savedata();
}

$companyonlineid = $crm->get_catalogueid();
if ($companyonlineid>0) {
	$str = file_get_contents("http://epagelmatias.gr/ws/getCompany.php?companyid=$companyonlineid");
	$xml   = simplexml_load_string($str);
	$array = func::XML2Array($xml);
	$online = array($xml->getName() => $array);
}


function createCompanySet($companyCRM, $companyOnline, $crmField, $onlineField, 
		$crmDateField, $onlineDateField, $description, 
		$dbo = "", $table="", $idField = "id", $descriptionField = "description",
		$type = "text") {
	
	$crmValue = $companyCRM->get_field($crmField);
	$crmDate = $crmDateField!=''? $companyCRM->get_field($crmDateField): "";
	$crmPopup = $crmValue;
	
	if ($type=="package") {
		$crmValue = func::vlookup("online_package", "PACKAGES", "id=$crmValue", $dbo);
	}
	
	if ($table!="") {
		$crmShort = func::vlookup($descriptionField, $table, "$idField=$crmValue", $dbo);
	}
	elseif ($type=="date") {
		$crmShort = func::str14toDate($crmValue);
	}
	elseif ($type=="html") {
	    $crmValueStripped = strip_tags($crmValue);
	    $crmShort = mb_substr($crmValueStripped, 0, 70);
	    if (mb_strlen($crmValueStripped)>70) {
	        $crmShort .= "...";
	    }	    
	    $crmPopup = $crmValueStripped;
	}
	elseif ($type=="password") {
		$crmShort = "******";
	}
	else {
		$crmShort = mb_substr($crmValue, 0, 70);
		if (mb_strlen($crmValue)>70) {
			$crmShort .= "...";
		}
	}
	
	$onlineValue = $companyOnline['company'][$onlineField];
	$onlineDate = $onlineDateField!=""? $companyOnline['company'][$onlineDateField]: "";
	$onlinePopup = $onlineValue;
	
	if ($table!="") {
		$onlineShort = func::vlookup($descriptionField, $table, "$idField=$onlineValue", $dbo);
	}
	elseif ($type=="date") {
		$onlineShort = func::str14toDate($onlineValue);
	}
	elseif ($type=="password" && $onlineValue!="") {
		$onlineShort = "******";
	}
	elseif ($type=="html") {
	    $onlineValueStripped = strip_tags($onlineValue);
	    $onlineShort = mb_substr($onlineValueStripped, 0, 70);
	    if (mb_strlen($onlineValueStripped)>70) {
	        $onlineShort .= "...";
	    }	    
	    $onlinePopup = $onlineValueStripped;
	}
	else {
		$onlineShort = mb_substr($onlineValue, 0, 70);
		if (mb_strlen($onlineValue)>70) {
			$onlineShort .= "...";
		}
	}
	
	if ($crmDate>$onlineDate) {
		$classCrm = "green-bg"; $classOnline = "red-bg";
	}
	elseif ($crmDate<$onlineDate) {
		$classCrm = "red-bg"; $classOnline = "green-bg";
	}
	else {
		$classCrm = "neutral-bg"; $classOnline = "neutral-bg";
	}
	
	$crmDateDate = func::str14toDate($crmDate);
	$onlineDateDate = func::str14toDate($onlineDate);
			
	echo "<tr>";	
	echo "<td style=\"font-weight:bold\">$description</td>";
	echo "<td><input data-onlineDateField=\"$onlineDateField\" data-crmdate=\"$crmDate\" data-onlinefield=\"$onlineField\" class=\"chk-crm\" type=\"checkbox\" /></td>";
	echo "<td title=\"$crmPopup\">$crmShort</td>";
	echo "<td class=\"crm-val\">$crmValue</td>";
	echo "<td class=\"$classCrm\">$crmDateDate</td>";
	echo "<td width=\"50\"> &nbsp; </td>";
	echo "<td><input data-crmdatefield=\"$crmDateField\" data-onlinedate=\"$onlineDate\" data-crmfield=\"$crmField\" class=\"chk-online\" type=\"checkbox\" /></td>";
	echo "<td title=\"$onlinePopup\">$onlineShort</td>";
	echo "<td class=\"online-val\">$onlineValue</td>";
	echo "<td class=\"$classOnline\">$onlineDateDate</td>";
	echo "</tr>";
	
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Epagelmatias CRM</title>

<link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/grid.css" rel="stylesheet" type="text/css" />
    <link href="css/global.css" rel="stylesheet" type="text/css" />

<style type="text/css">

.green-bg {
	background-color: rgb(200,255,200);
}

.red-bg {
	background-color: rgb(255,200,200);
}

.neutral-bg {
	background-color: rgb(200,200,200);
}



table.table {
	width:99%;
	max-width:1200px;
}

table.table td {
	padding:5px;
	border-bottom: 1px solid rgb(220,220,220);
}

tr.table-head td {
	font-weight: bold;
}

tr.table-bottom td {
	border-bottom: none;
}

td.crm-val, td.online-val {
	display:none;
}

div.button {
	padding:10px;
}

input[type='checkbox'] {
	width:20px;
	height:20px;
	vertical-align:middle; 
}

#btnCloseUpdate, .mybutton {
	padding:10px;
}

</style>

<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

<script>

var crmid = <?php echo $companyid; ?>;
var onlineid = <?php echo $companyonlineid; ?>; 

$(function() {
	$('#update-crm').click(function() {
		syncOnline2Crm();
	});

	$('#update-online').click(function() {
		syncCrm2Online();
	});

	$('#chk-batchselect-crm').click(function() {
		batchSelectCrm();
	});

	$('#chk-batchselect-online').click(function() {
		batchSelectOnline();
	});

});


function batchSelectCrm() {
	if ($('.chk-crm').first().is(':checked')) {
		$('.chk-crm').prop('checked', false);
	}
	else {
		$('.chk-crm').prop('checked', true);
	}
}

function batchSelectOnline() {
	if ($('.chk-online').first().is(':checked')) {
		$('.chk-online').prop('checked', false);
	}
	else {
		$('.chk-online').prop('checked', true);
	}
}



function syncOnline2Crm() {
	var fields = new Array();
	var vals = new Array();
	$('.chk-online').each(function() {
		if ($(this).is(':checked')) {
			var field = $(this).data('crmfield');
			fields.push(field);
			var myVal = $(this).parent().parent().find('td.online-val').html();
			vals.push(myVal);
			//dates
			var datefield = $(this).data('crmdatefield');
			if (datefield!="") {
				fields.push(datefield);
				var myDateVal = $(this).data('onlinedate');
				vals.push(myDateVal);
			}
		}
	});
	if (fields.length>0) {
		$.post('syncOnline2Crm.php', 
				{fields: fields, vals: vals, id:crmid})
		.done(function(data) {
			if (data=='1') {
				location.reload();
			}
			else {
				alert('Δεν έγινε καμμία ενημέρωση\r\n');
			}
		});
	}
	else {
		alert('Πρέπει αν επιλέξετε τουλάχιστον ένα πεδίο');
	}
}

function syncCrm2Online() {
	var fields = new Array();
	var vals = new Array();
	$('.chk-crm').each(function() {
		if ($(this).is(':checked')) {
			var field = $(this).data('onlinefield');
			fields.push(field);
			var myVal = $(this).parent().parent().find('td.crm-val').html();
			vals.push(myVal);
			//dates
			var datefield = $(this).data('onlinedatefield');
			if (datefield!="") {
				fields.push(datefield);
				var myDateVal = $(this).data('crmdate');
				vals.push(myDateVal);
			}
		}
	});
	if (fields.length>0) {
		var url = '../ws/updateCompany.php';
		//var url = 'http://epagelmatias.gr/ws/updateCompany.php';
		$.post(url, 
				{fields: fields, vals: vals, id: onlineid})
		.done(function(data) {
			//alert(data);
			if (data=='1') {
				location.reload();
			}
			if (data>1) {
				window.location.href = "syncCompany.php?companyid="+crmid+"&newonlineid="+data;
			}
			else if (data=='0')  {
				alert('Δεν έγινε καμμία ενημέρωση');
			}
		});
	}
	else {
		alert('Πρέπει αν επιλέξετε τουλάχιστον ένα πεδίο');
	}
	
}

</script>

<script type="text/javascript" src="js/code.js"></script>

</head>

<body>

<div style="padding:20px">

<h1>Συγχρονισμός δεδομένων εταιρείας</h1>

<table class="table">

<tr class="table-head">
<td style="width:150px"></td>
<td><input id="chk-batchselect-crm" type="checkbox" ></td>
<td>CRM</td>
<td style="width:90px"></td>
<td></td>
<td><input id="chk-batchselect-online" type="checkbox" ></td>
<td>ON-LINE</td>
<td style="width:90px"></td>
</tr>

<?php 

createCompanySet($crm, $online, "companyname", "company_name_gr",
		"companyname_dm", "companyname_dm", "Όνομα εταιρείας");
createCompanySet($crm, $online, "profession", "profession",
		"profession_dm", "profession_dm", "Επάγγελμα",
		$db1, "PROFESSIONS");
createCompanySet($crm, $online, "basiccategory", "basic_category",
		"basiccat_dm", "basiccat_dm", "Κατηγορία",
		$db1, "CATEGORIES");
createCompanySet($crm, $online, "area", "area",
		"area_dm", "area_dm", "Περιοχή", 
		$db1, "AREAS");
createCompanySet($crm, $online, "city_id", "city_id",
		"cityid_dm", "cityid_dm", "Πόλη",
		$db1, "EP_CITIES");
createCompanySet($crm, $online, "address", "address_gr",
		"address_dm", "address_dm", "Διεύθυνση");
createCompanySet($crm, $online, "zipcode", "zip_code",
		"zipcode_dm", "zipcode_dm", "Τ.Κ.");
createCompanySet($crm, $online, "geo_x", "geo_x",
		"geox_dm", "geox_dm", "Map Χ");
createCompanySet($crm, $online, "geo_y", "geo_y",
		"geoy_dm", "geoy_dm", "Map Y");
createCompanySet($crm, $online, "phone1", "phone",
		"phone1_dm", "phone1_dm", "Τηλέφωνο");
createCompanySet($crm, $online, "phone2", "phone2",
		"phone2_dm", "phone2_dm", "Τηλέφωνο 2");
createCompanySet($crm, $online, "fax", "fax",
		"fax_dm", "fax_dm", "Fax");
createCompanySet($crm, $online, "email", "email",
		"email_dm", "email_dm", "Email");
createCompanySet($crm, $online, "website", "website",
		"website_dm", "website_dm", "Website");
createCompanySet($crm, $online, "facebook", "facebook",
		"facebook_dm", "facebook_dm", "Facebook");
createCompanySet($crm, $online, "twitter", "twitter",
		"twitter_dm", "twitter_dm", "Twitter");
createCompanySet($crm, $online, "ShortDescription", "short_description_gr",
		"shortdescr_dm", "shortdescr_dm", "Σύντομη περιγραφή");
createCompanySet($crm, $online, "FullDescription", "full_description_gr",
		"fulldescr_dm", "fulldescr_dm", "Πλήρης περιγραφή",
		"", "", "", "",
		"html");
createCompanySet($crm, $online, "vn_keywords", "keywords_gr",
		"keywords_dm", "keywords_dm", "Keywords");
createCompanySet($crm, $online, "package", "package",
		"", "", "Πακέτο",
		$db1, "ONLINE_PACKAGES", "id", "description",
		"package");
createCompanySet($crm, $online, "username", "username",
		"", "", "Username");
createCompanySet($crm, $online, "password", "password",
		"password_dm", "password_dm", "Password",
		"", "", "", "",
		"password");
createCompanySet($crm, $online, "expires", "expires",
		"expires_dm", "expires_dm", "Ημερ. λήξης",
		"", "", "", "",
		"date");

?>

<tr class="table-bottom">
<td></td>
<td></td>
<td style="padding:20px 0px"><div id="update-online" class="button">Update online catalogue</div></td>
<td></td>
<td></td>
<td></td>
<td style="padding:20px 0px"><div id="update-crm" class="button">Update CRM</div></td>
<td></td>
</tr>

</table>

<form style="margin:0px; border:none">
<a class="button mybutton" style="padding:10px;" href="editcompany.php?id=<?php echo $companyid; ?>">Επιστροφή στην εταιρεία</a>
<?php
$btnCloseUpdate = new button("btnCloseUpdate", "Close & update", "close-update");
$btnCloseUpdate->set_method("UnlockCompany($companyid)");
$btnCloseUpdate->get_button_simple();            
?>
</form>    

</div>

</body>

</html>

