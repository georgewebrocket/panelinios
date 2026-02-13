<?php

require_once('php/session.php');
require_once('php/dataobjects.php');
require_once('php/controls.php');
require_once('inc.php');
$l = new mlng("EXPORTCSV",$lang,$db1);

$msg = "";
$strFieldHeaders="";
$export = 0;
$strSQLFields = "";
$sql = "";
$userExportSql='ExportSql'.$_SESSION['user_id'];
$isDownload = 0;
$myFile = "";
$sqlexport = "";

if (isset($_GET['export']) && $_GET['export'] == 1) {
    $err = 0;
    $export = 1;    
    
    if(isset($_POST['txtDelimStartEndField'])){
        $DelimStartEndField = $_POST['txtDelimStartEndField'];        
    }else{
        $DelimStartEndField = " ";
    }
    if(isset($_POST['txtDelimiterFields']) && $_POST['txtDelimiterFields'] != ""){
        $DelimiterFields = $_POST['txtDelimiterFields'];
        
        if(isset($_POST['chkCompanyField'])){
            $arrCompanyFields = $_POST['chkCompanyField'];    
            $strSQLFields = implode(",",$arrCompanyFields);// get fields for SQL
            for($i=0;$i<count($arrCompanyFields);$i++){
                $arrCompanyFields[$i] = $DelimStartEndField.$arrCompanyFields[$i].$DelimStartEndField;
                if($i <> count($arrCompanyFields)-1){
                    $arrCompanyFields[$i] .= $DelimiterFields;
                }                
            }

            $strFieldHeaders = implode($arrCompanyFields);// get headers

            /*Get SQL with criteria begin*/
            if(isset($_SESSION[$userExportSql])){
                $sqlexport=$_SESSION[$userExportSql];
                $sql = $sqlexport; 
            }
            /*Get SQL with criteria end*/
            if(isset($_POST['rbCompanies'])){
                $rbCompanies = $_POST['rbCompanies'];
                switch($rbCompanies){
                    case "all":
                        $sql = 'SELECT '.$strSQLFields.' FROM COMPANIES';
                        break;
                    case "selected":                    
                        $pos = strpos($sql, "FROM");
                        $sql = substr_replace($sql, 'SELECT '.$strSQLFields.' ', 0, $pos);
                    default :                
                }
            }

            $arrRes = $db1->getRS($sql);
            if(count($arrRes) > 0){
                foreach($arrRes as $rowKeys => $row){
                    $total = count($arrRes[$rowKeys]);
                    $counter = 0;
                    foreach($row  as  $cellKeys => $cell ){
                        $counter++;
                        $arrRes[$rowKeys][$cellKeys] = $DelimStartEndField.$cell.$DelimStartEndField;
                        if($counter <> $total){$arrRes[$rowKeys][$cellKeys] .= $DelimiterFields;}
                    }
                }
                

                $myFileName = 'export'.$_SESSION['user_id'].'.csv';
                $myFile = 'exportcsv/'.$myFileName;

                //wright header in file
                $myContent = $strFieldHeaders."\n";
                // detect the character encoding of the incoming file
                $encoding = mb_detect_encoding( $myContent, "auto" );
                file_put_contents($myFile, mb_convert_encoding($myContent, "UTF-8", $encoding));                
                //wright rows in file
                for($i=0;$i<count($arrRes);$i++){
                    $arrRes[$i];
                    $myContent = implode($arrRes[$i])."\n";// get rows from array
                    // detect the character encoding of the incoming file
                    $encoding = mb_detect_encoding( $myContent, "auto" );
                    file_put_contents($myFile, mb_convert_encoding($myContent, "UTF-8", $encoding), FILE_APPEND );  
                }
                $isDownload = 1;
                $msg .= $l->l('completed');
            }
            else{
                $err = 1;
                $msg .= $l->l('blank_companies')."</br>";
            }
        }   
        else{
            $err = 1;
            $msg .= $l->l('blank_company_fields')."</br>";
        }
    }
    else{
        $err = 1;
        $msg .= $l->l('blank_delimiter')."</br>";
    }
    
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
                
        <h1><?php echo $l->l("form_exportcsv"); ?></h1>   
        <?php if ($msg!="") { echo "<h2 class=\"msg\">".$msg."</h2>";} ?>
        <?php if($isDownload == 1){echo '<h2 id = "exportcsvdownload">'
            . '<a class="button fancybox500" href="'.$myFile.'" download="companies.csv">'.$l->l("download-csv").'</a> '.$l->l("file-csv").'</h2>';} ?>
        
        <form action="exportcsv.php?export=1&<?php echo $ltoken; ?>" method="POST">
            
            <div class="col-12">
                <?php 
                //echo '<h2>'.$l->l("select-companies").'</h2>';  
                //radio button for select companies
                $rbCompanies = new radiobutton("rbCompanies",
                        array($l->l("all-companies"),$l->l("selected-companies")),
                        array("all","selected"),
                        "rbCompanies",
                        array('checked="checked"',''));
                $rbCompanies->set_type("radio");
                $rbCompanies->get_Radiobutton();
                ?>
                
                <div class="clear"></div>
                <hr style="border:none; border-bottom: 1px dashed rgb(200,200,200)"></hr>
                
                <div class="col-6">
                    <?php
                    $fields = array("id","companyname","phone1","phone2","mobilephone","contactperson","basiccategory",
                        "reference","area","geo_x","geo_y","address","zipcode","email","website","facebook");
                    $fieldsChkChecked = array("1","","","","","","","","","","","","","","","");
                    //checkboxes for select fields from companies
                    for($i=0;$i<count($fields);$i++){
                        $isChecked = "";
                        if($fieldsChkChecked[$i] == "1"){$isChecked = ' checked="checked" ';}
                        echo '<div class="col-6"><label for="chkCompanyField'.$i.'">'.$fields[$i].'</label></div>';
                        echo '<div class="col-6"><input id="chkCompanyField'.$i.'" class="" type="checkbox" name="chkCompanyField[]" value="'.$fields[$i].'"'.$isChecked.'/></div>';                    

                    }
                    ?>
                </div>
                
                <div class="col-6">
                    <?php
                    $fields = array("twitter","package","discount","price","vatzone",
                        "catalogueid","expires","username","password","user",
                        "userdataentry","recalldate","recalltime","status","comment");
                    $fieldsChkChecked = array("","","","","","","","","","","","","","","");
                    //checkboxes for select fields from companies
                    for($i=16;$i<count($fields)+16;$i++){
                        $isChecked = "";
                        if($fieldsChkChecked[$i-16] == "1"){$isChecked = ' checked="checked" ';}
                        echo '<div class="col-6"><label for="chkCompanyField'.$i.'">'.$fields[$i-16].'</label></div>';
                        echo '<div class="col-6"><input id="chkCompanyField'.$i.'" class="" type="checkbox" name="chkCompanyField[]" value="'.$fields[$i-16].'"'.$isChecked.'/></div>';                    

                    }
                    ?>
                </div>
                
                <div style="clear: both"></div>
            
                <?php 
                // Θα επιλέγει τον χαρακτήρα που διαχωρίζει τα πεδία (default = , )
                $txtDelimiterFields = new textbox("txtDelimiterFields", $l->l("character-delimiter-fields"), "|");
                $txtDelimiterFields->get_Textbox();
                
                //Θα επιλέγει τον χαρακτήρα με τον οποίο θα αρχίζει και θα τελειώνει κάθε πεδίο (θα μπορεί να είναι κενό)
                $txtDelimStartEndField = new textbox("txtDelimStartEndField", $l->l("haracter-delim-start-end-field"), "~");
                $txtDelimStartEndField->get_Textbox();
                               
                //submit
                $btnOK = new button("BtnOk", $l->l('export'));            

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