<?

require_once("shows.php");
$action = isset($_POST['action']) ? $_POST['action'] : false;
$shows = new Shows();
$logged_in = $shows->checkLoggedIn();

function ticketDisplay($tickets) {
	if (substr($tickets, 0 , 7) == 'http://') {
		return "<a href='" . $tickets . "' target='_NEW'><img src='images/tickets_button.gif'></a>";
	}
	return $tickets;
}

if ($action == 'next_show') {
	if ($nextShow = $shows->nextShow()) {
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
	$gig = new Gig($date, $city, $venue, $info, $tickets, $id);
	$shows->editGig($gig);
	echo json_encode($shows->checkError());
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
        $a = array();
	echo json_encode($shows->checkError());
}

if ($action == 'delete' && $logged_in) {
	$shows->removeGig($_POST['id']);
        echo json_encode($shows->checkError());
}

if ($action == 'showsTable') {
    if($gigs = $shows->listAll()) {
        if (count($gigs) > 0) {
            $aReturn = array();
            foreach($gigs as $gig) {
                $a = array();
                $a['id'] = $gig->getId();
                $a['date'] = date('n/j/Y', strtotime($gig->date));
                $a['time'] = date('g:i a', strtotime($gig->date));
                $a['city'] = html_entity_decode($gig->city);
                $a['venue'] = html_entity_decode($gig->venue);
                $a['info'] = html_entity_decode($gig->info);
                $a['tickets'] = ticketDisplay(html_entity_decode($gig->tickets));
                $aReturn['rows'][] = $a;
            }
            $aReturn['logged_in'] = $logged_in;
            $aReturn['success'] = true;
            echo json_encode($aReturn);
        } else $aReturn['rows'] = false;
    } else echo json_encode($shows->getError());
}

?>