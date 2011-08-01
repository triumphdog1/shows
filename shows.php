<?PHP

require_once('db.php');
require_once('gig.php');
require_once('users.php');
session_start();
error_reporting('E_ALL');

class Shows extends DB {

	public function addGig($gig) {
	    return parent::dbQuery("INSERT INTO gigs (datetime, city, venue, info, tickets) VALUES(FROM_UNIXTIME('".$gig->datetime()."'), '".$gig->city()."', '".$gig->venue()."', '".$gig->info()."', '".$gig->tickets()."')");
	}

	public function editGig($gig) {
            return parent::dbQuery("UPDATE gigs SET datetime = FROM_UNIXTIME('".$gig->datetime()."'), city = '".$gig->city()."', venue = '".$gig->venue()."', info = '".$gig->info()."', tickets = '".$gig->tickets()."' WHERE id = '".$gig->id()."'");
	}

	public function removeGig($id) {
            return parent::dbQuery("DELETE FROM gigs WHERE id = $id");
	}

	public function listAll($p=null) {
		$a = array();
		$sql = "SELECT * FROM gigs ";
		if ($p == 'upcoming') $sql .= "WHERE datetime >= CURDATE() ";
		$sql .= "ORDER BY datetime ASC";
		$res = parent::dbFetch($sql);
		if ($res) {
                    foreach($res as $row) {
                        $gig = new Gig(strtotime($row['datetime']), $row['city'], $row['venue'], $row['info'], $row['tickets'], $row['id']);
                        $a[] = $gig;
                    }
		} else return false;
		return $a;
	}

	public function nextShow() {
            $res = parent::dbFetch("SELECT id, datetime, city, venue FROM gigs WHERE datetime >= CURDATE() ORDER BY datetime ASC LIMIT 1");
            if ($res) {
		$row = $res[0];
		return date('M j, Y', strtotime($row['datetime'])) . " @ " . $row['venue'] . " in " . $row['city'];
	    } else return "UNAVAILABLE";
	}
	
	public function checkLoggedIn() {
            return $_SESSION['logged_in'] ? true:false;
	}
        
}

?>