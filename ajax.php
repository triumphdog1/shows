<?

require_once("shows.php");
$action = isset($_POST['action']) ? $_POST['action'] : false;
$shows = new Shows();
$users = new Users();
$logged_in = $shows->checkLoggedIn();

if ($action == 'next_show') {
	echo $shows->nextShow();
}

if ($action == 'login') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $a = $users->testLogin($user, md5($pass));
    $e = array();
    if ($a) {
	$_SESSION['logged_in'] = true;
	if ($a == "admin") {
	    $_SESSION['admin'] = true;
	    $e['admin'] = true;
	}
	$e['success'] = true;
    } else {
	$_SESSION['logged_in'] = false;
	$e['success'] = false;
    }
    echo json_encode($e);
}

if ($action == 'logout') {
    $_SESSION['logged_in'] = false;
    $_SESSION['admin'] = false;
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
    if ($_POST['upcoming'] || !$logged_in) {
	$gigs = $shows->listAll('upcoming');
    } else {
	$gigs = $shows->listAll();
    }
    if($gigs) {
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