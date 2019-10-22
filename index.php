<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/admin.php');
require_once('include/class.php');
require_once('include/score.php');
require_once('include/course.php');
//$session->logout();
// if($session->is_logged_in() == false){
// 	$generalFunc->redirect_to('login.php');
// }
//get the app url
$exec = exec("hostname"); 
$hostname = trim($exec); 
$ip = gethostbyname($hostname);
$address = $ip . "/". basename(getcwd());

//check if its first user, redirect to register page for master admin
//check if their is already a user
$sql = "SELECT id FROM users WHERE master_admin = 1 LIMIT 1";
$found = array_shift(user::find_by_sql($sql));

//redirect to admin register page if their is not admin
if(!$found->id){
		$generalFunc->redirect_to('regadmin.php');
}


//get admin details
$profile = array_shift(admin::find_all());
//if the profile is empty tell the admin to create one
if(!isset($profile->school_name)){
$generalFunc->redirect_to('admin.php?msg=kindly create a profile');
}

//logout user
if(isset($_GET['logout'])){
	$session->logout();
}
//get class assigned to teacher
$class_id = 0;
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher"){
	$sql = "SELECT class_name,id FROM myclass WHERE teacher_id = {$_SESSION['user_id']}";
	$found_class = array_shift(myclass::find_by_sql($sql));
	if(!empty($found_class)){
	$class_id = $found_class->id;
	}
}

	//get students count of those without admission number
	if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin"){
		$sql = "SELECT COUNT(id) AS id FROM users WHERE admission_number = '' AND user_type='student'";
		$withoutadnumber = array_shift(user::find_by_sql($sql));
	}
	if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher"){
		$sql = "SELECT COUNT(id) AS id FROM users WHERE admission_number = '' AND user_type='student'  AND class_id= {$class_id}";
		$withoutadnumber = array_shift(user::find_by_sql($sql));
	}
	//all class teachers
	$sql = "SELECT teacher_id FROM myclass";
	$teacher_class = myclass::find_by_sql($sql);
	$single = array();
	foreach($teacher_class as $one){
	$single[] = $one->teacher_id;
	}
	//get all teachers id
	$sql = "SELECT id,title,full_name FROM users WHERE user_type = 'teacher'";
	$all_teachers = user::find_by_sql($sql);

	$countteachers = "";
	foreach($all_teachers as $one){
		if(!in_array($one->id , $single)){
			$countteachers++;
		}
	}

	//get improper submissions
	if(isset($_SESSION['user_type'])){
	if($_SESSION['user_type'] == "admin"){
	$sql = "SELECT COUNT(id) AS id FROM score WHERE notice  = 1";
	$improper = array_shift(myscore::find_by_sql($sql));	
	}elseif($_SESSION['user_type'] == "teacher"){
		//check if the teacher has a class assinged to hin/her
	if(!empty($found_class)){
	$sql1 = "SELECT class_name,id FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
	$found_class = array_shift(myclass::find_by_sql($sql1));
	$sql = "SELECT COUNT(id) AS id FROM score WHERE notice  = 1 AND class = '{$found_class->class_name}'";
	$improper = array_shift(myscore::find_by_sql($sql));
	}
}
	}

	//check if their is test for today
	if(!empty($found_class)){
	if($_SESSION['user_type'] == "teacher"){
		$today = date('m/d/Y' , time());
		$sql  = "SELECT COUNT(id) AS id FROM course WHERE (course_date = '{$today}') AND (class LIKE '%{$found_class->class_name}%')";
		$todays_test = array_shift(course::find_by_sql($sql));
	}
}


	//get rejected students
if(!empty($found_class)){
	$sql = "SELECT * FROM users WHERE reject = 1 AND class_id = {$found_class->id} AND user_type='student'";
	$rejected = user::find_by_sql($sql);
}
$allowed = true;
//check the browser in use
if(isset($_SERVER['HTTP_USER_AGENT'])){
	$agent = $_SERVER['HTTP_USER_AGENT'];	
	 if (!preg_match('/Chrome|Firefox/i', $agent)) {
	 	//prevent user acces
	 	$allowed = false;
	 	}
}

?>
<!DOCTYPE html>
<html>
<head>
<?php if(isset($_SESSION['full_name'])) : ?>
	<title>Welcome <?php echo $_SESSION['full_name']; ?></title>
<?php else : ?>
	<title>Welcome</title>
<?php endif; ?>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css?d=<?= date(time()) ?>">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js?d=<?= date(time()) ?>"></script>
	<?php if($session->is_logged_in() == true) : ?>
	<?php require 'script/mathjax.php'; ?>
	<?php endif; ?>
	<noscript>
	<style type="text/css">
	..noscript {
		display: block;
		color: red;
		font-size: 2em;
	}
		*{
			display : none;
		}
	</style>
	<span class="noscript">Please Enable Javascript And Refresh The Window.</span>
</noscript>
</head>

<body>
<?php if($allowed == false) : ?>
	<div id="browserError">
	<span class="error">Browser Not Supported kindly use Google Chrome or Mozilla Firefox</span>
	<div class="browerimagaes">
	<h4>Supported Browsers</h4>
	<img src="images/chrome.png" alt="">
	<img src="images/firefox.png" alt="">
	</div>
	</div>
	<?php return false; ?>
<?php endif; ?>
<div id="indexpage">
<div id="header">
		<div id="school_name">
		<?php if($profile->school_logo !== "") : ?>
			<img src="admin/<?php echo $profile->school_logo; ?>" alt="">
		<?php endif; ?>
		<?php if($profile->school_name !== "") : ?>
			<span><?php echo $profile->school_name; ?></span>
		<?php else : ?>
			<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin" && $_SESSION['master_admin'] == 1) : ?>
			<a id="edit_profile" href="admin.php" style="color: green;">Edit Profile <i class="fa fa-pencil"></i></a>
			<?php endif; ?>
		<?php endif; ?>	
	</div><!-- end of school_name -->
</div><!-- end of header -->
<div id="sidebar">
<!-- show teacher class -->
<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher")
 : ?>
<?php if (!empty($found_class)) : ?>
	<a class="teacher_class">Class  : <?php echo $found_class->class_name; ?></a>
<?php endif; ?>
<?php endif; ?>
	<a  href="index" title="Home">Home <i class="fa fa-home" aria-hidden="true"></i>
 </a>
	<?php if($session->is_logged_in() == false) : ?>
	<a data-link="writetest.php" title="write your test">Write Test <i class="fa fa-arrow-right" aria-hidden="true"></i>
	</a>
	<a  href="login" title="Login">Login 
	<i class="fa fa-sign-in" aria-hidden="true"></i>
	</a>
	<?php endif; ?>
		<!-- if user is admin -->
	<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin")

 : ?>
 <!-- THOSE THAT DID NOT SUBMIT PROPERLY -->
 <?php if($improper->id > 0) : ?>
 	<a data-link="notsubmitted.php" title="Improper Submissions" id="improper">Improper Submis.. <i class="fa fa-exclamation" aria-hidden="true"></i>
	</a>
<?php endif; ?>
 	<!-- check if master admin -->
 	<?php if($_SESSION['master_admin'] == 1) : ?>
 	<a  href="admin.php" title="Admin Panel">Admin 
	<i class="fa fa-arrow-right" aria-hidden="true"></i>
	</a>
 	<?php endif; ?>

	<a data-link="class.php" title="All Classes">Classes <i class="fa fa-arrow-right" aria-hidden="true"></i>
	</a>
	<a data-link="course.php" title="Add Test">Add Test <i class="fa fa-plus-square" aria-hidden="true"></i>
	</a>
	<a data-link="courseedit.php" title="Edit Test">Edit Test <i class="fa fa-pencil" aria-hidden="true"></i>
	</a>
	<a data-link="viewscore.php" title="View Test Score">View Test Results <i class="fa fa-eye" aria-hidden="true"></i>
	</a>
	<a data-link="register.php" title="Register Users">Register <i class="fa fa-plus" aria-hidden="true"></i>
	</a>
	<?php if(isset($withoutadnumber) && $withoutadnumber->id > 0) : ?>
 	<a id="withoutnumber" data-link="withoutadnumber.php" title="All Students Without Admission Number">Admission Number
 	<span id="withoutadnumbercount"><i class="fa fa-exclamation"></i></span>
	</a>
	<?php endif; ?>
	<!-- for teachers without class assignment -->
	<?php if(isset($countteachers) && $countteachers > 0) : ?>
	<a id="withoutclass" data-link="withoutclass.php" title="Teachers Without Classes">Assign Class

 	<span id="withoutadclasscount" data-count="<?php echo $countteachers; ?>"><i class="fa fa-exclamation"></i></span>
	</a>
	<?php endif; ?>
	<?php endif; ?>
	<!-- end of if user is admin -->
	<!-- if the user is a teacher -->
	<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher") : ?>
<?php if(!empty($found_class) && $improper->id > 0) : ?>
 	<a data-link="notsubmitted.php" title="Improper Submissions" id="improper">Improper Submis.. <i class="fa fa-exclamation" aria-hidden="true"></i>
	</a>
<?php endif; ?>
	<a data-link="course.php" title="Add Test">Add Test <i class="fa fa-pencil" aria-hidden="true"></i>
	<a data-link="courseedit.php" title="Edit Test">Edit Test <i class="fa fa-pencil" aria-hidden="true"></i>
	</a>
	<?php if(!empty($found_class)) : ?>
	<a data-link="register.php" title="Register Student">Register Student<i class="fa fa-plus" aria-hidden="true"></i>
	</a>
	<?php if(isset($withoutadnumber) && $withoutadnumber->id > 0) : ?>
 	<a id="withoutnumber" data-link="withoutadnumber.php" title="My Students Without Admission Number"> Admission Number<span id="withoutadnumbercount"><i class="fa fa-exclamation"></i></span>
	</a>
	<?php endif; ?>
	<a data-link="viewscore.php" title="View Test Scores Of My Students">View Test Results <i class="fa fa-eye" aria-hidden="true"></i>
	</a>
	</a>
	<a data-link="all_students.php" title="All My Students">My Students<i class="fa fa-plus" aria-hidden="true"></i>
	</a>
<?php endif; ?>
	<?php if(!empty($found_class) && $todays_test->id !== "0") : ?>
	<a data-link="todays_test.php" title="Today's Test">Today's Test<i class="fa fa-arrow-right" aria-hidden="true"></i><i class="testcount"><?php echo number_format($todays_test->id); ?></i>
	</a>
	<?php endif; ?>
	<?php endif; ?>

	<!-- end of if teacher -->
	<?php if($session->is_logged_in() == true) : ?>
		<a  href="?logout" title="Logout"><i class="fa fa-power-off" aria-hidden="true" style="color:red;"></i>
 Logout</a>
	<?php endif; ?>
	<span id="version" data-allow="<?php echo ($session->is_logged_in() == true) ? "false" : "true"; ?>">Version :  1.0</span>
</div><!-- end of sidebar -->
<div id="content">

<div class="nav_reload">
<div id="navigation">
	<span id="back"><i class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i>
</span>
<span id="front"><i class="fa fa-arrow-circle-right fa-2x" aria-hidden="true"></i>
</span>
	<?php if($session->is_logged_in() == true ) : ?>
		<span id="fullscreen" data-fs="yes"><i class="fa fa-arrows-alt"></i></span>
	<?php endif; ?>
	<?php if(isset($address)) : ?>
		<span id="app_url"> App Url : <?php echo $address; ?></span>
	<?php endif; ?>
	<!-- display term date -->
	<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin" && $_SESSION['master_admin'] == 1) :?>
	<div id="termdate">
	<div id="showtermdate">
	<?php
	$today = strtotime(date('m/d/Y' , time()));
	$oneweek = strtotime('+7 days' , $profile->term_date); 
		//give the admin and teacher one week before terminating the page
		$diff_allowed =(int)$oneweek -  (int)$profile->term_date;
		$todays_diff = (int)$today - $profile->term_date;
	?>
	<?php if($profile->term_date !== "0" && ((int)$profile->term_date  > (int)$today)) : ?>
		<span class="e_term">Next Session : <?php echo date('j M, Y' , $profile->term_date); ?></span>
	<?php elseif((int)$profile->term_date === (int)$today) : ?>
		<span>Next Session : <i class="fa fa-smile-o" style="color :green;"></i> Today</span>
		<?php elseif(((int)$profile->term_date < (int)$today) && ($diff_allowed >= $todays_diff)) : ?>
		<span>One Week Transfer Period <i class="fa fa-spin fa-spinner"></i></span>
	<?php else : ?>
	<span class="e_term"><i class="fa fa-info-circle" aria-hidden="true"></i>
 Select Next Session's Date</span>
	<?php endif; ?>
	</div><!-- end of show term date -->		
	</div><!-- term date -->
<?php endif; ?>
	<!-- show transfer for teacher -->
	<?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher") : ?>
		<?php
		$today = strtotime(date('m/d/Y' , time()));
		$oneweek = strtotime('+7 days' , $profile->term_date); 
		//give the admin and teacher one week before terminating the page
		$diff_allowed =(int)$oneweek -  (int)$profile->term_date;
		$todays_diff = (int)$today - $profile->term_date;
		$days_left = (int)date('d' , $diff_allowed) - (int)date('d' , $todays_diff);

		if($days_left == 0){
			$dayslefttext = "<span class=\"daysleft\">Last Day</span>";
		}elseif($days_left == 1){
			$dayslefttext = "<span class=\"daysleft\">A Day Left</span>";
		}else{
			$dayslefttext  = "<span class=\"daysleft\">{$days_left} Days Left</span>";
		}
		if($today >= $profile->term_date) {
			//get all students for this teacher
			if(!empty($found_class)){
				$sql = "SELECT COUNT(id) AS id FROM users WHERE (class_id={$found_class->id} AND ignore_transfer = 0 AND user_type='student' AND new = 0) AND acknowledge = 0";
				$found_students = array_shift(user::find_by_sql($sql));

				$sqlr = "SELECT COUNT(id) AS id FROM users WHERE (class_id={$found_class->id} AND ignore_transfer = 0 AND user_type='student') AND new = 1";
				$ackstudents = array_shift(user::find_by_sql($sqlr));
			}
		}
		?>
		<?php if(!empty($found_students) && $found_students->id !== "0" && ($todays_diff <= $diff_allowed))  : ?>
			<span id="trans_student"><i class="fa fa-info-circle"></i> Tranfer Students <?php echo $dayslefttext; ?></span>
		<?php endif; ?>
		<?php if(!empty($ackstudents) && $ackstudents->id !== "0"  && ($todays_diff <= $diff_allowed)) : ?>
			<span id="ack_student"><i class="fa fa-info-circle"></i> Acknowledge Students <span class="tnumber"><?php echo number_format($ackstudents->id); ?></span> <?php echo $dayslefttext; ?></span>
		<?php endif; ?>
	<?php endif; ?><!-- end of if teacher -->
	<!-- end of show tranfers for teacher -->
	<?php if(isset($rejected) && !empty($rejected)) : ?>
	<?php
	$rejectcount = count($rejected);
	$count = ($rejectcount ==  1) ? "<span class=\"r_student\" data-class=\"{$found_class->id}\">Refused Student : <span class=\"daysleft\">{$rejectcount}</span></span>" : "<span class=\"r_student\" data-class=\"{$found_class->id}\">Refused Students : <span class=\"daysleft\">{$rejectcount}</span></span>";
	echo $count;
	?>
	<?php endif; ?>
</div><!-- end of navigation -->
</div><!-- end of nav reload -->

	<div id="body">
	<?php
	if(isset($_SESSION['new_teacher']) && $_SESSION['new_teacher'] == 1 && isset($_SESSION['user_type']) && ($_SESSION['user_type'] == "teacher" || $_SESSION['user_type'] == "admin")) : ?>
<div id="updatePasswordDiv">
<h1>Hi <i class="fa fa-smile-o orange"></i> <?= $_SESSION['title'] . " "  .$_SESSION['full_name'] ?></h1>
<p>In order to continue, please change your password to prevent unauthorized access to your account</p>
<input type="password" name="password" placeholder="new password" id="UpNewPass">
<br />
<button type="button" id="UpPass" data-id="<?= $_SESSION['user_id'] ?>">Change</button>
</div>
<?php return false; ?>
<?php endif; ?>
	<div id="iframe_container">
		<iframe id="iframe" src="" width="100%" height="" frameborder="0" class="hide" allowfullscreen></iframe>
	</div>
	<?php if(trim($profile->description) !== "") : ?>
<div id="description" class="indexdesc"><?php echo $profile->excapeQuote($profile->description); ?></div>
<?php endif; ?>
	</div>
	
	<div id="footer">
<?php if($profile->school_name !== "") : ?>
	<span id="copyright">&copy; <?php echo $profile->school_name . " " . date('Y' , time()); ?> </span>
<?php endif; ?>
	<span id="developer">Developed by <a href="">Free Link Media</a></span>
	</div><!-- end of fotoer div -->
</div><!-- end of content -->
</div>
<!-- popup div -->
<div id="popup" class="hide popup">
<div class="content">
</div>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
