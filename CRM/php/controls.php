<?php

require_once 'utils.php';



/*
EXAMPLE
$gridUsers = new datagrid("gridAccounts", $conn,  
		"SELECT * FROM USERS ORDER BY username", 
		array("fullname","username","password","active","userprofile"), 
		array("NAME","USERNAME","PASSWORD","ACTIVE","PROFILE"),
		$ltoken, 0, 
		TRUE,"edituser.php","edit", 
		TRUE,"deluser.php","del"
		);
$gridUsers->set_colWidths(array("90%","150","150","50","50","30","30"));
$gridUsers->get_datagrid();
*/
class datagrid
{
    protected $_id, $_sql, $_fields, $_headers, $_edit, $_del, $_rowsPerPage, $_key;    
    protected $_gridheader, $_gridrows, $_rs;
    protected $_editUrl, $_delUrl;
    protected $_editLabel, $_delLabel;
    protected $_ltoken;
    protected $_tree, $_addNode, $_addNodeUrl, $_addLabel, $_parentKey, $_table;
    protected $_hasheaders;
    protected $_controlColWidths, $_colWidths;
    protected $_getsubgridpage;
    protected $_colsFormat;
    protected $_locale;
    protected $_conn;
    protected $_selectRow, $_selectLabel;
    protected $_footerAR, $_gridfooter;
    protected $_popup;



    public function __construct($id, $conn, $sql, $fields, $headers, $ltoken="l=gr", 
            $rowsPerPage=0, 
            $edit=FALSE, $editUrl="", $editLabel = "edit", 
            $del=FALSE, $delUrl="",  $delLabel = "del", 
            $key="id", $locale="GR") {
        $this->_id = $id;
        $this->_conn = $conn;
        $this->_sql = $sql;
        $this->_fields = $fields;
        $this->_headers = $headers;
        
        $this->_edit = $edit;
        $this->_editUrl = $editUrl;
        $this->_editLabel = $editLabel;
        if ($edit) {
            $this->_footerAR["edit"] = "";
        }
        
        $this->_del = $del;
        $this->_delUrl = $delUrl;
        $this->_delLabel = $delLabel;
        if ($del) {
            $this->_footerAR["del"] = "";
        }
        
        $this->_rowsPerPage = $rowsPerPage;
        $this->_key = $key;                
        $this->_ltoken = $ltoken;
        $this->_tree = FALSE;
        $this->_hasheaders = TRUE;
        $this->_controlColWidths = FALSE;
        $this->_getsubgridpage = "";
        $this->_locale = $locale;
        
        $this->_rs = $this->_conn->getRS($this->_sql);
        
        $this->_selectRow = FALSE;
        $this->_selectLabel = "select"; 
        $this->CreateFooter();
        
        $this->_popup = TRUE;
        
    }
    
    public function set_popup($val) {
        $this->_popup = $val;
    }
    
    private function CreateFooter() {
        for ($i=0;$i<count($this->_fields);$i++) {
            $this->_footerAR[$this->_fields[$i]] = "";
        }
    }
    
    public function col_sum($colKey, $format = "CURRENCY") {
        $myNr = 0;
        for ($i=0;$i<count($this->_rs);$i++) {
            $myNr += $this->_rs[$i][$colKey];
        }
        if ($format=="CURRENCY") {
            $myNr = func::nrToCurrency($myNr);
        }
        $this->_footerAR[$colKey] = $myNr;
    }
    
    public function setFooter($val) {
        $this->_footerAR = $val;
    }
    
    public function set_edit($editUrl,$editLabel="Edit") {
        $this->_edit = TRUE;
        $this->_editUrl = $editUrl;
        $this->_editLabel = $editLabel;
        $this->_footerAR["edit"] = "";
    }
    
    public function set_del($delUrl,$delLabel="Delete") {
        $this->_del = TRUE;
        $this->_delUrl = $delUrl;
        $this->_delLabel = $delLabel;
        $this->_footerAR["del"] = "";
    }
    
    public function set_TreeParams($addNode=FALSE, $addNodeUrl="", $addLabel="add", $parentKey="parentid", $table = "", $getsubgridpage = "") {
        $this->_tree = TRUE;
        $this->_addNode = $addNode;
        $this->_addNodeUrl = $addNodeUrl;
        $this->_addLabel = $addLabel;
        $this->_parentKey = $parentKey;
        $this->_table = $table;
        $this->_getsubgridpage = $getsubgridpage;
    }
    
    public function set_select($label="select") {
        $this->_selectRow = TRUE;
        $this->_selectLabel = $label;
        $this->_footerAR["select"] = "";
    }
    
    public function get_rs(){
        return $this->_rs;
    }
    
    public function set_rs($val){
        $this->_rs = $val; 
    }
    
    public function set_hasheaders($val) {
        $this->_hasheaders = $val;
    }
    
    public function set_colWidths($val) {
        $this->_controlColWidths = TRUE;
        $this->_colWidths = $val;
        
    }
    
    public function set_colsFormat($arr) {
        $this->_colsFormat = $arr;
    }
    
    public function set_locale($locale) {
        $this->_locale = $locale;
    }
    
    public function col_vlookup($colid, $coldescr, $table, $field, $conn) {
        for ($i=0;$i<count($this->_rs);$i++) {
            $this->_rs[$i][$coldescr] = func::vlookup($field, $table, "id=".$this->_rs[$i][$colid], $conn);
        }
    }
    
    public function col_vlookupRS($colid, $coldescr, $rs, $field) {
        for ($i=0;$i<count($this->_rs);$i++) {
            $this->_rs[$i][$coldescr] = func::vlookupRS($field, $rs, $this->_rs[$i][$colid]);
        }
    }
	
    public function col_func($colid, $coldescr, $sfunction, $factor="XX") {
            for ($i=0;$i<count($this->_rs);$i++) {
                    $this->_rs[$i][$coldescr] = str_replace($factor, $this->_rs[$i][$colid], $sfunction);
            }
    }
    
    public function removeDuplicates($colid) {
        $prev = "";
        for ($i=0;$i<count($this->_rs);$i++) {
            if ($this->_rs[$i][$colid]==$prev) {
                $this->_rs[$i][$colid] = "";
            }
            else {
               $prev = $this->_rs[$i][$colid];
            }
        }
    }
    
    
    public function GetItemsFromIds($conn, $colid, $myTable, $idField = "id", $descrField="description") {
        for ($i=0;$i<count($this->_rs);$i++) {
            $str = str_replace(array("[","]"), "", $this->_rs[$i][$colid]);
            $ar = explode(",", $str);
            $str2 = "";            
            for ($k=0;$k<count($ar);$k++) {
                $criteria = $idField."=".$ar[$k];
                $myItem = func::vlookup($descrField, $myTable, $criteria, $conn);
                if ($myItem!="") {
                    $str2 = func::ConcatSpecial($str2, "- ".$myItem, "<br/>");
                }
            }
                
            $this->_rs[$i][$colid] = $str2;            
        }
    }
    
    public function GetItemsFromIdsRS($colid, $rs, $descrField="description") {
        for ($i=0;$i<count($this->_rs);$i++) {
            $str = str_replace(array("[","]"), "", $this->_rs[$i][$colid]);
            $ar = explode(",", $str);
            $str2 = "";            
            for ($k=0;$k<count($ar);$k++) {                
                $myItem = func::vlookupRS($descrField, $rs, $ar[$k]);
                if ($myItem!="") {
                    $str2 = func::ConcatSpecial($str2, "- ".$myItem, "<br/>");
                }
            }
                
            $this->_rs[$i][$colid] = $str2;            
        }
    }
    
    
    private function createGridHeader() {
        
        if ($this->_controlColWidths) {
            $col=0;
            if ($this->_tree) {
                $this->_gridheader = "<th width=\"".$this->_colWidths[$col]."\">.</th>";
                $col++;
            }
            for ($i=0;$i<count($this->_headers);$i++) {
                $this->_gridheader .= "<th id=\"col_".$this->_fields[$i]."\" width=\"".$this->_colWidths[$col]."\">".$this->_headers[$i]."</th>";
                $col++;
            }
            if ($this->_edit) {
                $this->_gridheader .= "<th width=\"".$this->_colWidths[$col]."\">".$this->_editLabel."</th>";
                $col++;
            }
            if ($this->_del) {
                $this->_gridheader .= "<th width=\"".$this->_colWidths[$col]."\">".$this->_delLabel."</th>";
                $col++;
            }
            if ($this->_addNode) {
                $this->_gridheader .= "<th width=\"".$this->_colWidths[$col]."\">".$this->_addLabel."</th>";
                $col++;
            } 
            if ($this->_selectRow) {
                $this->_gridheader .= "<th width=\"".$this->_colWidths[$col]."\">".$this->_selectLabel."</th>";
                $col++;
            }
        }
        else {        
            if ($this->_tree) {
                $this->_gridheader = "<th>.</th>";
            }
            for ($i=0;$i<count($this->_headers);$i++) {
                $this->_gridheader .= "<th>".$this->_headers[$i]."</th>";
            }
            if ($this->_edit) {

                $this->_gridheader .= "<th>".$this->_editLabel."</th>";
            }
            if ($this->_del) {
                $this->_gridheader .= "<th>".$this->_delLabel."</th>";
            }
            if ($this->_addNode) {
                $this->_gridheader .= "<th>".$this->_addLabel."</th>";
            } 
            if ($this->_selectRow) {
                $this->_gridheader .= "<th>".$this->_selectLabel."</th>";
            }
        }
        
    }
    
    private function createGridRows() {
        $tdclass = "odd";
        $cols = 0;
        $noCacheHash = "&chash=" . date("YmdHis");
        
        $fancybox = $this->_popup? "fancybox": "";
        $target = $this->_popup? "": "target=\"_blank\"";
        
        for ($i=0;$i<count($this->_rs);$i++) {
            $this->_gridrows .= "<tr id=\"".$this->_rs[$i][$this->_key]."\">";
            if ($this->_tree) {
                $this->_gridrows .= "<td class=\"".$tdclass."\">";            
                //*****
                if ($this->_rs[$i]["nodes"]==1) {
                    $this->_gridrows .= "<a onclick=\"ExpandTree(this, '".$this->_getsubgridpage."');return false;\" id=\"a".$this->_rs[$i][$this->_key]."\" class=\"inline-button tree\" href=\"#\">";
                    $this->_gridrows .= "+"; //..............
                    $this->_gridrows .= "</a>";
                }
                $cols++;
                $this->_gridrows .= "</td>";
            }
            for ($k=0;$k<count($this->_fields);$k++) {
                $align = "left";
                if ($this->_colsFormat && $this->_colsFormat[$k]=="CURRENCY") {
                    $align = "right";
                }
                if ($this->_colsFormat && $this->_colsFormat[$k]=="NR") {
                    $align = "right";
                }
                $this->_gridrows .= "<td align=\"".$align."\" class=\"".$tdclass."\">";
                $curField = $this->_fields[$k];
                $cellval = "";
                if ($this->_colsFormat) {
                    $cellval = func::format($this->_rs[$i][$curField], $this->_colsFormat[$k], $this->_locale);
                }
                else {
                    $cellval = $this->_rs[$i][$curField];
                }
                $this->_gridrows .= $cellval;
                $this->_gridrows .= "</td>";
                $cols++;
            }
            if ($this->_edit) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">"; 
                $editurl = $this->_editUrl."?id=".$this->_rs[$i][$this->_key]."&".$this->_ltoken.$noCacheHash;
                $this->_gridrows .= "<a $target class=\" $fancybox\" href=\"".$editurl."\">";
                $this->_gridrows .= "<span title=\"Άνοιγμα / Διόρθωση\" class=\"fa fa-edit fa-lg\"></span>"; //..............
                $this->_gridrows .= "</a></td>";
                $cols++;
            }
            if ($this->_del) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">";
                $delurl = $this->_delUrl."?id=".$this->_rs[$i][$this->_key];
                $this->_gridrows .= "<a $target class=\" $fancybox\" href=\"".$delurl."\">";
                $this->_gridrows .= "<span title=\"Διαγραφή\" class=\"fa fa-trash fa-lg\"></span>"; //..............
                $this->_gridrows .= "</a></td>";
                $cols++;
            }
            if ($this->_addNode) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">";
                $addNodeUrl = $this->_addNodeUrl."?id=".$this->_rs[$i][$this->_key];
                $this->_gridrows .= "<a $target class=\" $fancybox\" href=\"".$addNodeUrl."\">";
                $this->_gridrows .= "<span title=\"Προσθήκη\" class=\"fa fa-plus fa-lg tooltip\"></span>"; //..............
                $this->_gridrows .= "</a></td>";
                $cols++;
            }
            
            if ($this->_selectRow) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">";              
                $this->_gridrows .= "<input class=\"checkrow\" type=\"checkbox\" name=\"chkRow".$i."\" value=\"".$this->_rs[$i][$this->_key]."\" /></td>"; /*Giorgos*/
//                $this->_gridrows .= "<input type=\"checkbox\" name=\"chkRow[]\" value=\"1\" /></td>";  /*Dimitrios*/
                $cols++;
            }
            
            $this->_gridrows .= "</tr>";
            
            //*****
            if ($this->_tree) {
                $this->_gridrows .= "<tr><td style=\"padding:0px\" colspan=\"".$cols."\"><div id=\"subgrid".$this->_rs[$i][$this->_key]."\" class=\"subgrid\"></div></td></tr>";
            }
            
            //allagi row class / for coloring purposes
            if ($tdclass=="odd") { $tdclass="even"; }
            else { $tdclass="odd";}
        }
        //$this->_gridrows .= "</tbody><tr><td class=\"table-footer\" colspan=\"".$cols."\"></td></tr>";
        //echo '</tbody>
    }
    
    private function CreateGridFooter() {
        $this->_gridfooter = "<tr>";
        $keys = array_keys($this->_footerAR);
        for ($i=0;$i<count($this->_footerAR);$i++) {
            $this->_gridfooter .= '<td class="table-footer">'.$this->_footerAR[$keys[$i]].'</td>';
        }
        /*if ($this->_edit) {
            $this->_gridfooter .= '<td class="table-footer">&nbsp;</td>';
        }*/
        /*if ($this->_del) {
            $this->_gridfooter .= '<td class="table-footer">&nbsp;</td>';
        }
        if ($this->_addNode) {
            $this->_gridfooter .= '<td class="table-footer">&nbsp;</td>';
        }
        if ($this->_selectRow) {
            $this->_gridfooter .= '<td class="table-footer">&nbsp;</td>';
        }*/
        $this->_gridfooter .= "</tr>";
    }
    
    public function get_datagrid() {
        $this->createGridHeader();
        $this->createGridRows();
        $this->CreateGridFooter();
        
        echo '<table id="'.$this->_id.'" class="datagrid" cellspacing="1">';
        //if ($this->_hasheaders) {
        echo "<thead>";    
        echo $this->_gridheader; 
        //}
        
        echo "</thead><tbody>";
        echo $this->_gridrows; 
        echo $this->_gridfooter;
        echo '</tbody></table>';
        
    }

}



/*
FORMAT DATE > class = datepicker (+javascript)
*/
class textbox
{
    protected $_id, $_label, $_val, $_multiline, $_placeholder, $_format, $_locale, $_class, $_type;
    protected $_disabled;
    
    public function __construct($id, $label, $val, $placeholder="", $multiline=FALSE) {
        $this->_id = $id;
        $this->_label = $label;
        $this->_val = $val;
        $this->_multiline = $multiline;
        $this->_placeholder = $placeholder;
        $this->_type = 'text';
        $this->_disabled = FALSE;
    }
    
    public function set_format($type, $locale="GR") {
        $this->_format = $type;
        $this->_locale = $locale;
        
        if ($type=="DATE") {
            if ($this->_class == "") {
                $this->_class = "datepicker";
            }            
            if ($this->_placeholder == "") {
                $this->_placeholder = "ΗΗ/ΜΜ/ΕΕΕΕ";
            }
            
        }
    }
    
    public function set_disabled() {
        $this->_disabled = TRUE;
    }
    
    public function set_multiline() {
        $this->_multiline = TRUE;
    }
    
    public function set_type($TextType="text") {
        $this->_type = $TextType;
    }
    
    public function set_locale($locale) {
        $this->_locale = $locale;
    }
    
    public function set_class($class) {
        $this->_class = $class;
    }

    public function get_Textbox() { 
        $input = $this->textboxSimple();
        echo '<div class="col-4">'.$this->_label.'</div>            
            <div class="col-8">'.$input.'</div>';
    }
    
    public function textboxSimple() {
        $val = "";
        if ($this->_format!="") {
            $val = func::format($this->_val, $this->_format, $this->_locale);
        }
        else {
            $val = $this->_val;
        }        
        switch ($this->_multiline) {
            case FALSE:
                
                if ($this->_disabled) {
                    return '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" readonly />';
                }
                else {
                    return '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" />';
                }
            case TRUE:
                
                if ($this->_disabled) {
                    return '<textarea class="'.$this->_class.'" name="'.$this->_id.'" id="'.$this->_id.'" cols="" rows="5" placeholder="'.$this->_placeholder.'" readonly>'.$val.'</textarea>';
                }
                else {
                    return '<textarea class="'.$this->_class.'" name="'.$this->_id.'" id="'.$this->_id.'" cols="" rows="5" placeholder="'.$this->_placeholder.'">'.$val.'</textarea>';
                }
            default:
        }
    }
    
    //GET DATA FROM TEXTBOX
    //RETURNS 20140708000000
    static function getDate($val, $locale) {
        if ($val=="") {
            return "";
        }
        else {
            return func::dateTo14str($val, "/", $locale);
        }
    }
    
    static function getCurrency($val, $locale) {
        return func::CurrencyToNr($val, $locale);
    }
    
    //func::CurrencyToNr($_POST['txtCharge'], config::$locale)
}

class button
{
    protected $_id, $_type, $_label, $_method;
    //TYPE=submit, button
    
    public function __construct($id, $label, $type="submit" ) {
        $this->_id = $id;
        $this->_label = $label;
        $this->_type = $type;        
    }
    
    public function set_method($val) {
        $this->_method = $val;
    }
    
    public function get_button() {
        echo '<div class="col-4"></div>            
            <div class="col-8">
            <input name="'.$this->_id.'" id="'.$this->_id.'" type="'.$this->_type.'" value="'.$this->_label.'" />
            </div>';        
    }
    
    public function get_button_simple() {
        switch ($this->_type) {
            case "submit":
                echo '<input name="'.$this->_id.'" id="'.$this->_id.'" type="'.$this->_type.'" value="'.$this->_label.'" />'; 
                break;
            case "close-update":
                echo "<input name=\"$this->_id\" id=\"$this->_id\"  onclick=\"window.parent.location.reload(false);\" type=\"button\" value=\"Close & update\" />";
                break;
            default :
                echo "<input name=\"$this->_id\" id=\"$this->_id\" onclick=\"".$this->_method."\" type=\"button\" value=\"".$this->_label."\" />";
                break;
        }  
        
    }
    
    
}


class comboBox
{
    protected $_id, $_sql, $_idField, $_descField, $_currentId, $_label;
    protected $_rs;
    protected $_disabled;
    protected $_extraAttr;
    protected $_dontSelect;
    protected $_zerochoice;
    
    public function __construct($id, $conn, $sql, $idField="id", $descField="description", $currentId=0, $label="") {
        $this->_id = $id;        
        $this->_idField = $idField;
        $this->_descField = $descField;
        $this->_currentId = $currentId;
        $this->_label = $label;
        
        if (strpos($sql, "ORDER BY")==FALSE) {
            $sql .= " ORDER BY " . $descField;
        }
        $this->_sql = $sql;
        if ($this->_sql!="") {
            $this->_rs = $conn->getRS($sql);
        }
        $this->_disabled = FALSE;
        
        $this->_dontSelect = "...";
        $this->_zerochoice = true;
    }
    
    public function set_zerochoice($val) {
        $this->_zerochoice = $val;
    }
    public function set_enableNoChoice($val) {
        $this->_zerochoice = $val;
    }
    
    public function set_dontSelect($val) {
        $this->_dontSelect = $val;
    }
    public function set_noChoiceDescription($val) {
        $this->_dontSelect = $val;
    }
    
    public function set_rs($rs) {
        $this->_rs = $rs;
    }
    
    
    public function set_disabled() {
        $this->_disabled = TRUE;
    }
    
    public function set_extraAttr($attr) {
        $this->_extraAttr = $attr;
    }
    
    public function set_label($val) {
        $this->_label = $val;
    }
    
    public function get_comboBox() {
        
        $control = $this->comboBox_simple();
        echo '<div class="col-4">'.$this->_label.'</div>            
            <div class="col-8">';
        echo $control;
        echo "</div>";
    }
    
    public function comboBox_simple() {
        $id = str_replace("[]", "", $this->_id); // multiselect
        $control = "<select ".$this->_extraAttr." name=\"" . $this->_id . "\" id=\"" . $id . "\" ";
        
        if ($this->_disabled == TRUE) {
            $control .= " disabled ";
        }
        $control .= " >";
        
        if ($this->_zerochoice) {
            $control .= "<option value=\"0\" >$this->_dontSelect</option>"; ////
        }
        for ($i=0;$i<count($this->_rs);$i++){             
            $cur_id = $this->_rs[$i][$this->_idField];
            $cur_descr = $this->_rs[$i][$this->_descField];
            if ($cur_id == $this->_currentId) {
                $control .= "<option value=\"" . $cur_id . "\" selected=\"selected\">" . $cur_descr . "</option>";
            }
            else {
                $control .= "<option value=\"" . $cur_id . "\" >" . $cur_descr . "</option>";
            }
        }
        $control .= "</select>";
        return $control;
        
    }
    
}

class checkbox
{
    protected $_id, $_label, $_val, $_locale, $_class, $_type; 
    protected $_disabled;
    
    public function __construct($id, $label, $val) {
        $this->_id = $id;
        $this->_label = $label;
        $this->_val = $val;
        $this->_type = "checkbox";
        $this->_disabled = FALSE;
        
    }
    
    public function set_disabled() {
        $this->_disabled = TRUE;
    }
    
    public function set_type($checkboxType="checkbox") {
        $this->_type = $checkboxType;
    }
    
    public function set_locale($locale) {
        $this->_locale = $locale;
    }
    
    public function set_class($class) {
        $this->_class = $class;
    }

    public function get_Checkbox() {
        $input = $this->checkboxSimple();
        echo '<div class="col-4"><label for="'.$this->_id.'">'.$this->_label.'</label></div>            
            <div class="col-8">'.$input.'</div>';
    }
    
    public function get_CheckboxInline() {
        $input = $this->checkboxSimple();
        echo $input . "&nbsp;" . "<label for=".$this->_id.">".$this->_label."</label>";
    }
    
    public function checkboxSimple() {
        $input = "";
        $isCheked = "";
        $isdisabled = "";
        if($this->_disabled){$isdisabled = ' disabled = "disabled"';}
        switch ($this->_val){
            case 1:
                $isCheked = ' checked = "checked"';
                break;
            case 0:
                $isCheked = "";
                break;
            default :
                $isCheked = "";
                break;
        }
        
        $input = '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="1"'.$isCheked.' '.$isdisabled.'>';        
        return $input;
        
    }
    
    static function getVal($val) {
        if ($val==1) {
            return 1;
        } else {
            return 0;
        }
    }
	
    static function getVal2($post, $attr) {
        if (isset($post[$attr])) {
            return 1;
        } else {
            return 0;
        }
    }
    
    
}

class hr
{
    public function get_hr() {
        echo '<div style="clear: both"></div>
            <hr style="height: 0px; border: none; border-bottom: 1px dashed rgb(200,200,200); width: 100%; float: left; margin:20px 0px"></hr>';
    }
}

class radiobutton
{
    protected $_id, $_label, $_val, $_locale, $_class, $_type, $_checked, $_rbname; 
    
    public function __construct($id, $label, $val, $rbName, $checked) {
        $this->_id = $id;
        $this->_label = $label;
        $this->_val = $val;
        $this->_type = "radio";
        $this->_rbname = $rbName;
        $this->_checked = $checked;
        
    }
    
    public function set_type($radioButtonType="radio") {
        $this->_type = $radioButtonType;
    }
    
    public function set_locale($locale) {
        $this->_locale = $locale;
    }
    
    public function set_class($class) {
        $this->_class = $class;
    }

    public function get_Radiobutton() {
        $input = "";
        $val = $this->_val;
        $label = $this->_label;
        $rbName = $this->_rbname;
        $checked = $this->_checked;
        for($i = 0;$i < count($val);$i++){
            $input = '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_rbname.'" id="'.$this->_id.$i.'" value="'.$val[$i].'" '.$checked[$i].'>';
            echo '<div class="col-4"><label for="'.$this->_id.$i.'">'.$label[$i].'</label></div>            
                    <div class="col-8">'.$input.'</div>';
        }        

    }
    
    
}


class autocomplete
{    
    protected $_id, $_hiddenid, $_table, $_id_field, $_descr_field, $_strval, $_idval, $_label, $_dbo;
    
    public function __construct($id, $table, $idval, $dbo) {
        $this->_id = $id;
        $this->_hiddenid = "h_" . $this->_id;
        $this->_table = $table;
        $this->_id_field = "id";
        $this->_descr_field = "description";
        if ($idval=="") {$idval=0;}
        $this->_idval = $idval;
        $this->_dbo = $dbo;
        
    }
    
    public function set_id_field($val) {
        $this->_id_field = $val;
    }
    
    public function set_descr_field($val) {
        $this->_descr_field = $val;
    }
    
    public function set_hiddenid($val) {
        $this->_hiddenid = $val;
    }
    
    public function set_label($val) {
        $this->_label = $val;
    }
    
    private function getControl() {
        $this->_strval = func::vlookup($this->_descr_field, 
                $this->_table, $this->_id_field."=".$this->_idval, $this->_dbo);        
        $res = "<input id=\"".$this->_id."\" type=\"text\" name=\"".
                $this->_id."\" value=\"".$this->_strval."\" />";
        $res .=  "<input type=\"hidden\" name=\"".$this->_hiddenid."\" id=\"".
                $this->_hiddenid."\" value=\"".$this->_idval."\" />";
        return $res;
        
    }
    
    private function getScript() {
        return "<script>
        $(function() {
            $( '#".$this->_id."' ).autocomplete(
            {
                source:'acdata.php?table=".$this->_table.
                "&idfield=".$this->_id_field.
                "&descrfield=".$this->_descr_field."',
                minLength:3,
                select: function( event, ui ) {
                $( '#".$this->_hiddenid."' ).val( ui.item.id ).trigger('change');                
                }
            })

        });
        </script>";
    }
    
    public function getAutocompleteSimple() {
        echo $this->getScript();
        echo $this->getControl();
    }
    
    public function getAutocomplete() {
        echo $this->getScript();
        echo "<div class=\"col-4\">".$this->_label."</div>";
        echo "<div class=\"col-8\">";
        echo $this->getControl();
        echo "</div>";
    }
    
    
    
}


/*
$l_list = new selectList("l_list", "MY FIELD", 
        $object->get_myField(), $db1);
$l_list->set_descrField("shortdescr");
$l_list->set_orderby("id");
echo $l_list->getSimpleList();

 * 
 $myField = selectList::getVal("l_list", $_POST);  
 *  */
class selectList
{
    protected $_id, $_table, $_id_field, $_descr_field, $_val, $_label, $_dbo, $_criteria, $_orderby;
    protected $_rs;


    public function __construct($id, $table = "", $val, $dbo) {
        $this->_id = $id;
        $this->_table = $table;
        $this->_id_field = "id";
        $this->_descr_field = "description";
        $this->_val = $val;
        $this->_dbo = $dbo;        
    }
    
    public function set_criteria($val) {
        $this->_criteria = $val;
    }
    
    public function set_label($val) {
        $this->_label = $val;
    }
    
    public function set_idField($val) {
        $this->_id_field = $val;
    }
    
    public function set_descrField($val) {
        $this->_descr_field = $val;
    }
    
    public function set_orderby($val) {
        $this->_orderby = $val;
    }
    
    public function set_rs($val) {
        $this->_rs = $val;
    }
    
    public static function getVal($id, $data) {
        $str = "";
        foreach($data as $key => $value){
            //echo strpos($key,$id)." / ";
            //echo $key."-".$value." / ";
            if (strpos($key,$id)!==false && strpos($key,$id)==0) {
                $ar = explode("___", $key);
                $str = func::ConcatSpecial($str, "[".$ar[count($ar)-1]."]", ",");
            }
        }
        return $str;

    }
    
    public function getList() {
        //show list
        
        echo '<div style="clear:both"></div>';
        echo '<div class="col-4"><h3>'.$this->_label.'</h3></div><div class="col-8">';
        
        echo $this->getSimpleList();
        
        echo '<div style="clear:both"></div>';
        echo '</div>';
        //echo '</div>';
    }
    
    public function getSimpleList() {
        //get rs
        if ($this->_rs) {
            $rs = $this->_rs;
        }
        else {
            $sql = "SELECT * FROM $this->_table";
            if ($this->_criteria!="") {
                $sql .= " WHERE $this->_criteria";
            }
            if ($this->_orderby!="") {
                $sql .= " ORDER BY $this->_orderby ";
            }
            else {
                $sql .= " ORDER BY $this->_descr_field ";
            }       
            $rs = $this->_dbo->getRS($sql);
        }
                
        $str = '<div id="'.$this->_id.'">';
                        
        for ($i=0;$i<count($rs);$i++) {
            $descr = $rs[$i][$this->_descr_field];
            $str .= '<div class="col-10">'.$descr.'</div>';
            
            $chkid = $this->_id . "_chk___" . $rs[$i][$this->_id_field];
            $checked = "";
            if (strpos($this->_val, "[".$rs[$i][$this->_id_field]."]")!==false || $this->_val=="ALL") {
                $checked = 'checked="checked"';
            }
            $chk = '<input type="checkbox" name="'.$chkid.'" id="'.$chkid.'" '.$checked.' />';
            $str .= '<div class="col-2">'.$chk.'</div>';
            $str .= '<div style="clear:both;height:0px"></div>';
        }
        
        $str .= "</div>";
        
        return $str;
        
        
    }
    
    public function getListCondensed() {
        echo '<div style="clear:both"></div>';
        echo '<div class="col-6" style="padding-top:10px">'.$this->_label.'</div><div class="col-6">';               
        echo $this->getSimpleList();
        echo '<div style="clear:both"></div>';
        echo '</div>';
        echo '</div>';
    }
    
    //emfanizw sto control selectlist 
    //ektos apo ta shops sta opoia einai o xristis manager
    //kai ta shops pou einai energa gia afti tin katigoria
    //etsi wste na min xa8oun an o xristis kanei save
    public static function AddOptions($myList, $extraList) {
        $ar_extraList = explode(",", $extraList);
        $myNewList = $myList;
        for ($i=0;$i<count($ar_extraList);$i++) { 
            if ($ar_extraList[$i]!="") {
                if (strpos($myList, $ar_extraList[$i])===FALSE) {
                    $myNewList .= ",".$ar_extraList[$i];
                }
            }
        }
        return $myNewList;
    }

}




class arrayControl
{
    
    protected $_id, $_columnCount, $_rowCount, $_styleClass, $_columnNames, 
            $_columnTypes, $_rowNames, $_canAddRows, $_canDelRows, $_val, 
            $_delimiterRows, $_delimiterColumns, $_columnWidths;
    
    public function __construct($id, $val, $columnCount, $canAddRows = TRUE,
            $canDelRows = TRUE, $delimiterRows = "///", $delimiterColumns = "||") {
        $this->_id = $id;
        $this->_val = $val;
        $this->_columnCount = $columnCount;
        $this->_canAddRows = $canAddRows;
        $this->_canDelRows = $canDelRows;
        $this->_delimiterRows = $delimiterRows;
        $this->_delimiterColumns = $delimiterColumns;
        $this->_val = $val;
        $this->_rowCount = 1;
    }
    
    public function setColumnNames($val) {
        $this->_columnNames = $val;
    }    
    
    public function setColumnWidths($val) {
        $this->_columnWidths = $val;
    }
    
    public function getControl() {
        $control = $this->_id;        
        
        $arRows = explode($this->_delimiterRows, $this->_val);
        $countRows = $this->_val==""? 0: count($arRows); 
        
        if ($this->_columnNames!="") {
            echo "<div class=\"head\"><div style=\"width:5%;display:inline-block\"></div>";
            for ($k = 0; $k < $this->_columnCount; $k++) {
                $width = $this->_columnWidths!=""? "style=\"width:".$this->_columnWidths[$k]."%\"": "";
                $val = $this->_columnNames[$k];
                echo "<input $width class=\"headcell\" type\"text\" readonly value=\"$val\" >";
            }
            echo "</div>";
        }
        
        echo "<div id=\"".$this->_id."\">";
        echo "<input id=\"$control-val\" name=\"$control-val\" type=\"hidden\" value=\"".$this->_val."\" >";
                
        for ($i = 0; $i < $countRows; $i++) {
            $arCol = explode($this->_delimiterColumns, $arRows[$i]);
            echo "<div class=\"row\">";
            echo "<div style=\"width:5%;display:inline-block\"><span class=\"fa fa-arrows-v\"></span></div>";
            for ($k = 0; $k < count($arCol); $k++) {                
                $val = $arCol[$k];
                $width = $this->_columnWidths!=""? "style=\"width:".$this->_columnWidths[$k]."%\"": "";                                
                echo "<input $width type\"text\" class=\"inputcell\" value=\"$val\">";
            }
            echo "&nbsp;<span class=\"fa fa-remove del-row\"></span>";
            
            echo "</div>";
        } 
        echo "</div>";
        
        
        echo $this->getScriptInit();
        
        if ($this->_canAddRows) {
            //echo "<div class=\"head\"><div style=\"width:30px;display:inline-block\"></div>";
            echo "<div style=\"width:30px;display:inline-block\"></div><a id=\"$control-addnew\"><span class=\"fa fa-plus\"></span></a><br/><br/>";
            echo $this->getScriptAddNew();
        }
        
        echo $this->getScriptDelRow();
        echo $this->getScriptUpdateArray();
    }
    
    
    public function getScriptInit() {
        $control = $this->_id;
        $script = <<<EOT
<script>        
    $(function() {
        $( "#$control" ).sortable({
            update: function(event, ui) {                
                var val = getArray('$control');
                $('#$control-val').val(val);
            }
        });
        $( "#$control" ).disableSelection();
    });
</script>
EOT;
        return $script;
    }
    
    
    
    public function getScriptAddNew() {
        $control = $this->_id;
        $newRow = "<div class=\"row\">";
        $newRow .= "<div style=\"width:5%;display:inline-block\"><span class=\"fa fa-arrows-v\"></span></div>";
        for ($k = 0; $k < $this->_columnCount; $k++) {            
            $width = $this->_columnWidths!=""? "style=\"width:".$this->_columnWidths[$k]."%\"": "";                                
            $newRow .= "<input $width type\"text\" class=\"inputcell\" value=\"\">";
        }
        $newRow .= "&nbsp;<span class=\"fa fa-remove del-row\"></span>";
        $newRow .= "<div>";
        $newRow = addslashes($newRow);
        $script = <<<EOT
<script>
    $(function() {
        $('#$control-addnew').css('cursor','pointer');        
        $('#$control-addnew').click(function() {
            $('#$control').append("$newRow");
                
            $('.del-row').click(function() {
                $(this).parent().remove();
            });
                
            $('#$control .inputcell').change(function() {
                var val = getArray('$control');
                $('#$control-val').val(val);
            });
                
        });
    });
</script>
EOT;
        return $script;
    }
    
    
    
    
    
    public function getScriptDelRow() {
        $control = $this->_id;
        $script = <<<EOT
<script>
    $(function() {
        $('.del-row').css('cursor','pointer');
        $('.del-row').click(function() {
            $(this).parent().remove();
                
            var val = getArray('$control');
            $('#$control-val').val(val);
                
        });
    });
</script>
EOT;
        return $script;
    }
    
    public function getScriptUpdateArray() {
        $control = $this->_id;
        $delimiterColumns = $this->_delimiterColumns;
        $delimiterRows = $this->_delimiterRows;
        $script = <<<EOT
<script>
    $(function() {
        $('#$control .inputcell').change(function() {
            var val = getArray('$control');
            $('#$control-val').val(val);
        });
    });
                
</script>

EOT;
        return $script;
        
    }
    
    public function getScriptGetArray() {
        $delimiterColumns = $this->_delimiterColumns;
        $delimiterRows = $this->_delimiterRows;
        
        $script = <<<EOT
<script>
function getArray(el) {
        var arr = '';
        $('#'+el+' > div.row').each(function() {
            var row = ''; 
            $(this).find('.inputcell').each(function() {
                row = row + $(this).val() + '$delimiterColumns' ;                
            });
            row = row.substring(0, row.length - 2);
            arr = arr==''? row: arr + '$delimiterRows' + row;
        });
        return arr;
    }                
                
</script>               

EOT;
        echo $script;
    }
    
}





?>
