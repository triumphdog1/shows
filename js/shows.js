$(document).ready(function() {
        reloadTable();
        $('.delete_button').live( 'click', function() {
            var answer = confirm('Really delete this gig?');
            if (answer) {
                    $.post('ajax.php', { 'action': 'delete', 'id': $(this).attr('rel') }, function(data) {
                       if (data.success) {
                            reloadTable();
                       } else alert(data.error);
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
            var tickets = ($('#tickets' + id + ' a').length) ? $('#tickets'+id).find('a').attr('href'): $('#tickets'+id).html();
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
            $.post('ajax.php', { 'action': 'showsTable' }, function(data) {
                    if (data.logged_in) {
                        $('#login-button').attr('src', 'images/logout_button.gif')
			if (data.admin) $('#cp-link').show();
                    } else {
			$('#cp-link').hide();
                        $('#login-button').attr('src', 'images/login_button.gif');
                    }
                    if (data.success) {
                        var s = "";
                        if (data.rows) {
                            s += "<table id='showsTable'>";
                            $.each(data.rows, function(d,row){
                                s += "<tr valign='top'><td><span id='date" + row['id'] + "'>" + row['date'] + "</span><br />"
                                    + "<span id='time" + row['id'] + "'>" + row['time'] + "</span><br /><br /><br /></td>"
                                    + "<td><span id='city" + row['id'] + "'>" + row['city'] + "</span><br /><span id='venue" + row['id'] + "'>" + row['venue'] + "</span></td>"
                                    + "<td width='150'><span id='info" + row['id'] + "'>" + row['info'] + "</span></td><td style='padding-right:10px;'>"
                                    + "<span id='tickets" + row['id'] + "'>"+ row['tickets'] + "</span></td>";
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
                        $('#showsTable').html(data.error);
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
		$('#password').val( MD5( $('#password').val() ) );  // MD5 password before post
                $.post("ajax.php", $('#loginForm').serialize(), function(data) {
                        if (data.success) {
                                reloadTable();
                        } else{
                                alert("Invalid username or password.");
                                $('#login').dialog("open");
                        }
                }, 'json');
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

        $('#login-button').live('click', function() {
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

        $('#showsForm').submit( function(e) {
            e.preventDefault();
            if (showsFormValidator.form()) {
                $.post('ajax.php', $('#showsForm').serialize(), function(data) {
                    if (!data.success) alert(data.error);
                    hideShowsForm();
                    reloadTable();
                }, 'json');
            }
        });
	
	$('#cpDialog').dialog({
	    autoOpen: false,
	    width: 300,
	    height: 'auto',
	    draggable: false,
	    resizable: false,
	    modal: true,
	    showButtonPanel: true,
	    closeText:"X"
	});
	
	$('#cp-link').live('click', function() {
	    cpReload();
	    $('#cpDialog').dialog("open");
	});
	
	$('#cpGoButton').live('click', function() {
	    var a = {};
	    a.id = $('.userSelect:checked').val();
	    a.action = $('#cpAction').val();
	    if (!a.id && a.action != 'addUser') {  //If no user is selected & not trying to add user
		alert("You must select a user!");
		return;
	    }
	    switch(a.action) {
		case 'addUser':
		    a.user = $('#userAdd').val();
		    a.admin = $('#addUserAdmin').is(':checked') ? "1":"0";
		    if (a.user == "") {
			alert("You must enter a username to add a user!");
			return;
		    }
		    if (checkUserExists(a.user)) return;
		case 'changePass':
		    if (!$('#passwords').is(':visible')) {  //checks to see if password boxes are already showing
			$('#withSelected').hide();
			$('#passwords').show();
			$('#cpGoButton').val('Change');
			$('#cpAddButton').val('Cancel');
			$('#pass1').focus();
			return;
		    } else {
			if (!cpCheckNewPasswords()) return;
			a.pass = MD5($('#pass1').val());
			break;
		    }
		case 'removeUser':
		    a.user = $('.userSelect:checked').attr('rel');
		    if (!confirm("Really delete user " + a.user + "?")) return;
		    break;
	    }
	    
	    $.post('ajax.php', a, function(data) {
		if (!data.success) {
		    alert(data.error);
		} else {
		    cpReload();
		}
	    }, 'json');
	});
	
	
	$('#cpAddButton').live('click', function() {
	    if ($(this).val() == 'Cancel') {
		resetCP();
		return;
	    } else {
		$('.userSelect:checked').attr('checked', false);
		$('#cpTable').append("<tr id='addUserRow'><td><input id='userAdd' class='addUserFields'></td><td><input type='checkbox' id='addUserAdmin'></td></tr>");
		$('#userAdd').focus();
		$('#withSelected').hide();
		$('#cpAction').append("<option value='addUser' selected='selected'>add</option>");
		$(this).val('Cancel')
		$('#cpGoButton').val('Add');
		$('#passwords').show();
	    }
	});
	
	function cpReload() {
	    var html = "";
	    resetCP();
	    $.post('ajax.php', {'action': 'cpReload'}, function(data) {
		if (!data.success) {
		    html = data.error;
		} else {
		    $.each(data.rows, function(d, row) {
			var a = row['admin'] == 1 ? "YES" : "NO";
			html += "<tr><td><input type='radio' name='userSelect' class='userSelect' rel='" + row['username'] + "' value='" + row['id'] + "' />" + row['username'] + "</td><td>" + a + "</td></tr>";
		    });
		}
		$('#cpTable').html(html);
	    }, 'json');
	}
	
	function cpCheckNewPasswords() {
	    if ($('#pass1').val() == "" || $('#pass2').val() == "") {  //if password fields are empty
		alert("You must enter a password in both fields!");
		return false;
	    }
	    if ($('#pass1').val() != $('#pass2').val()) {  //if passwords do not match
		alert("Your passwords do not match!");
		return false;
	    }
	    if ($('#pass1').val().length < 0) {
		alert("Your password must be at least 6 characters.");
		return false;
	    }
	    return true;
	}
	
	function resetCP() {
	    $('#cpAction option:first-child').attr('selected', 'selected');
	    $('#withSelected').show();
	    $('#passwords').hide();
	    $('#passwords > input').val('');
	    if ($('#cpTable tr:last').attr('id') == 'addUserRow') $('#cpTable tr:last').remove();  // if add user field exists, delete it
	    if ($('#cpAction').val() == 'addUser') $('#cpAction option:selected').remove();
	    $('#cpAddButton').val('Add User');
	    $('#cpGoButton').val('Go');
	    $('.userSelect:checked').attr('checked', false);
	}
	
	$('.addUserFields').live( 'keydown', function(e) {
            if(e.keyCode == 13) {       
                $('#cpGoButton').click();
            }
        });
	
	function checkUserExists(user) {
	    var found = false;
	    $.each($('.userSelect'), function () {
		if (user == $(this).attr('rel')) {
		    alert("That username is already in use!");
		    found = true;
		}
	    });
	    return found;
	}
});


