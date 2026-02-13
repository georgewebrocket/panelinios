<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

//Encryption:
$textToEncrypt = "Γιώργος Παπαγιάννης";
$encryptionMethod = "AES-256-CBC";
$secretHash = "MyCFsK23FF!@";

//$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

$iv_size = openssl_cipher_iv_length($encryptionMethod);
echo "IV-SIZE " . $iv_size . "<br/>";
$iv = openssl_random_pseudo_bytes($iv_size);
echo $iv . "<br/>";
$encryptedText = openssl_encrypt($textToEncrypt,$encryptionMethod,$secretHash, 0, $iv);

echo "Encrypted text " . $encryptedText . " - " . strlen($encryptedText) . "<br/>" ;

//Decryption:
//$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($encryptionMethod));
//echo $iv . "<br/>";
$decryptedText = openssl_decrypt($encryptedText, $encryptionMethod, $secretHash, 0, $iv);
print "Decrypted Text: ". $decryptedText . " - " . strlen($decryptedText);