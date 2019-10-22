<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/question.php');
if($session->is_logged_in() == false){
	die();
}
if(!isset($_GET['course_id']) && !isset($_GET['question_id'])){
	echo "No Test and Question Id Found";
	return false;	
}
//get question
$found = $question->find_by_id($_GET['question_id']);
if(!$found){
	die('<span style="color:red;">Question Not Found</span>');
}
$questionExcaped = $found->excapeQuote($found->question);
$optAExcaped = $found->excapeQuote($found->option_a);
$optBExcaped = $found->excapeQuote($found->option_b);
$optCExcaped = $found->excapeQuote($found->option_c);
$optDExcaped = $found->excapeQuote($found->option_d);
$answer = $found->excapeQuote($found->answer);

//check if the question was formerly a simple question
if(isset($_GET['simple'])){
	$_SESSION['simple_question'] = $_GET['question_id'];
	$_SESSION['simple_question_courseId'] = $_GET['course_id'];
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Complex Page</title>
	<meta charset="UTF-8" />
	<script type="text/javascript" src="script/jquery.js?data=<?= date(time()) ?>"></script>
	<script type="text/javascript" src="script/script.js?data=<?= date(time()) ?>"></script>
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css?date=<?= date(time()) ?>">
	<?php require 'script/mathjax.php'; ?>
	<?php include 'script/tinymce.php'; ?>

	
</head>
<body>
<div class="container" id="complexDiv">
<div id="toolbar" data-current="question">
<div id="opts">
<button type="button" id="question" data-name="Question" class="selected"><i class="fa fa-plus"></i> Question</button>
<button type="button" id="opt_a" data-name="Option A"><i class="fa fa-plus"></i> Option A</button>
<button type="button" id="opt_b" data-name="Option B"><i class="fa fa-plus"></i> Option B</button>
<button type="button" id="opt_c" data-name="Option C"><i class="fa fa-plus"></i> Option C</button>
<button type="button" id="opt_d" data-name="Option D"><i class="fa fa-plus"></i> Option D</button>
<select id="correctAnswer">
<?php if($answer == "a") : ?>
<option value="a">Option A</option>
<option value="b">Option B</option>
<option value="c">Option C</option>
<option value="d">Option D</option>
<?php elseif($answer == "b") : ?>
<option value="b">Option B</option>
<option value="a">Option A</option>
<option value="c">Option C</option>
<option value="d">Option D</option>
<?php elseif($answer == "c") : ?>
<option value="c">Option C</option>
<option value="a">Option A</option>
<option value="b">Option B</option>
<option value="d">Option D</option>
<?php elseif($answer == "d") : ?>
<option value="d">Option D</option>
<option value="a">Option A</option>
<option value="b">Option B</option>
<option value="c">Option C</option>
<?php endif; ?>
</select>
</div>
<button type="button" class="PreviewWysiwyg"><i class="fa fa-eye"></i> Preview</button>
<button type="button" class="PreviewAll"><i class="fa fa-eye"></i> Preview All</button>
<button type="button" id="updateComplex"  data-courseid="<?= $_GET['course_id'] ?>" data-questionid="<?= $_GET['question_id'] ?>"><i class="fa fa-plus"></i> Update</button>
<button type="button" id="gobackComplex"><i class="fa fa-arrow-left"></i> Back</button>
<div id="specialCharacters">
</div><!-- end of special characters -->
</div><!-- end of toolbar -->
<div class="godown">
<div class="symbolManager">
<?php include 'symbols.php'; ?>
</div><!-- symbol manager -->
<form action="comprehension" name="newComprehension" class="wysiwyg" id="newComprehension" data-which="question">
<span class="ComplexLabel">Question</span>
<textarea name="questionTextArea" id="questionTextArea"><?= $questionExcaped ?></textarea>
</form>
</div>
<div id="previewContent">
</div>
	</div>
<div id="popup" class="hide">
<div class="content">
</div>
</div>
</body>
<?php include 'editcomplexjs.php'; ?>
</html>