<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/class.php');
require_once('include/departments.php');

//define empty variables
$error = "";

//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif($_SESSION['user_type'] == "student"){
	$generalFunc->redirect_to('index.php');
}

//get errors
if(isset($_GET['error'])){
	$error = urldecode($_GET['error']);
}
//get teachers
$sql = "SELECT id,username,full_name,title FROM users WHERE user_type = 'teacher'";
$teachers = user::find_by_sql($sql);
//get classes

//if logged in teacher get teachers value
$found_teacher = user::find_by_id($_SESSION['user_id']);
$classes = myclass::find_all();
$all_depts = departments::find_all();
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome <?php echo $_SESSION['full_name']; ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>
	
		<div class="container">

		<form action="#" methdo="post" id="courseRegistration">
			<?php if($error !=="" && (!empty($teachers) && !empty($classes) ))  : ?>
	<span class="error"><?php echo $error; ?></span>
		<?php endif; ?>

		<?php if(empty($teachers)) : ?>
	<span class="error">kindly Add Teachers To The Database Click <a href="register.php?teacher">HERE <i class="fa fa-plus-circle"></i></a> To Add </span>
	<br />
	<?php
	return false;
	?>
		<?php elseif(empty($classes) && $_SESSION['user_type'] == "admin") : ?>
			<?php
			echo "<span class= \"error\">Please Add A Class To The Database Click <a title=\"add classes\" href=\"class.php\">Here</a></span>";
			return false;
			?>
		<?php elseif(empty($classes) && $_SESSION['user_type'] == "teacher") : ?>
			<span class="error">Please Tell an Administrator To Create A Class First Thank You.</span>
			<?php return false; ?>
		<?php endif; ?>
	
			<input type="text" id="course_name" name="course_name" placeholder="Test Name" required/><span>Teacher & class </span><br />
			<select name="teacher" data-title="Teacher">
			<?php if($_SESSION['user_type'] !== "teacher") : ?>
				<?php foreach($teachers as $one) : ?>
					<option value="<?php echo $one->id; ?>"><?php echo $one->title; ?> <?php echo ucwords($one->full_name); ?></option>
				<?php endforeach; ?>
			<?php else : ?>
				<option value="<?php echo $found_teacher->id; ?>" ><?php echo $found_teacher->title; ?> <?php echo ucwords($found_teacher->full_name); ?></option>
			<?php endif; ?>
			</select>
			<!-- select class here -->
			<select name="class" data-title="Class" id="select_test_class">
			<option value="">Select Class(s)</option>
				<?php foreach($classes as $one) : ?>
					<option value="<?php echo $one->class_name; ?>"><?php echo ucwords($one->class_name); ?></option>
				<?php endforeach; ?>
			</select>
			<div id="add_classes">
			</div><!-- end of display classes -->
			<!-- select departments -->
			<div id="sel_dept">
			<span id="span_dept">Select Department:</span>
			<select name="department" id="department_select_ce">
				<option value="">Select Department(s)</option>
				<?php foreach($all_depts as $depts) : ?>
					<option value="<?= $depts->id ?>"><?= $depts->name ?></option>
				<?php endforeach; ?>
			</select>
			<div id="add_departments">
			</div><!-- end of display classes -->
			</div><!-- end of sel dept div -->
			<!-- time settings -->
			<div id="duration">
			<span>Test Duration </span><br />
			<select name="duration_h" id="d_h">
				<?php for($x=0; $x<=5 ; $x++) : ?>
					<option value="<?php echo $x; ?>"><?php echo $x; ?>h</option>
				<?php endfor; ?>
			</select>
			<select name="duration_mins" id="d_min">
				<?php for($x=0; $x<=59 ; $x+=5) : ?>
					<option value="<?php echo $x; ?>"><?php echo $x; ?>mins</option>
				<?php endfor; ?>
			</select>
			</div><!-- duration div -->
				<div id="course_date">
				<span>Date of paper</span><br />
					<select name="day">
					<option value="">Day</option>
				<?php for($x=1; $x<=31 ; $x++) : ?>
					<?php if($x < 10) : ?>
					<option value="<?php echo 0 . $x; ?>"><?php echo $x; ?></option>
				<?php else : ?>
						<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
				<?php endif; ?>
				<?php endfor; ?>
				</select><!-- end of day -->
					<select name="month">
					<option value="">Month</option>
					<?php
					$month = array('jan','feb','march','april','may','june','july','aug','sept','oct','nov','dec');
					?>
					<?php
					$x = 1;
					?>
				<?php foreach($month as $one) : ?>
					<?php if($x < 10) : ?>
					<option value="<?php echo 0 . $x; ?>"><?php echo  $one; ?>
				<?php else : ?>
					<option value="<?php echo $x; ?>"><?php echo $one; ?>
				<?php endif; ?>
					<?php
					$x++;
					?>
					</option>
				<?php endforeach; ?>
				
				</select><!-- end of month -->
				<select name="year">
					<option value="<?php echo date('Y' , time()); ?>">
					<?php echo date('Y' , time()); ?>
					</option>
				</select>
				</div><!-- course date -->
				<button name="add_course" id="add_course" type="submit">Add</button>
		</form>
	</div><!-- end of container -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
