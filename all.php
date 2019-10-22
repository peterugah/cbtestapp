<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/class.php');
require_once('include/user.php');
require_once('include/course.php');
require_once('include/score.php');


$who ="";
if(isset($_GET['view']) && $_GET['view'] == "teacher"){
//all teacahers
$sql  = "SELECT title,full_name,id FROM users WHERE user_type = 'teacher' ORDER BY full_name";
$teachers = user::find_by_sql($sql);
//set display type
$who ="teacher";
}

if(isset($_GET['view']) && $_GET['view'] == "student"){
//all teacahers
$sql  = "SELECT full_name,id,admission_number FROM users WHERE user_type = 'student' ORDER BY full_name";
$student = user::find_by_sql($sql);
//set display type
$who ="student";
}
//if the user is admin
if(isset($_GET['view']) && $_GET['view'] == "admin"){
//all admin
$sql  = "SELECT title,full_name,id FROM users WHERE user_type = 'admin' AND master_admin = 0 ORDER BY full_name";
$admin = user::find_by_sql($sql);
//set display type
$who ="admin";
}

//show single user display
if(isset($_GET['single']) && $_GET['single'] !==""){
	$id = $user->prn($_GET['single']);
	$found_single =$user->find_by_id($id);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>	
	
<div id="tdiv">
<!-- display single user -->
<?php if(isset($_GET['single']) && $_GET['single'] !=="") : ?>
	
	<?php if(!empty($found_single)) : ?>
		<?php if($found_single->user_type == "teacher") : ?>
		<div class="show">
	<div id="detailsContainer">
	<?php
	//get user classes
	$sql="SELECT id,class_name FROM myclass WHERE teacher_id = {$found_single->id} ";
	$found_class = myclass::find_by_sql($sql);

	//get user course number
	$sql = "SELECT COUNT(id) AS id FROM course WHERE teacher_id= {$found_single->id}";
	$count = array_shift(course::find_by_sql($sql));
	?>
		
		<div class="details">
			<a class="a_me" data-id="<?php echo $found_single->id; ?>" data-type="teacher"><?php echo $found_single->title . " " . $found_single->full_name; ?></a>
			<div class="icons">
				<span class="show_class">
					<!-- show classes given to the teacher -->
					<?php foreach($found_class as $classone) : ?>
					<a href="class.php?class_id=<?php echo $classone->id; ?>"><?php echo $classone->class_name; ?></a>
					<?php endforeach; ?>
					
							<?php if(empty($found_class)) : ?>
							 <a href="class.php?assign=<?php echo $found_single->id; ?>" style="color:red;">assign class</a>";
							<?php endif; ?>
				</span>
				<span class="show_course">
					<!-- count of courses -->
				<?php if($count->id == 1) :  ?>
					<a href="courseedit.php?edit_as_teacher=<?php echo $found_single->id; ?>"><?php echo $count->id . " course"; ?></a>
				<?php else : ?>
						<a href="<?php 
						if($count->id > 0){
						echo "courseedit.php?edit_as_teacher=" . $found_single->id;
					}else{
						echo "#";
					}
						 ?>"><?php echo $count->id . " courses"; ?></a>
				<?php endif; ?>
				</span>
				<span class="remove" data-id="<?php echo $found_single->id; ?>"><i class="fa fa-remove"></i></span>
			</div>	
		</div>
		</div><!-- end of detials container -->
		</div><!-- end of my teacher -->
		<!-- check  if the user is a student -->
	<?php elseif($found_single->user_type == "student") : ?>
	<div class="show">
	<div id="detailsContainer">
	<?php
	//get all scores for the student
	$sql ="SELECT COUNT(id) AS id FROM score WHERE student_name = '{$found_single->full_name}'";
	$count = array_shift(myscore::find_by_sql($sql));
	//get students class is
	$sql ="SELECT class_id AS id FROM users WHERE id = {$found_single->id}";
	$class_id = array_shift(user::find_by_sql($sql));
	//get class
	$sql = "SELECT class_name FROM myclass WHERE id = {$class_id->id}";
	$found_class = array_shift(myclass::find_by_sql($sql));

	?>
		
		<div class="details">
		<?php if(!empty($found_class)) : ?>
			<span class="ca_name"><?php echo $found_class->class_name; ?></span>
		<?php endif; ?>
			<a class="a_me" data-id="<?php echo $found_single->id; ?>" data-type="student"><?php echo $found_single->full_name; ?></a>
			<div class="icons">
				<span class="show_class">
			<input type="text" value="<?php echo $found_single->admission_number; ?>" placeholder="admission number" id="Addnumber_<?php echo strtoupper($found_single->id); ?>" disabled/>
				</span>
				<span class="show_course">
					<?php if($count->id > 0) : ?>
					<a href="viewscore.php?view=<?php echo $found_single->full_name; ?>" style="color:green;">scores(<?php echo number_format($count->id); ?>)</a>
					<?php else : ?>
					no score 
					<?php endif; ?>
		
				</span>
				<span class="remove" data-id="<?php echo $found_single->id; ?>"><i class="fa fa-remove"></i></span>
			</div>	
		</div>
		</div><!-- end of details container -->
		</div><!-- end of student -->
	<?php endif; ?><!-- end of if teacher and student -->

	<?php else : ?>
		<span class="error">user not found</span>
	<?php endif; ?>
	<?php endif; ?>

<?php if(!isset($_GET['single']) && $who == "teacher") : ?>
	<?php if(empty($teachers)) : ?>
		<span class="error">No Record Found </span>
	<?php return false; ?>
	<?php endif; ?>
	<div class="show">
	<div id="searchdivall">
	<input type="text" name="searchval" id="searchval" value="" placeholder="Enter Teacher Name" data-who="teacher" data-id="<?php echo $one->id; ?>"></input>
	<label for="searchval"><i class="fa fa-search" aria-hidden="true"></i></label>
	</div>
	<div id="detailsContainer">
	<?php foreach($teachers as $one) : ?>
	<?php
	//get user classes
	$sql="SELECT id,class_name FROM myclass WHERE teacher_id = {$one->id} ";
	$found_class = myclass::find_by_sql($sql);

	//get user course number
	$sql = "SELECT COUNT(id) AS id FROM course WHERE teacher_id= {$one->id}";
	$count = array_shift(course::find_by_sql($sql));
	?>
		
		<div class="details">
			<a class="a_me" data-id="<?php echo $one->id; ?>" data-type="teacher"><?php echo $one->title . " " . $one->full_name; ?></a>
			<div class="icons">
				<span class="show_class">
					<!-- show classes given to the teacher -->
					<?php foreach($found_class as $classone) : ?>
					<a href="class.php?class_id=<?php echo $classone->id; ?>"><?php echo $classone->class_name; ?></a>
					<?php endforeach; ?>
					<?php
						if(empty($found_class)){
							echo "<a href=\"class.php?assign={$one->id}\" style=\"color:red;\">assign class</a>";
						}
					?>
				</span>
				<span class="show_course">
					<!-- count of courses -->
				<?php if($count->id == 1) :  ?>
					<a href="courseedit.php?edit_as_teacher=<?php echo $one->id; ?>"><?php echo $count->id . " test"; ?></a>
				<?php else : ?>
						<a href="<?php 
						if($count->id > 0){
						echo "courseedit.php?edit_as_teacher=" . $one->id;
					}else{
						echo "#";
					}
						 ?>"><?php echo $count->id . " tests"; ?></a>
				<?php endif; ?>
				</span>
				<span class="remove" data-id="<?php echo $one->id; ?>"><i class="fa fa-remove"></i></span>
			</div>	
		</div>
	
		
	<?php endforeach; ?>
		</div><!-- end of detials container -->
		</div><!-- end of my teacher -->
<?php endif; ?>




<!-- if its a student -->
<?php if(!isset($_GET['single']) && $who == "student") : ?>
	<?php if(empty($student)) : ?>
		<span class="error">No Record Found </span>
	<?php return false; ?>
	<?php endif; ?>
	<div class="show">
	<div id="searchdivall">
	<input type="text" name="searchval" id="searchval" value="" placeholder="Enter Student Name Or Admission Number" data-who="student" data-id="<?php echo $one->id; ?>"></input>
	<label for="searchval"><i class="fa fa-search" aria-hidden="true"></i></label>
	</div>
	<div id="detailsContainer">
	<?php foreach($student as $one) : ?>
	<?php
	//get all scores for the student
	$sql ="SELECT COUNT(id) AS id FROM score WHERE student_name = '{$one->full_name}'";
	$count = array_shift(myscore::find_by_sql($sql));

	//get students class is
	$sql ="SELECT class_id AS id FROM users WHERE id = {$one->id}";
	$class_id = array_shift(user::find_by_sql($sql));
	//get class
	$sql = "SELECT class_name FROM myclass WHERE id = {$class_id->id}";
	$found_class = array_shift(myclass::find_by_sql($sql));
	?>
		
		<div class="details">
		<?php if(!empty($found_class)) : ?>
			<span class="ca_name"><?php echo $found_class->class_name; ?></span>
		<?php endif; ?>
			<a class="a_me" data-id="<?php echo $one->id; ?>" data-type="student"><?php echo $one->full_name; ?></a>
			<div class="icons">
				<span class="show_class">
			<input type="text" value="<?php echo $one->admission_number; ?>" placeholder="admission number" id="Addnumber_<?php echo strtoupper($one->id); ?>" disabled/>
				</span>
				<span class="show_course">
					<?php if($count->id > 0) : ?>
					<a href="viewscore.php?view=<?php echo $one->full_name; ?>" style="color:green;">scores(<?php echo number_format($count->id); ?>)</a>
					<?php else : ?>
					no score 
					<?php endif; ?>
		
				</span>
				<span class="remove" data-id="<?php echo $one->id; ?>"><i class="fa fa-remove"></i></span>
			</div>	
		</div>
	<?php endforeach; ?>
		</div><!-- end of details container -->
		</div><!-- end of student -->
<?php endif; ?>


<!-- if its a admin -->
<?php if(!isset($_GET['single']) && $who == "admin") : ?>
	<div class="show">
	<?php foreach($admin as $one) : ?>
	<?php
	//get all scores for the student
	$sql ="SELECT COUNT(id) AS id FROM score WHERE student_name = '{$one->full_name}'";
	$count = array_shift(myscore::find_by_sql($sql));
	
	?>
		<div class="details">
			<a class="a_me" data-id="<?php echo $one->id; ?>" data-type="teacher"><?php echo $one->title . " " . $one->full_name; ?></a>
			<div class="icons">
				<span class="show_class">
				<button type="button" class="transferAdmin" data-id="<?php echo $one->id; ?>">transfer main admin</button>
				</span>
				<span class="remove" data-id="<?php echo $one->id; ?>"><i class="fa fa-remove"></i></span>
			</div>	
		</div>
		
	<?php endforeach; ?>
		</div><!-- end of student -->
<?php endif; ?>
</div><!-- end of container -->
</body>
</html>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>

