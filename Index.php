<!DOCTYPE HTML>
<?
require_once('model.php');
?>
<html>


<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link title="ui-theme" rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript">

$(document).ready(function() {
        reloadTable();
        $('.delete_button').live( 'click', function() {
            var answer = confirm('Really delete this gig?');
            if (answer) {
                    $.post('control.php', { 'action': 'delete', 'id': $(this).attr('rel') }, function(data) {
                       if (data.success) {
                            reloadTable();
                       }
                    }, 'json');
            }
        });

        $('.edit_button').live( 'click', function() {
            var id = $(this).attr('rel');
            var date = $('#date'+id).html();
            var time = $('#time'+id).html();
            var city = $('#city'+id).html();
            var venue = $('#venue'+id).html();
            var info = $('#info'+id).html();
            var tickets = $('#tickets'+id).attr('rel');
            editShowsForm(date,time,city,venue,info,tickets,id);
        });

        $('#addButton').live( 'click', function() {
            $('#showsFormDialog').dialog('open');
        });
        
        var loginValidator = $('#loginForm').validate({
            onsubmit: false,
            rules: {
                username: "required",
                password: "required"
            }

        });

        var showsFormValidator = $('#showsForm').validate({
            onsubmit: false,
            rules: {
                date: {
                    maxlength: '10',
                    required: true,
                    date: true
                },
                
                time: {
                    maxlength: '8',
                    required: true
                },
                
                city: {
                    maxlength: '50',
                    required: true
                },
                
                venue: {
                    maxlength: '100',
                    required: true
                },
                
                info: {
                    maxlength: '250',
                    required: false
                },
                
                tickets: {
                    maxlength: '250',
                    required: false
                }
            }
        });
        
        function reloadTable() {
            $.post('control.php', { 'action': 'showsTable' }, function(data) {
                    data.logged_in ? $('#login-button').attr('src', 'images/logout_button.gif') : $('#login-button').attr('src', 'images/login_button.gif');
                    if (data.success){
                        var s = "";
                        if (data.rows) {
                            s += "<table id='showsTable'>";
                            $.each(data.rows, function(d,row){
                                s += "<tr valign='top'><td><span id='date" + row['id'] + "'>" + row['date'] + "</span><br />"
                                    + "<span id='time" + row['id'] + "'>" + row['time'] + "</span><br /><br /><br /></td>"
                                    + "<td><span id='city" + row['id'] + "'>" + row['city'] + "</span><br /><span id='venue" + row['id'] + "'>" + row['venue'] + "</span></td>"
                                    + "<td width='150'><span id='info" + row['id'] + "'>" + row['info'] + "</span></td><td style='padding-right:10px;'>"
                                    + "<span id='tickets" + row['id'] + "' rel='" + row['tickets'] + "'>"+ row['tickets'] + "</span></td>";
                                if (data.logged_in) {
                                    s += "<td><img src='images/edit_button.gif' class='edit_button' rel='" + row['id'] + "'></td>"
                                        + "<td><img src='images/delete_button.gif' class='delete_button' rel='" + row['id'] + "'></td>";
                                }
                                s += "</tr>";
                            });
                            s += "</table>";
                        }
                        if (data.logged_in) {
                            s += "<div align=center><input type='button' id='addButton' value='Add Show'></div>";
                        }
                        $('#showsTable').html(s);
                    }else{
                        $('#shows.Table').html(data.error);
                    }
            }, 'json');
        }

        function hideLogin() {
                $('#login').dialog("close");
                $('#loginReset').click();
        }

        function hideShowsForm() {
                $('#showsFormDialog').dialog("close");
                $('#showsFormDialog').dialog("option", "title", "Add Show");
                $('#reset').click();
                $('#action').val('add');
                $('#info').html('');
        }

        // TODO: editShowsForm not displaying ticket link in form, but only when its a link.
        function editShowsForm(date, time, city, venue, info, tickets, id) {
            $('#date').datepicker("option", {
                minDate: null,
                defaultDate: date
            });
            $('#date').datepicker("refresh");

            $('#showsFormDialog').dialog("option", "title", "Edit Show");
            $('#id').val(id);
            $('#city').val($("<div/>").html(city).text());
            $('#venue').val($("<div/>").html(venue).text());
            $('#info').html($("<div/>").html(info).text());
            $('#tickets').val($("<div/>").html(tickets).text());
            $('#action').val('edit');
            $('#showsFormDialog').dialog("open");
            $('#time').timepicker('setTime', time);
            $('#date').datepicker('setDate', date);
            $('#date').datepicker('hide');
            $('#city').focus();
        }
        
        $('form').live( 'keyup', function(e) {
            if(e.keyCode == 13) {       
                $(this).submit();
            }
        });


        $('#date').datepicker({
                minDate:0,
                dateFormat: "m/d/yy",
                defaultDate:0,
                constrainInput:true
        });
        
        $('#time').timepicker({
                showPeriod: true,
                showLeadingZero: false,
                amPmText: ['am', 'pm'],
                defaultTime:"4:20 pm"
        });

        $('#loginForm').submit(function(e) {
            e.preventDefault();
            if ($('#loginForm').valid()) {
                $.post("control.php", $('#loginForm').serialize(), function(data) {
                        if (data) {
                                reloadTable();
                        } else{
                                alert("Invalid username or password.");
                                $('#login').dialog("open");
                        }
                });
                hideLogin();
            }
        });

        $('#login').dialog({
                autoOpen: false,
                height: 300,
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
                        $.post("control.php", {"action":"logout"}, function() {
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

        $('#showsForm').submit( function(e) {
            e.preventDefault();
            if (showsFormValidator.form()) {
                $.post('control.php', $('#showsForm').serialize(), function(data) {
                    if (!data.success) alert(data.error);
                    hideShowsForm();
                    reloadTable();
                }, 'json');
            }
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
		<div id="showsTable"></div>
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
				<input type="hidden" name="action" value="login" />
                                <input type="reset" name="loginReset" id="loginReset" style="display:none">
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
						<td>Time:</td>
					</tr>
					<tr>
						<td><input type='text' name='date' id='date' /></td>
						<td><input type='text' name='time' id='time' /></td>
					</tr>
				</table>
				<br />
				<label for="city">City:</label><br />
				<input name="city" id="city" size="42" maxlength="50" /><br />
				<br />
				<label for="venue">Venue:</label><br />
				<input name="venue" id="venue" size="42" maxlength="100" /><br />
				<br />
				<label for="info">More Information:</label><br />
				<textarea name="info" id="info" rows="4" cols="41"></textarea><br />
				<br />
				<label for="tickets">Ticket link or price</label><br />
				<input name="tickets" id="tickets" size="42" value="http://" maxlength="250" /><br />
				<br />
				<input type="hidden" name="action" id="action" value="add" />
                                <input type="hidden" name="id" id="id" value="" />
                                <input type="reset" name="reset" id="reset" style="display:none">
			</fieldset>
		</form>
	</div>
</div>

</html>