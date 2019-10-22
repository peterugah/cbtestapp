<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
if($session->is_logged_in() == true){
	$generalFunc->redirect_to('index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login Page</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>

	<div id="loginDiv">
		<h1>Login</h1>
		<form method="post" action="" id="loginform">
		<input type="text" name="username" class="username" placeholder="username" />
		<input type="password" name="password" class="password" placeholder="password" />
		<button type="submit" name="login" class="login">login</button>
		</form>
	</div>


	<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>

</html>