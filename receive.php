<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/class.php');
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('index.php');
}
if(isset($_SESSION['user_type']) && $_SESSION['user_type'] !=="teacher"){
	$generalFunc->redirect_to('index.php');
}
//get  the classes
$sql = "SELECT class_name,id FROM myclass WHERE teacher_id = {$_SESSION['user_id']}";
$found_class = array_shift(myclass::find_by_sql($sql));
if(!empty($found_class)){
	//get the students
	$sql = "SELECT * FROM users WHERE class_id={$found_class->id} AND (ignore_transfer = 0 AND user_type='student') AND new = 1";
	$found_students = user::find_by_sql($sql);
	//get all calsses
$sqlclass = "SELECT * FROM myclass WHERE class_name != '{$found_class->class_name}'";
$classes  = myclass::find_by_sql($sqlclass);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome <?php if(isset($_SESSION['full_name'])){
		echo $_SESSION['full_name']; 
		} ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>
	<?php 
	if(empty($found_students) || empty($classes)){
		echo "<span class=\"error\">No Student Or Class Found</span>";
		return false;
	}
	?>
	<div class="trans">
	<div id="head">
		<button type="button" id="tickall"><i class="fa fa-check"></i> Tick All</button>
		<button type="button" id="untick"><i class="fa fa-circle-o"></i> Untick All</button>
		<button type="button" id="acknowledge"><i class="fa fa-arrow-right"></i> Acknowledge Selected</button>
	</div><!-- end of head -->
	<div id="body">
	<?php foreach($found_students as $one) : ?>
		<?php
		//get the transfering classs
		$sqlc ="SELECT class_name FROM myclass WHERE id ={$one->former_class}";
		$foundOne = array_shift(myclass::find_by_sql($sqlc));
		if(empty($foundOne)){
			$classinfo = "";
		}else{
			$classinfo = $foundOne->class_name;
		}
		?>
		<div class="student_container">
		<div class="checks">
			<input type="checkbox" class="inputcheck" name="check[<?php echo $one->id; ?>]" id="check_<?php echo $one->id; ?>" />
			<label for="check_<?php echo $one->id; ?>" class="label_check"></label>
		</div>
		<div class="details">
			<span class="fn"><?php echo $one->full_name; ?></span>
			<?php
			if($one->department == 0){
				$department = "<span class=\"dp\">General <span class=\"class_one\">{$classinfo}</span></span>";
			}elseif($one->department == 1){
				$department = "<span class=\"dp\" style=\"color:green;\">Science <span class=\"class_one\">{$classinfo}</span></span>";
			}elseif($one->department == 2){
				$department = "<span class=\"dp\" style=\"color:green;\">Art <span class=\"class_one\">{$classinfo}</span></span>";
			}elseif($one->department == 3){
				$department = "<span class=\"dp\" style=\"color:green;\">Commercial <span class=\"class_one\">{$classinfo}</span></span>";
			}elseif($one->department == 4){
				$department = "<span class=\"dp\" style=\"color:green;\">Vocation <span class=\"class_one\">{$classinfo}</span></span>";
			}
			?>
			<?php echo $department ; ?>
			<span class="ad"><?php echo ($one->admission_number !== "")? $one->admission_number : '---'; ?></span>
			<button type="" class="reject" data-id="<?php echo $one->id; ?>" data-name="<?php echo $one->full_name; ?>">Refuse</button>
			<button type="" class="acceptInd" data-id="<?php echo $one->id; ?>">Acknowledge</button>
		</div><!-- end of details -->
		</div><!-- conainer for the student -->
		<?php endforeach; ?>
	</div><!-- end of body id -->
	
	</div><!-- end of container -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
