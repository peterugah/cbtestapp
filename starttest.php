<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
require_once('include/course.php');
require_once('include/question.php');
require_once('include/score.php');

if(isset($_SESSION['user_type'])){
	$generalFunc->redirect_to('login.php');
}if(!isset($_GET['course_id'])){
	$generalFunc->redirect_to('login.php');
}else{
	$theCourse = course::find_by_id($_GET['course_id']);

	if(isset($_SESSION['student_name'])){
	//check if student has written this course before
	$sql="SELECT id,score_date FROM score WHERE (student_name = '{$_SESSION['student_name']}' AND course_id = '{$_GET['course_id']}') AND class='{$_GET['class']}'";
	$if_taken = array_shift(myscore::find_by_sql($sql));
}
}
$timer  =  explode(':', $theCourse->duration);
//echo $theCourse->duration;
//print_r($timer);
if((int)$timer[0] !== 0){
	//format including hours
	if(strlen($timer[1]) == 1){
		$timeformat = "0{$timer[0]}.0{$timer[1]}.00";
	}else{
		//two digits
		$timeformat = "0{$timer[0]}.{$timer[1]}.00";
	}
}else{
	//format only muinutes
	//check if the minutes is less than 10
	if(strlen($timer[1]) == 1){
		$timeformat = "00.0{$timer[1]}.00";
	}else{
		//two digits
		$timeformat = "00.{$timer[1]}.00";
	}

}

//format remaining time 
$format = explode('.' , $timeformat);
//calculate on hours
if(((int)$format[0] !== 0 && (int)$format[1] == 0)){
$hour = (int)$format[0];
//if one hour format right awaye
if($hour == 1){
$remaining = "00:57:00";
}else{
	//format with remaning hours left
	$left = $hour-1;
	$remaining = "0{$left}:57:00";
}
}

//calculate on minutes
if(((int)$format[0] == 0 && (int)$format[1] !== 0) || ((int)$format[0] !== 0 && (int)$format[1] !== 0))  {
	//calculate base on minutes 
	$val = (int)$format[1] - 3;
	//end of format as int
	if(strlen($val) == 1){
		$remaining = "{$format[0]}:0{$val}:00";
	}else{
		$remaining = "{$format[0]}:{$val}:00";
	}
}

//calculate times lefgt
$thirthymins = "";
$tenmins = "";
$fivemins = "";
$twomins = "";
$oneminute = "";
//calculate on hours
if(((int)$format[0] !== 0 && (int)$format[1] == 0)){
$hour = (int)$format[0];
//if one hour format right awaye
if($hour == 1){
$thirthymins = "00:30:00";
$tenmins = "00:50:00";
$fivemins = "00:55:00";
$twomins = "00:58:00";
$oneminute = "00:59:00";
}else{
	//format with remaning hours left
	$left = $hour-1;
	$thirthymins = "0{$left}:30:00";
	$tenmins = "0{$left}:50:00";
	$fivemins = "0{$left}:55:00";
	$twomins = "0{$left}:58:00";
	$oneminute = "0{$left}:59:00";
}
}
//calculate on minutes
if(((int)$format[0] == 0 && (int)$format[1] !== 0) || ((int)$format[0] !== 0 && (int)$format[1] !== 0))  {
	$val = (int)$format[1];
	//calculate 30 minites
	if($val > 30){
	$cal = $val - 30;
	$thirthymins = (strlen($cal) == 1) ? "{$format[0]}:0{$cal}:00" : "{$format[0]}:{$cal}:00";
	}else{
		$thirthymins = $course->below_duration_and_1_hour($format , 30);
	}//end of 30 minutes
	//calculate 10 mins
	if($val > 10){
	$cal = $val - 10;
	$tenmins = (strlen($cal) == 1) ? "{$format[0]}:0{$cal}:00" : "{$format[0]}:{$cal}:00";
	}else{
		$tenmins = $course->below_duration_and_1_hour($format , 10);
	}
	//end of 10 mins
	//calculate 5 mins
	if($val > 5){
	$cal = $val - 5;
	$fivemins = (strlen($cal) == 1) ? "{$format[0]}:0{$cal}:00" : "{$format[0]}:{$cal}:00";
	}else{
		$fivemins = $course->below_duration_and_1_hour($format , 5);
	}
	//end of 5 mins
	//calculate 2 mins
	$cal = $val - 2;
	$twomins = (strlen($cal) == 1) ? "{$format[0]}:0{$cal}:00" : "{$format[0]}:{$cal}:00";
	//end of 2 mins
	//calculate 1 min
	$cal = $val - 1;
	$oneminute = (strlen($cal) == 1) ? "{$format[0]}:0{$cal}:00" : "{$format[0]}:{$cal}:00";
	//end of 1 mins
}
//unset if_taken to prvent errors
if(isset($_GET['score'])){
	unset($if_taken);
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php
	if(!empty($theCourse)){
		echo strtoupper($theCourse->excapeQuote($theCourse->course_name));
	}else{
		echo "success";
	}
	?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">

	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
	<script type="text/javascript" src="script/timer.js"></script>
	<script type="text/javascript" src="script/countdown.js"></script>
	<?php require 'script/mathjax.php'; ?>

	<script type="text/javascript">
	$(document).ready(function(){
	<?php if(isset($_GET['start'])) : ?>
	//full screen method
	function launchFullScreen(element) {
	  if(element.requestFullScreen) {
	    element.requestFullScreen();
	  } else if(element.mozRequestFullScreen) {
	    element.mozRequestFullScreen();
	  } else if(element.webkitRequestFullScreen) {
	    element.webkitRequestFullScreen();
	  }
	}

	//force user to use full screen
	if((window.fullScreen) || (window.innerWidth == screen.width && window.innerHeight == screen.height)) {
	//in fullscreen mode already
	} else {
	//hide the questions and trigger full screen on click
	$('#course_content').css('display' , 'none');
	$('#starttest').append("<button class=\"begin\">Begin In Full Screen...</button>");
	}

	//trgger begin
	$(document).on('click' , '.begin' , function () {
		$(this).remove();
		$('#course_content').css('display' , 'block');
		launchFullScreen(document.documentElement);
	});

	//get the total number of questions
	setTimeout(function () {
	$allquestions = $('#submitTest').children('.questions').length;
	} , 1000);
	//end of track and notify student to cross check his work
	<?php endif; ?>


	//alert if user leaves the page
	var $userleft = 0;
	$(window).on('blur' , function () {
		$userleft ++;
	});
	$(window).on('focus' , function () {
		if($userleft == 1){
		$('#warning').removeClass('hide').html('<i class="fa fa-meh-o" aria-hidden="true"></i> The next time you leave, I will submit').fadeIn(1000).delay(8000).fadeOut(1000 , function () {
			$(this).empty().addClass('hide');
		});
		}
		if($userleft > 1){
			//trigger submit
			$('#timediv').attr('data-autosubmit' , '1');
			$('#submitTest').trigger('submit');
		}
	});


	//disable page f5 refresh
	$(document).on("keydown", disableF5);
		function disableF5(e) { if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82) e.preventDefault(); };
	//submit score if user closes a page
	<?php if(isset($_GET['start']) && !isset($_GET['score'])) : ?>
	//track scores
	var $allow = 0;
	$(".questions input:radio").on('change' , function () {
		//also track to see if the students has finished the test
		var $selectedinputs = $('.questions input:radio:checked').length;
		if($selectedinputs == $allquestions){
			$allow++;
		}
		//track is student has ticked all
		if($allow == 1){
			//get current time and a minumum of 10minites left 
			$currentTime = parseFloat($('#timediv').text().split(':').join('.'));
			$minimumtime = parseFloat($('#timediv').attr('data-10mins').split(':').join('.'));
			//only show if the time is above ten minutes
			if($minimumtime > $currentTime){
		setTimeout(function () {
			$html = "<span style=\"font-size: 1em;\">Hi <?php echo $_SESSION['student_name']; ?> <i class=\"fa fa-smile-o\"></i>. I see you are done with your test. I suggest you go through it. Wish you the best.</span>";
			$('#warning').removeClass('hide').html($html).fadeIn(1000).delay(10000).fadeOut(1000 , function () {
			$(this).empty().addClass('hide');
		});
		} , 20000);
		}
	}
		//update tracks to the database
	 	 $scores = $('#submitTest').serialize();
	 	 $id = $('#center').attr('data-id');
	 		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : {track_score : true , scores : $scores , course_id : $id}
		});
	});
	//update students score once the student selects an answer
	//dont submit scores send flatt auto_submit
	var $course_id 	 = $('#center').attr('data-id');
	var $course_name = $('#center').attr('data-courseName');
	var $data = "submitTest=true&auto_submit=true&course_id=" + $course_id + "&notice=1&course_name=" +$course_name;
	 	$(window).on('unload' , function(){
    	$.ajax({
        type: 'POST',
        url: 'include/process.php',
        async:false,
        data: $data,
        success : function() {
        	window.opener.location.reload(true);
        }
    	});
	});
	});
	<?php endif; ?>
	//full screen
	
	</script>
</head>
<body>
<div id="starttest" class="container">
	<div id="course_content">
	<?php if(!isset($_SESSION['student_name']) || !isset($_SESSION['student_class'])) : ?>
	<span class="error">You Can No Longer Write This Test As You Refreshed The Page.</span>
	<?php
	return false;
	?>
	<?php endif; ?>
	
	<?php if(!empty($if_taken)) : ?>
		<?php
		echo "<span class=\"error\">Sorry You  Already Wrote This Test On " . date('j M, Y' , $if_taken->score_date) .  " . See Your Class Teacher For Any Issues Thank You. </span>";
		return false;
		?>
	<?php endif; ?>	
<?php if(!empty($theCourse) && isset($_GET['start'])) : ?>
	<div id="header">
	<span><?php echo strtoupper($theCourse->excapeQuote($theCourse->course_name)); ?></span>
	<!-- time the examaccordingly -->
	<?php
	$time = explode(':' , $theCourse->duration);
	// print_r($time);
	 $display = $time[0] . "h" . $time[1] ."m";
	// echo $display;
	?>
	<!-- this will hide the timmer div after a submission -->
	<?php if(!isset($_GET['hide'])) : ?>
	<div id="timediv" data-time="<?php echo $display; ?>" data-format="<?php echo $timeformat; ?>" data-allow="<?php echo $remaining; ?>" data-30mins="<?php echo $thirthymins; ?>" data-10mins="<?php echo $tenmins; ?>" data-5mins="<?php echo $fivemins; ?>" data-2mins="<?php echo $twomins; ?>" data-1mins="<?php echo $oneminute; ?>" data-autosubmit="0"></div>
	<?php endif; ?>

		<!-- this is to close the page after submission -->
	<?php if(isset($_GET['hide'])) : ?>
	<span id="mytimmer" data-time="5"></span>
	<?php endif; ?>

	<div class="hide" id="warning">
	</div>
	<?php if(isset($_SESSION['student_name'])) : ?>
	<span class="show_name_g">
	<?php echo $_SESSION['student_name']; ?>
	</span><!-- show students name -->
<?php endif; ?>
	</div><!-- end of header -->
<?php endif; ?>
	
<?php if(!empty($theCourse) && isset($_GET['start']) && !isset($_GET['score'])) : ?>

	<?php
	//check if the student has written this course before
	$sql = "SELECT id FROM score WHERE student_name = '{$_SESSION['student_name']}' AND class = '{$_SESSION['student_class']}' AND course_name = '{$theCourse->course_name}' ";
	$result = $database->query($sql);
	if($database->affected_rows($result) == 1){
		echo "<span class=\"error\">You Have Already Written This Test. See Your Class Teacher For Any Issues Thank You.</span>";
		return false;
	}

	?>

	<div id="center" data-id="<?php echo $theCourse->id; ?>" data-courseName="<?php echo $theCourse->course_name; ?>">
	<?php
	//get questions 
	$sql = "SELECT * FROM question WHERE course_id = {$theCourse->id} ORDER by RAND()";
	$found  = question::find_by_sql($sql);
	
	?>
	<form id="submitTest" action="" method="post" >
	<?php
	//numbering system
	$x = 0;
	?>	
	<?php foreach($found as $one) : ?>
		<?php
  
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
		<?php if(trim($one->comprehension) !== "") : ?>
		<div class="compdo" data-size="small">
		<?php if(trim($one->excapeQuote($one->instruction) !== "")) : ?>
		<div class="compins"><?= $one->instruction ?></div>
		<?php endif; ?>
		<div class="conts">
		<?= $one->excapeQuote($one->comprehension) ?>
		</div>
		</div><!-- comprehension display div -->	
		<?php endif; ?>
		<?php if(trim($one->comprehension) == "" ) : ?>
			<?php 
					$x++;
			?>
		<div class="questions">
			<span class="numbering"><?php echo $x  . ". ) "; ?></span>
			<span class="quest"><?php echo $one->question; ?></span>
			<div class="optionsDiv">
			<div class="single">
			<!-- option a -->
			<input name="answer[<?php echo $one->id; ?>]" id="a_<?php echo $one->id; ?>" type="radio" value="a" />
			<label for="a_<?php echo $one->id; ?>" data-value="a.) "></label>
			<span><?php echo $one->option_a; ?> </span>
			</div><!-- end of single -->
			<div class="single">
			<!-- option b -->
			<input name="answer[<?php echo $one->id; ?>]" id="b_<?php echo $one->id; ?>" type="radio" value="b" />
			<label for="b_<?php echo $one->id; ?>" data-value="b.) "></label>
			<span><?php echo $one->option_b; ?> </span>
			</div><!-- end of label -->
			<?php if($one->option_c !== "") : ?>
			<div class="single">
			<!-- option c -->
			<input name="answer[<?php echo $one->id; ?>]" id="c_<?php echo $one->id; ?>" type="radio" value="c" />
			<label for="c_<?php echo $one->id; ?>" data-value="c.) "></label>
			<span><?php echo $one->option_c; ?> </span>
			</div><!-- end of single -->
		<?php endif; ?>
		<?php if($one->option_d !== "") : ?>
			<div class="single">
			<!-- option d -->
			<input name="answer[<?php echo $one->id; ?>]" id="d_<?php echo $one->id; ?>" type="radio" value="d" />
			<label for="d_<?php echo $one->id; ?>" data-value="d.) "></label>
			<span><?php echo $one->option_d; ?> </span>
			</div><!-- end of single -->
		<?php endif; ?>

		</div><!-- end of optionsDiv -->
		</div><!-- end of questionDiv -->
	<?php endif; ?>
	<?php endforeach; ?>
	<button type="submit" id="submit">Submit</button>
	</form><!-- end of form -->
	</div><!-- end of course content -->
<?php elseif(isset($_GET['course_id']) && !isset($_GET['score'])): ?>
	<div id="Testwarning">
	<span>Please note that once you click the start button, if you leave, close or reload the browser, Your test will be automatically submitted!!!</span>
	</div>
	<div id="course_instruction">
	<?php
	if($theCourse->course_instruction !==""){
		echo "<span class=\"instruction\">Test instruction</span>";
		echo "<span>{$theCourse->course_instruction}</span>";
	}else{
		echo "<span>Wish You Success <i fa fa-smile></i></span>";
	}
	?>
	<button type="button" id="start">start</button>
<!-- display course instruction and start -->
	</div><!-- end of course instruction -->

<?php elseif(isset($_GET['score'])): ?>
	<!-- display the students score -->
<p class="student_score">You scored <?php echo "<span>'" . $_GET['score'] . "'</span>"; ?></p>
<?php endif; ?>
</div>
</div>
<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>
</html>
