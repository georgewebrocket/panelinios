<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
require_once('php/mydata.php');

$sql = "SELECT id, icode, idate, series, publisher FROM INVOICEHEADERS 
    WHERE idate>='20210101000000' AND series IN (1,3,4) AND myDataTransfered=0 
    ORDER BY icode ";
$rs = $db1->getRS($sql);
?>
<html>
    <head>
        <title>admin</title>
        <style>
            td {
                border:1px solid #888;
            }
            table {
                width:100%;
                max-width:1400px;
            }
        </style>
    </head>
    
    <body>
        <h1>Μαζική μεταφορά στο MyData</h1>
        <?php
        if ($rs) {
            echo "<table>";
            echo "<tr>";
            echo "<td width=\"15%\">ID</td>";
            echo "<td width=\"15%\">ΚΩΔΙΚΟΣ</td>";
            echo "<td width=\"15%\">ΗΜΕΡ/ΝΙΑ ΕΚΔΟΣΗΣ</td>";
            echo "<td width=\"15%\">ID ΣΕΙΡΑΣ</td>";
            echo "<td width=\"15%\">PUBLISHER</td>";
            echo "<td width=\"25%\">ERROR</td>";
            echo "</tr>";
            
            for ($i=0; $i < COUNT($rs); $i++) { 
                echo "<tr>";
                echo "<td>" . $rs[$i]['id'] . "</td>";
                echo "<td>" . $rs[$i]['icode'] . "</td>";
                $my_date = substr($rs[$i]['idate'], 6,2) . "/" . substr($rs[$i]['idate'], 4,2) . "/" . substr($rs[$i]['idate'], 0,4);
                echo "<td>" . $my_date . "</td>";
                echo "<td>" . $rs[$i]['series'] . "</td>";
                echo "<td>" . $rs[$i]['publisher'] . "</td>";
                
                echo "<td>";
                $publisher = $rs[$i]['publisher'];
                myData_SendInvoices($rs[$i]['id'], $db1, $publisher);
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </body>
    
</html>
