<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("IMPORTCSV",$lang,$db1);

$msg = "";
$import = 0;
$arrNull = array('...');
$strFields="";
$csvFileName = "";
$csvFilePath = "importcsv/";
$hasHeader = 0;
$arrfields = array("...","id","companyname","phone1","phone2","mobilephone",
    "contactperson","basiccategory","reference","area","geo_x","geo_y","address",
    "zipcode","email","website","facebook","twitter","package","discount","price",
    "vatzone","catalogueid","expires","username","password","user",
    "userdataentry","recalldate","recalltime","status","comment","phonecode","afm","doy","eponimia");

if (isset($_GET['import']) && $_GET['import'] == 1) {
    $err = 0;
    $import = 1;
    
    // get fields for SQL string
    if(isset($_POST['cField'])){
        $arrColumns = array_diff($_POST['cField'] , $arrNull);    
    }
    
    //if file CSV have or not have header
    if(isset($_POST['chkHasHeader'])){
        $hasHeader = 1;
    }
    
    //χαρακτήρα που διαχωρίζει τα πεδία (default = , )
    if(isset($_POST['txtDelimiterFields']) && $_POST['txtDelimiterFields'] != ""){
        $DelimiterFields = $_POST['txtDelimiterFields'];
    }
    else{
        $err = 1;
        $msg .= $l->l('blank_delimiter')."</br>";
    }
    //χαρακτήρα με τον οποίο θα αρχίζει και θα τελειώνει κάθε πεδίο (θα μπορεί να είναι κενό)
    if(isset($_POST['txtDelimStartEndField'])){
        $DelimStartEndField = $_POST['txtDelimStartEndField'];        
    }else{
        $DelimStartEndField = " ";
    }
//    if(isset($_POST['fileCSV'])){
        
        /**************FOR CSV begin************/
        //if there was an error uploading the file
        if ($_FILES["fileCSV"]["error"] > 0) {
            $err = 1;
            $msg .= $l->l('file_error')."</br>";
        }
        else {
            //Store file in directory "importcsv" with the name of "importcsv"Id".csv"
            $csvFileName = 'importcsv'.$_SESSION['user_id'].'.csv';
            move_uploaded_file($_FILES["fileCSV"]["tmp_name"], $csvFilePath . $csvFileName);   
            
            // Open the File.
            if (($handle = fopen($csvFilePath . $csvFileName, "r")) !== FALSE) {
                    
                // Set the parent multidimensional array key to 0.
                $nn = 0;
                while (($data = fgetcsv($handle, 65536, $DelimiterFields, $DelimStartEndField)) !== FALSE) {
                    // Count the total keys in the row.
                    $c = count($data);
                    // Populate the multidimensional array.
                    for ($x=0;$x<$c;$x++)
                    {
                        $csvarray[$nn][$x] = $data[$x];
                    }
                    $nn++;
                }
                // Close the File.
                fclose($handle);

                /*INSERT OR UPDATE DATABASE BEGIN*/
                //if has header get array without haeder
                if($hasHeader == 1){$arr0 = array_shift($csvarray); }
                $countColumn = count($csvarray[0]);

                //Create sql for INSERT
                $strFields = implode(",", $arrColumns);
                $arrValueIsert = array();
                for($i=0;$i<count($arrColumns);$i++){$arrValueIsert[] = "?";}
                $strValues = implode(",", $arrValueIsert);
                $sqlInsertString = 'INSERT INTO COMPANIES ('.$strFields.') VALUES ('.$strValues.')';



                //Create sql for UPDATE
                $arrFieldsUpdate = array();
                foreach ($arrColumns as $value) {
                     $arrFieldsUpdate[] = $value.'=?';
                }

                $strFieldsUpdate = implode(",", $arrFieldsUpdate);
                $sqlUpdateString = 'UPDATE COMPANIES SET '.$strFieldsUpdate.' WHERE id=?';

                for($r=0;$r<count($csvarray);$r++){
                    if($csvarray[$r][0] == 0){
                        $arrValueInsert = array();                    
                        foreach ($arrColumns as $key => $value) {
                            $arrValueInsert[] = $csvarray[$r][$key + 1];
                        }
                        $rs = $db1->execSQL($sqlInsertString, $arrValueInsert);
                    }
                    else{
                        $arrValueUpdate = array();
                        foreach ($arrColumns as $key => $value) {
                            $arrValueUpdate[] = $csvarray[$r][$key + 1];
                        }
                        $arrValueUpdate[] = $csvarray[$r][0];
                        $rs = $db1->execSQL($sqlUpdateString, $arrValueUpdate);
                    }
                }
                $msg .= $l->l('completed');
                /*INSERT OR UPDATE DATABASE END*/    
            }
            
        }
        /**************FOR CSV end************/
//    }
//    else{
//        $err = 1;
//        $msg .= $l->l('no_file_selected')."</br>";
//    }
    
    
    
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PANELINIOS- CRM</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/grid.css" rel="stylesheet" type="text/css" />
<link href="css/global.css" rel="stylesheet" type="text/css" />
<style>
    .form-container {
        max-width: 1100px;
    }
</style>
</head>

<body class="form">
    <div class="form-container">
                
        <h1><?php echo $l->l("form_importcsv"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        
        
       
        
        <form action="importcsv.php?import=1&<?php echo $ltoken; ?>" method="POST" enctype="multipart/form-data">
            
            <div class="col-12">
                
                <div class="col-8"><input type="file" id="fileCSV" name="fileCSV"></div>
                    
                <div class="col-6"><label for="chkHasHeader"><?php echo $l->l("has-header"); ?></label></div>
                <div class="col-6"><input id="chkHasHeader" class="" type="checkbox" name="chkHasHeader" value="1"/></div>               
                
                <div class="clear"></div>
                <hr style="border:none; border-bottom: 1px dashed rgb(200,200,200)"></hr>
                
                <?php
                    
                ?>
                <div class="col-6">
                    <?php 
                    for($f=1;$f<count($arrfields)-15;$f++){                        
                        echo '<div class="col-4">'.$l->l("field").' '.$f.'</div>';
                        echo '<div class="col-8">';
                            if($f == 1){echo '<select id="cField'.$f.'" name="cField[]" disabled="disabled">';}
                            else{echo '<select id="cField'.$f.'" name="cField[]">'; }
                                for($i=0;$i<count($arrfields);$i++){ 
                                        if($i == 1 && $f == 1){echo '<option value="'.$arrfields[$i].'" selected="selected">'.$arrfields[$i].'</option>';}
                                        else{ echo '<option value="'.$arrfields[$i].'">'.$arrfields[$i].'</option>'; }
                                }
                            echo '</select>';
                        echo '</div>';                        
                    } ?>
                </div> 
                
                
                <div class="col-6">
                    <?php
                    for($f=17;$f<count($arrfields);$f++){                        
                        echo '<div class="col-4">'.$l->l("field").' '.$f.'</div>';
                        echo '<div class="col-8">';
                            echo '<select id="cField'.$f.'" name="cField[]">'; 
                                for($i=0;$i<count($arrfields);$i++){ 
                                         echo '<option value="'.$arrfields[$i].'">'.$arrfields[$i].'</option>'; 
                                }
                            echo '</select>';
                        echo '</div>';                        
                    } ?>                    
                </div>
                
                <div class="clear"></div>
                <hr style="border:none; border-bottom: 1px dashed rgb(200,200,200)"></hr>
            
                <?php 
                // Θα επιλέγει τον χαρακτήρα που διαχωρίζει τα πεδία (default = , )
                $txtDelimiterFields = new textbox("txtDelimiterFields", $l->l("character-delimiter-fields"), "|");
                $txtDelimiterFields->get_Textbox();
                
                //Θα επιλέγει τον χαρακτήρα με τον οποίο θα αρχίζει και θα τελειώνει κάθε πεδίο (θα μπορεί να είναι κενό)
                $txtDelimStartEndField = new textbox("txtDelimStartEndField", $l->l("haracter-delim-start-end-field"), "~");
                $txtDelimStartEndField->get_Textbox();
                               
                //submit
                $btnOK = new button("BtnOk", $l->l('import'));            

                echo "<div class=\"col-4\"></div><div class=\"col-8\">";
                $btnOK->get_button_simple();
                
                echo "</div>";

                ?> 
            </div>
            <div style="clear: both;"></div>
            
        </form>
        
    </div>    
</body>
</html>