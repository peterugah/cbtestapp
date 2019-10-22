<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/score.php');
require_once('include/course.php');
require_once('include/class.php');
require_once('include/user.php');
require('include/departments.php');
//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');
}elseif($_SESSION['user_type'] !== "teacher"){
	$generalFunc->redirect_to('login.php');
}
//get students for the teacher by class
$sql1 = "SELECT id,class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
$found_class = array_shift(myclass::find_by_sql($sql1));
if(!empty($found_class)){
$sql = "SELECT id,full_name,admission_number,department FROM users WHERE (class_id = {$found_class->id} AND user_type='student') AND new = 0 ORDER BY full_name ASC";
$students  = user::find_by_sql($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Students</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body class="container viewstudents" id="viewcourse">
<?php if(empty($found_class)) : ?>
	<span class="error">Please Meet an Administrator to Assign A Class to You Thank You.</span>
	<?php return false; ?>
<?php endif; ?>
<div id="printdiv">
<button type="button" id="print"><i class="fa fa-print" aria-hidden="true"></i>
 Print</button>
</div>
	<?php if(!empty($students)) : ?>
		<div id="count_students">
		<?php if(count($students) == 1) : ?>
		<span><?php echo count($students); ?> STUDENT</span>
		<?php else : ?>
		<span><?php echo number_format(count($students)); ?> STUDENTS</span>
		<?php endif; ?>
		
		</div>
	<?php endif; ?>

<div id="labels">
	<span class="c1">Student Name</span>
	<span class="c2">Admission Number</span>
	<span class="c3">Test(s)</span>
	<span class="c4">Department</span>
</div>
<div id="searchresult">
<?php if(!empty($students)) : ?>
<?php foreach($students as $one) : ?>
	<?php
	//get student department
	$dept = departments::find_by_id($one->department);
	?>
	<div class="displayscores">
	<span class="c1"><?php echo $one->full_name; ?></span>
	<span class="c2" style="color:green;"><?php
	if($one->admission_number !== ""){
		echo $one->admission_number; 
	}else{
		echo "---";
	}
	 
	 ?></span>
	 <?php
	 $sql = "SELECT COUNT(id) AS id FROM score WHERE student_name = '{$one->full_name}' AND class='{$found_class->class_name}'";
	 $scores = array_shift(myscore::find_by_sql($sql));
	 if($scores->id !== "0"){
	 	$style = "";
	 	//get class
	 	if($found_class->class_name !== ""){
	 		$urlclass = "&class=".$found_class->class_name;
	 	}else{
	 		$urlclass="";
	 	}
	 	$text = "<a href=\"viewscore.php?view={$one->full_name}{$urlclass}\">" .  number_format($scores->id) . " test(s)</a>";
	 }else{
	 	$text = "No Test Record";	 
	 	$style = "style= \"color:orange;\"";
	 }
	 //get students department
	 $styledept = "style= \"color:green;\"";
	 if($dept->name == "General"){
	 	$textdept = "General";
	 	 $styledept = "style= \"color:#1e3f58;\"";
	 }else{
	 	$textdept = $dept->name;
	 }
	 ?>
	<span class="c3" <?php echo $style; ?>><?php echo $text; ?></span>
	<span class="c4" <?php echo $styledept; ?>><?php echo $textdept; ?></span>
	</div><!-- dislpay idividual scores in a div -->
<?php endforeach; ?>
<?php else : ?>
	<span style="color:red;">No Student Found</span>
<?php endif; ?>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
