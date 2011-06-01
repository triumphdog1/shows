<!DOCTYPE HTML>
<?
require_once('functions.php');
?>
<html>


<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link title="ui-theme" rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
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
                $('#info').html("");
		$('#reset').click();
                $('#tickets').val("http://")

	}
        
        function editShowsForm(date, time, city, venue, info, tickets, id) {
            $('.date').datepicker("option", {
                minDate: null,
                defaultDate: date
            });
            
            $('#showsFormDialog').dialog("option", "title", "Edit Show");
            $('#id').val(id);
            $('#city').val($("<div/>").html(city).text());
            $('#venue').val($("<div/>").html(venue).text());
            $('#info').html($("<div/>").html(info).text());
            $('#tickets').val($("div/>").html(tickets).text());
            $('#action').val('edit');
            $('#showsFormDialog').dialog("open");
            $('#time').timepicker('setTime', time);
            $('#date').datepicker('setDate', date);
            $('#date').datepicker('hide');
            $('#city').focus();
        }
	
	$(document).ready(function() {		
		$('.date').datepicker({
			minDate:0,
                        dateFormat: "m/d/yy",
			defaultDate:0,
                        constrainInput:true
		});
		$('.time').timepicker({
			showPeriod: true,
			showLeadingZero: false,
                        amPmText: ['am', 'pm'],
			defaultTime:"4:20 pm"
		});
		reloadTable();
		checkLogin();
		
		$('#loginForm').submit(function(e) {	
			$.post("ajax.php", $('#loginForm').serialize(), function(data) {
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
					$('#loginForm').submit();
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
                        showButtonPanel: true,
                        closeText:"X",
			buttons: {
				"Submit": function() {
					$('#showsForm').submit();
				},
				"Cancel": function() {
					hideShowsForm();
				}
			}
		});
		
		$('#showsForm').submit(function(e) {
                    $.post('ajax.php', $('#showsForm').serialize(), function(data) {
			if (data) alert(data);
			hideShowsForm();
                        reloadTable();
                    });
                    e.preventDefault();
		});
                /*
                $('#loginForm').validate({
                    highlight: function(element, errorClass) {
                        $(element).fadeOut(function() {
                            $(element).fadeIn();
                        });
                    }
                });
                */
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

<div style="display:none;">
	<div id="login" title="Login">
		<form method="post" id="loginForm">
			<fieldset>
				<label for="username">Username:</label>
				<input name="username" id="username" class="required" />
				<label for="password">Password:</label>
				<input type="password" name="password" id="password" class="required" />
				<input type="submit" style="display:none;" />
				<input type="hidden" name="action" value="login" />
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
						<td><input type='text' name='date' id='date' class='date' /></td>
						<td style="padding-left:20px"><input type='text' name='time' id='time' class='time' /></td>
					</tr>
				</table>
				<br />
				<label for="city">City:</label><br />
				<input name="city" id="city" size="42" /><br />
				<br />
				<label for="venue">Venue:</label><br />
				<input name="venue" id="venue" size="42" /><br />
				<br />
				<label for="info">More Information:</label><br />
				<textarea name="info" id="info" rows="4" cols="41"></textarea><br />
				<br />
				<label for="tickets">Ticket link or price</label><br />
				<input name="tickets" id="tickets" size="42" value="http://" /><br />
				<br />
				<input type="submit" style="display:none" />
                                <input type="reset" style="display:none" id="reset" />
				<input type="hidden" name="action" id="action" value="add" />
                                <input type="hidden" name="id" id="id" value="" />
			</fieldset>
		</form>
	</div>
</div>

</html>