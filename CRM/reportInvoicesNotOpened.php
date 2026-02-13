<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql="";
$d1 = ""; $d2 = ""; $series = 0;
$rs = NULL;
$showall = 0;

if (isset($_GET['search']) && $_GET['search']==1) {
    $d1 = $_POST['t_datestart'];
    $D1 = textbox::getDate($d1, $locale);
    $d2 = $_POST['t_dateend'];
    $D2 = textbox::getDate($d2, $locale);
    $series = $_POST['c_series'];
    
    $showall = checkbox::getVal2($_POST, "chk_showall");
    
    $sql = "SELECT C.id, I.id AS iid, I.idate, I.amount, I.vat, I.icode, "
            . "IF(C.eponimia = '' OR C.eponimia IS NULL, C.companyname, "
            . "C.eponimia) AS cname, C.afm, C.doy, C.address, IC.code AS scode, "
            . "C.phone1, C.phone2, C.mobilephone, '' AS MYMARK, '' AS PHONES, I.timesread  "
            . "FROM INVOICEHEADERS I INNER JOIN COMPANIES C ON I.company=C.id "
            . "INNER JOIN INVOICESERIES IC ON I.series = IC.id "
            . "WHERE idate>=$D1 AND iDate<=$D2 ";
    if ($showall==0) {
        $sql .= " AND (timesread IS NULL OR timesread='' OR timesread<1) ";
    }
    if ($series>0) {
        $sql .= " AND series = $series ";
    }
    $sql .= " ORDER BY icode ";
    
    $rs = $db1->getRS($sql);
    
    if ($rs) {
        for ($i = 0; $i < count($rs); $i++) {
            $invId = $rs[$i]['iid'];
            if ($rs[$i]['timesread']>=1) {
                $rs[$i]['MYMARK'] = "<span style=\"font-size:24px\"><span data-id=\"$invId\" class=\"fa fa-check-circle mark-as-not-opened\"></span></span>";
            }
            else {
                $rs[$i]['MYMARK'] = "<span style=\"font-size:24px\"><span data-id=\"$invId\" class=\"fa fa-check mark-as-opened\"></span></span>";
            }
            
            $rs[$i]['PHONES'] = $rs[$i]['phone1'] . " / " . $rs[$i]['phone2'] . " / " . $rs[$i]['mobilephone'];
        }
    }
    
}

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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>
<script>
$(document).ready(function() {	
	$("a.fancybox").fancybox({'type' : 'iframe', 'width' : 1200, 'height' : 550 });
        $("a.fancybox1000").fancybox({'type' : 'iframe', 'width' : 1000, 'height' : 800 });
});
</script>

<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-gr.js"></script>

<script>        
    $(function() {
        $( ".datepicker" ).datepicker($.datepicker.regional[ "<?php echo $locale; ?>" ]);
        });

</script>

<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

<script>        
    
    $.tablesorter.addParser({ 
        // set a unique id 
        id: 'dates', 
        is: function(s) { 
            // return false so this parser is not auto detected 
            return false; 
        }, 
        format: function(s) { 
            // format your data for normalization 01/12/2014 -> 20141201
            var ar = s.split("/");
            return ar[2]+ar[1]+ar[0];            
        }, 
        // set type, either numeric or text 
        type: 'text' 
    }); 


    $(function() {
        $("#dg").tablesorter({ 
            sortList: [[2,0],[3,0], [4,0]],
            headers: { 
                2: { 
                    sorter:'dates' 
                } 
            } 
        });        

        });
        
    $(function() {
        
        $(".mark-as-opened").click(function() {
            var invoiceId = $(this).data('id');
            var el = $(this);
            
            $.post("_invoiceSetOpened.php", 
                {
                    invoiceId:invoiceId,
                    status:'opened'
                }, 
                function(data) {
                    if (data==='OK') {
                        el.removeClass("fa-check");
                        el.removeClass("mark-as-opened");
                        el.addClass("fa-check-circle");
                        el.addClass("mark-as-not-opened");
                        bindMarkAsNotOpened();
                    }
                    else {
                        console.log(data);
                    }
                });    
        });
        
        bindMarkAsNotOpened();
        
    });
    
    function bindMarkAsNotOpened() {
        
        $(".mark-as-not-opened").click(function() {
            var invoiceId = $(this).data('id');
            var el = $(this);
            
            $.post("_invoiceSetOpened.php", 
                {
                    invoiceId:invoiceId,
                    status:'notopened'
                }, 
                function(data) {
                    if (data==='OK') {
                        el.removeClass("fa-check-circle");
                        el.removeClass("mark-as-not-opened");
                        el.addClass("fa-check");
                        el.addClass("mark-as-opened");                        
                    }
                    else {
                        console.log(data);
                    }
                });    
        });
        
    }

</script>

<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #dg {
        max-width: 1400px;
        margin-left: 1em;
    }
    
    h2.search-results {
        margin-left: 1em;
    }
    
</style>


</head>
    
    <body>
        <div class="dontprint">
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?>
        </div>

            <div class="main">
                
                <h2 style="margin-left:1em">ΤΙΜΟΛΟΓΙΑ ΜΗ ΑΝΟΙΓΜΕΝΑ</h2>
                
                <div class="form-container">

                    <form action="reportInvoicesNotOpened.php?search=1" method="POST">
                        
                        <?php
                        $t_datestart = new textbox("t_datestart", "ΗΜΕΡ. ΑΠΟ", $d1);
                        $t_datestart->set_format("DATE");
                        $t_datestart->set_locale($locale);                        
                        $t_datestart->get_Textbox();
                        
                        $t_dateend = new textbox("t_dateend", "ΗΜΕΡ. ΕΩΣ", $d2);
                        $t_dateend->set_format("DATE");
                        $t_dateend->set_locale($locale);                        
                        $t_dateend->get_Textbox();
                        
                        $c_series = new comboBox("c_series", $db1, 
                                "SELECT * FROM INVOICESERIES", "id", "code", 
                                $series, "ΣΕΙΡΑ");
                        $c_series->set_dontSelect("ΟΛΕΣ");
                        $c_series->get_comboBox();
                        
                        $chk_showall = new checkbox("chk_showall", "Εμφάνιση όλων", $showall);
                        $chk_showall->get_Checkbox();
                        
                        echo '<div class="dontprint">';
                        $btnOK = new button("btnOK", "ΑΝΑΖΗΤΗΣΗ");
                        $btnOK->get_button();
                        echo '</div>';
                        
                        ?>
                        <div style="clear: both"></div>
                        
                    </form>
                    
                </div>
                
                <?php
                if ($rs) {
                    $dg = new datagrid("dg", $db1, "", 
                        array("iid","idate", "scode", "icode", "cname", "afm", "doy", "address", "amount", "vat", "PHONES", "MYMARK"), 
                        array("ID","ΗΜΕΡ.", "ΣΕΙΡΑ", "ΚΩΔ", "ΕΠΩΝΥΜΙΑ", "ΑΦΜ", "ΔΟΥ", "ΔΝΣΗ", "ΚΑΘ.ΑΞΙΑ", "ΦΠΑ", "ΤΗΛ.", "MARK"), 
                        $ltoken);
                    $dg->set_rs($rs);
                    $dg->set_colsFormat(array("","DATE", "", "", "", "", "", "", "CURRENCY", "CURRENCY", "",""));
                    $dg->set_edit("editcompany.php", "COMPANY");
                    //$dg->col_sum("amount");
                    //$dg->col_sum("vat");
                    $dg->get_datagrid();
                    
                    echo "<br/><h2 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ". count($dg->get_rs()). "</h2>";
                    
                    
                }
                
                
                ?>
                
                <a class="button" onclick="window.print();" style="margin-left: 1em">ΕΚΤΥΠΩΣΗ</a>


            </div>

            <div style="clear: both"></div>    
            
            <div class="dontprint">
            <?php include "blocks/footer.php"; ?>       
            </div>
            
            
            
            
            
            
    </body>
    
</html>