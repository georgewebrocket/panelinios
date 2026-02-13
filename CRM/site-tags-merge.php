<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('../php/dataobjects.php');
require_once('php/controls.php');
require_once('php/db.php');

class connSite
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
}
$dbSite = new DB(connSite::$connstr, connSite::$username, connSite::$password);

$ids = $_REQUEST['ids'];
$ar = explode(",", $ids);

if ($_POST) {
  
  $theTag = $_POST['tag'];
  echo $theTag . "<br/>";
  echo $ids . "<br/>";
  
  $ids = str_replace($theTag, "0", $ids);
  //$ids = substr($ids, 0, strlen($ids)-1);
  
  $sql = "UPDATE company_tags SET tag_id=$theTag WHERE tag_id IN ($ids)";
  echo $sql . "<br/>";
  $ret = $dbSite->execSQL($sql);
  
  $sql = "DELETE FROM tags WHERE id IN ($ids)";
  echo $sql . "<br/>";
  $ret = $dbSite->execSQL($sql);
  
  
  
}

include "_thePopupHeader.php";

?>


<?php if (!$_POST) { ?>
<h1>Select tag</h1>

<form action="site-tags-merge.php" method="post" style="line-height: 30px; font-size: 18px;">
  
  <?php
  
  for ($i=0;$i<count($ar);$i++) {
    
    if ($ar[$i]!="") {
      
      $tagId = $ar[$i];
      $tagDescription = func::vlookup("description", "tags", "id=$tagId", $dbSite);
      
      echo <<<EOT
      
      <input type="radio" id="tag$i" name="tag" value="$tagId">
      <label for="tag$i">$tagDescription</label><br>
      
EOT;
      
    }
    
    
  }
  
  ?>
  
  <input type="hidden" name="ids" value="<?php echo $ids ?>" />
  
  <br/><br/>
  <input type="submit" name="btnMerge" value="Merge" />
  
  
</form>
<?php } else { ?>
<h2>Tags merged successfully</h2>
<input type="button" class="button" value="Close and Refresh List" onclick="window.parent.location.reload(false);" />
<?php }