<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/departments.php');
//check if their is already a user
$sql = "SELECT id FROM users WHERE master_admin = 1 LIMIT 1";
$found = array_shift(user::find_by_sql($sql));
if(isset($found->id)){
		$generalFunc->redirect_to('index.php');
}
//register master admin
if(isset($_POST['register'])){
	$error = "";
	$full_name = $_POST['full_name'];
	$split = explode(' ' , $full_name);
	if(count($split) < 2){
		$error = "full name not complete";
	}

	if(!$error){
		//create the admin
		$user->username = $_POST['username'];
		$user->password = $_POST['password'];
		$user->master_admin = 1;
		$user->user_type = "admin";
		$user->full_name = $_POST['full_name'];
		$user->new_user();
		//log in admin
		$login = user::authenticate($user->username , $user->password);
		$session->login($login);
		//create department
		$departments->name = "General";
		$departments->save();
		//create profile
		$generalFunc->redirect_to('admin.php?msg=kindly create a profile');
	}else{
		$generalFunc->redirect_to('?error');
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Welcome</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
</head>

<body>
<div id="loginDiv">
		<h1> Master Admin</h1>
		<form method="post" action="" id="register">
				<div class="sel_section">
			<label class="info"><i class="fa fa-info"></i> user Type</label>
			<select required class="select" name="user_type" class="user_type" id="utype">
			<option value="admin">Master Admin</option>
			</select>
		</div>	
		<div class="sel_section sel_title">
			<label class="info"><i class="fa fa-info"></i> Title</label>
			<select required class="select" name="title" class="user_type">
			<option value="Mr">Mr</option>
			<option value="Mrs">Mrs</option>
			<option value="Miss">Miss</option>
		</select>
		</div>

		<input  type="text" name="username" class="username" placeholder="User Name" />
		<input  type="password" name="password" class="password" placeholder="Password" />
		<input  required type="text" name="full_name" class="full_name" placeholder="Full Name" />
		<button type="submit" name="register" class="register">Register</button>
</form>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
