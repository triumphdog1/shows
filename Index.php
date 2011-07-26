<!DOCTYPE HTML>

<html>


<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<link title="ui-theme" rel="stylesheet" type="text/css" href="js/jquery-ui-1.8.13.custom.css" />
<script type="text/javascript" src="js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/shows.js"></script>
</head>


<body>
	<div id="main">
		<div id="login-link"><img id="login-button" src="images/login_button.gif"></div>
		<br /><br />
		<div id="header">Shows</div>
		<br /><br />
		<div id="showsTable"></div>
                <div align="center">
                    <br /><br /><br /><br /><br /><br />
                    <a href="../Shows.html"><img src="images/back.png"></a>
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

</html>