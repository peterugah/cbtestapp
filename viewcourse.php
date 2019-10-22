<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/course.php');
require_once('include/question.php');
//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');
}elseif($_SESSION['user_type'] == "student"){
$generalFunc->redirect_to('index.php');
}elseif(!isset($_GET['course_id'])){
	$generalFunc->redirect_to('login.php');
}else{
	$theCourse = course::find_by_id($_GET['course_id']);
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php
	if(!empty($theCourse)){
		echo strtoupper($theCourse->course_name);
	}else{
		echo "success";
	}
	?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css?date=<?= date(time()) ?>">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
	<?php require 'script/mathjax.php'; ?>
</head>
<body>
<?php if(empty($theCourse)) : ?>
	<span class="error">Sorry The Test Has Been Deleted.</span>
	<?php
	return false;
	?>
	<?php endif; ?>
<div  id="starttest" class="container">
	<div id="course_content">
		
<?php if(!empty($theCourse)) : ?>
	<div id="header">
	<span><?php echo strtoupper($theCourse->excapeQuote($theCourse->course_name)); ?></span>
	<?php if($_SESSION['user_type'] == "admin") : ?>
	<span class="cct"><?php echo strtoupper($theCourse->teacher); ?></span>
	<?php endif; ?>
	</div><!-- end of header -->
<?php endif; ?>
	
<?php if(!empty($theCourse)  && !isset($_GET['score'])) : ?>
	<div id="center" data-id="<?php echo $theCourse->id; ?>" data-courseName="<?php echo $theCourse->course_name; ?>" class="viewcoursediv">
	<?php
	//get questions 
	$sql = "SELECT * FROM question WHERE course_id = {$theCourse->id}";
	$found  = question::find_by_sql($sql);
	
	?>
	<?php
	//numbering system
	$x = 0;
	?>
	<?php if(empty($found)) : ?>
			<span class="error">No Questions Found For This Test</span>
			<?php
			return false;
			?>	
	<?php endif; ?>
	<?php foreach($found as $one) : ?>
		<?php if(trim($one->comprehension) !=="" ) : ?>
		<div>
		<div class="compDisplay displayComp" data-size="small">
		<div class="CompContent">
		<?php if(trim($one->excapeQuote($one->instruction) !== "")) : ?>
		<div class="ins_div"><?= $one->instruction ?></div>
		<?php endif; ?>
		<?= $one->excapeQuote($one->comprehension) ?>
		</div><!-- end of compContent -->
		<!-- if the question contains special characters -->
		</div><!-- comprehension display div -->
		</div>
		<?php elseif(trim($one->comprehension) == "") :?>
		<?php
  	$x++;
  		$questionval = $one->question;
		$optaval =  $one->option_a;
		$optbval =  $one->option_b;
		$optcval =  $one->option_c;
		$optdval =  $one->option_d;
		$one->question = $question->excapeQuote($questionval);
		$one->option_a = $question->excapeQuote($optaval);
		$one->option_b = $question->excapeQuote($optbval);
		$one->option_c = $question->excapeQuote($optcval);
		$one->option_d = $question->excapeQuote($optdval);
		?>
		<div class="questions">
			<span class="numbering"><?php echo $x  . ". ) "; ?></span>
			<span class="quest"><?php echo $one->question; ?></span>
			<div class="optionsDiv">
			<!-- option a -->
		
			<span class="outline">a.)</span> <label><?php echo $one->option_a; ?></label>
			<span class="outline">b.)</span> <label><?php echo $one->option_b; ?></label>
			<?php if($one->option_c !== "") : ?>
			<span class="outline">c.)</span> <label><?php echo $one->option_c; ?></label>
			<?php endif; ?>
			<?php if($one->option_d !== "") : ?>
			<span class="outline">d.)</span> <label><?php echo $one->option_d; ?></label>
			<?php endif; ?>
		</div><!-- end of optionsDiv -->
		</div><!-- end of questionDiv -->
	<?php endif; ?><!-- trim of comprehension -->
	<?php endforeach; ?>
	</div><!-- end of course content -->
<?php endif; ?>
</div>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
