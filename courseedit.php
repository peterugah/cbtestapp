<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/user.php');
require_once('include/course.php');
require_once('include/class.php');
require_once('include/question.php');
require_once('include/departments.php');
//$session->logout();

if($session->is_logged_in() == false){
$generalFunc->redirect_to('login.php');

}elseif($_SESSION['user_type'] == "student"){
$generalFunc->redirect_to('index.php');
}
//rearrange course display
if(($_SESSION['user_type'] == "teacher" || isset($_GET['edit_as_teacher'])) && (isset($_GET['course_id']) && $_GET['course_id'] !== "")){
	if($_SESSION['user_type'] == "teacher"){
	$sql = "SELECT * FROM course WHERE teacher_id = {$_SESSION['user_id']} ORDER BY id = {$_GET['course_id']} DESC"; 
	}else{
	$sql = "SELECT * FROM course WHERE teacher_id = {$_GET['edit_as_teacher']} ORDER BY id = {$_GET['course_id']} DESC";
	}

}elseif($_SESSION['user_type'] == "admin" && (isset($_GET['course_id']) && $_GET['course_id'] !== "")){
$sql = "SELECT * FROM course ORDER BY id = {$_GET['course_id']} DESC";
}
//get all course by the currently logged on teacher
if(($_SESSION['user_type'] == "teacher" || isset($_GET['edit_as_teacher']))  && !isset($_GET['course_id'])){
	if($_SESSION['user_type'] == "teacher"){
	$sql = "SELECT * FROM course WHERE teacher_id = {$_SESSION['user_id']} ORDER BY id DESC";
	}else{
	$sql = "SELECT * FROM course WHERE teacher_id = {$_GET['edit_as_teacher']} ORDER BY id DESC";
	}
}elseif($_SESSION['user_type'] == "admin"  && !isset($_GET['course_id'])){
$sql = "SELECT * FROM course ORDER BY id DESC";
}
$teachers_course = course::find_by_sql($sql);
if(empty($teachers_course)){
	$generalFunc->redirect_to('course.php?error=Kindly Create A Test First');
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
</head>
<body>
<div class=" courseediit">
<div id="header"><!-- header section -->
<?php if(isset($_GET['course_id'])) : ?>
	<?php
	$id = $course->prn($_GET['course_id']);
	$sql = "SELECT course_instruction ,course_date, id,update_date,update_user,admin FROM course WHERE id = {$id}";
	$instruction = array_shift(course::find_by_sql($sql));
	$todaysdate = strtotime(date('m/d/Y'));
	$dateofcourse  = strtotime($instruction->course_date);
	?>
	
	<?php if($dateofcourse < $todaysdate) : ?>
	<!-- check if the date of the test is in the past -->
	<div id="last_edit" class="alreadyTaken">
	<span>This Test Has Already Been Taken. Kindly Update The Test Date If You Wish To Conduct This Test Again.</span>
	<span class="hide"><i class="fa fa-remove"></i></span>
	</div><br /><!-- last edited user for already taken -->
<?php endif; ?>
	<?php if($dateofcourse !== $todaysdate) : ?>
	<?php if($instruction->update_user !== "") : ?>
	<div id="last_edit">
	<?php
	$day  =  date('d M Y' , $instruction->update_date);
	$time =  date('g:ia' ,  $instruction->update_date);
	?>
	<span>Last Update by <?php echo strtoupper($instruction->update_user); ?>, <?php echo $day; ?> at <?php echo $time; ?></span>
	<span class="hide"><i class="fa fa-remove"></i></span>
	</div><br /><!-- last edited user -->
<?php endif; ?>
	<label id="course_ins" for="course_instruction">Test Instruction: </label>
<textarea data-replace = "<?php
		if(trim($instruction->course_instruction == "")){
			echo "0";
		}else{
			echo "1";
		}
;?>" data-id="<?php echo $instruction->id; ?>" id="course_instruction" name="course_instruction"><?php if($instruction->course_instruction == ""){echo "specify a test instruction here !!!";}else{
	echo  $instruction->excapeQuote($instruction->course_instruction);
	}; ?></textarea>
<?php endif; ?><!-- end of check if course date has passed -->
<?php endif; ?>
</div><!-- header section -->

<div id="sidebar"><!-- sidebar section -->
<?php foreach ($teachers_course as $one) : ?>
<?php
$todaysdate = strtotime(date('m/d/Y'));
$dateofcourse  = strtotime($one->course_date);
?>

<!-- get number of questions for each course -->
<?php 

$num_of_questions = question::get_sum_question($one->id);
//get course time stamp
$time = explode(":" , $one->duration);
$date = explode("/" , $one->course_date);
//echo $mktime;
?>
<div class="teachers_course" data-id="<?php echo $one->id; ?>">
<div class="course_name_sidebar">
<a data-courseid="<?php echo $one->id; ?>"  data-edit="true" href="
<?php if(isset($_GET['edit_as_teacher'])) :?>
?edit_as_teacher=<?php echo $_GET['edit_as_teacher'] . "&"; ?>course_id=<?php echo $one->id; ?>
<?php else : ?>

?course_id=<?php echo $one->id; ?>
<?php endif; ?>


" class="cedit"><?php echo ucwords( $one->excapeQuote($one->course_name)); ?></a>
<span class="del_course" data-id="<?php echo $one->id; ?>"><i class="fa fa-remove"></i></span>
<?php if($_SESSION['user_type'] == "admin") : ?>
<span class="tname">Teacher : <?php echo $one->teacher; ?></span>
<?php endif;?>
</div>
<?php
	///properly display date
	$unix = strtotime($one->course_date);
	$date = date('M j, Y' , $unix);


	//use this to know if the test day has passed
	$today = strtotime(date('m/d/Y'));
	$examday = strtotime($one->course_date);
	if($today > $examday){
		$edit = "data-edit=\"false\"";
	}else{
		$edit = "data-edit=\"true\"";
	}
?>
<?php if($todaysdate  == $dateofcourse) : ?>
<a class="viewcourselink" href="viewcourse.php?course_id=<?php echo $one->id; ?>" >View Test</a>
<?php else : ?>
	<?php
	//format duration
	$time = explode(':', $one->duration);
	$format = $time[0] . "h:" . $time[1] . "m";
	?>
<span id="duration_ce" <?php echo $edit; ?> data-value="<?php echo $one->duration; ?>"><?php echo $format; ?></span>
<span id="course_date_ce" <?php echo $edit; ?>><?php echo $date ?></span>
<span id="num_of_questions_ce" data-edit="false"><?php echo($num_of_questions == 1) ? $num_of_questions . " Question"  :$num_of_questions . " Questions"; ?></span>
<span id="class_ce" <?php echo $edit; ?>><?php 
if(strlen($one->class) < 11){
	echo $one->class;
}else{
echo substr($one->class , 0 , 11) . "..."; 
}
if($one->class == ""){
	echo "<em class=\"red\">Select Class</em>";
}
?></span>
<?php endif; ?>
<div id="activate">
<input name="" type="checkbox" value="" data-id="<?php echo $one->id; ?>" id="activate_btn_<?php echo $one->id; ?>" <?php if($one->activate == 1){echo "checked=\"checked\"";} ?>><label for="activate_btn_<?php echo $one->id; ?>"> </label>
<?php if($one->activate == 1) : ?>
	<span style="color:green;">activated</span>
<?php else : ?>
	<span>activate</span>
<?php endif; ?>
</div>
</div><!-- end of teachers course -->
<?php endforeach;?>
</div><!-- sidebar section -->

<div id="center"><!-- center section -->
<?php require 'script/mathjax.php'; ?>
<div class="reload">
<?php if(!isset($_GET['course_id'])) : ?>
	<p class="error">Click A Test On The Sidebar To Edit!!! </p>
<?php elseif(!isset($_GET['edit']) && isset($_GET['course_id'])) : ?>
	<?php 
	//check if course has been taken 
	$sql = "SELECT course_date FROM course WHERE id = {$_GET['course_id']}";
	$result = array_shift(course::find_by_sql($sql));
	$today = strtotime(date('m/d/Y'));
	$examday = strtotime($result->course_date);
	if($today == $examday){
		echo "<span class=\"error\">Sorry You Can't Edit This Test Because The Test Is Scheduled For Today.</span>";
		return false;
	}
	?>

<?php
//get the needed questions
$id = $generalFunc->prn($_GET['course_id']);
$sql = "SELECT * FROM question WHERE course_id = {$id}";
$result = question::find_by_sql($sql);

//check that this course belongs to this teacher

?>
<?php
		//get course name
		$course_name = course::find_by_id($id);
?>
<!-- check of the date has passed or if course status == 1 -->
<?php if(!empty($course_name) && $course_name->status == 1) : ?>
	<span class="error">Sorry You Can't Edit This Test Because The Test Date Is In The Past.</span>

<?php elseif(empty($result)) : ?>
	<div class="no_questions">
	<a class="add_question" href="question.php?course_id=<?php echo $course_name->id; ?>">Add Questions</a>

<span class="error">No Record Of Questions For This Test </span>
	</div>
<?php else  : ?>
	<div id="top">
	<div id="cnamediv">
	Test Title: <input id="course_name_ce" type="text" value="<?php echo $course_name->excapeQuote(ucwords($course_name->course_name)); ?>" disabled  data-id="<?php echo $course_name->id; ?>" placeholder="Enter A Test Name">
	<span data-penEdit><i class="fa fa-pencil"></i></span>
	<a href="viewcourse.php?course_id=<?php echo $course_name->id; ?>"  title="View This Test" class="vview"><i class="fa fa-eye" aria-hidden="true"></i></a>

	</div><!-- end of cname div -->
	<a class="add_question" href="question.php?course_id=<?php echo $course_name->id; ?>">Add Questions</a>
	</div><!-- end of top -->
	<!-- display question on that course || dont display comprehensions -->
	<?php foreach($result as $one) : ?>
	<!-- if its comprehension -->
	<?php if(trim($one->comprehension) !=="" && $one->complex == 0  ) : ?>
		<div class="compDisplay" data-size="small" data-id="<?= $one->id ?>">
		<div class="CompContent">
		<?php if(trim($one->instruction !== "")) : ?>
		<div class="ins_div"><?= $one->excapeQuote($one->instruction) ?></div>
		<?php endif; ?>
		<?= $one->excapeQuote($one->comprehension) ?>
		</div><!-- end of compContent -->
		<!-- if the question contains special characters -->
		<div class="edit_section">
			<span class="delete" data-id="<?= $one->id ?>" data-courseid="<?=  $course_name->id ?>"><i class="fa fa-remove"></i></span>
			<a class="edit_sc" href="comp.php?compid=<?php echo $one->id; ?>&simple"><i class="fa fa-pencil"></i></a>
		</div><!-- end of edit section -->
		</div><!-- comprehension display div -->
	<?php endif; ?><!-- dont display comprehension questions -->
	<!-- complex questions -->
<?php if(trim($one->comprehension) == "" && $one->complex !== "0" ) : ?>
<div class="closer" data-id="<?= $one->id ?>">
<span class="numbering"><i class="fa fa-arrow-right" aria-hidden="true"></i>
</span>
	<div class="posible_options" data-courseid="<?php echo$_GET['course_id']; ?>" data-id="<?= $one->id; ?>">
			<?php if($one->answer == "a") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="a">option a</option>
				<option value="b">option b</option>
				<option value="c">option c</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "b") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="b">option b</option>
				<option value="a">option a</option>
				<option value="c">option c</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "c") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="c">option c</option>
				<option value="b">option b</option>
				<option value="a">option a</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "d") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="d">option d</option>
				<option value="b">option b</option>
				<option value="c">option c</option>
				<option value="a">option a</option>
			</select>
			<?php endif; ?>
			</div><!-- end of posible options -->
<div class="courseditcomplex">
<div class="edit_section">
			<span class="delete" data-id="<?= $one->id ?>" data-courseid="<?=  $course_name->id ?>"><i class="fa fa-remove"></i></span>
			<a class="edit_sc" href="editcomplex?course_id=<?php echo $course_name->id; ?>&question_id=<?php echo $one->id; ?>&simple"><i class="fa fa-pencil"></i></a>
</div><!-- end of edit section -->
<div class="displayComplexQuestions">
<div class="question">
<?= $one->excapeQuote($one->question); ?>
</div><!-- end of question -->
<div class="answerDiv">
<div class="answers <?php if($one->answer == "a"){echo "answerSelect";} ?>">
<span class="optis">a.) </span>
<?= $one->excapeQuote($one->option_a) ?>
</div><!-- each answers (a) -->
<div class="answers <?php if($one->answer == "b"){echo "answerSelect";} ?>">
<span class="optis">b.) </span>
<?= $one->excapeQuote($one->option_b) ?>
</div><!-- each answers (b) -->
<?php if(trim($one->option_c) !== "") : ?> 
<div class="answers <?php if($one->answer == "c"){echo "answerSelect";} ?>">
<span class="optis">c.) </span>
<?= $one->excapeQuote($one->option_c) ?>
</div><!-- each answers (c) -->
<?php endif; ?><!-- not empty option c -->
<?php if(trim($one->option_d) !== "") : ?> 
<div class="answers <?php if($one->answer == "d"){echo "answerSelect";} ?>">
<span class="optis">d.) </span>
<?= $one->excapeQuote($one->option_d) ?>
</div><!-- each answers (d) -->
<?php endif; ?><!-- not empty option c -->
</div><!-- end of answer div -->
</div><!-- end of display complex questions -->
</div><!-- end of course edit complex section -->
</div><!-- end of closer -->
<?php endif; ?><!-- end of complex question section -->
	

	<?php if(trim($one->comprehension) == "" && $one->complex == 0 ) : ?>	
		<!-- show questions without special characters -->
		<div id="result_div" data-id="<?= $one->id ?>">
		<div id="result_title">
			<span class="numbering"><i class="fa fa-arrow-right" aria-hidden="true"></i>
</span>
			<input id="title[<?php echo $one->question; ?>]" type="text" value="<?php echo($one->excapeQuote($one->question)); ?>" class="questions" data-questionId="<?php echo $one->id; ?>" <?php if($one->special_character == 1) :?> disabled <?php endif; ?> data-courseid="<?php echo $_GET['course_id']; ?>" placeholder="Enter A Question"/>
			
			<!-- if the question contains special characters -->
			<div class="edit_section">
			<span class="delete" data-courseid="<?php echo $_GET['course_id']; ?>"><i class="fa fa-remove"></i></span>
			<a class="edit_sc" href="question.php?course_id=<?php echo $course_name->id; ?>&edit=<?php echo $one->id; ?>&simple"><i class="fa fa-pencil"></i></a>
			</div><!-- end of edit section -->


			<!-- list posible options -->
			<div class="posible_options" data-courseid="<?php echo$_GET['course_id']; ?>">
			<?php if($one->answer == "a") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="a">option a</option>
				<option value="b">option b</option>
				<option value="c">option c</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "b") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="b">option b</option>
				<option value="a">option a</option>
				<option value="c">option c</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "c") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="c">option c</option>
				<option value="b">option b</option>
				<option value="a">option a</option>
				<option value="d">option d</option>
			</select>
			<?php endif; ?>
			<?php if($one->answer == "d") : ?>
			<i class="fa fa-check-square-o" aria-hidden="true"></i>
			<select>
				<option value="d">option d</option>
				<option value="b">option b</option>
				<option value="c">option c</option>
				<option value="a">option a</option>
			</select>
			<?php endif; ?>
			</div><!-- end of posible options -->
			<?php
			//echo $one->answer;
			?>
			</div><!-- end of result title -->
			<!-- get the answer and the options -->
			<div id="result_options" data-questionId="<?php echo $one->id; ?>" data-courseid="<?php echo $_GET['course_id']; ?>">
			<!-- if option a -->
		<?php if($one->answer == "a") : ?>
			<span data-answer data-value="option_a_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?>
			>
			a) 
				<?php echo $one->excapeQuote($one->option_a); ?>
			</span>
		<?php else: ?>
			<!-- show to normal option and also make answer -->
			<span data-value="option_a_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?> >
			a) <?php echo $one->excapeQuote($one->option_a); ?>
			</span>
				
		<?php endif; ?>
		<!-- if option b -->
		<?php if($one->answer == "b") : ?>
			<span data-answer data-value="option_b_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?>>
			b) 
				<?php echo $one->excapeQuote($one->option_b); ?>
			</span>
		<?php else: ?>
			<!-- show to normal option and also make answer -->
			<span data-value="option_b_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?> >
			b) <?php echo $one->excapeQuote($one->option_b); ?>
			</span>
				
		<?php endif; ?>

<!-- if option c -->
<?php if($one->option_c !== "") : ?>
		<?php if($one->answer == "c") : ?>
			<span data-answer data-value="option_c_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?>>
			c) 
				<?php echo $one->excapeQuote($one->option_c); ?>
			</span>
		<?php else: ?>
			<!-- show to normal option and also make answer -->
			<span data-value="option_c_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?> >
			c) <?php echo $one->excapeQuote($one->option_c); ?>
			</span>
				
		<?php endif; ?>
	<?php endif; ?>

	<!-- if option d -->
<?php if($one->option_d !== "") : ?>
		<?php if($one->answer == "d") : ?>
			<span data-answer data-value="option_d_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?>>
			d) 
				<?php echo $one->excapeQuote($one->option_d); ?>
			</span>
		<?php else: ?>
			<!-- show to normal option and also make answer -->
			<span data-value="option_d_<?php echo $one->id; ?>" <?php if($one->special_character == 0 )  : ?>data-valedit="true"
			<?php endif; ?>>
			d) <?php echo $one->excapeQuote($one->option_d); ?>
			</span>
				
		<?php endif; ?>
	<?php endif; ?>
	</div><!-- end of result options -->
	</div><!-- end of result div -->
		<?php endif; ?><!-- dont display comprehension questions -->
	<?php  endforeach; ?>
<?php endif; ?>
<?php elseif(isset($_GET['edit']) && !empty($_GET['edit'])) : ?>

	<div id="dcd">
	<!-- this section edits course values -->
		<?php
			$id  = $course->prn($_GET['edit']);
			$sql = "SELECT duration,class,course_date,department FROM course WHERE id={$id}";
			$found = array_shift(course::find_by_sql($sql));

			$current_date = strtotime(date('m/d/Y'));
			$test_Date = strtotime($found->course_date);
			if($current_date ==  $test_Date){
				//return user to a link where he can view the course 
				echo "<span class=\"error\" >Sorry You Can't Edit This Test Because The Test Is Scheduled For Today.</span>";
				return false;
			}
			?>
		<?php if($found) : ?>
			<?php
			$date = explode("/"  , $found->course_date);
			$time = explode(":" , $found->duration);
				
			//GET CALSSES
			$test_classes = explode(',' , $found->class);
			$htmlclass = "";
			foreach($test_classes as $one){
		if($one !== ""){
			$htmlclass .= "<div class=\"individual_class\">";
		 	$htmlclass .= "<span data-value=\"". $one ."\">" . $one . "</span>";
		 	$htmlclass .="<span class=\"delete_one\"><i class=\"fa fa-remove\"></i></span>";
		 	$htmlclass .="</div>";
		 }
			}	

			//get deparments
			$departments = explode(',', $found->department);
			$htmldept = "";
			foreach($departments as $one){
				if($one !== ""){
					$foundDept = departments::find_by_id($one);
					if($foundDept){
					$text = $foundDept->name;
					}
				}else{
					$text = "";	
				}
				
		if($one !== ""){
			$htmldept .= "<div class=\"individual_class\">";
		 	$htmldept .= "<span data-value=\"". $one ."\">" . $text . "</span>";
		 	$htmldept .="<span class=\"delete_one\"><i class=\"fa fa-remove\"></i></span>";
		 	$htmldept .="</div>";
		 }
			}
			?>		


		<div class="sec1" data-id="<?php echo $id; ?>">
		<div class="format">Format : Day / Month / Year</div>
		<label>Date</label>
		  <input data-date type="text" maxlength="2" placeholder="Day" name="day" value="<?php  
		if($date[1][0] == 0){
			echo $date[1][1];
		}else{
			echo $date[1];
		} ?> ">/
		<input data-date type="text"  placeholder="Month" name="month" value="<?php  
		if($date[0][0] == 0){
			echo $date[0][1];
		}else{
			echo $date[0];
		}

		 ?> " maxlength="2">/
		<input data-date type="text" maxlength="4" placeholder="Year" name="year" value="<?php  echo date('Y' , time()) ?> " disabled>
			<button type="button" id="update_date">update</button>
		</div>
		<div class="sec1"  data-id="<?php echo $id; ?>">
		<label>Duration</label>
		Hour<input type="text" placeholder="Hour" name="hour" value="<?php  echo $time[0]; ?> " maxlength="2">
		Minute<input type="text" maxlength="2" placeholder="Minute" name="minute" value="<?php  echo $time[1]; ?> ">
		<button type="button" id="update_duration">update</button>
		</div>
		<div class="sec1"  data-id="<?php echo $id; ?>">
		<label>Class</label>
		<?php
			$sql="SELECT class_name FROM myclass order by class_name = '{$found->class}' DESC";
			$foundCALSSES = myclass::find_by_sql($sql);
			?>
		<select id="change_class">
			<option vla="">---</option>
	<?php foreach($foundCALSSES as $one) : ?>
		<option value="<?php echo $one->class_name; ?>"><?php echo $one->class_name; ?></option>
	<?php endforeach; ?>
		</select>
			<button type="button" id="update_class_ce" data-id="<?php echo $id; ?>">update</button>
			<div id="add_classes" class="dd">
			<?php echo $htmlclass; ?>
			</div><!-- end of display classes -->
		</div>
		<!-- department section -->
		<div class="sec1"  data-id="<?php echo $id; ?>">
		<label>Department</label>
		<select id="change_department">
			<option vla="">---</option>
			<?php
			$allDepts = departments::find_all();
			?>
		<?php foreach($allDepts as $dept) : ?>
			<option value="<?= $dept->id ?>"><?= $dept->name ?></option>
		<?php endforeach; ?>
		</select>
			<button type="button" id="update_department_ce" data-id="<?php echo $id; ?>">update</button>
			<div id="add_departments" class="dd">
			<?php echo $htmldept; ?>
			</div><!-- end of display classes -->
		</div>
		<!-- end of department section -->
	<?php else : ?>
		<span class="error" style="font-size : 1.5em;">sorry no test was not found</span>
	<?php endif; ?><!-- if course is found -->
		</div><!-- end of edit durtation class and date -->
<?php endif; ?>
</div><!-- end of reload -->
</div><!-- center section -->
</div><!-- end of courseedit -->
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
<div id="info_edit">

	
</div><!-- info div -->
<!-- check if returning from simple to compex question -->
<?php if(isset($_SESSION['simple_question'])) : ?>
	<script>
		$theVal = $('div.closer[data-id=<?= $_SESSION['simple_question'] ?>]').offset().top;
		if($theVal == undefined){
		$theVal = $('div.compDisplay[data-id=<?= $_SESSION['simple_question'] ?>]').offset().top;
		}
		if($theVal  == undefined){
		$theVal = $('div#result_div[data-id=<?= $_SESSION['simple_question'] ?>]').offset().top;
		}
		if($theVal == undefined){
			$theVal = 0;
		}
		$scrollTop = $theVal;
		$('html').animate({
			scrollTop : $scrollTop,
		} , 1000, function(){
			$('div.closer[data-id=<?= $_SESSION['simple_question'] ?>]').fadeOut('fast' , function(){
				$(this).fadeIn('fast');
			});
		});
	</script>
	<?php 
	unset($_SESSION['simple_question']);
	unset($_SESSION['simple_question_courseId']);
	?>
<?php endif; ?>
</body>
</html>
