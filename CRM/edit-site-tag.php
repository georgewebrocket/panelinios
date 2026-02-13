<?php


ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
//require_once('php/session.php');
require_once('../php/dataobjects.php');
require_once('php/controls.php');
require_once('php/db.php');
//require_once('inc.php');
require_once('../_norm.php');

class connSite
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panelinios_site;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
}

$dbSite = new DB(connSite::$connstr, connSite::$username, connSite::$password);

$id = $_REQUEST['id'];

$tag = new tags($dbSite, $id);
$description = $tag->get_description();
$alt_description = $tag->get_alt_description();

$save = FALSE;

if ($_POST) {
  $description = $_POST['t_description'];
  $alt_description = $_POST['t_alt_description'];
  
  $tag->set_description($description);
  $tag->set_alt_description($alt_description);
  
  $normDescriptions = norm($description . " " . $alt_description);
  $tag->set_norm_descriptions($normDescriptions);
  
  $seo_url = convert_to_url($description);
  $tag->set_seo_url($seo_url);
  
  $save = $tag->Savedata();
  
}

include "_thePopupHeader.php";

?>

<h1>Site Tag</h1>

<?php
if ($save) {
  echo "<h2>Τα δεδομένα αποθηκεύτηκαν</h2>";
}

?>

<form action="edit-site-tag.php" method="post">
  
  <?php
  
  $t_id = new textbox("id", "ID", $tag->get_id(), "");
  $t_id->get_Textbox();
  
  $t_description = new textbox("t_description", "Περιγραφή", $tag->get_description(), "");
  $t_description->get_Textbox();
  
  $t_alt_description = new textbox("t_alt_description", "Εναλλ. Περιγραφή", $tag->get_alt_description(), "");
  $t_alt_description->get_Textbox();
  
  $btnOK = new button("BtnOk", "Αποθήκευση"); 
  $btnOK->get_button_simple();
  
  
  ?>
  
  <input type="button" class="button" value="Close and Refresh List" onclick="window.parent.location.reload(false);" />
  
  
</form>

<?php
include "_thePopupFooter.php";