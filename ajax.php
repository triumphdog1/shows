<?

require_once("shows.php");
$action = isset($_POST['action']) ? $_POST['action'] : false;
$shows = new Shows();
$logged_in = $shows->checkLoggedIn();

if ($action == 'next_show') {
	echo $shows->nextShow();
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
	$datetime = strtotime($_POST['date'] . " " . $_POST['time']);
	$city = $_POST['city'];
	$venue = $_POST['venue'];
	$info = $_POST['info'];
	$tickets = $_POST['tickets'];
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($datetime, $city, $venue, $info, $tickets, $id);
	$shows->editGig($gig);
	echo json_encode($shows->checkError());
}

if ($action == 'add' && $logged_in) {
	$datetime = strtotime($_POST['date'] . " " . $_POST['time']);
	$city = $_POST['city'];
	$venue = $_POST['venue'];
	$info = $_POST['info'];
	$tickets = $_POST['tickets'];
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($datetime, $city, $venue, $info, $tickets);
	$shows->addGig($gig);
	echo json_encode($shows->checkError());
}

if ($action == 'delete' && $logged_in) {
	$shows->removeGig($_POST['id']);
        echo json_encode($shows->checkError());
}

if ($action == 'showsTable') {
    if($gigs = $shows->listAll()) {
        if (count($gigs) > 0) { 
            foreach ($gigs as $gig) {
                $aReturn['rows'][] = $gig->makeArray();
            }
            $aReturn['logged_in'] = $logged_in;
            $aReturn['success'] = true;
            echo json_encode($aReturn);
        } else $aReturn['rows'] = false;
    } else echo json_encode($shows->checkError());
}

?>