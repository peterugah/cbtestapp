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
//get all student scores for admin
if($_SESSION['user_type'] == "admin"){
$sql  = "SELECT * FROM score ORDER BY student_name ASC";
$found_scores = myscore::find_by_sql($sql);
}elseif($_SESSION['user_type'] == "teacher"){
//get all student scores for teacher in a class
$sql1 = "SELECT class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
$found_class = array_shift(myclass::find_by_sql($sql1));
if(!empty($found_class)){
$sql = "SELECT * FROM score WHERE class = '{$found_class->class_name}' ORDER BY student_name ASC";
$found_scores = myscore::find_by_sql($sql);
}
}
//view courses of selected name
if($_SESSION['user_type'] == "admin") {
if(isset($_GET['view']) && $_GET['view'] !==""){
	$sql="SELECT * FROM score WHERE student_name = '{$_GET['view']}' ORDER BY id DESC";
	$found_scores = myscore::find_by_sql($sql);
}
}
//if teacher
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher" && isset($_GET['view']) && $_GET['view'] !==""){
$sql="SELECT * FROM score WHERE student_name = '{$_GET['view']}' AND class='{$found_class->class_name}' ORDER BY id DESC";
	$found_scores = myscore::find_by_sql($sql);
}
//view course of seleted name and class
if(isset($_GET['view']) && $_GET['view'] !=="" && isset($_GET['class']) && $_GET['class'] !==""){
	$sql="SELECT * FROM score WHERE student_name = '{$_GET['view']}' AND class= '{$_GET['class']}' ORDER BY id DESC";
	$found_scores = myscore::find_by_sql($sql);
}
//show results by suject
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin" && isset($_GET['test']) && $_GET['test'] !==""){
$sql="SELECT * FROM score WHERE course_name = '{$_GET['test']}' ORDER BY id DESC";
	$found_scores = myscore::find_by_sql($sql);
}
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher" && isset($_GET['test']) && $_GET['test'] !==""){
$sql="SELECT * FROM score WHERE course_name = '{$_GET['test']}' AND class='{$found_class->class_name}' ORDER BY id DESC";
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
<body class="container" id="viewcourse">
<?php if($_SESSION['user_type'] == "teacher" && empty($found_class)) : ?>
<span class="error">Please Meet An Administrator to Assign A Class to You Thank You.</span>
<?php return false; ?>
<?php endif; ?>
<div id="printdiv">
<button type="button" id="print"><i class="fa fa-print" aria-hidden="true"></i>
 Print</button>
 <?php if(!empty($found_scores)) : ?>
<a id="downloadScores" href="include/download_scores.php?type=result<?php if(isset($_GET['view']) && $_GET['view'] !== ""){
	//get student full name
	echo "&user=" . $_GET['view'];

	}if(isset($_GET['class']) && $_GET['class'] !==""){
		//get sdtudent class
		echo "&class=" . $_GET['class'];
		}
	if(isset($_GET['test']) && $_GET['test'] !== ""){
		echo "&test=" . $_GET['test'];
	}
		 ?>"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
<?php endif; ?>
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
	<input type="text" name="searchval" id="searchval" value="<?php echo $value; ?>" placeholder="Enter Student Name, Subject or Class" data-notice="0" />
	<label for="searchval"><i class="fa fa-search" aria-hidden="true"></i></label>
	<?php if($_SESSION['user_type'] == "admin") : ?>
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
	<span><?php echo "\"No Teacher Assigned To This Class\""; ?></span>
	<?php endif; ?>
	<?php endif; ?>
<?php endif; ?><!-- end of if admin -->

</div><!-- end of search div -->
<div id="labels">
	<span class="c1">Student Name</span>
	<span class="c2">Test</span>
	<span class="c3">Class</span>
	<span class="c4">Date & Time</span>
	<span class="c5">Score</span>
</div>
<div id="searchresult">
<?php if(!empty($found_scores)) : ?>
<?php foreach($found_scores as $one) : ?>
	<div class="displayscores">
	<span class="c1"><a href="?view=<?php echo $one->student_name; ?>&msg=showing results for <?php echo $one->student_name; ?>"><?php echo $one->excapeQuote($one->student_name); ?></a></span>
	<span class="c2"><?php echo  "<a href=\"?test={$one->course_name}&msg=showing results for {$one->course_name}\">" . $one->excapeQuote($one->course_name) . "</a>"; ?></span>
	<span class="c3">
	<a 
	<?php if($_SESSION['user_type'] == "admin") : ?>
	href="?view=<?php echo $one->student_name; ?>&msg=showing <?php echo $one->class; ?> results for <?php echo $one->student_name; ?>&class=<?php echo $one->class; ?>"
	<?php endif; ?>
	><?php echo $one->class; ?></a>
	</span>
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
	<span style="color:red;">No Test Result Found</span>
<?php endif; ?>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
