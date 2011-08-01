<?PHP

$dbuser = "tgbr";
$dbpass = "tony";
$db = "tgbr";

class DB {

	public function dbConnect() {
		global $dbuser;
		global $dbpass;
		global $db;
		$con = mysql_connect("localhost", $dbuser, $dbpass);
		if (!$con) {
			$this->setError("Failed to connect to database server!");
			return false;
		}
		if ( !mysql_select_db($db) ) {
			$this->setError("Connected but can not find database $db\n" . mysql_errno($con) . " : " . mysql_error($con));
			return false;
		}
		return $con;
	}
	
	public function dbFetch($sql) {
            $con = $this->dbConnect();
                if ($con) {
                    $res = mysql_query($sql);
                    if (!$res) {
                        $_SESSION['error'] = true;
                        $this->setError("Failed to execute sql!\n" . mysql_errno($con) . " : " . mysql_error($con));
                        return false;
                    }
                }
            $a = array();
            while($row = mysql_fetch_array($res)) {
                    $a[] = $row;
            }
            mysql_close();
            return $a;
	}
	
	public function dbQuery($sql) {
            $con = $this->dbConnect();
            if ($con) {
                $res = mysql_query($sql);
                if (!$res) {
                    $this->setError("Failed to execute sql!\n" . mysql_errno($con) . " : " . mysql_error($con));
                    return false;
                }
            }
            mysql_close();
            return true; 
	}
        
        public function checkError() {
            $a = array();
	    $a['success'] = $_SESSION['error'] ? false:true;
            if ($_SESSION['error']) {
                $a['error'] = $_SESSION['msg'];
                $this->clearError();
            }
            return $a;
        }
        
        public function setError($msg) {
            $_SESSION['error'] = true;
            $_SESSION['msg'] = $msg;
        }
        
        public function clearError() {
            $_SESSION['error'] = false;
            $_SESSION['msg'] = '';
        }
}

?>