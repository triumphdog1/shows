<?

require_once("model.php");
$logged_in = $_SESSION['logged_in'];
$action = isset($_POST['action']) ? $_POST['action'] : false;
$shows = new shows();

function ticketDisplay($tickets) {
	if (substr($tickets, 0 , 7) == 'http://') {
		return "<a href='" . $tickets . "' target='_NEW'><img src='images/tickets_button.gif'></a>";
	}
	return $tickets;
}

if ($action == 'next_show') {
        $nextShow = $shows->nextShow();
	if ($nextShow) {
		echo date('M j, Y', strtotime($nextShow['date'])) . " @ " . $nextShow['venue'] . " in " . $nextShow['city'];
	} else {		
		echo "UNAVAILABLE";
	}
}

if ($action == 'login') {
	$username = $_POST['username'];
	$password = $_POST['password'];
        $e = array();
	if ($username == $user1 && $password == $pass1 OR $username == $user2 && $password == $pass2) {
		$_SESSION['logged_in'] = true;
                $e['success'] = true;
	} else {
		$_SESSION['logged_in'] = false;
                $e['success'] = false;
	}
        echo json_encode($e);
}

if ($action == 'logout') {
	$_SESSION['logged_in'] = false;
}

if ($action == 'checkLogin') {
	echo $logged_in;
}

if ($action == 'edit' && $logged_in) {
	$id = $_POST['id'];
	$date = strtotime($_POST['date'] . " " . $_POST['time']);
	$city = htmlentities($_POST['city'], ENT_QUOTES);
	$venue = htmlentities($_POST['venue'], ENT_QUOTES);
	$info = htmlentities($_POST['info'], ENT_QUOTES);
	$tickets = htmlentities($_POST['tickets'], ENT_QUOTES);
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($date, $city, $venue, $info, $tickets);
	$gig->setId($id);
	$shows->editGig($gig);
        if ($_SESSION['error']) {
            $a['success'] = false;
            $a['error'] = $_SESSION['msg'];
            $_SESSION['error'] = false;
            $_SESSION['msg'] = "";
        } else {
            $a['success'] = true;
        }
        echo json_encode($a);
}

if ($action == 'add' && $logged_in) {
	$date = strtotime($_POST['date'] . " " . $_POST['time']);
	$city = htmlentities($_POST['city'], ENT_QUOTES);
	$venue = htmlentities($_POST['venue'], ENT_QUOTES);
	$info = htmlentities($_POST['info'], ENT_QUOTES);
	$tickets = htmlentities($_POST['tickets'], ENT_QUOTES);
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($date, $city, $venue, $info, $tickets);
	$shows->addGig($gig);
        if ($_SESSION['error']) {
            $a['success'] = false;
            $a['error'] = $_SESSION['msg'];
            $_SESSION['error'] = false;
            $_SESSION['msg'] = "";
        } else {
            $a['success'] = true;
        }
        json_encode($a);
}

if ($action == 'delete' && $logged_in) {
	$shows->removeGig($_POST['id']);
        if ($_SESSION['error']) {
            $a['success'] = false;
            $a['error'] = $_SESSION['msg'];
            $_SESSION['error'] = false;
            $_SESSION['msg'] = "";
        } else {
            $a['success'] = true;
        }
        echo json_encode($a);
}

if ($action == 'showsTable') {
    global $logged_in;
    $gigs = $logged_in ? $shows->listAll() : $shows->listUpcoming();
    if ($_SESSION['error']) {
        $aReturn['success'] = false;
        $aReturn['error'] = $_SESSION['msg'];
        $_SESSION['error'] = false;
        $_SESSION['msg'] = "";
    } else {
        if (count($gigs) > 0) {
            foreach($gigs as $gig) {    
                $a['id'] = $gig->id;
                $a['date'] = date('n/j/Y', strtotime($gig->date));
                $a['time'] = date('g:i a', strtotime($gig->date));
                $a['city'] = html_entity_decode($gig->city);
                $a['venue'] = html_entity_decode($gig->venue);
                $a['info'] = html_entity_decode($gig->info);
                $a['tickets'] = ticketDisplay(html_entity_decode($gig->tickets));
                $aReturn['rows'][] = $a;
            }
        } else $aReturn['rows'] = false;
        $aReturn['logged_in'] = $logged_in;
        $aReturn['success'] = true;
    }
    echo json_encode($aReturn);
}

if ($action == 'displayShows') {
    $gigs = $shows->listUpcoming();
    if ($_SESSION['error']) {
        $aReturn['success'] = false;
        $aReturn['error'] = $_SESSION['msg'];
        $_SESSION['error'] = false;
        $_SESSION['msg'] = "";
    } else {
        if (count($gigs) > 0) {
            $html = "<table id='showsTable' cellpadding='30px'>";
            foreach($gigs as $gig) {
                $date = date('n/j/Y', strtotime($gig->date));
                $time = date('g:i a', strtotime($gig->date));
                $city = html_entity_decode($gig->city);
                $venue = html_entity_decode($gig->venue);
                $info = html_entity_decode($gig->info);
                $tickets = html_entity_decode($gig->tickets);
                if (substr($tickets, 0 , 7) == 'http://') {
                    $tickets = "<a href='$tickets' target='_NEW'><img src='shows/images/tickets_button.gif'></a>";
                }
                $html .= "<tr><td>$date<br />$time<br /></td><td>$city<br />$venue</td>"
                    . "<td>$info</td><td>$tickets</td></tr>";
            }
            $html .= "</table>";
        } else $html = "Currently no upcoming shows.  Check back soon!";
        $aReturn['success'] = true;
        $aReturn['msg'] = $html;
    }
    echo json_encode($aReturn);
}

?>