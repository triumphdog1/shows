<?

require_once("functions.php");
$logged_in = $_SESSION['logged_in'];
$action = isset($_POST['action']) ? $_POST['action'] : false;
if ($action == 'next_show') {
	if ($nextShow = nextShow()) {
		echo date('M j, Y', strtotime($nextShow['date'])) . " @ " . $nextShow['venue'] . " in " . $nextShow['city'];
	} else {		
		echo "UNAVAILABLE";
	}
}

if ($action == 'login') {
	$username = $_POST['username'];
	$password = $_POST['password'];
	if ($username == $user1 && $password == $pass1 OR $username == $user2 && $password == $pass2) {
		echo $_SESSION['logged_in'] = true;
	} else {
		echo $_SESSION['logged_in'] = false;
	}
}

if ($action == 'logout') {
	$_SESSION['logged_in'] = false;
}

if ($action == 'checkLogin') {
	echo $logged_in;
}

if ($action == 'edit' && $logged_in) {
	//arraymap('htmlentities', $_POST);
	$id = $_POST['id'];
	$date = strtotime($_POST['date'] . " " . $_POST['hour'] . ":" . $_POST['minute'] . " " . $_POST['ampm']);
	$city = htmlentities($_POST['city'], ENT_QUOTES);
	$venue = htmlentities($_POST['venue'], ENT_QUOTES);
	$info = htmlentities($_POST['info'], ENT_QUOTES);
	$tickets = htmlentities($_POST['tickets'], ENT_QUOTES);
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($date, $city, $venue, $info, $tickets);
	$gig->setId($id);
	echo (editGig($gig)) ?
		"$date added!" : false;
}

if ($action == 'add' && $logged_in) {;
	$date = strtotime($_POST['date'] . " " . $_POST['time']);
	$city = htmlentities($_POST['city'], ENT_QUOTES);
	$venue = htmlentities($_POST['venue'], ENT_QUOTES);
	$info = htmlentities($_POST['info'], ENT_QUOTES);
	$tickets = htmlentities($_POST['tickets'], ENT_QUOTES);
	if($tickets == "http://") $tickets = "";
	$gig = new Gig($date, $city, $venue, $info, $tickets);
	echo (addGig($gig)) ? 
		"$venue" : "0";
}

if ($action == 'delete' && $logged_in) {
	echo removeGig($_POST['id']);
}

if ($action == 'showsTable') {
	global $logged_in;
	$gigs = $logged_in ? listAll() : listUpcoming();
	$html = "<script type='text/javascript'>
		$('.delete_button').click(function() {
			var answer = confirm('Really delete this gig?');
			if (answer) {
				$.post('ajax.php', { 'action': 'delete', 'id': $(this).attr('rel') }, function() {
					reloadTable();
				});
			}
		});
		
		$('.edit_button').click(function() {
			var id = $(this).attr('rel');
                        var date = $('#date'+id).html();
                        var time = $('#time'+id).html();
                        var city = $('#city'+id).html();
                        var venue = $('#venue'+id).html();
                        var info = $('#info'+id).html();
                        var tickets = $('#tickets'+id).attr('rel');
                        editShowsForm(date,time,city,venue,info,tickets,id);
		});
		
		$('#addButton').click(function() {
			$('#showsFormDialog').dialog('open');
		});
		</script>";
	$html .= "<table id='showsTable'>";
	foreach($gigs as $gig) {
		$id = $gig->id;
		$date = date('n/d/Y', strtotime($gig->date));
		$time = date('g:i a', strtotime($gig->date));
		$city = html_entity_decode($gig->city);
		$venue = html_entity_decode($gig->venue);
		$info = html_entity_decode($gig->info);
		$tickets = html_entity_decode($gig->tickets);
		$html .= "<tr valign='top'><td><span id='date$id'>$date</span><br />
			<span id='time$id'>$time</span><br /><br /><br /></td>
			<td><span id='city$id'>$city</span><br /><span id='venue$id'>$venue</span></td>
			<td><span id='info$id'>$info</span></td><td style='padding-right:10px;'>
                        <span id='tickets$id' rel='$tickets'>". ticketDisplay($tickets) . "</span></td>";
                if ($logged_in) $html .= "<td><img src='images/edit_button.gif' class='edit_button' rel='$id' style='cursor:pointer'></td>
			<td><img src='images/delete_button.gif' class='delete_button' rel='$id' style='cursor:pointer'></td>";
		$html .= "</tr>";
	}
	$html .= "</table>";
	if ($logged_in) $html .= "<div align=center><input type='button' id='addButton' value='Add Show'></div>";
	echo $html;
}

?>