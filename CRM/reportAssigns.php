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

$rsUsersNC = NULL;
$rsUsersRE = NULL;
$date1 = "";
$date2 = "";


if ($_POST) {
    
        //     $sql0 = "UPDATE `ACTIONS` SET `product_cat` = digits(`product_categories`) WHERE status2 IN (18,19) AND product_cat IS NULL";
        $sql0 = "UPDATE `ACTIONS` SET `product_cat` = REGEXP_REPLACE(`product_categories`, '[^0-9]', '') WHERE status2 IN (18,19) AND product_cat IS NULL";
    $ret = $db1->execSQL($sql0);
    
    
    $date1 = $_POST['t_date1'];
    $date1a = textbox::getDate($date1, $locale);
    $date1a = func::str14toDateTime($date1a, "-", "EN");
    //echo $date1a;
    
    $date2 = $_POST['t_date2'];
    $date2x = $date2==""? $date1: $date2; 
    $date2a = textbox::getDate($date2x, $locale);
    $date2a = func::str14toDateTime($date2a, "-", "EN");
    $date2a = substr($date2a, 0, 10) . " 23:59:59";
        
    $sql = "SELECT USERS.id AS userid, `STATUS`.`description`, `STATUS`.id AS STATUSID, 
            COUNT(COMPANIES.id) AS MYCOUNT
            FROM `ACTIONS` 
            INNER JOIN COMPANIES ON ACTIONS.company = COMPANIES.id
            INNER JOIN COMPANIES_STATUS ON ACTIONS.company = COMPANIES_STATUS.companyid 
            AND ACTIONS.product_cat = COMPANIES_STATUS.productcategory
            INNER JOIN `STATUS` ON COMPANIES_STATUS.status = `STATUS`.id
            INNER JOIN USERS ON ACTIONS.user = USERS.id
            WHERE ACTIONS.status2 = 18
            AND ACTIONS.atimestamp >=? AND ACTIONS.atimestamp <=?
            GROUP BY USERS.id, `STATUS`.`description`, `STATUS`.id";
    
    //echo $sql;
    //var_dump(array($date1a, $date2a));
    
    $rs = $db1->getRS($sql, array($date1a, $date2a));
        
    if ($rs) {
        
        $rsUsersNC = $db1->getRS("SELECT id, fullname, '' AS RECALL, '' AS  NEGATIVE, "
                . "'' AS AGREED, '' AS HASPAYED, '' AS MYRETURN, '' AS MYTOTAL "
                . "FROM USERS WHERE active=1 AND is_agent=1 ORDER BY fullname ");
        
        
        $recallsCount = 0;
        $negativeCount = 0;
        $agreedCount = 0;
        $payedCount = 0;
        $returnCount = 0;
        $totalCount = 0;
        
        
        for ($i = 0; $i < count($rsUsersNC); $i++) {
            
            $userid = $rsUsersNC[$i]['id'];

            //recall
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 3));
            //var_dump($rsFind);
            $rsUsersNC[$i]['RECALL'] = $rsFind['MYCOUNT'];
            $recallsCount += $rsUsersNC[$i]['RECALL'];
            $rsUsersNC[$i]['RECALL'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=3&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['RECALL'] . "</a>";
            
            //arnitikos
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 4));
            $rsUsersNC[$i]['NEGATIVE'] = $rsFind['MYCOUNT'];
            //arnisi ananeosis
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 15));
            $rsUsersNC[$i]['NEGATIVE'] += $rsFind['MYCOUNT'];
            $negativeCount += $rsUsersNC[$i]['NEGATIVE'];
            $rsUsersNC[$i]['NEGATIVE'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=4,15&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['NEGATIVE'] . "</a>";
            
            //simfonise
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 5));
            $rsUsersNC[$i]['AGREED'] = $rsFind['MYCOUNT'];
            //ektipothike
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 6));
            $rsUsersNC[$i]['AGREED'] += $rsFind['MYCOUNT'];
            $agreedCount += $rsUsersNC[$i]['AGREED'];
            $rsUsersNC[$i]['AGREED'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=5,6&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['AGREED'] . "</a>";
            
            //plirose
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 9));
            $rsUsersNC[$i]['HASPAYED'] = $rsFind['MYCOUNT'];
            $payedCount += $rsUsersNC[$i]['HASPAYED'];
            $rsUsersNC[$i]['HASPAYED'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=9&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['HASPAYED'] . "</a>";
            
            //epistrofi
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersNC[$i]['id'], 8));
            $rsUsersNC[$i]['MYRETURN'] = $rsFind['MYCOUNT'];
            $returnCount += $rsUsersNC[$i]['MYRETURN'];
            $rsUsersNC[$i]['MYRETURN'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=8&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['MYRETURN'] . "</a>";
            
            //total
            $totalFind = arrayfunctions::findInArraySum($rs, 
                    array("userid"), 
                    array($rsUsersNC[$i]['id']),
                    "MYCOUNT");
            $rsUsersNC[$i]['MYTOTAL'] = $totalFind;
            $totalCount += $rsUsersNC[$i]['MYTOTAL'];
            $rsUsersNC[$i]['MYTOTAL'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=0&actionstatus=18&date1=$date1a&date2=$date2a\" >" . $rsUsersNC[$i]['MYTOTAL'] . "</a>";
            
        }
        
                       
        
    }
    
    
    $sql2 = "SELECT USERS.id AS userid, `STATUS`.`description`, `STATUS`.id AS STATUSID, 
            COUNT(COMPANIES.id) AS MYCOUNT
            FROM `ACTIONS` 
            INNER JOIN COMPANIES ON ACTIONS.company = COMPANIES.id
            INNER JOIN COMPANIES_STATUS ON ACTIONS.company = COMPANIES_STATUS.companyid 
            AND ACTIONS.product_cat = COMPANIES_STATUS.productcategory
            INNER JOIN `STATUS` ON COMPANIES_STATUS.status = `STATUS`.id
            INNER JOIN USERS ON ACTIONS.user = USERS.id
            WHERE ACTIONS.status2 = 19
            AND ACTIONS.atimestamp >=? AND ACTIONS.atimestamp <=?
            GROUP BY USERS.id, `STATUS`.`description`, `STATUS`.id";
        
    $rs = $db1->getRS($sql2, array($date1a, $date2a));
        
    if ($rs) {
        
        $recalls2Count = 0;
        $negative2Count = 0;
        $agreed2Count = 0;
        $payed2Count = 0;
        $return2Count = 0;
        $total2Count = 0;
        
        $rsUsersRE = $db1->getRS("SELECT id, fullname, '' AS RECALL, '' AS  NEGATIVE, "
                . "'' AS AGREED, '' AS HASPAYED, '' AS MYRETURN, '' AS MYTOTAL "
                . "FROM USERS WHERE active=1 AND is_agent=1 ORDER BY fullname ");
        
        for ($i = 0; $i < count($rsUsersRE); $i++) {
            
            $userid = $rsUsersRE[$i]['id'];

            //recall
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 3));
            //var_dump($rsFind);
            $rsUsersRE[$i]['RECALL'] = $rsFind['MYCOUNT'];
            $recalls2Count += $rsUsersRE[$i]['RECALL'];
            $rsUsersRE[$i]['RECALL'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=3&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['RECALL'] . "</a>";
            
            //arnitikos
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 4));
            $rsUsersRE[$i]['NEGATIVE'] = $rsFind['MYCOUNT'];
            //arnisi ananeosis
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 15));
            $rsUsersRE[$i]['NEGATIVE'] += $rsFind['MYCOUNT'];
            $negative2Count += $rsUsersRE[$i]['NEGATIVE'];
            $rsUsersRE[$i]['NEGATIVE'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=4,15&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['NEGATIVE'] . "</a>";
            
            //simfonise
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 5));
            $rsUsersRE[$i]['AGREED'] = $rsFind['MYCOUNT'];
            //ektipothike
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 6));
            $rsUsersRE[$i]['AGREED'] += $rsFind['MYCOUNT'];
            $agreed2Count += $rsUsersRE[$i]['AGREED'];
            $rsUsersRE[$i]['AGREED'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=5,6&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['AGREED'] . "</a>";
            
            //plirose
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 9));
            $rsUsersRE[$i]['HASPAYED'] = $rsFind['MYCOUNT'];
            $payed2Count += $rsUsersRE[$i]['HASPAYED'];
            $rsUsersRE[$i]['HASPAYED'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=9&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['HASPAYED'] . "</a>";
            
            
            //epistrofi
            $rsFind = arrayfunctions::findInArray($rs, 
                    array("userid", "STATUSID"), 
                    array($rsUsersRE[$i]['id'], 8));
            $rsUsersRE[$i]['MYRETURN'] = $rsFind['MYCOUNT'];
            $return2Count += $rsUsersRE[$i]['MYRETURN'];
            $rsUsersRE[$i]['MYRETURN'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=8&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['MYRETURN'] . "</a>";
            
            //total
            $totalFind = arrayfunctions::findInArraySum($rs, 
                    array("userid"), 
                    array($rsUsersRE[$i]['id']),
                    "MYCOUNT");
            $rsUsersRE[$i]['MYTOTAL'] = $totalFind;
            $total2Count += $rsUsersRE[$i]['MYTOTAL'];
            $rsUsersRE[$i]['MYTOTAL'] = "<a class=\"fancybox\" href=\"getCompanies.php?mytype=callswithstatus&userid=$userid&status=0&actionstatus=19&date1=$date1a&date2=$date2a\" >" . $rsUsersRE[$i]['MYTOTAL'] . "</a>";
            
        }
        
        $recalls2Count = func::nrToCurrency($recalls2Count, $locale);
        $negative2Count = func::nrToCurrency($negative2Count, $locale);
        $agreed2Count = func::nrToCurrency($agreed2Count, $locale);
        $payed2Count = func::nrToCurrency($payed2Count, $locale);
        $return2Count = func::nrToCurrency($return2Count, $locale);
        $total2Count = func::nrToCurrency($total2Count, $locale);
               
        
    }


    //show log
    $date1 = $_POST['t_date1'];
    $date1b = textbox::getDate($date1, $locale);

    $date2 = $_POST['t_date2'];
    $date2b = $date2==""? $date1: $date2; 
    $date2b = textbox::getDate($date2b, $locale);
    $date2b = substr($date2b, 0, 8) . "235959";

    $sql = "SELECT * FROM CUSTOMER_ASSIGNMENTS WHERE ca_datetime>=? AND ca_datetime<=?";
    $rsAssigns = $db1->getRS($sql, array($date1b,$date2b));
       
    
}

?>
<html>
<head>
    <?php include "./_head.php"; ?> 

    <style>

        #grid1, #grid2 {
            max-width: 1000px;
            margin-left: 20px;
        }



    </style>

</head>
<body>

    <?php include "blocks/header.php"; ?>
    <?php include "blocks/menu.php"; ?> 

    <div class="main">

        <h1 style="margin-left: 20px;">Έλεγχος αναθέσεων (Νέες κλήσεις / Ανανεώσεις)</h1>
        
        
        <form action="reportAssigns.php" method="post" style="max-width: 500px">
            
            <?php
            
            $t_date1 = new textbox("t_date1", "Ημερ. από", $date1);
            $t_date1->set_format("DATE");
            $t_date1->set_locale($locale);                        
            $t_date1->get_Textbox();

            $t_date2 = new textbox("t_date2", "έως", $date2);
            $t_date2->set_format("DATE");
            $t_date2->set_locale($locale);                        
            $t_date2->get_Textbox();
            
            $btnSearch = new button("btnSearch", "SEARCH");
            $btnSearch->get_button();
            
            ?>
            
            <div class="clear"></div>
            
        </form>
        
        <div class="spacer-50"></div>


        <div style="margin-left: 20px;">
                <?php 
                
                foreach ($rsAssigns as $assign) {
                echo "<h2>".func::str14toDate($assign['ca_datetime']) . " " . $assign['title'] ."</h2>";
                echo "<p>" . $assign['details'] . "</p>";
                }
                
                ?>
        </div>

        <hr style="margin-top:30px;margin-bottom:30px;">


        
        <?php
        
        if ($rsUsersNC) {
            
            echo '<h2 style="margin-left: 20px;">Νέες κλήσεις</h2>';
            
            $grid1 = new datagrid("grid1", $db1, "", 
                array("id", "fullname", "RECALL", "NEGATIVE", "AGREED","HASPAYED","MYRETURN", "MYTOTAL"), 
                array("ID", "Χρήστης", "Recalls", "Αρνητικοί", "Συμφώνησε","Πλήρωσε","Επιστροφή", "Total"));
            $grid1->set_rs($rsUsersNC);
            
            $grid1->setFooter(array("", "", $recallsCount, $negativeCount, $agreedCount, 
                $payedCount, $returnCount, $totalCount));

            $grid1->get_datagrid();
            
        }
        
        ?>
        
        
        <div class="spacer-50"></div>
        
        
        
        
        <?php
        
        if ($rsUsersRE) {
            
            echo '<h2 style="margin-left: 20px;">Ανανεώσεις</h2>';
            
            $grid2 = new datagrid("grid2", $db1, "", 
                array("id", "fullname", "RECALL", "NEGATIVE", "AGREED","HASPAYED","MYRETURN", "MYTOTAL"), 
                array("ID", "Χρήστης", "Recalls", "Αρνητικοί", "Συμφώνησε","Πλήρωσε","Επιστροφή", "Total"));
            $grid2->set_rs($rsUsersRE);

            $grid2->setFooter(array("", "", $recalls2Count, $negative2Count, $agreed2Count, 
                $payed2Count, $return2Count, $total2Count));

            $grid2->get_datagrid();
            
        }
        
        ?>
        
        <div class="spacer-50"></div>
        
        
    </div>
    
    
    <div style="clear: both"></div>   
    
    <?php include "blocks/footer.php"; ?> 
        

</body>
</html>