<?
require_once('auth.php');
session_start();
error_reporting('E_ALL ^ E_WARNING');

class Gig
{
	private $id;
	public $date;
	public $city;
	public $venue;
	public $info;
	public $tickets;
	public function __construct($date, $city, $venue, $info, $tickets)
	{
		$this->date = $date;
		$this->city = $city;
		$this->venue = $venue;
		$this->info = $info;
		$this->tickets = $tickets;
	}
	public function setId($a) {
		$this->id = $a;
	}
	public function getId($a) {
		return $this->id;
	}
}

class db {
	public function dbConnect() {
		global $dbuser;
		global $dbpass;
		global $db;
		$con = mysql_connect("localhost", $dbuser, $dbpass);
		if (!$con) {
			$_SESSION['error'] = true;
			$_SESSION['msg'] = "Failed to connect to database server!";
			return false;
		}
		if ( !mysql_select_db($db) ) {
			$_SESSION['error'] = true;
			$_SESSION['msg'] = "Connected but can not find database $db\n" . mysql_errno($con) . " : " . mysql_error($con);
			return false;
		}
		return $con;
	}
	public function dbQuery($sql) {
		$con = dbConnect();
			if ($con) {
				if (!mysql_query($sql) ) {
						$_SESSION['error'] = true;
						$_SESSION['msg'] = "Failed to execute sql!\n" . mysql_errno($con) . " : " . mysql_error($con) . "'";
						return false;
				}
			}
		mysql_close();
		return true;
	}
}

class Shows extends db {
	public function addGig($gig) {
		return $this->dbQuery("INSERT INTO gigs (date, city, venue, info, tickets) VALUES(FROM_UNIXTIME('$gig->date'), '$gig->city', '$gig->venue', '$gig->info', '$gig->tickets')");
	}

	public function editGig($gig) {
		return "EUREKA";
		//return parent::dbQuery("UPDATE gigs SET date = FROM_UNIXTIME('$gig->date'), city = '$gig->city', venue = '$gig->venue', info = '$gig->info', tickets = '$gig->tickets' WHERE id = '$gig->id'");
	}

	public function removeGig($id) {
		return $this->dbQuery("DELETE FROM gigs WHERE id = $id");
	}

	public function listAll() {
		$con = $this->dbConnect();
		if($con){
			$a = array();
			$res = mysql_query("SELECT * FROM gigs ORDER BY date ASC");
			if ($res) {
				While ($row = mysql_fetch_array($res)) {
						$gig = new Gig($row['date'], $row['city'], $row['venue'], $row['info'], $row['tickets']);
						$gig->setId($row['id']);
						$a[$row['id']] = $gig;
				}
			} else {
				$a['success'] = false;
				$a['error'] = "Failed to locate database tables!\n" . mysql_errno($con) . " : " . mysql_error($con) . "'";
				$_SESSION['error'] = true;
				$_SESSION['msg'] = $e['error'];
			}
			mysql_close();
			return $a;
		} else return false;
	}
	public function listUpcoming() {
		$con = $this->dbConnect();
		if($con) {
				$a = array();
				$res = mysql_query("SELECT * FROM gigs WHERE date >= CURDATE() ORDER BY date ASC");
				if ($res) {
					While ($row = mysql_fetch_array($res)) {
							$gig = new Gig($row['date'], $row['city'], $row['venue'], $row['info'], $row['tickets']);
							$gig->setId($row['id']);
							$a[$row['id']] = $gig;
					}
				} else {
					$a['success'] = false;
					$a['error'] = "Failed to locate database tables!" . mysql_errno($con) . " : " . mysql_error($con) . "'";
					$_SESSION['error'] = true;
					$_SESSION['msg'] = $e['error'];
				}
				mysql_close();
				return $a;
			} else return false;
	}


	public function nextShow() {
		$con = $this->dbConnect();
		if($con) {
				$sql = "SELECT * FROM gigs WHERE date >= CURDATE() ORDER BY date ASC";
				$res = mysql_query($sql);
				if ($row = mysql_fetch_array($res)) {
						$a = array();
						$a['date'] = $row['date'];
						$a['city'] = $row['city'];
						$a['venue'] = $row['venue'];
				} else return false;
				mysql_close();
				return $a;
			}
	}
}

?>