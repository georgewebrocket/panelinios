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
        
        $this->_del = $del;
        $this->_delUrl = $delUrl;
        $this->_delLabel = $delLabel;
        
        $this->_rowsPerPage = $rowsPerPage;
        $this->_key = $key;                
        $this->_ltoken = $ltoken;
        $this->_tree = FALSE;
        $this->_hasheaders = TRUE;
        $this->_controlColWidths = FALSE;
        $this->_getsubgridpage = "";
        $this->_locale = $locale;
        
        if ($sql!="") {
            $this->_rs = $this->_conn->getRS($this->_sql);
        }
        
        
        $this->_selectRow = FALSE;
        $this->_selectLabel = "select";
        
    }
    
    public function set_edit($editUrl,$editLabel="Edit") {
        $this->_edit = TRUE;
        $this->_editUrl = $editUrl;
        $this->_editLabel = $editLabel;
    }
    
    public function set_del($delUrl,$delLabel="Delete") {
        $this->_del = TRUE;
        $this->_delUrl = $delUrl;
        $this->_delLabel = $delLabel;
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
    
    
    private function createGridHeader() {
        
        if ($this->_controlColWidths) {
            $col=0;
            if ($this->_tree) {
                $this->_gridheader = "<th width=\"".$this->_colWidths[$col]."\">.</th>";
                $col++;
            }
            for ($i=0;$i<count($this->_headers);$i++) {
                $this->_gridheader .= "<th width=\"".$this->_colWidths[$col]."\">".$this->_headers[$i]."</th>";
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
                $editurl = $this->_editUrl."?id=".$this->_rs[$i][$this->_key]."&".$this->_ltoken;
                $this->_gridrows .= "<a class=\" fancybox\" href=\"".$editurl."\">";
                $this->_gridrows .= "<img src=\"img/open.png\"/>"; //..............
                $this->_gridrows .= "</a></td>";
                $cols++;
            }
            if ($this->_del) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">";
                $delurl = $this->_delUrl."?id=".$this->_rs[$i][$this->_key];
                $this->_gridrows .= "<a class=\" fancybox\" href=\"".$delurl."\">";
                $this->_gridrows .= "<img src=\"img/del.png\"/>"; //..............
                $this->_gridrows .= "</a></td>";
                $cols++;
            }
            if ($this->_addNode) {
                $this->_gridrows .= "<td style=\"text-align:center\" class=\"".$tdclass."\">";
                $addNodeUrl = $this->_addNodeUrl."?id=".$this->_rs[$i][$this->_key];
                $this->_gridrows .= "<a class=\" fancybox\" href=\"".$addNodeUrl."\">";
                $this->_gridrows .= "<img src=\"img/add.png\"/>"; //..............
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
        $this->_gridrows .= "<tr><td class=\"table-footer\" colspan=\"".$cols."\"></td></tr>";
    }
    
    public function get_datagrid() {
        $this->createGridHeader();
        $this->createGridRows();
        
        echo '<table id="'.$this->_id.'" class="datagrid" cellspacing="1">';
        //if ($this->_hasheaders) {
            echo $this->_gridheader; 
        //}
        echo $this->_gridrows;        
        echo '</table>';
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
    
    public function set_format($type) {
        $this->_format = $type;
        
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
//        $input = "";
//        $val = "";
//        if ($this->_format!="") {
//            $val = func::format($this->_val, $this->_format, $this->_locale);
//        }
//        else {
//            $val = $this->_val;
//        }       
//        
//        switch ($this->_multiline) {
//            case FALSE:
//                $input = '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" />';
//                break;
//            case TRUE:
//                $input = '<textarea class="'.$this->_class.'" name="'.$this->_id.'" id="'.$this->_id.'" cols="" rows="10" placeholder="'.$this->_placeholder.'">'.$val.'</textarea>';
//                break;
//            default:
//        }        
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
                //return '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" />';
                //disabled="disabled"
                if ($this->_disabled) {
                    return '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" disabled="disabled" />';
                }
                else {
                    return '<input class="'.$this->_class.'" type="'.$this->_type.'" name="'.$this->_id.'" id="'.$this->_id.'" value="'.$val.'" placeholder="'.$this->_placeholder.'" />';
                }
            case TRUE:
                //return '<textarea class="'.$this->_class.'" name="'.$this->_id.'" id="'.$this->_id.'" cols="" rows="10" placeholder="'.$this->_placeholder.'">'.$val.'</textarea>';
                if ($this->_disabled) {
                    return '<textarea class="'.$this->_class.'" name="'.$this->_id.'" id="'.$this->_id.'" cols="" rows="5" placeholder="'.$this->_placeholder.'" disabled="disabled">'.$val.'</textarea>';
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
    
    public function set_class($class) {
        $this->_class = $class;
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
                echo "<input onclick=\"window.parent.location.reload(false);\" type=\"button\" value=\"Close & update\" />";
                break;
            default :
                echo "<input onclick=\"".$this->_method."\" type=\"button\" value=\"".$this->_label."\" />";
                break;
        }  
        
    }
    
    
}



class comboBox
{
    protected $_id, $_sql, $_idField, $_descField, $_currentId, $_label;
    protected $_rs;
    protected $_disabled;
    protected $_dontSelect;
    protected $_zerochoice;
	protected $_size;
    
    public function __construct($id, $conn, $sql, $idField="id", $descField="description", $currentId=0, $label="") {
        $this->_id = $id;        
        $this->_idField = $idField;
        $this->_descField = $descField;
        $this->_currentId = $currentId;
        $this->_label = $label;
        
        if (strpos($sql, "ORDER BY")==FALSE && $sql!="") {
            $sql .= " ORDER BY " . $descField;
        }
        $this->_sql = $sql;
        if ($this->_sql!="") {
            $this->_rs = $conn->getRS($sql);
        }
        //$this->_rs = $conn->getRS($sql);
        $this->_disabled = FALSE;
        $this->_dontSelect = "...";
        $this->_zerochoice = true;
		$this->_size = 0;
    }
    
    public function set_zerochoice($val) {
        $this->_zerochoice = $val;
    }
    
    
    public function set_disabled() {
        $this->_disabled = TRUE;
    }
    
    public function set_dontSelect($val) {
        $this->_dontSelect = $val;
    }
	
	public function set_size($val) {
        $this->_size = $val;
    }
    
    
    public function get_comboBox() {
        
        $control = $this->comboBox_simple();
        echo '<div class="col-4">'.$this->_label.'</div>            
            <div class="col-8">';
        echo $control;
        echo "</div>";
    }
    
    public function comboBox_simple() {
        $id = str_replace("[]", "", $this->_id);
		$size = $this->_size>0? "size=".$this->_size: "";
        $control = "<select name=\"" . $this->_id . "\" id=\"" . $id . "\" ";
        
        if ($this->_disabled == TRUE) {
            $control .= " disabled ";
        }
        $control .= " $size >";
        
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
    
    public function getCount() {
        return count($this->_rs);
    }
    
    public function set_rs($rs) {
        $this->_rs = $rs;
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
        echo '<div class="col-4"><label for="'.$this->_id.'">'.$this->_label.'</label></div>            
            <div class="col-8">'.$input.'</div>';
    }
    
    static function getVal($val) {
        if ($val==1) {
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
            <hr style="height: 0px; border: none; border-bottom: 1px dashed rgb(200,200,200); width: 95%; float: left;"></hr>';
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
    protected $_styleClass;
    protected $_placeholder;
    protected $_max_rows, $_descr_field_2, $_descr_field_3, $_descr_field_4; 
    protected $_getscript;


    public function __construct($id, $table, $idval, $dbo, $getscript = TRUE) {
        $this->_id = $id;
        $this->_hiddenid = "h_" . $this->_id;
        $this->_table = $table;
        $this->_id_field = "id";
        $this->_descr_field = "description";
        if ($idval=="") {$idval=0;}
        $this->_idval = $idval;
        $this->_dbo = $dbo;
        $this->_max_rows = 0;
        $this->_getscript = $getscript;
    }
    
    public function set_id_field($val) {
        $this->_id_field = $val;
    }
    
    public function set_max_rows($val) {
        $this->_max_rows = $val;
    }
    
    public function set_placeholder($val) {
        $this->_placeholder = $val;
    }
    
    public function set_descr_field($val) {
        $this->_descr_field = $val;
    }
    
    public function set_descr_field_2($val) {
        $this->_descr_field_2 = $val;
    }
    
    public function set_descr_field_3($val) {
        $this->_descr_field_3 = $val;
    }
    
    public function set_descr_field_4($val) {
        $this->_descr_field_4 = $val;
    }
    
    public function set_hiddenid($val) {
        $this->_hiddenid = $val;
    }
    
    public function set_label($val) {
        $this->_label = $val;
    }
    
    public function set_styleClass($val) {
        $this->_styleClass = $val;
    }
    
    public function set_text($val) {
        $this->_strval = $val;
    }
    
    private function getControl() {
        if ($this->_strval=="") {
            $this->_strval = func::vlookup($this->_descr_field, 
                $this->_table, $this->_id_field."=".$this->_idval, $this->_dbo);
        }
        $res = "<input class=\"".$this->_styleClass."\" id=\"".$this->_id."\" type=\"text\" name=\"".
                $this->_id."\" value=\"".$this->_strval."\" placeholder=\"".$this->_placeholder."\" />";
        $res .=  "<input type=\"hidden\" name=\"".$this->_hiddenid."\" id=\"".
                $this->_hiddenid."\" value=\"".$this->_idval."\" />";
        return $res;
        
    }
    
    private function getScript() {
        $link = "_acdata.php?table=".$this->_table.
                "&idfield=".$this->_id_field.
                "&descrfield=".$this->_descr_field;
        if ($this->_max_rows>0) {
            $link .= "&maxrows=".$this->_max_rows;
        }
        if ($this->_descr_field_2!="") {
            $link .= "&descrfield2=".$this->_descr_field_2;
        }
        if ($this->_descr_field_3!="") {
            $link .= "&descrfield3=".$this->_descr_field_3;
        }
        if ($this->_descr_field_4!="") {
            $link .= "&descrfield4=".$this->_descr_field_4;
        }
        
        return "<script>
        $(function() {
            $( '#".$this->_id."' ).autocomplete(
            {
                source:'$link',
                minLength:3,
                select: function( event, ui ) {
                $( '#".$this->_hiddenid."' ).val( ui.item.id ).trigger('change');                
                }
            })

        });
        </script>";
        
//        return "<script>
//        $(function() {
//            $( '#".$this->_id."' ).autocomplete(
//            {
//                source:'_acdata.php?table=".$this->_table.
//                "&idfield=".$this->_id_field.
//                "&descrfield=".$this->_descr_field."',
//                minLength:3,
//                select: function( event, ui ) {
//                $( '#".$this->_hiddenid."' ).val( ui.item.id ).trigger('change');                
//                }
//            })
//
//        });
//        </script>";
        
    }
    
    public function getAutocompleteSimple() {
        if ($this->_getscript) {
            echo $this->getScript();
        }
        echo $this->getControl();
    }
    
    public function getAutocomplete() {
        if ($this->_getscript) {
            echo $this->getScript();
        }
        echo "<div class=\"col-4\">".$this->_label."</div>";
        echo "<div class=\"col-8\">";
        echo $this->getControl();
        echo "</div>";
    }
    
    
    
}


class selectList
{
    protected $_id, $_table, $_id_field, $_descr_field, $_val, $_label, $_dbo, $_criteria;
    
    public function __construct($id, $table, $val, $dbo) {
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
        echo '</div>';
    }
    
    public function getSimpleList() {
        //get rs
        $sql = "SELECT * FROM $this->_table";
        if ($this->_criteria!="") {
            $sql .= " WHERE $this->_criteria";
        }
        $sql .= " ORDER BY $this->_descr_field ";
        
        $rs = $this->_dbo->getRS($sql);
                
        $str = '<div id="'.$this->_id.'">';
                        
        for ($i=0;$i<count($rs);$i++) {
            $descr = $rs[$i][$this->_descr_field];
            $str .= '<div class="col-11">'.$descr.'</div>';
            
            $chkid = $this->_id . "_chk___" . $rs[$i][$this->_id_field];
            $checked = "";
            if (strpos($this->_val, "[".$rs[$i][$this->_id_field]."]")!==false) {
                $checked = 'checked="checked"';
            }
            $chk = '<input type="checkbox" name="'.$chkid.'" id="'.$chkid.'" '.$checked.' />';
            $str .= '<div class="col-1">'.$chk.'</div>';
            $str .= '<div style="clear:both;height:0px"></div>';
        }
        
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



?>