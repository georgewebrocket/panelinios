<?php

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$sql = "SELECT id, catalogueid FROM COMPANIES ORDER BY id";
$rs = $db1->getRS($sql);

for ($i=0;$i<count($rs);$i++) {
    
  if ($rs[$i]['catalogueid']!="" && $rs[$i]['catalogueid']!="") {
    
    $sql = "SELECT id, url_online, url_cms, url_rewrite_gr FROM companies WHERE id=?";
    $rsOnline = $dbSite->getRS($sql, array($rs[$i]['catalogueid']));
    if ($rsOnline) {
        if ($rsOnline[0]['url_rewrite_gr']!='') {
            $url_online = "https://www.epagelmatias.gr/" . $rsOnline[0]['url_rewrite_gr'];
        }
        else {
            $url_online = "https://www.epagelmatias.gr/εταιρεια/" . $rsOnline[0]['id'];
        }
        
        $sql = "UPDATE COMPANIES SET online_url=? WHERE id=?";
        $res = $db1->execSQL($sql, array($url_online, $rs[$i]['id']));
        echo $rs[$i]['id'] . " - " . $url_online . "<br/>";
        
    }
    
  }
    
}


