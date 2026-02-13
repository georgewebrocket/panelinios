<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$rs = FALSE; $err = ""; $msg = ""; $voucherids = "";

$courier = $_REQUEST['courier'];

if ($_POST) {
    require 'phpmailer/PHPMailerAutoload.php';
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('courier@pc4u.gr', 'EPAGELMATIAS.GR');
    
    if ($courier=="RED") {
        $mail->addAddress("info@redcourier.gr", "RED COURIER");
        //$mail->addAddress("george.apollo@gmail.com", "RED COURIER");
    }
    if ($courier=="ACS") {
        $mail->addAddress("bbk@acscourier.gr", "ACS COURIER");
        //$mail->addAddress("george.apollo@gmail.com", "RED COURIER");
    }
    
    $mail->addCC('courier@pc4u.gr', 'admin');
    //$mail->addBCC('george.apollo@gmail.com', 'admin2');
    $mail->Subject = "Επιπρόσθετες κινήσεις αποστολών Epagelmatias.gr";
    $message = $_POST['t_message'];
    $mail->msgHTML($message);
    
    if (!$mail->send()) {
        $err = "Mailer Error: " . $mail->ErrorInfo;
    } else {
        $msg = "Η λίστα στάλθηκε στον Courier με επιτυχία.";
        
        $voucherIds = $_REQUEST['voucherids'];
        $sql = "UPDATE VOUCHERS SET courier_note_archive = CONCAT('<strong>$now</strong><br/>', second_note_for_courier, '<br/><br/>', COALESCE(courier_note_archive,'')), second_note_for_courier = '' WHERE id IN ($voucherIds)";
        //echo $sql;
        $rs = $db1->execSQL($sql);
        
    }
    
    
}
else {
    if ($courier=="RED") {
        $vcodeCourier = "vcode2";
    }
    if ($courier=="ACS") {
        $vcodeCourier = "vcode3";
    }
    
    $sql = "SELECT id, customer, vcode2, second_note_for_courier FROM VOUCHERS "
            . "WHERE COALESCE(second_note_courier_sent,0)<>1 "
            . "AND COALESCE(second_note_for_courier, '') <> ''";
    if ($courier=="RED") {
        $sql .= " AND COALESCE(vcode3,'')=''";
    }
    if ($courier=="ACS") {
        $sql .= " AND COALESCE(vcode3,'')<>''";
    }
    $rs = $db1->getRS($sql);
    
    for ($i = 0; $i < count($rs); $i++) {
        $companyid = $rs[$i]['customer'];
        $rs[$i]['customer'] = func::vlookup("companyname", "COMPANIES", "id=$companyid", $db1);
        $voucherids .= $rs[$i]['id'];
        if ($i<count($rs)-1) {
            $voucherids .= ",";
        }
    }

    if ($rs) {
        $grid = new datagrid("grid", $db1, "", 
                array("$vcodeCourier", "customer", "second_note_for_courier"), 
                array("ΚΩΔ.", "ΠΕΛΑΤΗΣ", "ΣΗΜΕΙΩΣΕΙΣ"), 
                $ltoken);
        $grid->set_rs($rs); 
    }
    
}

$myStyle = <<<EOT
<style>        
    #grid {
        max-width: 1100px;
    }
    #t_message table {
        border: 1px solid black;
    }
    .mce-item-table td {
        border: 1px solid black !important;
    }
</style>
EOT;

include '_theHeader.php';

echo "<h1>Σημειώσεις για Courier $courier</h1>";

if ($rs) {
    echo "<form action=\"report2ndNotesForCourier.php?voucherids=$voucherids\" method=\"post\" style=\"max-width:800px\">";
    echo '<textarea name="t_message" id="t_message" rows="10" cols="20" >';
    echo "<table width=\"100%\" cellspacing=\"0\" style=\"border: 1px solid black; max-width:800px\">";
    
    echo "<tr>";
    echo "<td style=\"border: 1px solid black; font-weight:bold\">ΚΩΔ VOUCHER</td>";
    echo "<td style=\"border: 1px solid black; font-weight:bold\">ΠΕΛΑΤΗΣ</td>";
    echo "<td style=\"border: 1px solid black; font-weight:bold\">ΣΗΜΕΙΩΣΕΙΣ</td>";
    echo "</tr>";
    
    for ($i = 0; $i < count($rs); $i++) {
        echo "<tr>";
        echo "<td style=\"border: 1px solid black\">" . $rs[$i]["$vcodeCourier"] . "</td>";
        echo "<td style=\"border: 1px solid black\">" . $rs[$i]['customer'] . "</td>";
        echo "<td style=\"border: 1px solid black\">" . $rs[$i]['second_note_for_courier'] . "</td>";
        echo "</tr>";
    }    
    echo "</table>";
    echo '</textarea>';
    
    
    echo "<div class=\"clear\" style=\"height:20px\"></div>";
    echo "<input id=\"btn-send-mail\" type=\"submit\" class=\"button\" value=\"Αποστολή email\" />";
    
    echo '</form>';
    



$myScript = <<<EOT
<!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>-->
<script src="https://cdn.tiny.cloud/1/fiq8lm63smdu8mc2fhs375nntdcf27e6r8gdeb3c5zqzllnl/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector:'#t_message',
            menubar : false}); 

    </script>        
EOT;

}
else {
    if ($msg!="" || $err!="") {
        if ($msg!="") {
            echo "<h3>$msg</h3>";
        }
        if ($err!="") {
            echo "<h3>$err</h3>";
        }
    }
    else {
        echo "<h3>Δεν υπάρχουν στοιχεία</h3>";
    }
    
}

include '_theFooter.php';