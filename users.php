<?php
require_once("db.php");
class Users extends DB {
    
    public function addUser($user, $pass, $admin=0) {
	$user = htmlentities($user, ENT_QUOTES);
        return parent::dbQuery("INSERT INTO users (username, password, admin) VALUES('$user', '" . $this->salt($pass) . "', '$admin')");
    }
    
    public function changePass($id, $pass) {
        return parent::dbQuery("UPDATE users SET password = '" . $this->salt($pass) . "' WHERE id = '$id'");
    }
    
    public function isAdmin($id) {
        $r = parent::dbFetch("SELECT admin FROM users WHERE id = '$id'");
        return $r[0]['admin'];
    }
    
    public function setAdmin($id, $a) {
        $a = $a ? "1":"0";
        return parent::dbQuery("UPDATE users SET admin = '$a' WHERE id = '$id'");
    }
    
    public function removeUser($id) {
        return parent::dbQuery("DELETE FROM users WHERE id = '$id'");
    }
    
    public function listUsers() {
	$r = parent::dbFetch("SELECT id, username, admin FROM users");
	foreach ($r as $row) {
	    $row['username'] = html_entity_decode($row['username']);
	}
	return $r;
    }
    
    public function testLogin($user, $pass) {
	$user = htmlentities($user, ENT_QUOTES);
	$r = parent::dbFetch("SELECT id, admin FROM users WHERE username = '$user' AND password = '" . $this->salt($pass) . "'");
	if (count($r) > 0) {
	    if ($r[0]['admin'] == 1) return "admin";
	    return true;
	} else return false;
    }
    
    public function userExists($user) {
	$user = htmlentities($user, ENT_QUOTES);
	$r = parent::dbFetch("SELECT username FROM users WHERE username = '$user'");
	if (count($r) > 0) {
	    return true;
	}else return false;
    }
    
    public function salt($pass) {
	return md5( md5("salt") . ":@:$pass");
    }
    
}

?>
