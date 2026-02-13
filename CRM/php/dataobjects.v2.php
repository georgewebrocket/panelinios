<?php


/*
FIELDS
id
fullname
username
password
active
userprofile
useraccess
costperhour
is_agent
time_start
time_stop
working_days
photo
sign
*/
class USERS
{

protected $_myconn, $_id, $_fullname, $_username, $_password, $_active, $_userprofile, $_useraccess, $_costperhour, $_is_agent, $_time_start, $_time_stop, $_working_days, $_photo, $_sign ;

public function __construct($myconn, $_id, $my_rows = NULL, $_ssql = '') {
    $all_rows = NULL;
    $this->_id = $_id;
    $this->_myconn = $myconn;
    if ($my_rows==NULL) {
        $ssql = "SELECT * FROM USERS WHERE id=?";
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
        $this->_fullname = $all_rows[0]['fullname'];
        $this->_username = $all_rows[0]['username'];
        $this->_password = $all_rows[0]['password'];
        $this->_active = $all_rows[0]['active'];
        $this->_userprofile = $all_rows[0]['userprofile'];
        $this->_useraccess = $all_rows[0]['useraccess'];
        $this->_costperhour = $all_rows[0]['costperhour'];
        $this->_is_agent = $all_rows[0]['is_agent'];
        $this->_time_start = $all_rows[0]['time_start'];
        $this->_time_stop = $all_rows[0]['time_stop'];
        $this->_working_days = $all_rows[0]['working_days'];
        $this->_photo = $all_rows[0]['photo'];
        $this->_sign = $all_rows[0]['sign'];
    }
}

public function get_id() {
    return $this->_id;
}

public function fullname($val = NULL) {
    if ($val === NULL) {        return $this->_fullname;
    }
    else {        $this->_fullname = $val;
    }
}

public function username($val = NULL) {
    if ($val === NULL) {        return $this->_username;
    }
    else {        $this->_username = $val;
    }
}

public function password($val = NULL) {
    if ($val === NULL) {        return $this->_password;
    }
    else {        $this->_password = $val;
    }
}

public function active($val = NULL) {
    if ($val === NULL) {        return $this->_active;
    }
    else {        $this->_active = $val;
    }
}

public function userprofile($val = NULL) {
    if ($val === NULL) {        return $this->_userprofile;
    }
    else {        $this->_userprofile = $val;
    }
}

public function useraccess($val = NULL) {
    if ($val === NULL) {        return $this->_useraccess;
    }
    else {        $this->_useraccess = $val;
    }
}

public function costperhour($val = NULL) {
    if ($val === NULL) {        return $this->_costperhour;
    }
    else {        $this->_costperhour = $val;
    }
}

public function is_agent($val = NULL) {
    if ($val === NULL) {        return $this->_is_agent;
    }
    else {        $this->_is_agent = $val;
    }
}

public function time_start($val = NULL) {
    if ($val === NULL) {        return $this->_time_start;
    }
    else {        $this->_time_start = $val;
    }
}

public function time_stop($val = NULL) {
    if ($val === NULL) {        return $this->_time_stop;
    }
    else {        $this->_time_stop = $val;
    }
}

public function working_days($val = NULL) {
    if ($val === NULL) {        return $this->_working_days;
    }
    else {        $this->_working_days = $val;
    }
}

public function photo($val = NULL) {
    if ($val === NULL) {        return $this->_photo;
    }
    else {        $this->_photo = $val;
    }
}

public function sign($val = NULL) {
    if ($val === NULL) {        return $this->_sign;
    }
    else {        $this->_sign = $val;
    }
}

public function Savedata() {
    if ($this->_id==0) {
    $ssql = "INSERT INTO USERS (
    fullname,
    username,
    password,
    active,
    userprofile,
    useraccess,
    costperhour,
    is_agent,
    time_start,
    time_stop,
    working_days,
    photo,
    sign
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $result = $this->_myconn->execSQL($ssql, array(
        $this->_fullname,
        $this->_username,
        $this->_password,
        $this->_active,
        $this->_userprofile,
        $this->_useraccess,
        $this->_costperhour,
        $this->_is_agent,
        $this->_time_start,
        $this->_time_stop,
        $this->_working_days,
        $this->_photo,
        $this->_sign));
    $ssql = $this->_myconn->getLastIDsql('USERS');

        $newrows = $this->_myconn->getRS($ssql);
        $this->_id = $newrows[0]['id'];
    }
    else {
        $ssql = "UPDATE USERS set
        fullname = ?,
        username = ?,
        password = ?,
        active = ?,
        userprofile = ?,
        useraccess = ?,
        costperhour = ?,
        is_agent = ?,
        time_start = ?,
        time_stop = ?,
        working_days = ?,
        photo = ?,
        sign = ?
        WHERE id = ?";
        $result = $this->_myconn->execSQL($ssql, array(
        $this->_fullname,
        $this->_username,
        $this->_password,
        $this->_active,
        $this->_userprofile,
        $this->_useraccess,
        $this->_costperhour,
        $this->_is_agent,
        $this->_time_start,
        $this->_time_stop,
        $this->_working_days,
        $this->_photo,
        $this->_sign,
        $this->_id));
    }
    if ($result===false) {
        return false;
    }
    return true;
}

public function Delete() {
    $ssql = "DELETE FROM USERS WHERE id=?";
    $result = $this->_myconn->execSQL($ssql, array($this->_id));
    if ($result===false) {
        return false;
    }
else {
    return true;
}
}

}