<?
require_once('auth.php');
session_start();
error_reporting('E_ALL ^ E_NOTICE');

function ticketDisplay($tickets) {
	if (substr($tickets, 0 , 7) == 'http://') {
		return '<a href="' . $tickets . '" target="_NEW"><img src="images/tickets_button.gif"></a>';
	}
	return $tickets;
}

function dbQuery($sql) {
	global $dbuser;
	global $dbpass;
	global $db;
	$con = mysql_connect("localhost", $dbuser, $dbpass);
	if (!$con) {
		echo "Failed to Establish Connection!<br />". mysql_errno($con) . " : " . mysql_error($con);
		return false;
	}
	if ( !mysql_select_db($db) ) {
		echo "Failed to select database!<br />" . mysql_errno($con) . " : " . mysql_error($con);
		return false;
	}
	if (!mysql_query($sql)) {
		echo "Failed to execute sql!<br />" . mysql_errno($con) . " : " . mysql_error($con);
		return false;
	}
	mysql_close();
	return true;
}

function dbConnect() {
	global $dbuser;
	global $dbpass;
	global $db;
	$con = mysql_connect("localhost", $dbuser, $dbpass);
	if (!$con) {
		echo "Failed to Establish Connection!<br />". mysql_errno($con) . " : " . mysql_error($con);
		return false;
	}
	
	if ( !mysql_select_db($db) ) {
		echo "Failed to select database!<br />" . mysql_errno($con) . " : " . mysql_error($con);
		return false;
	}
	return true;
}

function table_exists() { 
	global $db;
	dbConnect();
	$tables = mysql_list_tables ($db);
	while (list ($temp) = mysql_fetch_array ($tables)) {
		if ($temp == 'gigs') {
			mysql_close();
			return TRUE;
		}
	}
	dbQuery("
		CREATE TABLE IF NOT EXISTS `gigs` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `date` datetime NOT NULL,
		  `city` text NOT NULL,
		  `venue` text NOT NULL,
		  `info` text NOT NULL,
		  `tickets` text NOT NULL,
		  KEY `id` (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=158 ;");
	mysql_close();
	return FALSE;
}

function addGig($gig) {
	return dbQuery("INSERT INTO gigs (date, city, venue, info, tickets) VALUES(FROM_UNIXTIME('$gig->date'), '$gig->city', '$gig->venue', '$gig->info', '$gig->tickets')");
}

function editGig($gig) {
	return dbQuery("UPDATE gigs SET date = FROM_UNIXTIME('$gig->date'), city = '$gig->city', venue = '$gig->venue', info = '$gig->info', tickets = '$gig->tickets' WHERE id = '$gig->id'");
}

function removeGig($id) {
	return dbQuery("DELETE FROM gigs WHERE id = $id");
}

function listAll() {
	dbConnect();
	$a = array();
	$res = mysql_query("SELECT * FROM gigs");// WHERE date >= CURDATE() ORDER BY date ASC");
	While ($row = mysql_fetch_array($res)) {
		$gig = new Gig($row['date'], $row['city'], $row['venue'], $row['info'], $row['tickets'], $row['sold_out']);
		$gig->setId($row['id']);
		$a[$row['id']] = $gig;
	}
	mysql_close();
	return $a;
}

function listUpcoming() {
	dbConnect();
	$a = array();
	$res = mysql_query("SELECT * FROM gigs WHERE date >= CURDATE() ORDER BY date ASC");
	While ($row = mysql_fetch_array($res)) {
		$gig = new Gig($row['date'], $row['city'], $row['venue'], $row['info'], $row['tickets'], $row['sold_out']);
		$gig->setId($row['id']);
		$a[$row['id']] = $gig;
	}
	mysql_close();
	return $a;
}


function nextShow() {
	dbConnect();
	$sql = "SELECT * FROM gigs WHERE date >= '" . date() . "' ORDER BY date DESC";
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

class Gig
{
	public $id;
	public $date;
	public $city;
	public $venue;
	public $info;
	public $tickets;
	function __construct($date, $city, $venue, $info, $tickets)
	{
		$this->date = $date;
		$this->city = $city;
		$this->venue = $venue;
		$this->info = $info;
		$this->tickets = $tickets;
	}
	
	function setId($a) {
		$this->id = $a;
	}

}

?>