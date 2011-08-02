<!DOCTYPE HTML>

<html>


<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link title="ui-theme" rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/MD5.js"></script>
<script type="text/javascript" src="js/shows.js"></script>
</head>


<body>
	<div id="main">
		<div id="login-link"><img id="login-button" src="images/login_button.gif" /></div>
		<span id="cp-link"><img id="cp-button" src="images/cp.png" width="18" height="18" title="Control Panel" /></span>
		<br /><br />
		<div id="header">Shows</div>
		<br /><br />
		<div id="showsTable"></div>
                <div align="center">
                    <br /><br /><br /><br /><br /><br />
                </div>
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

<div style="display:none">
    <div id="cpDialog" title="User Management">
	<div id="cpBody">
	    <table id="cpTableParent">
		<tr>
		    <th style='padding-right:100px;'>Username</th><th>Admin</th>
		</tr>
		<tbody id="cpTable"></tbody>
	    </table>
	    <div id="passwords" style="display:none; margin-top:10px; margin-bottom:10px;">
		<div style="clear:both"></div>
		Password:<input type="password" id="pass1" style="float:right; width:150px;">
		<div style="clear:both"></div>
		Again:<input type="password" id="pass2" style="float:right; width:150px;">
		<div style="clear:both"></div>
	    </div>	    
	    <div id='withSelected' style='margin-top:10px; margin-bottom:10px;'>
		With Selected:
		<select id='cpAction' style='float:right'>
		    <option value=""></option>
		    <option value="changePass">Change Pass</option>
		    <option value="removeUser">Remove</option>
		    <option value="makeAdmin">Make Admin</option>
		    <option value="noAdmin">No Admin</option>
		</select>
	    </div>
	    <div style='clear:both'></div>
	    <input type=button id='cpAddButton' style='float:left' value='Add User'>
	    <input type=button id='cpGoButton' style='float:right' value='Go'>
	</div>
    </div>
</div>

</html>