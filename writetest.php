<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/class.php');
//make sure you prevent teacher for signin in and admin
//get all classe
$all_classes = myclass::find_all();
if($session->is_logged_in() == true){
	//$generalFunc->redirect_to('index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome <?php if(isset($_SESSION['full_name'])){
		echo $_SESSION['full_name']; 
		} ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>
	
	<div class="container  writecontainer">
	<?php if(empty($all_classes)) : ?>
		<?php
		echo "<span class=\"error\">Admin Yet To Create A Class</span>";
		return false;
		?>
	<?php endif; ?>
	<div id="entername" class="current">
	<form id="showtestform" action="">
		<input id="enternameinput" data-classid="" type="text" value="" name="entername" placeholder="Enter Your Name" required>
		<div id="display_names">
			
		</div><!-- end of display names -->
		<button type="submit" id="showtest">show my test for today</button>
	</form>
		</div><!-- enter name -->
		
		<div id="displaycourses">
			<!-- display courses for that day -->
		</div><!-- end of display courses -->
		
	</div><!-- end of container -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
