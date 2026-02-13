<?php
/*
ini_set('display_errors',1); 
error_reporting(E_ALL);
*/
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('php/utils.php');
require_once('inc.php');
/*
$sql = "SELECT * FROM COMPANIES WHERE id<>7 AND (`allphonesdigits` LIKE '%2105316566%')";
$rs = $db1->getRS($sql);
echo count($rs);
exit();*/

$rsDoubles = array(array());
$rs = FALSE;
$rsdIndex = 0;

$nrOfDoubles = 10;

if (isset($_REQUEST['t_count'])) {
    $nrOfDoubles = $_REQUEST['t_count'];
    
	$sql = "SELECT id,companyname,phone1digits,phone2digits,faxdigits,mobiledigits,allphonesdigits FROM COMPANIES WHERE nodoubles = 0 OR nodoubles IS NULL ORDER BY id"; 
	// LIMIT 1000 
    $rs = $db1->getRS($sql);
    for ($i=0;$i<count($rs);$i++) {
    	$err = FALSE;
    	$myId = $rs[$i]['id'];
    	$myPhone1 = $rs[$i]['phone1digits'];
    	$myPhone2 = $rs[$i]['phone2digits'];
    	$myFax = $rs[$i]['faxdigits'];
    	$myMobile = $rs[$i]['mobiledigits'];
    	
    	if (strlen($myPhone1)<7 &&  strlen($myPhone2)<7 && strlen($myFax)<7 && strlen($myMobile)<7) {
    		$err = TRUE;
    	}
    	if (!$err) {
	    	$sql = "SELECT * FROM COMPANIES WHERE id<>$myId";
	    	$criteria = '';
	    	if (strlen($myPhone1)>=7) {
	    		$criteria = func::ConcatSpecial($criteria, "`allphonesdigits` LIKE '%$myPhone1%'", " OR ");
	    	}
	    	if (strlen($myPhone2)>=7) {
	    		$criteria = func::ConcatSpecial($criteria, "`allphonesdigits` LIKE '%$myPhone2%'", " OR ");
	    	}
	    	if (strlen($myFax)>=7) {
	    		$criteria = func::ConcatSpecial($criteria, "`allphonesdigits` LIKE '%$myFax%'", " OR ");
	    	}
	    	if (strlen($myMobile)>=7) {
	    		$criteria = func::ConcatSpecial($criteria, "`allphonesdigits` LIKE '%$myMobile%'", " OR ");
	    	}
	    	if ($criteria!='') {
	    		$sql .= " AND ($criteria)";
	    	}
	    	
	    	//echo $sql ."<br/>";
	    	$rs2 = $db1->getRS($sql);
			//echo count($rs2) ."<br/>";
	    	
	    	if ($rs2) {
		    	$myDoubles = '';
		    	for ($k=0;$k<count($rs2);$k++) {    	
		    		$doubleid = $rs2[$k]['id'];
		    		$doubleDescription = $rs2[$k]['companyname'];
		    		$doubleWithLink = "<a target=\"_blank\" href=\"editcompany.php?id=$doubleid\">
		    		$doubleid - $doubleDescription </a>";
		    		$myDoubles = func::ConcatSpecial($myDoubles, $doubleWithLink, "<br/>");
		    	}		    	
		    	$rsDoubles[$rsdIndex]['id'] = $rs[$i]['id'];
		    	$rsDoubles[$rsdIndex]['companyname'] = $rs[$i]['companyname'];
		    	$rsDoubles[$rsdIndex]['phones'] = $rs[$i]['allphonesdigits'];
		    	$rsDoubles[$rsdIndex]['doubles'] = $myDoubles;
		    	$rsdIndex++;
		    	if ($rsdIndex>=$nrOfDoubles) { break; }
	    	}
	    	else {
	    		$company = new COMPANIES($db1, $myId);
	    		$company->set_nodoubles(1);
	    		$company->Savedata();
	    	}
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



<style>
    
    .form-container {
        max-width: 875px;
        min-height: 0px;
    }
    
    #dg {
        max-width: 1200px;
        margin-left: 20px;
    }
    
    h2.search-results {
        margin-left: 20px;
    }
    
</style>


</head>
    
    <body>
        <div class="dontprint">
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?>
        </div>

            <div class="main">
                
                <h2 style="margin-left:1em">ΕΛΕΓΧΟΣ ΔΙΠΛΟΕΓΓΡΑΦΩΝ</h2>
                
                <div class="form-container">

                    <form action="reportDoubleEntries.php" method="POST">
                        
                        <?php
                        $t_count = new textbox("t_count", "ΑΡΙΘ. ΔΙΠΛΟΕΓΓΡΑΦΩΝ", $nrOfDoubles);                     
                        $t_count->get_Textbox();
                        
                        
                        
                        echo '<div class="dontprint">';
                        $btnOK = new button("btnOK", "ΑΝΑΖΗΤΗΣΗ");
                        $btnOK->get_button();
                        echo '</div>';
                        
                        ?>
                        <div style="clear: both"></div>
                        
                    </form>
                    
                </div>
                
                <?php
                if (isset($_REQUEST['t_count'])) {
                if ($rsdIndex>0) {
                    $dg = new datagrid("dg", $db1, "", 
                        array("id","companyname","phones", "doubles"), 
                        array("ID","ΕΠΩΝΥΜΙΑ","ΤΗΛ.", "ΔΙΠΛΟΕΓΓΡΑΦΕΣ"), 
                        $ltoken);
                    $dg->set_edit("editcompany.php", "COMPANY");
                    $dg->set_rs($rsDoubles);
                    $dg->set_popup(FALSE);
                    $dg->get_datagrid();
                    
                    echo "<br/><h2 style=\"margin-left:1em\">ΣΥΝΟΛΟ: ". count($dg->get_rs()). "</h2>";
                    
                    
                }
                else {
                	echo "Δεν βρέθηκαν εγγραφές";
                }
                }
                
                ?>
                
                

            </div>

            <div style="clear: both"></div>    
            
            <div class="dontprint">
            <?php include "blocks/footer.php"; ?>       
            </div>
            
    </body>
    
</html>