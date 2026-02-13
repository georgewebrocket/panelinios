<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$companyid = $_REQUEST['companyid'];

$sql = "SELECT * FROM COMPANY_CHANGES WHERE companyid=? ORDER BY cdatetime DESC";
$rs = $db1->getRS($sql, array($companyid));

$rsUsers = $db1->getRS("SELECT * FROM USERS");

for ($i = 0; $i < count($rs); $i++) {
    $rs[$i]['userid'] = func::vlookupRS("fullname", $rsUsers, $rs[$i]['userid']);
    $rs[$i]['cdatetime'] = func::str14toDateTime($rs[$i]['cdatetime']);
}

?>
<html>
    <head>
        <title>Company Changes</title>
        <link href="css/reset.css" rel="stylesheet" type="text/css" />
        <link href="css/grid.css" rel="stylesheet" type="text/css" />
        <link href="css/global.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
    <?php

    $grid = new datagrid("grid", $db1, "", 
            array("fieldname","val1", "val2", "userid", "cdatetime"), 
            array("Field name","Value 1", "Value 2", "USER", "DATE/TIME"));
    $grid->set_rs($rs);
    $grid->get_datagrid();

    ?>
    </body>
</html>
