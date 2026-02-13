<?php

//panelinios

define('USER_RED_API', 54); //user for red api actions

$token = "gjbbKBlYUcyRYhfZJ6Ym1X3uouym8eDe9j9kBeIree01e532"; // panelinios


//$url = "https://tracking.redcourier.gr/api/GetLastStatus.php?TokenAPI=$token";


$my_voucher = $voucherCodes;

$url = "https://web.redcourier.gr/api/v5.0/GetVoucherLastStatus?voucher=$my_voucher";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$result = curl_exec($ch); 
curl_close($ch);  

$res = json_decode($result);

$obj = $res;


if (is_object($obj)) {
    //$myStatus = $obj->status;
    //$myVcode3 = $obj->ACSVoucher;

    $success = $obj->success;

    if ($success) {
        $myStatus = $obj->data;
    
        if ($rsVoucher[$i]['courier_notes']!=$myStatus 
                /*|| $rsVoucher[$i]['vcode3']!=$myVcode3*/) {
            $voucherid = $rsVoucher[$i]['id'];
            $voucher = new VOUCHERS($db1, $rsVoucher[$i]['id']);
            
            $voucher->set_courier_notes($myStatus);
            
            //$voucher->set_vcode3($myVcode3);                    
            
            $flag = substr($myStatus, 0, 20);
            if ($flag == "Παραδόθηκε") {
                $voucher->set_courier_status(5); // status delivered 
                //echo " / DELIVERED";
            }

            $status_array = explode(" ", $myStatus);
            if ($status_array[0]=="Επιστροφή") {
                $voucher->set_courier_status(3); //returned
                
                //company status recall
                $voucher_user_id = $voucher->get_userid();                
                $customer_id = $voucher->get_customer();
                
                $transaction_ids = $voucher->get_transactionids();
                $trans_ids = explode(",", $transaction_ids);
                foreach ($trans_ids as $transaction_id) {
                    $transaction = new TRANSACTIONS($db1, $transaction_id);
                    $package_id = $transaction->get_package();
                    $package = new PACKAGES($db1, $package_id);
                    $product_category = $package->get_product_category();

                    include "_updateVoucherStatusRed2026.php";

                    $transaction->set_status(3); //returned-canceled
                    $transaction->Savedata();
                    
                }
                if ($transaction_ids=="") {
                    $product_category = 1;
                    include "_updateVoucherStatusRed2026.php";
                }


            }
            
            $voucher->Savedata();

            echo "VOUCHER " . $voucherid . " - " . $myStatus . "<br/>";
            
        }

    }
    
    
}
  




//get all status
$url = "https://web.redcourier.gr/api/v5.0/GetVoucherFullTrackingStatus?voucher=$my_voucher";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
$result = curl_exec($ch); 
curl_close($ch);  

$res = json_decode($result);
echo "<strong>".$my_voucher . "</strong><br/>";

$success = $res->success;
if ($success) {
    $history = $res->data;
    foreach($history as $obj) {
        if (is_object($obj)) {
            $status_id = $obj->status_id;
            echo "- $status_id " . $obj->status . "<br/>";
            if ($status_id==6 && $rsVoucher[$i]['vcode3']=="") {
                //Προώθηση αποστολής σε συνεργάτη
                $voucherid = $rsVoucher[$i]['id'];
                $voucher = new VOUCHERS($db1, $rsVoucher[$i]['id']);
                $myVcode3 = $obj->additional_data;
                $voucher->set_vcode3(extractInParentheses($myVcode3));
                $voucher->Savedata();
                echo "VOUCHER CODE3 UPDATED: " . $myVcode3 . "<br/>";
            }
        }
    }
}