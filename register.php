<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/class.php');
require_once('include/departments.php');
//$session->logout();

if($session->is_logged_in() == false){
	$generalFunc->redirect_to('index.php');
}elseif(!isset($_SESSION['user_type'])){
	$generalFunc->redirect_to('index.php');
}

//get class id and name
$sql ="SELECT id,class_name FROM myclass ORDER BY class_name ASC";
$classes = myclass::find_by_sql($sql);
//get departments
$found_dept  = departments::find_all();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register Page</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>

	<div id="loginDiv">
		<h1>Register</h1>
		<form method="post" action="" id="register" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
				<div class="sel_section">
			<label class="info"><i class="fa fa-info"></i> User Type</label>
			<select required class="select" name="user_type" class="user_type" id="utype">
			<option value="student">Student</option>
			<?php if($_SESSION['user_type'] !=="teacher") : ?>
			<option value="teacher">Teacher</option>
			<?php if($_SESSION['master_admin'] == 1) : ?>
			<option value="admin">Admin</option>
			<?php endif; ?>
			<?php endif; ?>

		</select>
		</div>	
		<div class="sel_section sel_title">
			<label class="info"><i class="fa fa-info"></i> Title</label>			
			<select required class="select" name="title" class="user_type">
			<option value="Mr">Mr</option>
			<option value="Mrs">Mrs</option>
			<option value="Miss">Miss</option>
		</select>
		</div>

		<?php if($_SESSION['user_type'] !== "teacher") : ?>	
		<input type="text" name="username" class="username" placeholder="User Name" />
		<input  type="password" name="password" class="password" placeholder="Password" />
		<?php endif; ?>
		<input required type="text" name="full_name" class="full_name" placeholder="Full Name" />
		<input  type="text" name="admission_number" class="admission_number" placeholder="Admission Number" />
		<!-- department section -->
			<div id="department">
			<label class="info"><i class="fa fa-info"></i> Department</label>
			<select name="department" id="department_select">
				<?php foreach($found_dept as $dept) : ?>
					<option value="<?= $dept->id ?>"><?= $dept->name ?></option>
				<?php endforeach; ?>
			</select>
			</div><!-- end of department div -->
			<!-- class section -->
		<div id="class_section">
		<?php if(!empty($classes) && $_SESSION['user_type'] == "admin"): ?>
			<label class="info"><i class="fa fa-info"></i> Class</label><br />
			<select name="class_name" id="select_class">
			<option value="">---</option>
				<?php foreach($classes as $one) : ?>
					<option value="<?php echo $one->id; ?>"><?php echo $one->class_name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php elseif($_SESSION['user_type'] == "teacher") : ?>
			<?php
			//get class for teacher
			$sql = "SELECT id,class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']}";
			$found = array_shift(myclass::find_by_sql($sql));
			?>
				<?php if($found) : ?>
				<label class="info"><i class="fa fa-info"></i> Class</label><br />
				<select name="class_name" id="select_class">
					<option value="<?php echo $found->id; ?>"><?php echo $found->class_name; ?></option>
			</select>
				<?php else : ?>
				<span class="errorp">Sorry Please Meet An Admin To Assign A Class To You. Thank You.</span>
				<?php endif; ?>
				
		<?php else : ?>
				<span class="errorp">Please Create A Class First</span>
			<?php endif; ?> 	
			</div><!-- end of class section -->
		<button type="submit" name="register" class="register">Register</button>
	
		</form>
	</div>


	<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>

</html>