<?php


class wp_post
{

protected $_myconn, $_ID, $_post_author, $_post_date, $_post_date_gmt, $_post_content, $_post_title, $_post_excerpt, $_post_status, $_comment_status, $_ping_status, $_post_password, $_post_name, $_to_ping, $_pinged, $_post_modified, $_post_modified_gmt, $_post_content_filtered, $_post_parent, $_guid, $_menu_order, $_post_type, $_post_mime_type, $_comment_count ;

protected $_tprefix, $_intro_text, $_more_text, $_feature_image_path;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '', $tprefix = "wp_") {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    
    $this->_tprefix = $tprefix;

    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM ".$this->_tprefix."posts WHERE ID=?";
        $all_rows = $this->_myconn->getRS($ssql, array($_id));
    }
    else if ($_ssql!='') {
        $ssql = $_ssql;
        $all_rows = $this->_myconn->getRS($ssql);
        }
    else {
        $rows = $my_rows;
        $all_rows = arrayfunctions::filter_by_value($rows, 'ID', $this->_id);
    }
    $icount = count($all_rows);

    if ($icount==1) {
        $this->_post_author = $all_rows[0]['post_author'];
        $this->_post_date = $all_rows[0]['post_date'];
        $this->_post_date_gmt = $all_rows[0]['post_date_gmt'];
        $this->_post_content = $all_rows[0]['post_content'];
        $this->_post_title = $all_rows[0]['post_title'];
        $this->_post_excerpt = $all_rows[0]['post_excerpt'];
        $this->_post_status = $all_rows[0]['post_status'];
        $this->_comment_status = $all_rows[0]['comment_status'];
        $this->_ping_status = $all_rows[0]['ping_status'];
        $this->_post_password = $all_rows[0]['post_password'];
        $this->_post_name = $all_rows[0]['post_name'];
        $this->_to_ping = $all_rows[0]['to_ping'];
        $this->_pinged = $all_rows[0]['pinged'];
        $this->_post_modified = $all_rows[0]['post_modified'];
        $this->_post_modified_gmt = $all_rows[0]['post_modified_gmt'];
        $this->_post_content_filtered = $all_rows[0]['post_content_filtered'];
        $this->_post_parent = $all_rows[0]['post_parent'];
        $this->_guid = $all_rows[0]['guid'];
        $this->_menu_order = $all_rows[0]['menu_order'];
        $this->_post_type = $all_rows[0]['post_type'];
        $this->_post_mime_type = $all_rows[0]['post_mime_type'];
        $this->_comment_count = $all_rows[0]['comment_count'];
		
        $myAr = explode('<!--more-->', $this->_post_content);
        
        if (count($myAr)==2) {
            $this->_intro_text = $myAr[0];
            $this->_more_text = $myAr[1];
        }

        $this->do_get_feature_image();
        $this->do_get_post_categories($this->_id);
        
        
        
    }
    else {
    	$this->_id = 0;
    }
}

public function get_id() {
    return $this->_id;
}

public function get_post_author() {
    return $this->_post_author;
}
public function set_post_author($val) {
    $this->_post_author = $val;
}

public function get_post_date() {
    //return $this->_post_date;
	$date = date_create($this->_post_date);
	return date_format($date, 'd/m/Y');
}

public function get_post_date2() {
    return $this->_post_date;
	
}

public function set_post_date($val) {
    $this->_post_date = $val;
}

public function get_post_date_gmt() {
    return $this->_post_date_gmt;
}
public function set_post_date_gmt($val) {
    $this->_post_date_gmt = $val;
}

public function get_post_content() {
    return $this->_post_content;
}
public function set_post_content($val) {
    $this->_post_content = $val;
}

public function get_post_title() {
    return $this->_post_title;
}
public function set_post_title($val) {
    $this->_post_title = $val;
}

public function get_post_excerpt() {
    return $this->_post_excerpt;
}
public function set_post_excerpt($val) {
    $this->_post_excerpt = $val;
}

public function get_post_status() {
    return $this->_post_status;
}
public function set_post_status($val) {
    $this->_post_status = $val;
}

public function get_comment_status() {
    return $this->_comment_status;
}
public function set_comment_status($val) {
    $this->_comment_status = $val;
}

public function get_ping_status() {
    return $this->_ping_status;
}
public function set_ping_status($val) {
    $this->_ping_status = $val;
}

public function get_post_password() {
    return $this->_post_password;
}
public function set_post_password($val) {
    $this->_post_password = $val;
}

public function get_post_name() {
    return $this->_post_name;
}
public function set_post_name($val) {
    $this->_post_name = $val;
}

public function get_to_ping() {
    return $this->_to_ping;
}
public function set_to_ping($val) {
    $this->_to_ping = $val;
}

public function get_pinged() {
    return $this->_pinged;
}
public function set_pinged($val) {
    $this->_pinged = $val;
}

public function get_post_modified() {
    return $this->_post_modified;
}
public function set_post_modified($val) {
    $this->_post_modified = $val;
}

public function get_post_modified_gmt() {
    return $this->_post_modified_gmt;
}
public function set_post_modified_gmt($val) {
    $this->_post_modified_gmt = $val;
}

public function get_post_content_filtered() {
    return $this->_post_content_filtered;
}
public function set_post_content_filtered($val) {
    $this->_post_content_filtered = $val;
}

public function get_post_parent() {
    return $this->_post_parent;
}
public function set_post_parent($val) {
    $this->_post_parent = $val;
}

public function get_guid() {
    return $this->_guid;
}
public function set_guid($val) {
    $this->_guid = $val;
}

public function get_menu_order() {
    return $this->_menu_order;
}
public function set_menu_order($val) {
    $this->_menu_order = $val;
}

public function get_post_type() {
    return $this->_post_type;
}
public function set_post_type($val) {
    $this->_post_type = $val;
}

public function get_post_mime_type() {
    return $this->_post_mime_type;
}
public function set_post_mime_type($val) {
    $this->_post_mime_type = $val;
}

public function get_comment_count() {
    return $this->_comment_count;
}
public function set_comment_count($val) {
    $this->_comment_count = $val;
}

//*****************************
public function get_intro_text() {
	return $this->_intro_text;		
}

public function get_more_text() {
	return $this->_more_text;
}

public function get_feature_image() {
	return $this->_feature_image_path;
}

public function get_post_categories_ids() {
	return $this->_post_categories_ids;
}

protected function do_get_feature_image() {
	$blankimg = "blank.jpg"; //...
	$myimg = "";
        //echo $this->_id;
	$postid = func::vlookup("meta_value", $this->_tprefix."postmeta", "post_id=" . $this->_id . " AND meta_key='_thumbnail_id'", $this->_myconn);
        //echo "POST-ID=".$postid;
	if ($postid!='') {
            $myimg = func::vlookup("guid", $this->_tprefix."posts", "ID=" . $postid, $this->_myconn);
	}
	if ($myimg == "") {
            $this->_feature_image_path = $blankimg;
	}
	else {
            $this->_feature_image_path = $myimg;
	}
}

protected function do_get_post_categories($postId) {
	$ssql = "SELECT term_taxonomy_id FROM ".$this->_tprefix."term_relationships WHERE object_id = " . $postId . " AND term_taxonomy_id IN (SELECT term_taxonomy_id FROM ".$this->_tprefix."term_taxonomy WHERE taxonomy LIKE 'category')";
	$all_rows = $this->_myconn->getRS($ssql);
	$arr_temp = array();
	if ($all_rows) {
		foreach($all_rows as $arr_ids){
			foreach($arr_ids as $arr_id){			
				$arr_temp[] = $arr_id;
			}			
		}
	}
	$this->_post_categories_ids = $arr_temp;
}

public function get_custom_field($fieldname) {
	return func::vlookup('meta_value',$this->_tprefix.'postmeta',"post_id=". $this->_id . 
		" AND meta_key = '" . $fieldname . "'", $this->_myconn);
}

//*********************************----->


public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO ".$this->_tprefix."posts (
    post_author,
    post_date,
    post_date_gmt,
    post_content,
    post_title,
    post_excerpt,
    post_status,
    comment_status,
    ping_status,
    post_password,
    post_name,
    to_ping,
    pinged,
    post_modified,
    post_modified_gmt,
    post_content_filtered,
    post_parent,
    guid,
    menu_order,
    post_type,
    post_mime_type,
    comment_count
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_post_author,
        $this->_post_date,
        $this->_post_date_gmt,
        $this->_post_content,
        $this->_post_title,
        $this->_post_excerpt,
        $this->_post_status,
        $this->_comment_status,
        $this->_ping_status,
        $this->_post_password,
        $this->_post_name,
        $this->_to_ping,
        $this->_pinged,
        $this->_post_modified,
        $this->_post_modified_gmt,
        $this->_post_content_filtered,
        $this->_post_parent,
        $this->_guid,
        $this->_menu_order,
        $this->_post_type,
        $this->_post_mime_type,
        $this->_comment_count));
    $ssql = $this->_myconn->getLastIDsql($this->_tprefix.'posts');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE ".$this->_tprefix."posts set
        post_author = ?,
        post_date = ?,
        post_date_gmt = ?,
        post_content = ?,
        post_title = ?,
        post_excerpt = ?,
        post_status = ?,
        comment_status = ?,
        ping_status = ?,
        post_password = ?,
        post_name = ?,
        to_ping = ?,
        pinged = ?,
        post_modified = ?,
        post_modified_gmt = ?,
        post_content_filtered = ?,
        post_parent = ?,
        guid = ?,
        menu_order = ?,
        post_type = ?,
        post_mime_type = ?,
        comment_count = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_post_author,
        $this->_post_date,
        $this->_post_date_gmt,
        $this->_post_content,
        $this->_post_title,
        $this->_post_excerpt,
        $this->_post_status,
        $this->_comment_status,
        $this->_ping_status,
        $this->_post_password,
        $this->_post_name,
        $this->_to_ping,
        $this->_pinged,
        $this->_post_modified,
        $this->_post_modified_gmt,
        $this->_post_content_filtered,
        $this->_post_parent,
        $this->_guid,
        $this->_menu_order,
        $this->_post_type,
        $this->_post_mime_type,
        $this->_comment_count,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM ".$this->_tprefix."posts WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}



class wp_category
{
    protected $_id, $_cat_description, $_cat_title, $_cat_arr_posts, $_countplus;
	protected $_myconn, $_tprefix;
        protected $_rs;
    
	//post_name -> slug
    public function __construct($myconn, $_id, $my_rows = NULL, $order_by = "post_name", $limit = 0, $offset = 0, $tprefix = "wp_") {
        $all_rows = NULL;
		$this->_myconn = $myconn;
		$this->_tprefix = $tprefix;
		
        $this->_id = $_id;
        
		if ($my_rows==NULL) {
									
            $term_id = func::vlookup("term_taxonomy_id", $this->_tprefix."term_taxonomy", "term_id=" . $_id,$this->_myconn);			
			$this->_cat_description = func::vlookup("description", $this->_tprefix."term_taxonomy", "term_id=" . $_id, $this->_myconn);
			
			$this->_cat_title = func::vlookup("name", $this->_tprefix."terms", "term_id=" . $_id, $this->_myconn);
			
			$ssql = "SELECT object_id FROM ".$this->_tprefix."term_relationships WHERE term_taxonomy_id IN (" . $term_id . ")";
			
			$all_rows = $this->_myconn->getRS($ssql); //////
			$iCount = count($all_rows);
			
			$postids ="";			
			foreach( $all_rows as $postid){
				$postids = MyUtils::myconcat($postids, $postid['object_id'], ', ');
			}
						

			if($postids==""){$postids="0";}
			$ssql = "SELECT * FROM ".$this->_tprefix."posts where ID IN (" . $postids . ") AND (`post_status`='publish' OR `post_status`='future') AND `post_type`='post' ORDER BY " . $order_by;
			if ($limit!=0) {
				$limit++;
				$ssql .= " limit " . $limit;
			}
			if ($offset!=0) {
				$ssql .= " offset " . $offset;
			}
			
			//$ssql = "SELECT * FROM `wp_posts`";
			$all_rows = $this->_myconn->getRS($ssql);
			$this->_countplus = count($all_rows);
			
			if ($limit == $this->_countplus) {
				$lastelement = array_pop($all_rows); //remove last element
			}
                        
                        
			
        }
        else {
            $all_rows = $my_rows;
            //$all_rows = MyUtils::filter_by_value_new_key($rows, 'ID', $this->_id);            
        }		
		
        
		$arr_posts = array();
		foreach ($all_rows as $posts){
			$arr_posts[] = new wp_post($this->_myconn, $posts['ID'], $all_rows, "", $this->_tprefix);
		}
		$this->_cat_arr_posts = $arr_posts;
                
                $this->_rs = $all_rows;
		
		
    }
	
	public function get_cat_id() {
        return $this->_id;
    }
    
    public function get_rs() {
        return $this->_rs;
    }
	
	public function get_countplus() {
        return $this->_countplus;
    }
	
	public function get_cat_title() {
        return $this->_cat_title;
    }
	
	public function get_cat_description() {
        return $this->_cat_description;
    }
	
	public function get_cat_arr_posts() {
        return $this->_cat_arr_posts;
    }
    
    

}




?>