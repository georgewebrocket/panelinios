<?php



require_once('php/session.php');

require_once('php/dataobjects.php');

require_once('php/controls.php');

require_once('inc.php');



$sql = "SELECT V.id, C.companyname, IF(C.courier_address<>'', C.courier_address, C.address )  AS address, IF(C.courier_zipcode<>'', C.courier_zipcode, C.zipcode) AS zipcode, CITIES.description AS city, IF(C.courier_phone<>'', C.courier_phone, CONCAT(C.mobilephone, ' ', C.phone1, ' ', C.phone2)) AS phones, V.deliverydate, V.deliverytime, V.deliverynotes, N'' AS comments, V.vcode, V.amount, C.courier_notes, V.customer FROM VOUCHERS V INNER JOIN COMPANIES C ON V.customer = C.id INNER JOIN EP_CITIES CITIES ON IF(C.courier_city<>0, C.courier_city, C.city_id) = CITIES.id WHERE V.export_to_excel = 1 AND V.exported_to_excel = 0";

//echo $sql;

$rs = $db1->getRS($sql);



for ($i = 0; $i < count($rs); $i++) {

    $rs[$i]['comments'] = "Ώρα παράδοσης &nbsp; " . func::str14toDate($rs[$i]['deliverydate']) . " " .
            $rs[$i]['deliverytime'] . " / " . $rs[$i]['deliverynotes'];

    if (mb_strlen($rs[$i]['comments'], 'utf-8')>128) {
        $rs[$i]['comments'] = "<div style=\"background-color:yellow\">".$rs[$i]['comments']."</div>";
    }    

    if (mb_strlen($rs[$i]['companyname'], 'utf-8')>64) {
        $rs[$i]['companyname'] = "<div style=\"background-color:yellow\">".$rs[$i]['companyname']."</div>";
    }

    if (mb_strlen($rs[$i]['address'], 'utf-8')>64) {
        $rs[$i]['address'] = "<div style=\"background-color:yellow\">".$rs[$i]['address']."</div>";
    }

    if (mb_strlen($rs[$i]['phones'], 'utf-8')>32) {
        $rs[$i]['phones'] = "<div style=\"background-color:yellow\">".$rs[$i]['phones']."</div>";
    }
    $customerId = $rs[$i]['customer'];
    $rs[$i]['customer'] = "<a target=\"_blank\" href=\"editcompany.php?id=$customerId\">$customerId</a>";   

}



$grid = new datagrid("grid", $db1, "", 
        array("companyname", "address", "zipcode", "city", "phones", "comments", "id", "amount", "customer"), 
        array("ΕΠΩΝΥΜΙΑ", "ΔΙΕΥΘΥΝΣΗ", "ΤΚ", "ΠΟΛΗ", "ΤΗΛΕΦΩΝΑ", "ΣΧΟΛΙΑ", "ΑΡ. ΠΑΡΑΓ.", "ΑΝΤΙΚΑΤΑΒΟΛΗ", "CUSTOMER")
        );

$grid->set_rs($rs);

$grid->set_colsFormat(array("","","","","","","","CURRENCY"));



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>PANELINIOS- CRM</title>

<link href="css/reset.css" rel="stylesheet" type="text/css" />

<link href="css/grid.css" rel="stylesheet" type="text/css" />

<link href="css/global.css" rel="stylesheet" type="text/css" />

<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />

        

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>

<script type="text/javascript" src="js/jquery.cookie.js"></script>        

<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>

<script type="text/javascript" src="js/code.js"></script>


<script>

$(function() {
    
    $("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 800 });
    $("a.fancybox500").fancybox({'type' : 'iframe', 'width' : 500, 'height' : 450 });	
    
    
});

</script>




<style>

    

    #grid {

        max-width: 1000px;

    }

    

</style>



</head>

    

<body>

    

    <?php include "blocks/header.php"; ?>

    <?php include "blocks/menu.php"; ?>

    

    <div class="main">

        

    <?php   

    $grid->get_datagrid();

    $sql = str_replace("SELECT", "SLCT", $sql);

    echo "<br/>&nbsp;&nbsp;&nbsp;<a class=\"button\" href=\"getExcel.php?filename=vouchers&sql=$sql&fields=companyname,address,zipcode,city,phones,comments,id,amount\">Export to Excel</a> &nbsp; &nbsp;";

    //eponimia,address,city,phones,comments,vcode,amount"
    
    echo "<a id=\"red-api\" class=\"button fancybox\" href=\"transferVouchersToRed.php?sql=$sql\">Transfer to RED via API</a>";
    

    ?>
    

    </div>
   

    <?php include "blocks/footer.php"; ?> 
    
    <script>
    
    $(function() {
        
        $("#red-api").mouseup(function() {
            $(this).hide();
            //$(this).attr("href", "blankpage.php");
            //$(this).css("background-color", "#ccc");
            //$(this + ":hover").css("background-color", "#ccc");
        });
        
    });
    
    </script>
    

</body>

</html>