<?
require_once('auth.php');
require_once('db.php');
require_once('gig.php');
session_start();
error_reporting('E_ALL');

class Shows extends DB {
	public function addGig($gig) {
		return parent::dbQuery("INSERT INTO gigs (date, city, venue, info, tickets) VALUES(FROM_UNIXTIME('$gig->date'), '$gig->city', '$gig->venue', '$gig->info', '$gig->tickets')");
	}

	public function editGig($gig) {
		return parent::dbQuery("UPDATE gigs SET date = FROM_UNIXTIME('$gig->date'), city = '$gig->city', venue = '$gig->venue', info = '$gig->info', tickets = '$gig->tickets' WHERE id = '$gig->id'");
	}

	public function removeGig($id) {
		return parent::dbQuery("DELETE FROM gigs WHERE id = $id");
	}

	public function listAll() {
		/*
		$con = parent::dbConnect();
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
		*/
		$a = array();
		$res = parent::dbFetch("SELECT * FROM gigs ORDER BY date ASC");
		if ($res) {
			foreach($res as $row) {
				$gig = new Gig($row['date'], $row['city'], $row['venue'], $row['info'], $row['tickets'], $row['id']);
				$a[] = $gig;
			}
		} else {
			$a['success'] = false;
			$a['error'] = "Failed to locate database tables!\n" . mysql_errno($con) . " : " . mysql_error($con) . "'";
			$_SESSION['error'] = true;
			$_SESSION['msg'] = $e['error'];
		}
		mysql_close();
		return $a;
	}
	public function listUpcoming() {
		$con = parent::dbConnect();
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
		$con = parent::dbConnect();
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