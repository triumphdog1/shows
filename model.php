<?
require_once('auth.php');
session_start();
error_reporting('E_ALL ^ E_NOTICE');
$err = false;
$errMsg = "";

// TODO: Optimize model code.  Make use of dbQuery function
function dbQuery($sql) {
	global $dbuser;
	global $dbpass;
	global $db;
        global $err;
        global $errMsg;
	$con = mysql_connect("localhost", $dbuser, $dbpass);
	if (!$con) {
		$e['success'] = false;
                $e['error'] = "Failed to Establish Connection!<br />". mysql_errno($con) . " : " . mysql_error($con) . "'";
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($e);
                return false;
	}
	if ( !mysql_select_db($db) ) {
                $e['success'] = false;
                $e['error'] = "Failed to select database!<br />" . mysql_errno($con) . " : " . mysql_error($con) . "'";
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($e);
                return false;
	}
        if ( !mysql_query($sql) ) {
                $e['success'] = false;
                $e['error'] = "Failed to execute sql!<br />" . mysql_errno($con) . " : " . mysql_error($con) . "'";
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($e);
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
                $e['success'] = false;
                $e['error'] = "Failed to Establish Connection!<br />";// . mysql_errno($con) . " : " . mysql_error($con);
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($e);
                return false;
	}
	
	if ( !mysql_select_db($db) ) {
                $e['success'] = false;
                $e['error'] = "Failed to select database!<br />" . mysql_errno($con) . " : " . mysql_error($con);
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($e);
                return false;
	}
	return true;
}

function table_exists() { 
    global $db;
    $con = dbConnect();
    if ($con) {
        $tables = mysql_list_tables ($db);
        while (list ($temp) = mysql_fetch_array ($tables)) {
                if ($temp == 'gigs') {
                        mysql_close();
                        return true;
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
        return false;
    }
    return true;
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
	if(dbConnect()){
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
                $a['error'] = "Failed to locate database tables!";
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($a);
            }
            mysql_close();
            return $a;
        } else return false;
}

function listUpcoming() {
	if(dbConnect()) {
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
                $a['error'] = "Failed to locate database tables!";
                $err = true;
                $errMsg = $e['error'];
                echo json_encode($a);
            }
            mysql_close();
            return $a;
        } else return false;
}


function nextShow() {
	if(dbConnect()) {
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