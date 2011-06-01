<!DOCTYPE HTML>
<?
require_once('functions.php');
?>
<html>

//
<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link title="ui-theme" rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript">
	
	function reloadTable() {
                
		$.post('ajax.php', { 'action': 'showsTable' }, function(data) {
			$('#showsTable').html(data);
		});
		$('#showsFrame', top.document).height('1000px');
		
	}
	
	function checkLogin() {
		$.post('ajax.php', { 'action': 'checkLogin' }, function(data) {
			data ? $('#login-button').attr('src', 'images/logout_button.gif') : $('#login-button').attr('src', 'images/login_button.gif');
		});
	}
	
	function hideLogin() {
		$('#login').dialog("close");
	}
	
	function hideShowsForm() {
		$('#showsFormDialog').dialog("close");
                $('#showsFormDialog').dialog("option", "title", "Add Show");
		$('#reset').click();

	}
        
        function editShowsForm(date, time, city, venue, info, tickets, id) {
            $('.date').datepicker("option", {
                minDate: null,
                defaultDate: date
            });  
            $('#showsFormDialog').dialog("option", "title", "Edit Show");
            $('#date').val(date);
            $('#time').val(time);
            $('#city').val(city);
            $('#venue').val(venue);
            $('#info').html(info);
            $('#tickets').val(tickets);
            $('#action').val("edit");
            $('#showsFormDialog').dialog("open");     
        }
	
	$(document).ready(function() {		
		$('.date').datepicker({
			minDate:0,
                        dateformat: "m/d/yy",
			defaultDate:0
		});
		$('.time').timepicker({
			showPeriod: true,
			showLeadingZero: false,
                        timeFormat: "h:m tt",
			defaultTime:"4:20 PM"
		});
		reloadTable();
		checkLogin();
		
		$('#login-form').submit(function(e) {	
			$.post("ajax.php", $('#login-form').serialize(), function(data) {
				if (data) {
					reloadTable();
					checkLogin();
				} else{
					alert("Login failed!");
					$('#login').dialog("open");
				}
			});
			hideLogin();
			e.preventDefault()
		});
		
		$('#login').dialog({
			autoOpen: false,
			height: 280,
			width: 280,
			draggable: false,
			resizable: false,
			modal: true,
			buttons: {
				"Login": function() {
					$('#login-form').submit();
				},
				"Cancel": function() {
					hideLogin();
				}
			}
		});
		
		$('#login-button').click(function() {
			if ($('#login-button').attr('src') == 'images/login_button.gif') {
				$('#login').dialog("open");
			} else if ($('#login-button').attr('src') == 'images/logout_button.gif') {
				$.post("ajax.php", {"action":"logout"}, function() {
					reloadTable();
					checkLogin();
				});
			}
		});
		
		$('#showsFormDialog').dialog({
			autoOpen: false,
			height: 600,
			width: 500,
			draggable: false,
			resizable: false,
			modal: true,
			buttons: {
				"Add": function() {
					$('#showsForm').submit();
				},
				"Cancel": function() {
					hideShowsForm();
				}
			}
		});
		
		$('#showsForm').submit(function(e) {
                    $.ajaxSetup({async: false});
                    $.post('ajax.php', $('#showsForm').serialize(), function(data) {
			if (data == 0) alert("Failed to add show!");
			hideShowsForm();
                    });
                    $.ajaxSetup({async: true});
                    e.preventDefault();
		});
	});
</script>
</head>


<body>
	<div id="main">
		<div id="login-link"><img id="login-button" src="images/login_button.gif"></div>
		<br /><br />
		<div id="header">Shows</div>
		<br /><br />
		<div id="showsTable""></div>
	</div>
</body>

<div style="display:none">
	<div id="login" title="Login">
		<form method="post" id="login-form">
			<fieldset>
				<label for="username">Username:</label>
				<input name="username" id="username">
				<label for="password">Password:</label>
				<input type="password" name="password" id="password">
				<input type="submit" style="display:none">
				<input type="hidden" name="action" value="login">
			</fieldset>
		</form>
	</div>
</div>

<div style="display:none">
	<div id="showsFormDialog" title="Add Show">
            <form method="post" id="showsForm" action="">
			<fieldset>
				<table>
					<tr>
						<td>Date:</td>
						<td style="padding-left:20px">Time:</td>
					</tr>
					<tr>
						<td><input type='text' name='addDate' id='addDate' class='date'></td>
						<td style="padding-left:20px"><input type='text' name='addTime' id='addTime' class='time'></td>
					</tr>
				</table>
				<br />
				<label for="city">City:</label><br />
				<input name="city" id="city" size="42"><br />
				<br />
				<label for="venue">Venue:</label><br />
				<input name="venue" id="venue" size="42"><br />
				<br />
				<label for="info">More Information:</label><br />
				<textarea name="info" id="info" rows="4" cols="41"></textarea><br />
				<br />
				<label for="tickets">Ticket link or price</label><br />
				<input name="tickets" id="tickets" size="42"><br />
				<br />
				<input type="submit" style="display:none">
                                <input type="reset" style="display:none" id="reset">
				<input type="hidden" name="action" id="action" value="add">
			</fieldset>
		</form>
	</div>
</div>

</html>