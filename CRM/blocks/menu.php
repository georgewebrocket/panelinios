<?php

$menu0 = $db1->getRS("SELECT * FROM MENU WHERE active=1 AND parent=0 ORDER BY morder");
echo "<div class=\"menu\">";
echo "<div style=\"float:left; font-weight:700; padding:10px 30px\"> PANELINIOS CRM </div>";
echo "<ul class=\"level0\">";

$noCacheHash = "&chash=" . date("YmdHis");

$class="";

for ($i=0;$i<count($menu0);$i++) {
    $class="";
    //if ($_SESSION['user_profile'] >= $menu0[$i]['auth']) {
    if (strpos($_SESSION['user_access'], "[".$menu0[$i]['auth']."]")!==false) {
        if ($menu0[$i]['link']!='') { 
            $link = explode("###",$menu0[$i]['link']);
            if (count($link)>1) { $class="fancybox";}
            
            $myLink = count(explode("?",$menu0[$i]['link']))==1? $menu0[$i]['link']."?".$ltoken.$noCacheHash: $menu0[$i]['link']."&".$ltoken.$noCacheHash;
            
            $newTab = isset($menu0[$i]['newtab']) && $menu0[$i]['newtab']==1? "target=\"_blank\"": "";
            
            echo "<li class=\"level0\"><a $newTab class=\"$class\" href=\"$myLink\">".$menu0[$i][$lang]."</a>"; 
            //echo "<li class=\"level0\"><a href=\"".$menu0[$i]['link']."?".$ltoken."\">".$menu0[$i][$lang]."</a>"; 
        }
        else { echo "<li class=\"level0\">".$menu0[$i][$lang];}
    } 
    
    $menu1 = $db1->getRS("SELECT * FROM MENU WHERE active=1 AND parent=".$menu0[$i]['id']." ORDER BY morder");
    if (count($menu1)>0) {
        echo "<ul class=\"level1\">";
        for ($j=0;$j<count($menu1);$j++) {
            $class="";
            //if ($_SESSION['user_profile'] >= $menu1[$j]['auth']) {
            if (strpos($_SESSION['user_access'], "[".$menu1[$j]['auth']."]")!==false) {
                if ($menu1[$j]['link']!='') { 
                    $link = explode("###",$menu1[$j]['link']);
                    if (count($link)>1) { $class="fancybox";}
                    
                    $myLink = count(explode("?",$menu1[$j]['link']))==1? $menu1[$j]['link']."?".$ltoken.$noCacheHash: $menu1[$j]['link']."&".$ltoken.$noCacheHash;
                    
                    $newTab = isset($menu1[$i]['newtab']) && $menu1[$j]['newtab']==1? "target=\"_blank\"": "";
                    
                    echo "<li class=\"level1\"><a $newTab class=\"$class\" href=\"$myLink\">".$menu1[$j][$lang]."</a>";
                    //echo "<li class=\"level1\"><a href=\"".$menu1[$j]['link']."?".$ltoken."\">".$menu1[$j][$lang]."</a>"; 
                }            
                else { echo "<li class=\"level1\">".$menu1[$j][$lang];}
            }
            $menu2 = $db1->getRS("SELECT * FROM MENU WHERE active=1 AND parent=".$menu1[$j]['id']." ORDER BY morder");
            if (count($menu2)>0) {
                echo "<ul class=\"level2\">";
                for ($k=0;$k<count($menu2);$k++) {
                    $class="";
                    //if ($_SESSION['user_profile'] >= $menu2[$k]['auth']) {
                    if (strpos($_SESSION['user_access'], "[".$menu2[$k]['auth']."]")!==false) {
                        if ($menu2[$k]['link']!='') { 
                            $link = explode("###",$menu2[$k]['link']);
                            if (count($link)>1) { $class="fancybox";}
                            
                            $myLink = count(explode("?",$menu2[$k]['link']))==1? $menu2[$k]['link']."?".$ltoken.$noCacheHash: $menu2[$k]['link']."&".$ltoken.$noCacheHash;
                            
                            echo "<li class=\"level2\"><a class=\"$class\" href=\"$myLink\">".$menu2[$k][$lang]."</a>"; 
                            //echo "<li class=\"level2\"><a href=\"".$menu2[$k]['link']."?".$ltoken."\">".$menu2[$k][$lang]."</a>"; 
                        }
                        else { echo "<li class=\"level2\">".$menu2[$k][$lang];}
                    }
                }
                echo "</li>";
                echo "</ul>";
            }
            echo "</li>";
        }
        echo "<div style=\"clear:both;\"></div>";
        echo "</ul>";
    }
    echo "</li>";
}
echo "<div style=\"clear:both;\"></div>";
echo "</ul>";
echo "</div>";

?>

