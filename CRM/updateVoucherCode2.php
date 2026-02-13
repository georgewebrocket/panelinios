<?php

ini_set('display_errors',0); 
// error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

header('Content-Type: text/html; charset=utf-8');

function extractInParentheses($string) {
    if (preg_match('/\((.*?)\)/', $string, $matches)) {
        return $matches[1]; // everything inside parentheses
    }
    return null;
}

$fieldsCount = 19; //18
$lineLength = 8192;
$err = FALSE;
$msg = "";

$idsStatusDelivered = "";
$courierId = 1; //red

if ($_POST) {
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');
    $lines = array();
    while( ($row = fgetcsv($fh, $lineLength, ";")) !== FALSE ) {
        $lines[] = $row;
        //var_dump($row);
        //echo "<br/><br/>";
    }
    //validate file
    for ($i = 0; $i < count($lines); $i++) {
        if (count($lines[$i])!=$fieldsCount) {
            $msg .= "Error at voucher code " . $lines[$i][0] . "<br/>";
            $err = TRUE;
        }    
    }
    
    if (!$err) {
        for ($i = 0; $i < count($lines); $i++) {
            $vcode = $lines[$i][8];
            $vcode2 = $lines[$i][0];
            $vcode3 = $lines[$i][18]; ///
            $courier_notes = $lines[$i][10];
            //echo $courier_notes . "<br/>";
            $sql = "SELECT * FROM VOUCHERS WHERE vcode=? AND courier = $courierId";
            $rs = $db1->getRS($sql, array($vcode));
            
            if ($rs) {
                $voucher = new VOUCHERS($db1, $rs[0]['id'], $rs);
                $voucher->set_vcode2($vcode2);
                $voucher->set_vcode3($vcode3);
                $voucher->set_courier_notes($courier_notes);
                $flag = substr($courier_notes, 0, 20);
                if ($flag=="Παραδόθηκε") {
                    if ($voucher->get_courier_status()==1) {
                        $voucher->set_courier_status(5);
                        $idsStatusDelivered .= $voucher->get_id(). " ";
                        echo $voucher->get_id() . " OK / NEW<br/>";
                    }
                    else {
                        echo $voucher->get_id() . " OK<br/>"; 
                    }
                                       
                }
                else {
                    echo substr($courier_notes, 0, 20) . "<br/>";
                }
                
                $res = $voucher->Savedata();
                
            }
            
        }
        $msg = "All vouchers updated. Status delivered: " . $idsStatusDelivered;
    }
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS - CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
<link href="fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>        
<script type="text/javascript" src="fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="js/code.js"></script>

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
        <h1>Update vouchers' status (Code 2)</h1>
        
        <?php
        if ($msg!="") {
            echo "<h2>$msg</h2>";
        }
        ?>
        
        <form action="updateVoucherCode2.php" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" name="btn_submit" value="Upload File" />
        </form>
        
        <div class="spacer-20"></div>
        
        <a class="button" href="updateVoucherCode2.php?red=1">Update with RED API</a>
        
        <div style="padding:20px 0px">
            <?php

            if (isset($_GET['red'])) {
                
                $start = !empty($_GET['start']) ? (int)$_GET['start'] : 0;

                $sql = "SELECT * FROM VOUCHERS WHERE courier_status = 1 AND courier=1 AND vcode2<>'' LIMIT 40 OFFSET $start";
                $rsVoucher = $db1->getRS($sql);                
                
                if ($rsVoucher) {
                    $countVouchers = count($rsVoucher);
                    echo "Updating status of $countVouchers pending vouchers ...<br/>";
                    
                    for ($i = 0; $i < count($rsVoucher); $i++) {
                        $voucherCodes = $rsVoucher[$i]['vcode2'];
                        //echo "$voucherCodes<br/>";
                        include "_getVoucherStatusRed.php"; 
                        
                    }
                    //echo "Finished.<br/>";
                    ?>

                    <?php if (count($rsVoucher)==40) { ?>

                        <div id="timer" style="font-size: 24px; font-weight:bold; text-align: center; margin-top: 20px;">60</div>
                        <script>
                            setTimeout(() => {
                                let baseUrl = "https://crm.panelinios.gr/updateVoucherCode2.php";
                                let params = new URLSearchParams({
                                    start: "<?php echo $start + 40 ?>",
                                    red: 1
                                });
                                window.location.href = `${baseUrl}?${params.toString()}`;
                            }, 61000);


                            $(document).ready(function () {
                                let countdown = 60; // Start at 60 seconds
                                const timerElement = $('#timer'); // Select the timer element

                                const timer = setInterval(function () {
                                    timerElement.text(countdown); // Update the timer display
                                    countdown--; // Decrement the countdown
                                    
                                    if (countdown < 0) {
                                        clearInterval(timer); // Stop the timer when it reaches 0
                                        timerElement.text('Time’s up!'); // Display "Time’s up!" when the timer ends
                                    }
                                }, 1000); // Run the code every 1 second (1000 milliseconds)
                            });


                        </script>
                    <?php } else { echo "<br/><br/><h3 style=\"padding:5px 20px;background-color:#afa\">Finished</h3>";} ?>

                    <?php
                }
                else {
                    echo "<br/><br/><h3 style=\"padding:5px 20px;background-color:#afa\">Δεν υπάρχουν άλλα voucher σε εκκρεμμότητα</h3>";
                }
                
            }

            ?>
        </div>
        
        
    </div>
    
    <?php include "blocks/footer.php"; ?> 
        
</body>
</html>