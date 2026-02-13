<?php

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
                
                if (self::IsMultiDim($rs)) {
                    return $rs;
                }
                else {
                    return FALSE;
                }
                
		//return $rs;
	}
        
        static function IsMultiDim($array) {
            if (count($array) == count($array, COUNT_RECURSIVE)) 
            {
              return FALSE;
            }
            else
            {
              return TRUE;
            }
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
			$sqltype = substr($sql, 0, 6);
			switch ($sqltype) {
                case "INSERT":
                    return $this->_conn->lastInsertId();
                default:
                    $rs = $stmt->rowCount();
                    return $rs;
                    //return $stmt->errorInfo();
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
        
	public function getConn() {
		return $this->_conn;
	}
	
}

class RS_PAGE
{
    private $_dbo, $_sql, $_rs, $_nrOfRows, $_currentPage, $_countAll, $_countAllRows, $_link, 
            $_pageSClass, $_currentSClass, $_prev, $_next, $_first, $_last ;
    
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
                //echo "<!--$sql-->";
            }
            if ($params == NULL) {
                $rsCount = $dbo->getRS($sql);
            }
            else {
                $rsCount = $dbo->getRS($sql,$params);
            }
            
            $this->_countAllRows =count($rsCount);
            //pages
            $this->_countAll = $this->_nrOfRows!=0? ceil(count($rsCount)/$this->_nrOfRows): 0;
            
            
            
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
        for ($i = $this->_currentPage - $nrPrevNext;$i < $this->_countAll && $i<$this->_currentPage + $nrPrevNext +1;$i++) {
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
            echo "<a class=\"$this->_pageSClass-prev\" href=\"".$this->_link.$prev."\">$this->_prev</a>";                    
        } 
    }
    
    public function getNext() {
        if ($this->_currentPage<$this->_countAll-1) { 
            $next = $this->_currentPage+1; 
            $pageIndex = $next + 1;
            echo "<a class=\"$this->_pageSClass-next\" href=\"".$this->_link.$next."\">$this->_next</a>";             
        }
    }
    
    
    public function getFirst() {
        $prev = 0;
	if ($this->_currentPage>0) {            
            echo "<a class=\"$this->_pageSClass-first\" href=\"".$this->_link.$prev."\">$this->_first</a>";
        } 
        else {
            //echo "<span class=\"$this->_pageSClass-first pagination-inactive\" >$this->_first</span>";
        }
    }
	
    public function setFirst($val) {
            $this->_first = $val;
    }

    public function getLast() {
        $last = $this->_countAll -1; 
        if ($this->_currentPage<$this->_countAll-1) {            
            echo "<a class=\"$this->_pageSClass-last\" href=\"".$this->_link.$last."\">$this->_last</a>";             
        }
        else {
            //echo "<span class=\"$this->_pageSClass-last pagination-inactive\">$this->_last</span>";
        }
    }
	
    public function setLast($val) {
        $this->_last = $val;
    }
    
    public function setPrev($val) {
        $this->_prev = $val;
    }
    
    public function setNext($val) {
        $this->_next = $val;
    }
    
    
    public function getCount() {
        return $this->_countAllRows;
    }
    
}


?>