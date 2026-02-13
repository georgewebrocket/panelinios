<div class="header" style="position: fixed; top:0px; left:0px">
    
    <div class="spacer-50"></div>
    <div class="spacer-10"></div>
    
    <div class="col-12">
        
        <?php
        $myUser = new USERS($db1, $_SESSION['user_id']);
        $userFullname = $myUser->get_fullname();
        $userPhoto = $myUser->get_photo();
        
        ?>
        
        <div style="text-align: center; margin-bottom: 50px;">
            <a class="fancybox900" href="editUserPref.php?id=<?php echo $_SESSION['user_id'] ?>">
                <div style="width:90px; height: 90px; margin: auto; border-radius: 45px; border:2px solid #fc6; margin-bottom: 10px; background-image: url(<?php echo $userPhoto ?>); background-size: cover; background-position: center;"></div>
            </a>
            
            
            <?php echo $userFullname; ?>
            
        </div>
        
        <div style="background-color: #ddd; padding:10px; color:#222; margin-bottom: 30px;">
            
            <?php
            $sDate3 = date("Ymd"); //func::str14toDate($sDate, "-","EN");
            $criteriaSDate3 = " tdatetime='".$sDate3."000000' ";
            $criteriaStatus3 = " AND status IN (1,2) AND transactiontype=1 ";
            $sql3 = "SELECT COUNT(id) AS MyCount FROM TRANSACTIONS A WHERE "
                    . $criteriaSDate3 . $criteriaStatus3;
            $rs3 = $db1->getRS($sql3);
            echo "<h3>Πωλ. ημέρας </h3>";
            echo "PANELINIOS : ".$rs3[0]['MyCount'];
            echo "<br/>";
            $rs3b = $db2->getRS($sql3);
            echo "EPAGELMATIAS: ".$rs3b[0]['MyCount'];
            
            ?>
            
        </div>
        
    </div>
    
    <div class="col-12">
        <?php if (strpos($_SESSION['user_access'], "[1]")!==false) { ?>
        <h3>Στατιστικά</h3>
        <?php include "compare-users.php"; ?>
        <?php } ?>
        
        
        
    </div>
    
    <div style="clear: both"></div>
    
    
    
</div>

