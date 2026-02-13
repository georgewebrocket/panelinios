<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

if (!isset($_SESSION['authorized'])) {
     header("Location: index.php");
}

if (isset($_SESSION['authorized']) && $_SESSION['authorized']<>1) {
    header("Location: index.php");
    exit;
}

//$_SESSION['start'] = time(); // taking now logged in time
//
//if(!isset($_SESSION['expire'])){
//    $_SESSION['expire'] = $_SESSION['start'] + (1* 10) ; // ending a session in 30 seconds
//}
$now = time(); // checking the time now when home page starts

if(isset($_SESSION['expire'])){
    if($now > $_SESSION['expire'])
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>