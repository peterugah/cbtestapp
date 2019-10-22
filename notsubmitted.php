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
}elseif(!isset($_SESSION['user_type'])){
	$generalFunc->redirect_to('login.php');
}
//get all student scores 
if($_SESSION['user_type'] == "admin"){
$sql  = "SELECT * FROM score WHERE notice = 1 ORDER BY student_name DESC";
$found_scores = myscore::find_by_sql($sql);
}elseif($_SESSION['user_type'] == "teacher"){
	//get all student scores for teacher in a class
$sql1 = "SELECT class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
	$found_class = array_shift(myclass::find_by_sql($sql1));
$sql = "SELECT * FROM score WHERE class = '{$found_class->class_name}' AND notice = 1 ORDER BY student_name DESC";
$found_scores = myscore::find_by_sql($sql);
}
//view courses of selected name
if(isset($_GET['view']) && $_GET['view'] !==""){
	$sql="SELECT * FROM score WHERE student_name = '{$_GET['view']}' AND notice = 1";
	$found_scores = myscore::find_by_sql($sql);
}
//view course of seleted name and class
if(isset($_GET['view']) && $_GET['view'] !=="" && isset($_GET['class']) && $_GET['class'] !==""){
	$sql="SELECT * FROM score WHERE student_name = '{$_GET['view']}' AND class= '{$_GET['class']}' AND notice = 1";
	$found_scores = myscore::find_by_sql($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php if(isset($_GET['view'])){echo $_GET['view'];}else{echo "score";} ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body class="container impropersubmit" id="viewcourse">
<div id="printdiv">
<button type="button" id="print"><i class="fa fa-print" aria-hidden="true"></i>
 Print</button>
</div>
<?php if(isset($_GET['msg'])) :?>
	<div id="msg">
		<span><?php echo $_GET['msg']; ?></span>
	</div>
	<div id="backbtn" data-inforight="refresh page">
		<a href="?"><i class="fa fa-chevron-circle-left" aria-hidden="true" title="refresh"></i>
</a>
	</div>
<?php endif; ?>


<div id="searchdiv">
<?php
	if(isset($_GET['view']) && !empty($_GET['view'])){
		$value = trim($_GET['view']);
	}else{
		$value ="";
	}
?>
	<!-- get class teacher  -->
	<?php if(isset($_GET['class']) && !empty($_GET['class'])) : ?>
	<?php
	$sql = "SELECT teacher_id FROM myclass WHERE class_name = '{$_GET['class']}'";
	$found = array_shift(myclass::find_by_sql($sql));
	if(!empty($found)){
	//get the teachers full_name and title
	$sql="SELECT title,full_name FROM users WHERE id = '{$found->teacher_id}'";
	$found = array_shift(User::find_by_sql($sql));
	}
	?>
	<span id="classTeacher">Class Teacher of <?php echo strtoupper($_GET['class']); ?> : 
	<?php if(!empty($found)) : ?>
	<?php echo $found->title . " " . $found->full_name; ?></span>
	<?php else : ?>
	<?php echo "\"No teacher assigned to this class\""; ?></span>
	<?php endif; ?>
	<?php endif; ?>
</div><!-- end of search div -->
<div id="labels">
	<span class="c1">Student Name</span>
	<span class="c2">Course</span>
	<span class="c3">class</span>
	<span class="c4">Date & Time</span>
	<span class="c5">Score</span>
</div>
<div id="searchresult">
<?php if(!empty($found_scores)) : ?>
<?php foreach($found_scores as $one) : ?>
	<div class="displayscores">
	<span class="c1"><a href="?view=<?php echo $one->student_name; ?>&msg=showing results for '<?php echo $one->student_name; ?>'"><?php echo $one->student_name; ?></a></span>
	<span class="c2"><?php echo  "<a href=\"viewcourse.php?course_id={$one->course_id}\">" . $one->course_name . "</a>"; ?></span>
	<span class="c3"><a href="?view=<?php echo $one->student_name; ?>&msg=showing '<?php echo $one->class; ?>' results for '<?php echo $one->student_name; ?>'&class=<?php echo $one->class; ?>"><?php echo $one->class; ?></a></span>
	<span class="c4"><?php echo date('j M, Y' , $one->score_date) . " at " . date('g:ia' , $one->score_date); ?></span>
	<?php
	//get score and average
	$average = $one->max_score / 2;
	$average = round($average);
	if($one->score < $average){
		$style = "style=\"color : red;\"";
	}elseif($one->score == $average){
			$style = "style=\"color : maroon;\"";
	}else{
		$style = "style=\"color : green;\"";
	}
	?>
	<span class="c5" <?php echo $style; ?>><?php echo $one->score;?> / <?php echo $one->max_score; ?></span>
	<?php if($_SESSION['master_admin'] == 1) : ?>
	<span class="c6" data-id= "<?php echo $one->id; ?>" id="removeScore"><i class="fa fa-remove"></i></span>
<?php endif; ?>
</div><!-- dislpay idividual scores in a div -->
<?php endforeach; ?>
<?php else : ?>
	<span style="color:red;">no record found</span>
<?php endif; ?>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
