<?php

class conn2
{       
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_epagelmatias_crm;charset=utf8';
	static $username = 'epagelma_eds';
	static $password = 'ep259EDS#';        

}

class conn1
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panel_crm;charset=utf8';
    static $username = 'epagelma_panel_user';
    static $password = 'SxTe@V3d_Eb@';
        

}

class connSiteOld
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_epagelmatias;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';   
    
}

/*
EXAMPLE
$mydb = new DB("mysql:host=db25.grserver.gr:3306;dbname=v6zijufi_epagelmatias_crm;charset=utf8",
"epagelmatias",
"gp1117EP#");
*/
class DB
{
	protected $_conn;
	
	public function __construct($strconn,$username,$password) {
		$this->_conn = new PDO($strconn, $username, $password);
	}
	
	/*
	EXAMPLES	
	$rs = $mydb->getRS("SELECT * FROM USERS");
	$rs = $mydb->getRS("SELECT * FROM USERS WHERE username=? AND password=?", array("george", "123"));
	//RETURNS a 2-dimensional array (rows/fields)
	echo $rs[0]['id'];
	*/
	public function getRS($sql, $params = NULL) {
		$stmt = $this->_conn->prepare($sql);
		if ($params==NULL) {
			$stmt->execute();
		}
		else {
			$stmt->execute($params);
		}
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            $err = $stmt->errorInfo();
            if (count($err)==3 && $err[2]!="") { echo "<!--".$err[2]."-->"; }
	
            return $rs;
	}
	
	/*
	EXAMPLES
	$rs = $mydb->execSQL("UPDATE USER SET password=? WHERE username=?", array("456","george"));
	echo $rs; //returns affected rows = 1
	=============================
	$rs = $mydb->execSQL("INSERT INTO USERS (fullname, username, password) VALUES (?,?,?)", 
		array("George Papagiannis", "george","456"));
	echo $rs; //returns last inserted id
	=============================
	$rs = $mydb->execSQL("DELETE FROM USERS WHERE username=?", 
		array("george"));
	echo $rs; //returns last inserted id
	*/
	public function execSQL($sql, $params = NULL) {
		try {
			$stmt = $this->_conn->prepare($sql);
			if ($params==NULL) {
            	$stmt->execute();
			}
			else {
            	$stmt->execute($params);
			}
			
			
			$err = $stmt->errorInfo();
			echo "<!--<pre>";
			var_dump($err);
			echo "</pre>-->";
			
			
			
			$sqltype = substr($sql, 0, 6);
			switch ($sqltype) {
				case "INSERT":
					return $this->_conn->lastInsertId();
				default:
					$rs = $stmt->rowCount();
					return $rs;                            
			}
		}
		catch(PDOException $ex) {
			echo "ERROR-".$ex->getMessage();	
			return false;
		}	
		
	}
	
	public function getLastIDsql($table) {
		return "SELECT * FROM ".$table." WHERE id=".$this->_conn->lastInsertId();
	}
	
	public function getCols($table) {
		$sql = "SHOW COLUMNS FROM ".$table;
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//print_r($rs);
		$cols = array();
		for ($i=0;$i<count($rs);$i++) {
			$cols[$i] = $rs[$i]['Field'];	
		}
		return $cols;
		
	}	
	
}



/*
$sql = "SELECT * FROM USERS WHERE category=?";
$fields = "ID, FULLNAME"; $fields = "";
$orderBy = " ORDER BY ID ";
$params = array(10);
$curPage = 0; 
if (isset($_GET['page'])) {
$curPage = $_GET['page'];
}
$rowsperpage = 20;
$link = "category.php?id=10&page=";
$rsPage = new RS_PAGE($db1, $sql, $fields, $orderBy, $rowsperpage, $curPage, $params, $link);
$rs = $rsPage->getRS();
$rsPage->getPageLinks();
$rsPage->getPrev();
$rsPage->getNext();
$rsPage->getCount();
*/
class RS_PAGE
{
    private $_dbo, $_sql, $_rs, $_nrOfRows, $_currentPage, $_countAll, $_countAllRows, $_link, 
            $_pageSClass, $_currentSClass, $_prev, $_next, $_first, $_last;
    
    public function __construct($dbo, $sql, $fields, $orderBy, $nrOfRows = 10,
            $currentPage = 0, $params = NULL, $link = "", 
            $RS = NULL, $RSCOUNT = 0 ) 
    {
        $this->_dbo = $dbo;
        $this->_nrOfRows = $nrOfRows;
        $this->_link = $link;
        $this->_currentPage = $currentPage;
        $this->_pageSClass = "paginate";
        $this->_currentSClass = "paginate-current";
        $this->_prev = "<";
        $this->_next = ">";
		$this->_first = "<<";
		$this->_last = ">>";
        
        $start = $this->_currentPage * $this->_nrOfRows;
        
        if ($sql!='') {            

            if ($fields!="") {
                $sql = str_replace("*", $fields, $sql);
            }
            if ($params == NULL) {
                $rsCount = $dbo->getRS($sql);
            }
            else {
                $rsCount = $dbo->getRS($sql,$params);
            }
            //echo "<!--$sql-->";
            $this->_countAllRows =count($rsCount);
            //pages
            $this->_countAll = ceil(count($rsCount)/$this->_nrOfRows);
            
            $sql .= " $orderBy ";            
            $sql .= " LIMIT $start, $nrOfRows ";
            //echo $sql;
            if ($params == NULL) {
                $this->_rs = $dbo->getRS($sql);
            }
            else {
                $this->_rs = $dbo->getRS($sql,$params);
            }            
            
        }
        
        if ($RS!=NULL) {            
            $this->_countAll = intval($RSCOUNT/$this->_nrOfRows);
            $this->_rs = $RS;
        }
         
    }
    
    public function getRS() {
        return $this->_rs;
    }
    
    public function getPageLinks($nrPrevNext = 5) {
        for ($i = $this->_currentPage - $nrPrevNext;$i < $this->_countAll && $i<$this->_currentPage + $nrPrevNext;$i++) {
            if ($i>=0) {
                $startCurrent = $i;
                $pageIndex = $i + 1;
                if ($i==$this->_currentPage) {
                    echo "<a class=\"$this->_currentSClass\" href=\"".$this->_link.$i."\">$pageIndex</a> ";
                }
                else {
                    echo "<a class=\"$this->_pageSClass\" href=\"".$this->_link.$i."\">$pageIndex</a> ";
                }
            }
            
        }
    }
    
    public function getPrev() {
        if ($this->_currentPage>0) { 
            $prev = $this->_currentPage-1;
            echo "<a class=\"$this->_pageSClass\" href=\"".$this->_link.$prev."\">$this->_prev</a>";                    
        }
		else {
			echo "<span class=\"$this->_pageSClass pagination-inactive\" >$this->_prev</span>";
		}
    }
	
	public function setPrev($val) {
		$this->_prev = $val;
	}
    
    public function getNext() {
        if ($this->_currentPage<$this->_countAll-1) { 
            $next = $this->_currentPage+1; 
            $pageIndex = $next + 1;
            echo "<a class=\"$this->_pageSClass\" href=\"".$this->_link.$next."\">$this->_next</a>";             
        }
		else {
			echo "<span class=\"$this->_pageSClass pagination-inactive\" >$this->_next</span>";
		}
    }
	
	public function setNext($val) {
		$this->_next = $val;
	}
    
    public function getCount() {
        return $this->_countAllRows;
    }
	
	public function getFirst() {
        $prev = 0;
		if ($this->_currentPage>0) {            
            echo "<a class=\"$this->_pageSClass\" href=\"".$this->_link.$prev."\">$this->_first</a>";
        } 
		else {
			echo "<span class=\"$this->_pageSClass pagination-inactive\" >$this->_first</span>";
		}
    }
	
	public function setFirst($val) {
		$this->_first = $val;
	}
	
	public function getLast() {
        $last = $this->_countAll -1; 
		if ($this->_currentPage<$this->_countAll-1) {            
            echo "<a class=\"$this->_pageSClass\" href=\"".$this->_link.$last."\">$this->_last</a>";             
        }
		else {
			echo "<span class=\"$this->_pageSClass pagination-inactive\">$this->_last</span>";
		}
    }
	
	public function setLast($val) {
		$this->_last = $val;
	}
    
}






class EMAILS
{

protected $_myconn, $_rs, $_id, $_from_address, $_to_address, $_cc_address, $_bcc_address, $_replyto_address, $_subject, $_body, $_email_date, $_company, $_email_id, $_email_account, $_email_type, $_attachments, $_mark, $_spam, $_trash, $_isread ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM EMAILS WHERE id=?";
        $all_rows = $this->_myconn->getRS($ssql, array($_id));
        }
    else if ($_ssql!='') {
        $ssql = $_ssql;
        $all_rows = $this->_myconn->getRS($ssql);
        }
    else {
        $rows = $my_rows;
        $all_rows = arrayfunctions::filter_by_value($rows, 'id', $this->_id);
    }
    $icount = count($all_rows);

    if ($icount==1) {
        $this->_from_address = $all_rows[0]['from_address'];
        $this->_to_address = $all_rows[0]['to_address'];
        $this->_cc_address = $all_rows[0]['cc_address'];
        $this->_bcc_address = $all_rows[0]['bcc_address'];
        $this->_replyto_address = $all_rows[0]['replyto_address'];
        $this->_subject = $all_rows[0]['subject'];
        $this->_body = $all_rows[0]['body'];
        $this->_email_date = $all_rows[0]['email_date'];
        $this->_company = $all_rows[0]['company'];
        $this->_email_id = $all_rows[0]['email_id'];
        $this->_email_account = $all_rows[0]['email_account'];
        $this->_email_type = $all_rows[0]['email_type'];
        $this->_attachments = $all_rows[0]['attachments'];
        $this->_mark = $all_rows[0]['mark'];
        $this->_spam = $all_rows[0]['spam'];
        $this->_trash = $all_rows[0]['trash'];
        $this->_isread = $all_rows[0]['isread'];

        $this->_rs = $all_rows[0];

    }
}

public function get_id() {
    return $this->_id;
}

public function get_rs() {
    return $this->_rs;
}

public function get_from_address() {
    return $this->_from_address;
}
public function set_from_address($val) {
    $this->_from_address = $val;
}

public function get_to_address() {
    return $this->_to_address;
}
public function set_to_address($val) {
    $this->_to_address = $val;
}

public function get_cc_address() {
    return $this->_cc_address;
}
public function set_cc_address($val) {
    $this->_cc_address = $val;
}

public function get_bcc_address() {
    return $this->_bcc_address;
}
public function set_bcc_address($val) {
    $this->_bcc_address = $val;
}

public function get_replyto_address() {
    return $this->_replyto_address;
}
public function set_replyto_address($val) {
    $this->_replyto_address = $val;
}

public function get_subject() {
    return $this->_subject;
}
public function set_subject($val) {
    $this->_subject = $val;
}

public function get_body() {
    return $this->_body;
}
public function set_body($val) {
    $this->_body = $val;
}

public function get_email_date() {
    return $this->_email_date;
}
public function set_email_date($val) {
    $this->_email_date = $val;
}

public function get_company() {
    return $this->_company;
}
public function set_company($val) {
    $this->_company = $val;
}

public function get_email_id() {
    return $this->_email_id;
}
public function set_email_id($val) {
    $this->_email_id = $val;
}

public function get_email_account() {
    return $this->_email_account;
}
public function set_email_account($val) {
    $this->_email_account = $val;
}

public function get_email_type() {
    return $this->_email_type;
}
public function set_email_type($val) {
    $this->_email_type = $val;
}

public function get_attachments() {
    return $this->_attachments;
}
public function set_attachments($val) {
    $this->_attachments = $val;
}

public function get_mark() {
    return $this->_mark;
}
public function set_mark($val) {
    $this->_mark = $val;
}

public function get_spam() {
    return $this->_spam;
}
public function set_spam($val) {
    $this->_spam = $val;
}

public function get_trash() {
    return $this->_trash;
}
public function set_trash($val) {
    $this->_trash = $val;
}

public function get_isread() {
    return $this->_isread;
}
public function set_isread($val) {
    $this->_isread = $val;
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO EMAILS (
    from_address,
    to_address,
    cc_address,
    bcc_address,
    replyto_address,
    subject,
    body,
    email_date,
    company,
    email_id,
    email_account,
    email_type,
    attachments,
    mark,
    spam,
    trash,
    isread
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_from_address,
        $this->_to_address,
        $this->_cc_address,
        $this->_bcc_address,
        $this->_replyto_address,
        $this->_subject,
        $this->_body,
        $this->_email_date,
        $this->_company,
        $this->_email_id,
        $this->_email_account,
        $this->_email_type,
        $this->_attachments,
        $this->_mark,
        $this->_spam,
        $this->_trash,
        $this->_isread));
    $ssql = $this->_myconn->getLastIDsql('EMAILS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE EMAILS set
        from_address = ?,
        to_address = ?,
        cc_address = ?,
        bcc_address = ?,
        replyto_address = ?,
        subject = ?,
        body = ?,
        email_date = ?,
        company = ?,
        email_id = ?,
        email_account = ?,
        email_type = ?,
        attachments = ?,
        mark = ?,
        spam = ?,
        trash = ?,
        isread = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_from_address,
        $this->_to_address,
        $this->_cc_address,
        $this->_bcc_address,
        $this->_replyto_address,
        $this->_subject,
        $this->_body,
        $this->_email_date,
        $this->_company,
        $this->_email_id,
        $this->_email_account,
        $this->_email_type,
        $this->_attachments,
        $this->_mark,
        $this->_spam,
        $this->_trash,
        $this->_isread,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM EMAILS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}