<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once('php/config.php');
require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');

$id = $_REQUEST['id'];
$emailTempl = new EMAIL_TEMPLATES($db1, $id);


if ($_POST) {
    $emailTempl->set_description($_POST['t_description']);
    $emailTempl->set_bodytext($_POST['t_bodytext']);
    
    $emailTempl->Savedata();
    $id = $emailTempl->get_id();
    
}


$myStyle = <<<EOT
<style>
        
    #t_bodytext {
        height: 400px;
    }
        
</style>
        
EOT;

include "_thePopupHeader.php";

?>

<form action="editEmailTemplate.php?id=<?php echo $id; ?>" method="post">
    
    <h1 style="padding-left: 0px">Email template</h1>
    
    <?php
    
    $t_id = new textbox("t_id", "ID", $emailTempl->get_id());
    $t_id->set_disabled();
    $t_id->get_Textbox();
    
    $t_description = new textbox("t_description", "Description", $emailTempl->get_description());
    $t_description->get_Textbox();
    
    $t_bodytext = new textbox("t_bodytext", "bodytext", $emailTempl->get_bodytext());
    $t_bodytext->set_multiline();
    $t_bodytext->get_Textbox();
    
    echo "<div style=\"clear:both; height:30px\"></div>";
    
    $btnOK = new button("brnOK", "Αποθήκευση");
    $btnOK->get_button();
    
    
    ?>
    
    <div class="clear"></div>
        
    
</form>


<?php


$myScript = <<<EOT

<!--<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>-->

<script src="https://cdn.tiny.cloud/1/fiq8lm63smdu8mc2fhs375nntdcf27e6r8gdeb3c5zqzllnl/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
        selector:'#t_bodytext',
        plugins: "link, code, image, table ",
        entity_encoding : "raw",
        relative_urls: false,
        convert_urls: false
    }); 

</script>        
   
        
EOT;

include "_thePopupFooter.php";