<?php

require_once 'db.php';
require_once 'config.php';

$db1 = new DB(conn1::$connstr,conn1::$username,conn1::$password);

if (isset($_POST['TxtTable'])) {
    $mytable = $_POST['TxtTable'];
    if ($mytable!='') {
        $cols = $db1->getCols($mytable);
        $colscount = count($cols);
    }
}

?>

<html>
<head>
<title>DB CLASS MAKER</title>

<style>
    body {
        font-family:Courier, sans-serif;
    }
</style>

</head>
<body>

<form action="classbuilder.php" method="post">
<input name="TxtTable" id="TxtTable" type="text">
<input name="BtnOK" type="submit" value="Submit">

</form>

<?php if (isset($_POST['TxtTable'])) { ?>

<h1><?php echo $mytable; ?></h1>

<?php

$tab = '&nbsp;&nbsp;&nbsp;&nbsp;';

echo 'class '.$mytable. '<br/>{<br/><br/>';

//protected vars
echo 'protected $_myconn, $_rs, ';
for ($i=0;$i<$colscount;$i++) {
	echo '$_' . $cols[$i];
	if ($i<$colscount-1) {
		echo ', ';
	} 
	else {
		echo ' ';	
	}
}
echo ';<br/><br/>';

//construct function
echo 'public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = \'\') { <br/>
	'.$tab.'$all_rows = NULL; <br/>
	'.$tab.'$this->_id = $_id; <br/>
    '.$tab.'$this->_myconn = $myconn; <br/>
	'.$tab.'if ($my_rows==NULL) { <br/>
	'.$tab.$tab.'$ssql = "SELECT * FROM ' . $mytable . ' WHERE id=?"; <br/>
	'.$tab.$tab.'$all_rows = $this->_myconn->getRS($ssql, array($_id)); <br/>
	'.$tab.$tab.'} <br/>		
	'.$tab.'else if ($_ssql!=\'\') { <br/>
	'.$tab.$tab.'$ssql = $_ssql; <br/>
	'.$tab.$tab.'$all_rows = $this->_myconn->getRS($ssql); <br/>
	'.$tab.$tab.'} <br/>		
	'.$tab.'else { <br/>
	'.$tab.$tab.'$rows = $my_rows; <br/>
	'.$tab.$tab.'$all_rows = arrayfunctions::filter_by_value($rows, \'id\', $this->_id); <br/>            
	'.$tab.'}<br/>
	'.$tab.'$icount = count($all_rows); <br/><br/>
	'.$tab.'if ($icount==1) { <br/>';
	
for ($i=1;$i<$colscount;$i++) {
	echo $tab.$tab.'$this->_' . $cols[$i] . ' = $all_rows[0][\'' . $cols[$i] . '\']; <br/>';
}
echo "<br/>".$tab.$tab.'$this->_rs = $all_rows[0];<br/><br/>';
echo $tab.'} <br/>';
echo '} <br/><br/>';

//fields
echo 'public function get_id() { <br/>';
echo $tab.'return $this->_id;  <br/>';
echo '}  <br/><br/>';

echo 'public function get_rs() { <br/>';
echo $tab.'return $this->_rs;  <br/>';
echo '}  <br/><br/>';



for ($i=1;$i<$colscount;$i++) {
	echo 'public function get_' . $cols[$i] . '() { <br/>';
	echo $tab.'return $this->_' . $cols[$i] . ';  <br/>';
	echo '}  <br/>';
	
	echo 'public function set_' . $cols[$i] . '($val) { <br/>';
	echo $tab.'$this->_' . $cols[$i] . ' = $val;  <br/>';
	echo '}  <br/><br/>';	
}

//Savedata function
echo 'public function Savedata() { <br/>
    '.$tab.'if ($this->_id==0) { <br/>
    '.$tab.'$ssql = "INSERT INTO ' . $mytable . ' ( <br/>';
for ($i=1;$i<$colscount;$i++) {
	echo  $tab.$cols[$i];
	if ($i<$colscount-1) { echo ',<br/>'; } 
	else { echo '<br/>'; }
}

echo $tab.') VALUES (';
for ($i=1;$i<$colscount;$i++) {
	echo  '?';
	if ($i<$colscount-1) { echo ', '; } 
	else { echo ')"; <br/>';	}
}
echo $tab.'$result = $this->_myconn->execSQL($ssql, array( <br/>';
for ($i=1;$i<$colscount;$i++) {
	echo  $tab.$tab.'$this->_' . $cols[$i];
	if ($i<$colscount-1) { echo ', <br/>'; } 
	else { echo ')); <br/>'; }
}							

echo $tab.'$ssql = $this->_myconn->getLastIDsql(\''.$mytable.'\');<br/>';

echo '<br/>
	'.$tab.$tab.'$newrows = $this->_myconn->getRS($ssql); <br/>
	'.$tab.$tab.'$this->_id = $newrows[0][\'id\']; <br/>			
	'.$tab.'} <br/>
	'.$tab.'else { <br/>
	'.$tab.$tab.'$ssql = "UPDATE ' . $mytable . ' set <br/>';
for ($i=1;$i<$colscount;$i++) {
	echo  $tab.$tab.$cols[$i] . ' = ?';
	if ($i<$colscount-1) { echo ', <br/>'; } 
	else { echo '<br/>'; }
}	
echo $tab.$tab.'WHERE id = ?"; <br/>';
echo $tab.$tab.'$result = $this->_myconn->execSQL($ssql, array( <br/>';
for ($i=1;$i<$colscount;$i++) {
	echo  $tab.$tab.'$this->_' . $cols[$i];
	if ($i<$colscount-1) { echo ', <br/>'; } 
	else { echo ',<br/>'.$tab.$tab.'$this->_id));<br/>'; }
}							
echo $tab.'} <br/>
	'.$tab.'if ($result===false) { <br/>
	'.$tab.$tab.'return false; <br/>
	'.$tab.'} <br/>		
	'.$tab.'return true; <br/>
	} <br/><br/>';

//Delete function
echo 'public function Delete() { <br/>
    '.$tab.'$ssql = "DELETE FROM ' . $mytable . ' WHERE id=?"; <br/>
    '.$tab.'$result = $this->_myconn->execSQL($ssql, array($this->_id));  <br/>  
    '.$tab.'if ($result===false) { <br/>
    '.$tab.$tab.'return false; <br/>
    '.$tab.'} <br/>else { <br/>		
    '.$tab.'return true; <br/>}<br/>
    } <br/><br/>';
	
echo '}';

?>


<?php } ?>


</body>

</html>





