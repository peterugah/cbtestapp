<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/class.php');
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('index.php');
}
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] !=="teacher"){
	$generalFunc->redirect_to('index.php');
}
//get  the classes
$sql = "SELECT class_name,id FROM myclass WHERE teacher_id = {$_SESSION['user_id']}";
$found_class = array_shift(myclass::find_by_sql($sql));
if(!empty($found_class)){
	//get the refused students
	$sql = "SELECT full_name FROM users WHERE class_id={$found_class->id} AND reject = 1 AND user_type='student'";
	$found_students = user::find_by_sql($sql);
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
<div id="therefused">
<?php if(!empty($found_students)) : ?>
	<span id="">The Follolwing Students Where Declined, And Have Been Returned Back To Your Class:</span>
	<?php foreach($found_students as $one) : ?>
		<span class="declined"><?php echo $one->full_name; ?></span>
	<?php endforeach; ?>
	<?php endif; ?>
</div>

<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
