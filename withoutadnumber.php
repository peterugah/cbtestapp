<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/admin.php');
require_once('include/class.php');

//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif(!isset($_SESSION['user_type'])){
	$generalFunc->redirect_to('index.php?error=not admin');
}
//get all students without admission number
if($_SESSION['user_type'] == "admin"){
$sql = "SELECT id,full_name,class_id FROM users WHERE admission_number = '' AND user_type= 'student' ORDER BY full_name ASC";
$found = user::find_by_sql($sql);

}elseif($_SESSION['user_type'] == "teacher"){
$sql1 = "SELECT id FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
	$found_class = array_shift(myclass::find_by_sql($sql1));
$sql = "SELECT id,full_name FROM users WHERE admission_number = '' AND user_type= 'student' AND class_id = {$found_class->id} ORDER BY full_name ASC";
$found = user::find_by_sql($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
<?php if(isset($_SESSION['full_name'])) : ?>
	<title>welcome <?php echo $_SESSION['full_name']; ?></title>
<?php else : ?>
	<title>welcome</title>
<?php endif; ?>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>

<body>
<div id="withoutadnumber">
<?php if(empty($found)) : ?>
	<span class="error"> No Record Found </span>
<?php endif; ?>
<?php foreach($found as $one) : ?>
	<div class="show">
		<?php
				if($_SESSION['user_type'] == "admin"){
					$sql = "SELECT class_name FROM myclass WHERE id = {$one->class_id}";
					$found = array_shift(myclass::find_by_sql($sql));
					echo "<div class=\"show_class_one\">";
					if($found){
					echo "<span style=\"color:green; padding-left:1em;\">" . $found->class_name . "</span>";
					}else{
					echo "<span style=\"color:red; padding-left:1em;\">" . "Not Found" . "</span>";	
					}
					echo "</div>";
				}
			
			?>
		<label for="ad_number<?php echo $one->id; ?>"><?php echo $one->full_name; ?>
		</label>	
		<input data-id="<?php echo $one->id; ?>" type="text" placeholder="admission number"  id="ad_number<?php echo $one->id; ?>">
		<button type="button" class="up_admin">update</button>
		<i class="loader fa fa-spin fa-spinner hide" ></i>
	</div>
<?php endforeach; ?>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
