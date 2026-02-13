<?php

//ini_set('display_errors',1); 
//error_reporting(E_ALL);

require_once('php/dataobjects.php');
header('Content-Type: text/html; charset=utf-8');
$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

$delim1 = "%%%";
$delim2 = "$$$";
$delim3 = "###";

$sql = "SELECT * FROM PRINTJOB WHERE pstatus=1 ORDER BY id LIMIT 5";
$printjobs = $db1->getRS($sql);

for ($i=0;$i<count($printjobs);$i++) {
    
    $printjob = new PRINTJOB($db1,$printjobs[$i]['id'],$printjobs);
    
    echo $printjob->get_id();
    echo $delim2;
    
    echo $printjob->get_ptemplate();
    echo $delim2;
    
    echo $printjob->get_printername();
    echo $delim2;
    
    //get details
    $sql = "SELECT * FROM PRINTDETAILS WHERE jobid=?";
    $printdetails = $db1->getRS($sql, array($printjobs[$i]['id']));
    
    //bookmarks
    for ($k=0;$k<count($printdetails);$k++) {
        $printdetail = new PRINTDETAILS($db1,$printdetails[$k]['id'],$printdetails);
        echo $printdetail->get_bookmark();
        if ($k<count($printdetails)-1) {
            echo $delim3;
        }
    }
    echo $delim2;
    
    //texts
    for ($k=0;$k<count($printdetails);$k++) {
        $printdetail = new PRINTDETAILS($db1,$printdetails[$k]['id'],$printdetails);
        echo $printdetail->get_ptext();
        if ($k<count($printdetails)-1) {
            echo $delim3;
        }
    }
    echo $delim2;
    
    //formats
    for ($k=0;$k<count($printdetails);$k++) {
        $printdetail = new PRINTDETAILS($db1,$printdetails[$k]['id'],$printdetails);
        echo $printdetail->get_pformat();
        if ($k<count($printdetails)-1) {
            echo $delim3;
        }
    }
    
    
    if ($i<count($printjobs)-1) {
        echo $delim1;
    }
}


?>