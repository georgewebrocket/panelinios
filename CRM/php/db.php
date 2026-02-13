<?php

class conn1
{       
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_panel_crm;charset=utf8';
    static $username = 'epagelma_panel_user';
    static $password = 'SxTe@V3d_Eb@';       

}

class conn2
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_epagelmatias_crm;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';
        

}

class connSiteOld
{
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_epagelmatias;charset=utf8';
    static $username = 'epagelma_eds';
    static $password = 'ep259EDS#';   
    
}

class connCRM5
{       
    static $connstr = 'mysql:host=localhost;port=3306;dbname=epagelma_crm5;charset=utf8';
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
	public function execSQL($sql, $params = NULL, $debug = FALSE) {
		try {
			$stmt = $this->_conn->prepare($sql);
			if ($params==NULL) {
            	$stmt->execute();
			}
			else {
            	$stmt->execute($params);
			}
			
			if ($debug) {
				$err = $stmt->errorInfo();
                if ($err[0]!=0) {
                    echo "<pre>";
                    var_dump($err);
                    echo "</pre>";
                }
				
			}
			
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


?>