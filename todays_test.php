<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/score.php');
require_once('include/course.php');
require_once('include/class.php');
require_once('include/user.php');
//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');
}elseif($_SESSION['user_type'] !== "teacher"){
	$generalFunc->redirect_to('login.php');
}

//get teachers class
$sql1 = "SELECT class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
$found_class = array_shift(myclass::find_by_sql($sql1));
//get todays test
if(!empty($found_class)){
 $today = date('m/d/Y' , time());
$sql  = "SELECT teacher,course_name,class,activate,department FROM course WHERE (course_date = '{$today}') AND (class LIKE '%{$found_class->class_name}%') ORDER BY activate = 1 DESC";
$todays_test = course::find_by_sql($sql);
}else{
	$todays_test = "";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Todays Test</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body class="container viewstudents" id="todaysTest">
<?php if(empty($todays_test)|| $todays_test=="") : ?>
<span class="error">No Test For <?php echo $found_class->class_name; ?> Today</span>
	<?php return false; ?>
	<?php endif; ?>
<?php if(!empty($todays_test)) : ?>
<?php foreach($todays_test as $one) : ?>
	<div id="test_container">
	<span class="title"><span class="subject"><?php echo strtoupper($one->course_name); ?></span> by <?php echo $one->teacher; ?></span>
	<?php
		$str = str_replace(',', " ", $one->class);
		$string = str_replace($found_class->class_name , "<span class=\"my_class\">{$found_class->class_name}</span>" , $str);
	?>
	<span class="classes">Class(s): <?php echo $string ; ?>
	<span class="dpt"></span>

	</span>
	<?php if($one->activate == 1) : ?>
	<span class="activate">activated</span>
	<?php else : ?>
	<span class="deactivate">deactivated</span>
	<?php endif; ?>
	</div><!-- end of test container -->
<?php endforeach; ?>

<?php endif; ?><!-- end of it test is today -->

<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
