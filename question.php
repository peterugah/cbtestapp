<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/course.php');
require_once('include/question.php');
//$session->logout();
if($session->is_logged_in() == false){
	$generalFunc->redirect_to('login.php');

}elseif($_SESSION['user_type'] == "student"){
	$generalFunc->redirect_to('index.php');
}elseif(!isset($_GET['course_id']) || $_GET['course_id'] ==""){
	$generalFunc->redirect_to('course.php?error=kindly Create A Test First!!!');
}

//get course name 
$id = $course->prn($_GET['course_id']);
$the_course = course::find_by_id($id);
if(empty($the_course)){
	$generalFunc->redirect_to('course.php?error=No Test Found, Create A New One');
}
//get question numbers
$question_sum = question::get_sum_question($id);

//if edit
if(isset($_GET['edit']) && isset($_GET['course_id']) && $_GET['edit'] !== ""){
	$id = $course->prn($_GET['edit']);
	$edit = question::find_by_id($id);
}

//check if its returning from a complex quesiton page
if(isset($_SESSION['simple_question'])){
	$generalFunc->redirect_to('courseedit?course_id='.$_SESSION['simple_question_courseId']. '&goto=' .$_SESSION['simple_question']);
}
if(isset($_GET['simple'])){
	$_SESSION['simple_question'] = $_GET['edit'];
	$_SESSION['simple_question_courseId'] = $_GET['course_id'];
}

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
	<?php require 'script/mathjax.php'; ?>
</head>
<body>
	<div class="container oneatatime active" id="generalQuestions">
		<div class="contain">

		<h1 class="course_name"><?php echo $the_course->excapeQuote($the_course->course_name); ?></h1>
	
<?php if($question_sum !== 0) : ?>
	<?php if(!isset($_GET['edit'])) : ?>
	<div id="not">
		
				<span id="num_of_questions">
				<?php echo ($question_sum == 1) ? $question_sum . " Question" : $question_sum . " Questions";  ?>
				</span>
			
				<a id="edit_course" href="courseedit.php?course_id=<?php echo urlencode($_GET['course_id']); ?>">Edit  <i class="fa fa-arrow-left"></i></a>
				<button class="notInner" id="loadComprehension">Comprehension</button>
				<button class="notInner" id="loadComplex" data-link="complex.php?course_id=<?= $the_course->id?>">Complex Question</button>
	</div>
			<?php endif; ?><!-- dont display for question edit -->
			<?php endif; ?>
	<!-- include symbols -->
	<?php require('symbols.php'); ?>
			<!-- new question -->
	<?php if(!isset($_GET['edit'])) : ?>

			<form action="#" methdo="post" id="questionRegistration" autocomplete="off">
			<div id="questionDiv">
			<div id="q_div">
			<label  for="question">Question?</label>
			<textarea name="question" id="question" placeholder="Question" required></textarea>

			</div><!-- end of q div -->

			<div id="opt_div">
			<!-- options section -->

			<label>Options</label>
			<input type="text" name="option_a" id="option_a" placeholder="option a *" required value="" />
			<input type="text" name="option_b" id="option_b" placeholder="option b *" required value="" />
			<input type="text" name="option_c" id="option_c" placeholder="option c" value="" />
			<input type="text" name="option_d" id="option_d" placeholder="option d" value="" />
			</div><!-- end of opt div -->
			<div id="ans_div">
				<label for="answer">Select Answer</label>
				<select name="answer" id="answer" value="">
					<option value="">----</option>
					<option value="a">option a</option>
					<option value="b">option b</option>
					<option value="c">option c</option>
					<option value="d">option d</option>
				</select>
			</div><!-- end of answer div -->
			</div><!-- end of question div -->
			<input type="text" value="<?php echo urlencode($_GET['course_id']); ?>" id="course_id" name="course_id"/>
			<input type="text" id="special_character" data-value="0" name="special_character"/>
			<button id="submit_questions" name="submit_questions" type="submit">Add Question</button>
		</form>
		<!-- update question -->
		<?php elseif(isset($_GET['edit']) && isset($_GET['course_id']) && $_GET['edit'] !== "" && !empty($edit)) : ?>
			<a class="" id="furtherEdit" href="editcomplex?course_id=<?= $the_course->id?>&question_id=<?= $edit->id ?>&simple"><i class="fa fa-pencil"></i> Edit Further</a>

			<form action="#" methdo="post" id="questionRegistrationUpdate" autocomplete="off">
			<div id="questionDiv">
			<div id="q_div">
			<label  for="question">Question?</label>
			<textarea name="question" id="question" placeholder="Question" required><?php echo $edit->excapeQuote($edit->question); ?></textarea>

			</div><!-- end of q div -->

			<div id="opt_div">
			<!-- options section -->

			<label>Options</label>
			<input type="text" name="option_a" id="option_a" placeholder="option a *" required value="<?php echo $edit->excapeQuote($edit->option_a); ?>" />
			<input type="text" name="option_b" id="option_b" placeholder="option b *" required value="<?php echo $edit->excapeQuote($edit->option_b); ?>" />
			<input type="text" name="option_c" id="option_c" placeholder="option c" value="<?php echo $edit->excapeQuote($edit->option_c); ?>" />
			<input type="text" name="option_d" id="option_d" placeholder="option d" value="<?php echo $edit->excapeQuote($edit->option_d); ?>" />
			</div><!-- end of opt div -->
			<div id="ans_div">
				<label for="answer">Select Answer</label>
				<select name="answer" id="answer" value="">
				<?php if($edit->answer == "a") : ?>
					<option value="a">option a</option>
					<option value="b">option b</option>
					<option value="c">option c</option>
					<option value="d">option d</option>
				<?php elseif($edit->answer == "b") : ?>
					<option value="b">option b</option>
					<option value="a">option a</option>
					<option value="c">option c</option>
					<option value="d">option d</option>
				<?php elseif($edit->answer == "c") : ?>
					<option value="c">option c</option>
					<option value="a">option a</option>
					<option value="b">option b</option>
					<option value="d">option d</option>
				<?php elseif($edit->answer == "d") : ?>
					<option value="d">option d</option>
					<option value="a">option a</option>
					<option value="b">option b</option>
					<option value="c">option c</option>
				<?php endif; ?>
				</select>
			</div><!-- end of answer div -->
			</div><!-- end of question div -->
			<input type="text" value="<?php echo urlencode($_GET['course_id']); ?>" id="course_id" name="course_id"/>
			<input type="text" id="special_character" data-value="0" name="special_character" value="<?php echo $edit->special_character; ?>" />
			<input type="text" id="question_id" name="question_id" value="<?php echo $edit->id; ?>"/>
			<button id="submit_questions" name="submit_questions" type="submit">Update Question</button>
			<!-- an error getting qeustion -->
		<?php else : ?>
			<span class="error">Error : Question Not Found</span>
		<?php endif; ?>
		</div><!-- end of contain -->
		<div id="viewsymbols">

		</div>
	</div><!-- end of container -->
<div id="comprehensionContainer" class="oneatatime">
<?php include('comprehension.php'); ?>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
