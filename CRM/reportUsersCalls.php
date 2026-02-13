<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// ini_set('display_errors',1); 
// error_reporting(E_ALL);

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

//newcalls
$sql1 = "SELECT COMPANIES_STATUS.userid, COUNT(COMPANIES.id) AS MYCOUNT 
    FROM COMPANIES 
    INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
    WHERE COMPANIES.active=1 
    AND (COMPANIES.commstatus IS NULL OR COMPANIES.commstatus='')
    AND COMPANIES_STATUS.status = 3
    GROUP BY COMPANIES_STATUS.userid";

$rsNewcalls = $db1->getRS($sql1);



//recalls
$sql2 = "SELECT COMPANIES_STATUS.userid, COUNT(COMPANIES.id) AS MYCOUNT
    FROM COMPANIES 
    INNER JOIN COMPANIES_STATUS ON COMPANIES.id = COMPANIES_STATUS.companyid
    WHERE COMPANIES.active=1 
    AND (COMPANIES.commstatus IS NOT NULL AND COMPANIES.commstatus<>'')
    AND COMPANIES_STATUS.status = 3
    GROUP BY COMPANIES_STATUS.userid";

$rsRecalls = $db1->getRS($sql2);

$rsUsersRN = $db1->getRS(
        "SELECT id, fullname, active, '' AS RECALLS, '' AS NEWCALLS, "
        . "'' AS TCALLS FROM USERS ORDER BY fullname");

$recallsCount = 0;
$newcallsCount = 0;
$totalcallsCount = 0;

for ($i = 0; $i < count($rsUsersRN); $i++) {
    
    $userid = $rsUsersRN[$i]['id'];
    
    $recalls = arrayfunctions::findInArray($rsRecalls, 
            array("userid"), array($userid));
    $rsUsersRN[$i]['RECALLS'] = $recalls['MYCOUNT'];
    $recallsCount += $recalls['MYCOUNT'];
    
    $newcalls = arrayfunctions::findInArray($rsNewcalls, 
            array("userid"), array($userid));
    $rsUsersRN[$i]['NEWCALLS'] = $newcalls['MYCOUNT'];
    $newcallsCount += $newcalls['MYCOUNT'];
        
    $rsUsersRN[$i]['TCALLS'] = $rsUsersRN[$i]['NEWCALLS'] + $rsUsersRN[$i]['RECALLS'];
    $totalcallsCount += $rsUsersRN[$i]['TCALLS'];
    
    $rsUsersRN[$i]['RECALLS'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=recalls&userid=$userid\" >" 
            . $rsUsersRN[$i]['RECALLS'] . "</a>";
    $rsUsersRN[$i]['NEWCALLS'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=newcalls&userid=$userid\" >" 
            . $rsUsersRN[$i]['NEWCALLS'] . "</a>";
    $rsUsersRN[$i]['TCALLS'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=totalcalls&userid=$userid\" >" 
            . $rsUsersRN[$i]['TCALLS'] . "</a>";
    
    
}

// $recallsCount = func::nrToCurrency($recallsCount, $locale);
// $newcallsCount = func::nrToCurrency($newcallsCount, $locale);
// $totalcallsCount = func::nrToCurrency($totalcallsCount, $locale);



?>
<html>
    <head>
        <?php include "./_head.php"; ?> 
        
        <style>
            
            #grid1, #grid2 {
                max-width: 800px;
                margin-left: 20px;
            }
            
            
            
        </style>
        
    </head>
    <body>
        
        <?php include "blocks/header.php"; ?>
        <?php include "blocks/menu.php"; ?> 
        
        <div class="main">
            
            <h1 style="margin-left: 20px;">Εκκρεμείς κλήσεις (Recalls/New calls)</h1>
            <?php            
            if ($rsUsersRN) {
                $grid1 = new datagrid("grid1", $db1, "", 
                    array("id", "fullname", "active", "RECALLS", "NEWCALLS", "TCALLS"), 
                    array("ID", "Χρήστης", "Active", "Recalls", "New calls", "ΣΥΝΟΛΟ"));
                $grid1->set_rs($rsUsersRN);
                
                $grid1->setFooter(array("","","", $recallsCount, $newcallsCount, $totalcallsCount));
                
                $grid1->get_datagrid();
                
            }            
            ?>
            
            
            
            
            
        </div>
        
        
        <div style="clear: both"></div>   
        <?php include "blocks/footer.php"; ?> 
        

    </body>
</html>


