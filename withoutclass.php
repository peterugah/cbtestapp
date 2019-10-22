<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/admin.php');
require_once('include/class.php');

//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif($_SESSION['user_type'] !== "admin"){
	$generalFunc->redirect_to('index.php?error=not admin');
}
//all class teachers
$sql = "SELECT teacher_id FROM myclass";
$teacher_class = myclass::find_by_sql($sql);
$single = array();
foreach($teacher_class as $one){
	$single[] = $one->teacher_id;
}
//get all teachers id
$sql = "SELECT id,title,full_name FROM users WHERE user_type = 'teacher' ORDER BY full_name DESC";
$all_teachers = user::find_by_sql($sql);
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

	<div class="show">
		
		<?php foreach($all_teachers as $one) : ?>
			<?php if(!in_array($one->id, $single)) : ?>
				<div id="withoutclass">
				<span><?php echo $one->title . " " . $one->full_name; ?> </span>
				<a href="class.php?assign=<?php echo urlencode($one->id); ?>"><i class="fa fa-plus-square"></i> Assign class</a>
				</div>
			<?php endif; ?>		
		<?php endforeach; ?>

	</div>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
