<?

require_once("shows.php");
$action = isset($_POST['action']) ? $_POST['action'] : false;
$shows = new Shows();
$users = new Users();
$logged_in = $shows->checkLoggedIn();
$admin = $_SESSION['admin'];

if ($action == 'next_show') {
	echo $shows->nextShow();
}

if ($action == 'login') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $a = $users->testLogin($user, $pass);
    $e = array();
    if ($a) {
	$_SESSION['logged_in'] = true;
	if ($a === "admin") {
	    $_SESSION['admin'] = true;
	    $e['admin'] = true;
	} else $e['admin'] = false;
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
	$aReturn['logged_in'] = $logged_in;
	$aReturn['admin'] = $admin;
        $aReturn['success'] = true;
        if (count($gigs) > 0) { 
            foreach ($gigs as $gig) {
                $aReturn['rows'][] = $gig->makeArray();
            }
        } else $aReturn['rows'] = false;
	echo json_encode($aReturn);
    } else echo json_encode($shows->checkError());
}

if ($action == 'cpReload') {
    if ($logged_in && $admin) {
	$u = $users->listUsers();
	if (count($u) > 0) {
	    $r = array();
	    $r['success'] = true;
	    $r['rows'] = $u;
	    echo json_encode($r);
	} else echo json_encode($users->checkError());
    } else {
	$r = array();
	$r['success'] = false;
	$r['error'] = "You don't have access to this!";
	echo json_encode($r);
    }
}

if ($action == 'removeUser' && $logged_in && $admin) {
    $users->removeUser($_POST['id']);
    echo json_encode($shows->checkError());
}

if ($action == 'makeAdmin' && $logged_in && $admin) {
    $users->setAdmin($_POST['id'], 1);
    echo json_encode($shows->checkError());
}

if ($action == 'noAdmin' && $logged_in && $admin) {
    $users->setAdmin($_POST['id'], 0);
    echo json_encode($shows->checkError());
}

if ($action == 'changePass' && $logged_in) {
    $users->changePass($_POST['id'], $_POST['pass']);
    echo json_encode($users->checkError());
}

if ($action == 'addUser' && $logged_in && $admin) {
    if (!$users->userExists($_POST['user'])) {
	$users->addUser($_POST['user'], $_POST['pass'], $_POST['addUserAdmin']);
	echo json_encode($users->checkError());
    } else {
	$r = array();
	$r['success'] = false;
	$r['error'] = "That username already exists!";
	echo json_encode($r);
    }
}

?>