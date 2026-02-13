<?php


/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');


class connSite
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
}

$dbSite = new DB(connSite::$connstr, connSite::$username, connSite::$password);



$rs = FALSE;
$term1 = "";
$term2 = "";
$start = "";
$stop = "";


if ($_REQUEST) {
  
  $term1 = $_REQUEST['term1'];
  $term2 = $_REQUEST['term2'];
  
  $start = $_REQUEST['start']!=""? $_REQUEST['start']:1;
  $stop = $_REQUEST['stop']!=""? $_REQUEST['stop']: $start + 500 - 1 ;
  $limit = $stop - $start +1;
  $offset = $start - 1;
  
  $sql = "SELECT id, description, '' AS MARK FROM tags WHERE id>0 ";
  if ($term1!="") {
    $sql .= " AND description LIKE '%$term1%' ";
  }
  
  if ($term2!="") {
    $sql .= " OR description LIKE '%$term2%' ";
  }
  
  if (isset($_REQUEST['latest'])) {
    $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset  ";
  }
  else {
    $sql .= " ORDER BY description LIMIT $limit OFFSET $offset  ";
  }
  
  
  
  $rs = $dbSite->getRS($sql);

  for ($i=0;$i<count($rs);$i++) {
    $id = $rs[$i]['id'];
    $rs[$i]['MARK'] = "<input type=\"checkbox\" class=\"mark-tag\" data-id=\"$id\" style=\"width:20px; height:20px\" />";
    
  }
  
}



include '_theHeader.php';

?>

<h1>Site tags</h1>

<form action="site-tags.php" method="get">
  
  <input type="text" name="term1" value="<?php echo $term1 ?>" style="padding:5px; width:150px" />
  - OR -
  <input type="text" name="term2" value="<?php echo $term2 ?>" style="padding:5px; width:150px" />
  ||  Records
  <input type="text" name="start" value="<?php echo $start ?>" style="padding:5px; width:100px" />
  -
  <input type="text" name="stop" value="<?php echo $stop ?>" style="padding:5px; width:100px" />
  
  <input type="submit" value="Search" />
  
  <input type="button" id="btnMerge" value="Merge" />
  
  <a class="button" href="site-tags.php?latest=1" >Πρόσφατα tags</a>
  
</form>


<?php

if ($rs) {
  
  $grid = new datagrid("grid", $dbSite, "", 
        array("id","description", "MARK"), 
        array("ID","ΠΕΡΙΓΡΑΦΗ", "SELECT"), 
        $ltoken);
  $grid->set_rs($rs);
  $grid->set_edit("edit-site-tag.php", "EDIT");
  $grid->get_datagrid();
  
}


$myScript = <<<EOT

<script>

$(function() {
  
  $('#btnMerge').click(function() {
    
    var ids = '';
    
    $('.mark-tag').each(function() {
      if ($(this).is(':checked')) {
        var id = $(this).data('id');
        ids += id + ',';
      }
    });
    
    ids = ids.substring(0, ids.length - 1);
    
    console.log(ids);
    
    var src = 'https://crm.panelinios.gr/site-tags-merge.php?ids='+ids;
    console.log(src);
    
    $.fancybox.open({
    	href  : src,
    	type : 'iframe'
    });
    
  });
  
});

</script>


EOT;


include '_theFooter.php';
