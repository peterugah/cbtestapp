<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/class.php');
require_once('include/user.php');

//define empty variables
$error = "";

//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif($_SESSION['user_type'] !== "admin"){
	$generalFunc->redirect_to('index.php');
}
$sql="SELECT * FROM myclass ORDER BY class_name ASC";
$all_classes = myclass::find_by_sql($sql);
//if its an assign
if(isset($_GET['assign']) && $_GET['assign'] !== ""){
	$id = $user->prn($_GET['assign']);
$sql = "SELECT  id , full_name , title FROM users WHERE user_type = 'teacher' ORDER BY id = {$id} DESC";
}else{
$sql = "SELECT  id , full_name , title FROM users WHERE user_type = 'teacher' ";
}
$all_teachers = user::find_by_sql($sql);
//print_r($all_teachers);
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome <?php echo $_SESSION['full_name']; ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js?date=<?= date(time()) ?>"></script>
</head>
<body>
	
		<div class="container class_container">
		<?php if(empty($all_teachers)) : ?>
			<span class="error" style="color:green;">kindly Add Teachers To The Database Click <a href="register.php?teacher">HERE <i class="fa fa-plus-circle"></i></a> To Add </span>
			<?php 
			return false;
			?>
		<?php endif; ?>
		<div id="class_sidebar" >
		<div class="reload">
		<?php foreach($all_classes as $one) : ?>
			<?php
			$sql = "SELECT full_name,title FROM users WHERE id = " . $one->teacher_id;
			$teachers_name = array_shift(user::find_by_sql($sql));
			?>
			<div class="class_details" data-id="<?php echo $one->id; ?>">
				
			
			<H1 ><?php echo $one->class_name; ?></H1>
<?php
if(!empty($teachers_name)){
	$name = "<span>" . $teachers_name->title . " " . $teachers_name->full_name . "</span>";
}else{
	$name = "<span style=\"color : red;\">No teacher assigned</span>";
}
?>
			<?php echo $name;  ?>
			</div>
		<?php endforeach; ?>
		</div>
		</div>
		<div id="center">
		<?php if(isset($_GET['class_id']) && ($_GET['class_id'] !== "")) : ?>
			<?php
			//get detials by id
			$id = $user->prn($_GET['class_id']);
			$found_class = myclass::find_by_id($id);
			if(!empty($found_class)) {
			$teachers_id = $found_class->teacher_id;
			$sql = "SELECT id,title,full_name FROM users WHERE user_type = 'teacher' ORDER BY id = {$teachers_id} DESC";
			$teahers_result = user::find_by_sql($sql);
		}else{
			echo "<spanc class=\"error\">class id not found</span><br />";
			echo "<a href=\"class.php\" class=\"a_button\" ><i class=\"fa fa-plus-circle\"></i> Create A New Class</a>";
			return false;
		}
			?>
			<a href="class.php" class="a_button" ><i class="fa fa-plus-circle"></i> New Class</a>
			<form action="" method=""  id="update_class" data-classId = "<?php echo $id; ?>" data-oldval="<?php echo $found_class->class_name; ?>">
				<input type="text" name="new_class" id="new_class" placeholder="enter class name" required value="<?php echo $found_class->class_name; ?>" />	
				<select name="class_teacher" >

				<?php foreach($teahers_result as $one) : ?>

						<option value="<?php echo $one->id; ?>"><?php echo $one->title . " " . $one->full_name; ?></option>
					
				<?php endforeach; ?>
				</select>
				<div class="buttons">
				<button type="submit" name="update_class" id="update_class" data-classId = "<?php echo $id; ?>" data-oldval="<?php echo $found_class->class_name; ?>">Update</button>
				<button type="submit" name="delete_class" id="delete_class" data-id="<?php echo $found_class->teacher_id; ?>"><i class="fa fa-remove"></i></button>
				</div>
			</form>
		<?php else : ?>
			<?php if(!empty($all_classes)) : ?>
			<span class="notice">Click On A Class By The Side To Edit !!!</span>
		<?php else : ?>
			<span class="notice">Add First Class <i class="fa fa-smile-o "></i>
</span>
		<?php endif; ?>
			<!-- add new class -->
			<form action="" method=""  id="addClass">
				<input type="text" name="new_class" id="new_class" placeholder="enter class name" required />	
				<select name="class_teacher" >
			<?php if(!isset($_GET['assign']))  : ?>
				<option value=""> --- select teacher --- </option>
			<?php endif; ?>
				<?php foreach($all_teachers as $one) : ?>

						<option value="<?php echo $one->id; ?>"><?php echo $one->title . " " . $one->full_name; ?></option>
					
				<?php endforeach; ?>
				</select>
				<button type="submit" name="add_class" id="add_class">Add</button>
			</form>
		<?php endif; ?>

		</div>
		</div><!-- end of container -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
