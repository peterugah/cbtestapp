<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/class.php');
require_once('include/user.php');
require_once('include/admin.php');
require_once('include/departments.php');

//found departments
$foundDepts = departments::find_all();
//define empty variables
$error = "";

//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif($session->is_logged_in() == true && $_SESSION['user_type'] !== "admin"){
	$generalFunc->redirect_to('index?error=not admin');
}elseif($session->is_logged_in() == true && $_SESSION['master_admin'] == 0){
	$generalFunc->redirect_to('index?error=not master admin');
}
//all teachers
$sql = "SELECT COUNT(id) AS id FROM users WHERE user_type = 'teacher' ";
$all_teachers = array_shift(user::find_by_sql($sql));
//all students
$sql = "SELECT COUNT(id) AS id FROM users WHERE user_type = 'student' ";
$all_students = array_shift(user::find_by_sql($sql));
//all admins
$sql = "SELECT COUNT(id) AS id FROM users WHERE user_type = 'admin' AND master_admin = 0 ";
$all_admins = array_shift(user::find_by_sql($sql));
// all users
$sql = "SELECT COUNT(id) AS id FROM users";
$all_users = array_shift(user::find_by_sql($sql));


//get admin detials
$admin_result = array_shift(admin::find_all());
//print_r($admin_result);
?>
<!DOCTYPE html>
<html>
<head>
	<title>welcome <?php echo $_SESSION['full_name']; ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css?date=<?= date(time()) ?>">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js?date=<?= date(time()) ?>"></script>
	<?php include 'script/admin.php'; ?>
</head>
<body>
	
		<div class="admin_container">
		<header id="header" >
					<h1>Admin Pannel </h1>
					<a href="index" id="home">Home <i class="fa fa-home fa-2x"></i></a>
		</header><!-- /header -->
	<?php if(isset($_GET['msg'])) : ?>
		<span id="notification" ><?php echo $_GET['msg']; ?></span>
	<?php endif; ?>
				<form id="admin_form" action="" method="" enctype="multipart/form-data">
				<div class="school_name">
				<span class="sidebar">School name : </span>
				<input type="text" name="school_name"  id="school_name" placeholder="School Name" required value="<?php if(!empty($admin_result)) :?><?php echo ucwords($admin_result->school_name); ?><?php endif; ?>">
				</div>
				<div id="logodiv">
				<span>School Logo : </span>
				<?php
				if(!empty($admin_result)){
				if($admin_result->school_logo !== ""){
					$val = 1;
				}else{
					$val = 0;
				}
				if($admin_result->school_logo == ""){
				$logo = "";
				}else{
				$logo = "admin/" . $admin_result->school_logo;
				}
			}
				?>
					<input type="file" data-uplaod="<?php echo $val; ?>" name="logo" id="school_logo" accept="image/*">
<label for="school_logo" id="logo_label"><i class="fa fa-upload" aria-hidden="true"></i> upload
<div class="display_logo">
<?php if(!empty($admin_result)) : ?>
<?php if($admin_result->school_logo !=="") : ?>
	<img src="admin/<?php echo $admin_result->school_logo; ?>" alt="">
<?php endif; ?>				
<?php endif; ?>
</div>
</label>
				</div>
				<div class="description">
				<span class="sidebar">School description : </span>
					<textarea name="description" id="description"><?php if(!empty($admin_result)) :?><?php echo $admin_result->description; ?><?php endif; ?></textarea>

				</div>
				<!--- departments section -->
				
				<div id="mangeDepts">
				
				<div type="button" id="addDepartment"><i class="fa fa-plus"></i> Add Department</div>
				
				<?php if(!empty($foundDepts)) : ?>
				<?php foreach($foundDepts as $one) : ?>
					<div class="individual_department">
					<span data-value="<?= $one->name ?>"><?= $one->name ?></span>
					<?php if($one->name !== "General") : ?>
					<div class="edit_sec">
					<i class="fa fa-remove" data-name="<?= $one->name ?>"></i>
					<i class="fa fa-pencil" data-name="<?= $one->name ?>"></i>
					</div>
				<?php endif; ?><!-- if not general -->
					</div>
				<?php endforeach; ?>
				<?php endif; ?><!-- end of depts section -->
				</div><!-- end of manage depts -->
			
				<div class="total_users">
				<div id="th">
				<span>	Total users : <?php echo number_format($all_users->id); ?> </span>
				<a id="all_t"><?php echo number_format($all_teachers->id); ?> teachers</a>
				<a id="all_st"><?php echo number_format($all_students->id); ?> students</a>
				<a id="all_ad"><?php echo number_format($all_admins->id); ?>     admin(s)</a>
				</div>
				</div>
			</form>
				<div class="display">
				
				</div>
			
		</div><!-- end of container -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
