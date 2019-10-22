<?php 
	header('Content-Type: text/html; charset=utf-8');
	require_once('session.php');
	require_once('database.php');
	require_once('user.php');
	require_once('class.php');
	require_once('course.php');
	require_once('question.php');
	require_once('generalFunc.php');
	require_once('admin.php');
	require_once('score.php');
	require_once('pictures.php');
	require_once('uploads.php');
	require_once('departments.php');

//update department
	if(isset($_POST['update_dept']) && $_POST['update_dept'] == true && $question->
	is_ajax()){	
		$old_val = trim($_POST['old_val']);
		$new_val = ucwords(trim($_POST['new_val']));
		$sql = "UPDATE departments SET name='{$new_val}' WHERE name='{$old_val}'";
		if($database->query($sql)){
			echo "done";
		}else{
			echo "Unable To Update Department. Please Try Again";
		}
	}


//delete department
	if(isset($_POST['delete_dept']) && $_POST['delete_dept'] == true && $question->
	is_ajax()){	
		$name = trim($_POST['name']);
		//update students in that department to general 
		$sql = "SELECT id FROM departments WHERE name = '{$name}'";
		$id = array_shift(departments::find_by_sql($sql));
		$sql =" UPDATE users SET department = '1' WHERE user_type ='student' AND department = '{$id->id}'";
		$database->query($sql);
		$sql = "DELETE FROM departments WHERE name = '{$name}'";
		if($database->query($sql)){
			echo "done";
		}else{
			echo "Unable To Delete Department. Please Try Again";
		}
	}

//new department
	if(isset($_POST['new_department']) && $_POST['new_department'] == true && $question->
	is_ajax()){	
		//check if department exist
		$name = strtolower($departments->prl($_POST['name']));
		$check = $departments->check_if('name', $name);
		if(!empty($check)){
			echo "" . ucwords($check->name) . " Department Already Exist";
			return false;
		}
		$departments->name = ucwords($name);
		if(!$departments->save()){
			echo "Error Trying To Save Department Please Try Again.";
			return false;
		}
		echo "done";
		
	}

//upload images to the server
	if(isset($_POST['upload_image']) && $_POST['upload_image'] == true && $question->
	is_ajax()){	
		//attach file
		if($uploads->attach_file($_FILES['image']) == false){
			$error =  implode('<br />' , $uploads->errors);
			echo "error: {$error}";
			return false;
		}
		if($uploads->save() == false){
			$error =  implode('<br />' , $uploads->errors);
			if(trim($error) !=="This File Already Exist"){
			echo "error: {$error}";
			}
			return false;
		}
		echo  "include/".$uploads->new_name;
	}

//update complex question
	if(isset($_POST['update_complex_question']) && $_POST['update_complex_question'] == true && $question->
	is_ajax()){	
		$question->question = $database->escape_value($_POST['question']);
		$question->option_a = $database->escape_value($_POST['opt_a']);
		$question->option_b = $database->escape_value($_POST['opt_b']);
		$question->option_c = $database->escape_value($_POST['opt_c']);
		$question->option_d = $database->escape_value($_POST['opt_d']);
		$question->course_id = $_POST['course_id'];
		$question->answer = $_POST['answer'];
		$question->id = $_POST['question_id'];
		$question->complex = 1;
		if($question->save()){
		$course->update_activity($question->course_id);
			echo "yes";
		}else{
			//error
			echo "No Changes Where Made. Please Update A field";
		}
	}

//save complex question
	if(isset($_POST['new_complex_question']) && $_POST['new_complex_question'] == true && $question->
	is_ajax()){	
		$question->question = $database->escape_value($_POST['question']);
		$question->option_a = $database->escape_value($_POST['opt_a']);
		$question->option_b = $database->escape_value($_POST['opt_b']);
		$question->option_c = $database->escape_value($_POST['opt_c']);
		$question->option_d = $database->escape_value($_POST['opt_d']);
		$question->course_id = $_POST['course_id'];
		$question->answer = $_POST['answer'];
		$question->complex = 1;
		if($question->save()){
		$course->update_activity($question->course_id);
			echo "yes";
		}else{
			//error
			echo "Unable To Add Question Pls Try Again Later";
		}
	}
//save current complex question satae
	if(isset($_POST['save_state']) && $_POST['save_state'] == true && $question->
	is_ajax()){		
		$current = $_POST['current'];
		switch ($current) {
			case $current:
				$_SESSION[$current] = $_POST['content'];
				$_SESSION['question_type'] = $current;
				break;
			default:
				$_SESSION['question'] =  $_POST['content'];
				$_SESSION['question_type'] = 'question';
				break;
		}
		echo $_SESSION[$current];
	}	

//update comprehension
	if(isset($_POST['update_comprehension']) && $_POST['update_comprehension'] == true && $question->
	is_ajax()){	
	$question->instruction = $database->escape_value($_POST['instruction']);
	$question->comprehension = $database->escape_value($_POST['comprehension']);
	$question->id = $_POST['id'];
	$sql = "UPDATE question SET instruction = '{$question->instruction}' , comprehension = '{$question->comprehension}' WHERE id = {$question->id}";
	if($database->query($sql)){
		echo "yes";
	}else{
		echo "Unable To Update";
	}
	}

//show comprehension
if(isset($_GET['show_comprehension']) && $_GET['show_comprehension'] == true && $question->
	is_ajax()){	
	if(!isset($_SESSION['comprehension']) && !isset($_SESSION['instruction'])){
		$output = "<span class=\"error\">Sorry Could Not Load Up The Comprehension, Try Viewing It From The Test Edit Page.</span>";
		echo $output;
		return false;
	}
	//session variables are set
	if(isset($_SESSION['comprehension']) && isset($_SESSION['instruction'])){
		$output  ="<div id=\"previewContent\" data-compId =\"{$_SESSION['compId']}\" >";
		$output .= "<div id=\"toolbar\">";
		$output .="<button id=\"edit_comprehension_popup\"><i class=\"fa fa-pencil\"></i> Edit</button>";
		$output .="</div><!-- end of toolbar -->";
		$output .="<div id=\"compContent\">";
		$output .="<div class=\"ins_div\">{$generalFunc->excapeQuote($_SESSION['instruction'])}</div>";
		$output .="{$generalFunc->excapeQuote($_SESSION['comprehension'])}";
		$output .="</div><!-- end of compCOntent -->";
		$output .="</div><!-- end of preview content -->";
		echo $output;
		return true;
	}

}

//add comprehension
if(isset($_POST['add_comprehension']) && $_POST['add_comprehension'] == true && $course->
	is_ajax()){	
	$question->instruction = $database->escape_value($_POST['instruction']);
	$question->comprehension = $database->escape_value($_POST['comprehension']);
	$question->course_id = $_POST['id'];
	if($question->comprehensionExist() == true){
		echo "Sorry You Have Created This Comprehension Before";
		return false;
	}
	//limit comprehension to 3
	if($question->limitComprehension() >= 10){
		echo "Sorry You cannot Create More Than Ten Comprehensions";
		return false;
	}
	if($question->save()){
		$_SESSION['comprehension'] = $_POST['comprehension'];
		$_SESSION['instruction'] = $_POST['instruction'];
		$_SESSION['compId'] = $database->insert_id();
 		echo "yes";
	}
	
}

//remove reject
	if(isset($_POST['refused'])  && $_POST['refused'] == true){
		$sql = "UPDATE users SET reject = 0 WHERE class_id = {$_POST['class']} AND user_type= 'student' ";
		if($database->query($sql)){
			echo true;
		}
	}
//reject student
	if(isset($_POST['reject'])  && $_POST['reject'] == true){
		$sql = "UPDATE users SET new = 0 , class_id = former_class , reject = 1 ,former_class = 0 WHERE id ={$_POST['id']} AND user_type='student'";
		if($database->query($sql)){
			echo true;
		}
	}
//ignore student
	if(isset($_POST['ignore'])  && $_POST['ignore'] == true){
		$sql = "UPDATE users SET ignore_transfer = 1 WHERE id ={$_POST['id']} AND user_type='student'";
		if($database->query($sql)){
			echo true;
		}
	}
//transfer individual
	if(isset($_POST['transfer_individual'])  && $_POST['transfer_individual'] == true){
		$sql= "UPDATE users SET class_id = {$_POST['class']}, former_class = {$_POST['former_class']}, new = 1 WHERE id = {$_POST['id']} AND user_type='student'";
		if($database->query($sql)){
		echo true;	
		}else{
		echo "Error Transfering Try Again";
		}
	}

//acknowlege individual
		if(isset($_POST['acknowledge_individual'])  && $_POST['acknowledge_individual'] == true){
		$sql= "UPDATE users SET former_class = 0, new = 0, acknowledge = 1 WHERE id = {$_POST['id']} AND user_type='student' ";
		if($database->query($sql)){
		echo true;	
		}else{
		echo "Error Try Again";
		}
	}

//acknowledge selected students
		if(isset($_POST['acknowledgeselected'])  && $_POST['acknowledgeselected'] == true){
		$checks = $_POST['check'];
		foreach($checks as $key=>$val){
			$sql= "UPDATE users SET former_class = 0, new = 0, acknowledge = 1 WHERE id = {$key} AND user_type='student' ";
		$database->query($sql);
		}
		echo true;
	}

//transfer selected students
	if(isset($_POST['transferselected'])  && $_POST['transferselected'] == true){
		$checks = $_POST['check'];
		foreach($checks as $key=>$val){
			$sql= "UPDATE users SET class_id = {$_POST['class']}, former_class={$_POST['former_class']}, new = 1 WHERE id = {$key} AND user_type='student' ";
		$database->query($sql);
		}
		echo true;
	}

//update term date
	if(isset($_POST['update_termdate'])  && $_POST['update_termdate'] == true){
		$date = strtotime($_POST['date']);
		$today = strtotime(date('m/d/Y' , time()));
		if($today > $date){
			echo "You Have Selected A Date In The Past";
			return false;
		}
		//remove acknowledge from users
		if($date >= $today){
			$sqla = "UPDATE users SET acknowledge = 0 , ignore_transfer = 0, new = 0 , reject = 0 , former_class = 0 WHERE user_type = 'student'";
			$database->query($sqla);
		}
		//update date
		$sql = "UPDATE admin_panel SET term_date={$date}";
		if($database->query($sql)){
			echo date('j M, Y' , $date);
		}

	}

//get deleted scores
	if(isset($_GET['get_scores'])  && $_GET['get_scores'] == true){
		if(file_exists('scorelog.txt')){
		$file = file_get_contents('scorelog.txt');
		echo trim($file);	
		}	
	}

//upload logo
if(isset($_FILES['logo'])  && $course->is_ajax()){
	//attach the logo
	if($photo->attach_file($_FILES['logo']) == true){
		//upload the image
		$photo->save();
		//resize the image
		$photo->resize($photo->new_name, $photo->new_name, 40, 40, $photo->extention);
		//update the database
		$sql = "UPDATE admin_panel SET school_logo = '" . basename($photo->new_name) ."'";
		if($database->query($sql)){
			//echo url if the image
			echo basename($photo->new_name);
		}
	}
}
//update actvate
if(isset($_POST['update_activate']) && $_POST['update_activate'] == true && $course->is_ajax()){
	$checked = ($_POST['value'] == "true") ? 1 : 0;	
	$id = $_POST['id'];
	$sql = "UPDATE course SET activate = {$checked} WHERE id= {$id}";
	$database->query($sql);
	

}

//update admission number only
if(isset($_POST['updateadmissionnumber']) && $_POST['updateadmissionnumber'] == true && $course->is_ajax()){	
	$admission_number = strtoupper($database->escape_value($_POST['admission_number']));
	$id = $user->prn($_POST['id']);
	//check if admission number is avialable
	$sql = "SELECT id FROM users WHERE admission_number = '{$admission_number}' AND user_type='student'";
	$query = $database->query($sql);
	if($database->affected_rows() == 1){
		echo "A Student Already Has That Number";
		return false;
	}else{
	$sql = "UPDATE users SET admission_number = '{$admission_number}' WHERE id= {$id}";
	if($database->query($sql)){
		echo true;
	}
}
}	


//show user to admin
if(isset($_POST['searchUser']) && $_POST['searchUser'] == true && $course->is_ajax()){	
	$who = $_POST['who'];
	$value = $_POST['value'];
	$output = "";
	if($user->prl($who) == "teacher"){
		$sql = "SELECT title , full_name, id FROM users WHERE full_name LIKE '%{$value}%' AND user_type=\"{$who}\" ";
		$result = User::find_by_sql($sql);
		if(empty($result)){
			echo "<span class=\"error\">No Record Found<span>";
			return false;
		}
		foreach($result as $result){
		$output .= "<div class=\"displayAll\">";
		$output .="<span class=\"name\" data-id=\"{$result->id}\">{$result->title} {$result->full_name}</span>";
		$output .= "</div>";
		}
	}elseif($user->prl($who) == "student"){
		$sql = "SELECT full_name, id, admission_number,class_id FROM users WHERE (full_name LIKE '%{$value}%' OR admission_number LIKE '%{$value}%') AND user_type='{$who}'";
		$show = User::find_by_sql($sql);
		if(empty($show)){
			echo "<span class=\"error\">No Record Found<span>";
			return false;
		}
		foreach($show as $one){
			//get the student class name 
			$sqlclass = "SELECT class_name FROM myclass WHERE id={$one->class_id}";
			$foundclass = array_shift(myclass::find_by_sql($sqlclass));
			if(!empty($foundclass)){
				$classname = "" . $foundclass->class_name;
			}else{
				$classname = "";
			}
			$output .= "<div class=\"displayAll\">";
			if($one->admission_number !== ""){
				$number = "" . $one->admission_number;
			}else{
				$number ="";
			}
		$output .="<span class=\"name dd1\" data-id=\"{$one->id}\">{$one->full_name}</span><span class=\"dd2\">{$number}</span><span class=\"dd3\">{$classname}</span>";
		$output .= "</div>";
	}
	}

	echo $output;
	//var_dump($result);
}

//transfer administration
		if(isset($_POST['transfer_admin']) && $_POST['transfer_admin'] == true && $course->is_ajax()){
			$current_id =  $_SESSION['user_id'];
			$sql = "UPDATE users SET master_admin = 0 WHERE id = {$current_id}";
			if($database->query($sql)){
				$sql = "UPDATE users SET master_admin = 1 WHERE id = {$_POST['id']}";
				$database->query($sql);
				echo "Yes Removed Master Admin";
				$session->logout();
			}
		}
	
//delete user from databse
		if(isset($_POST['delete_user']) && $_POST['delete_user'] == true && $course->is_ajax()){
		$user->id  = $_POST['id'];
		if($user->delete()){
			echo true;
		}else{
			echo "Could Not Delete User";
		}
		}

//update password
	if(isset($_POST['update_password']) && $_POST['update_password'] == true && $course->is_ajax()){
		$user->id = $_POST['id'];
		$user->password = hash('sha512' , $database->escape_value($_POST['password']));
		if(isset($_POST['update_new']) && $_POST['update_new'] == true){
		$sql = "UPDATE users SET hashed_password = '{$user->password}', new = 0 WHERE id = {$user->id}";
		unset($_SESSION['new_teacher']);
		}else{
		$sql = "UPDATE users SET hashed_password = '{$user->password}', new = 1 WHERE id = {$user->id}";
		}
		if($database->query($sql)){
			echo "Password Updated";
		}
	}
//upadate users in admin panel
if(isset($_POST['update_user_in_admin']) && $_POST['update_user_in_admin'] == true && $course->is_ajax()){

	if(isset($_POST['username'])){
		$user->username = $_POST['username'];
	}else{
		$user->username = "";
	}
	if(isset($_POST['admission_number'])){
		$user->admission_number = strtoupper($_POST['admission_number']);
	}else{
		$user->admission_number = "";
	}
	if(isset($_POST['title'])){
		$user->title = $_POST['title'];
	}else{
		$user->title = "";
	}
	if(isset($_POST['full_name'])){
		$user->full_name = $_POST['full_name'];
	}else{
		$user->full_name = "";
	}
	if(isset($_POST['id'])){
		$user->id = $_POST['id'];
	}
	$user->department = isset($_POST['department']) ? $_POST['department'] : 0;
		
		if($_POST['type'] == "student"){
		//cross check admision numer 
		$sql1 = "SELECT id,admission_number FROM users WHERE admission_number = '{$user->admission_number}'  AND user_type = 'student'";
		$iffree = array_shift(user::find_by_sql($sql1));
		if(!empty($iffree) && $iffree->id !== $user->id && $iffree->admission_number !==""){
			echo "Admission Number Already Exist";
			return false;
		}
		//get the student name and update the scores tab
		$sql = "SELECT full_name FROM users WHERE id = {$user->id}";
		$full_name = array_shift(user::find_by_sql($sql));
		}

	$sql = "UPDATE users SET username = '{$user->username}' , admission_number = '{$user->admission_number}' , title = '{$user->title}' , full_name = '{$user->full_name}',department ={$user->department} WHERE id = {$user->id}";
	if($database->query($sql)){
		echo true;
		//update the student name on the scores tab
		if($_POST['type'] == "student"){
		$sql = "UPDATE score SET student_name = '{$user->full_name}' WHERE student_name = '{$full_name->full_name}'";
		$database->query($sql);
	}
	}


}

//delete score
	if(isset($_POST['deleteScore']) && $_POST['deleteScore'] == true && $course->is_ajax()){
		$myscore->id = $_POST['id'];
		if($myscore->delete()){
			echo true;
			//update log
			$content  = "<div class=\"log_score\"><span>Student name: {$_POST['student_name']}</span>";
			$content .="<span>Class : {$_POST['class']}</span>";
			$content .="<span>Test : {$_POST['test']}</span>";
			$content .="<span>Score : {$_POST['score']}</span>";
			$content .="<span>Deleted by : {$_SESSION['title']} {$_SESSION['full_name']}</span>";
			$content .="<span>Reason : {$_POST['reason']}</span>";
$date = date('d M Y' , time()) .  " at " . date('g:ia' ,time());
			$content .="<span>Time : {$date}</span></div>";
			$generalFunc->wirte_score_log($content);
		}

	}

//show scores
if(isset($_POST['searchscore']) && $_POST['searchscore'] == true && $course->is_ajax()){
	$output = "";
	$value = $database->escape_value(strtolower($_POST['value']));

	$split = explode(',' , $_POST['value']);
	
	//$notice = $_POST['notice'];
	if($_SESSION['user_type'] == "admin"){
	$sql = "SELECT * FROM score WHERE student_name LIKE '%{$value}%' OR course_name LIKE '%{$value}%' OR class LIKE '%{$value}%'";
	$found = myscore::find_by_sql($sql);
	}elseif($_SESSION['user_type'] == "teacher"){
	$sql1 = "SELECT class_name FROM myclass WHERE teacher_id = {$_SESSION['user_id']} LIMIT 1";
	$found_class = array_shift(myclass::find_by_sql($sql1));
	if(!empty($found_class)){
	$sql = "SELECT * FROM score WHERE (student_name LIKE '%{$value}%' OR course_name LIKE '%{$value}%') AND class = '{$found_class->class_name}'";	
		$found = myscore::find_by_sql($sql);
	}else{
	$found ="";

}
	
}
	if(!empty($found)){
		global $generalFunc;
		//display result
		foreach($found as $one) {
			$excapaedNmae = $one->excapeQuote($one->student_name);
			$excapaedCourse = $one->excapeQuote($one->course_name);
			$one->student_name = $excapaedNmae;
			$one->course_name = $excapaedCourse;
	$output .="<div class=\"displayscores\">";
	$output .="<span class=\"c1\"><a href=\"?view=$one->student_name&msg=showing results for '$one->student_name'\"> $one->student_name</a></span>";
	$output .="<span class=\"c2\"><a href=\"?test={$one->course_name}&msg=showing results for {$one->course_name}\">$one->course_name </a></span>";	
	if($_SESSION['user_type'] == "admin"){
	$output .="<span class=\"c3\"><a href=\"?view=$one->student_name&msg=showing '$one->class' results for '$one->student_name'&class=$one->class\">$one->class</a></span>";
	}else{
		$output .="<span class=\"c3\"><a href=\"#\">$one->class</a></span>";
	}
	$output .="<span class=\"c4\">" . date('j M, Y' , $one->score_date) . " at " . date('g:ia' , $one->score_date) . "</span>";
	//get score and average
	$average = $one->max_score / 2;
	$average = round($average);
	if($one->score < $average){
		$style = "style=\"color : red;\"";
	}elseif($one->score == $average){
			$style = "style=\"color : maroon;\"";
	}else{
		$style = "style=\"color : green;\"";
	}

	$output.="<span class=\"c5\" $style >$one->score / $one->max_score</span>";
	if($_SESSION['master_admin'] == 1){
	$output .="<span class=\"c6\" data-id= \"$one->id\" id=\"removeScore\"><i class=\"fa fa-remove\"></i></span>";
	}
	$output .="</div><!-- seperate individual scores into divs -->";
		}//end of loop
	}else{
		$output .= "<span class=\"error\">No Record Of Such Found</span>";
	}
	echo $output;
}

//track scores
if(isset($_POST['track_score']) && $_POST['track_score'] == true && $course->is_ajax()){
	$re = array("19"=>"a" , "20"=>"b" , "21"=>"a");
	parse_str($_POST['scores'] , $kept['answer']);
	$theScore = $kept['answer']['answer'];
	//get the questions and answers and calculate the difference
	$id = $question->prn($_POST['course_id']);
	$sql  = "SELECT id,answer FROM question WHERE course_id = {$id}";
	$found = question::find_by_sql($sql);
	//total number of questions
	$total_number = count($found);
	$answers = array();
	//define the answer array
	foreach($found as $one){
		//$answers[$one->id] = $key->answer;
		$answers[$one->id] = $one->answer;
	}
	$diff = count(array_diff_assoc($answers, $theScore));
	$_SESSION['trackedScore'] = $total_number - $diff;
}


//submit test
if(isset($_POST['submitTest']) && $_POST['submitTest'] == true && $course->is_ajax()){

	//still make the submission by putting a null value for answer if empty
	if(!isset($_POST['answer']) || empty($_POST['answer'])){
			$_POST['answer'][] = "";
	}
	//get the answer of the questions accordingly
	$id = $question->prn($_POST['course_id']);
	$sql  = "SELECT id,answer FROM question WHERE course_id = {$id} AND comprehension = ''";
	$found = question::find_by_sql($sql);
	//total number of questions
	$total_number = count($found);
	//var_dump($found);
	$answers = array();
	//define the answer array
	foreach($found as $one){
		//$answers[$one->id] = $key->answer;
		$answers[$one->id] = $one->answer;
	}
	//final answers
	$diff = count(array_diff_assoc($answers, $_POST['answer']));

	//the score variable is the actual score
	$score = $total_number - $diff;
	
	//update the students result
	$myscore->course_name = $_POST['course_name'];
	if(isset($_SESSION['student_name'])){
	$myscore->student_name = ucwords($_SESSION['student_name']);
	}
	if(isset($_SESSION['student_class'])){
	$myscore->class = $_SESSION['student_class'];
	}
	//if its a browser submit auto submit data
	if(isset($_POST['auto_submit'])) {
		$myscore->score  = isset($_SESSION['trackedScore']) ? $_SESSION['trackedScore'] : 0;
	}else{
		$myscore->score  = $score;
	}
	$myscore->course_id = $id;
	$myscore->max_score =$total_number;
	$myscore->score_date = date(time());
	if(isset($_POST['notice'])){
		$myscore->notice = $_POST['notice'];
	}
	//check if student has written the test before 
	$sql = "SELECT class , course_name FROM score WHERE (student_name  = '{$myscore->student_name}' AND course_name = '{$myscore->course_name}') AND class='{$myscore->class}' ";
	$result = $database->query($sql);
	if($database->affected_rows($result) == 1){
		echo "Sorry You Have Already Written This Test";
		return false;
	}else{
	$myscore->create();
	unset($_SESSION['trackedScore']);
	//return the students score
	echo "success/{$score}/{$total_number}";	
	}
}

//update course instruction
	if(isset($_POST['update_instruction']) && $_POST['update_instruction'] == true && $course->is_ajax()){
		$id = $course->prn($_POST['id']);
		$instruction = $database->escape_value(ucwords($_POST['instruction']));
		$sql= "UPDATE course SET course_instruction = '{$instruction}' WHERE id = {$id}";
	if($database->query($sql)){
		//update user activity
		$course->update_activity($id);
		echo true;
	}
	}

//update class
	if(isset($_POST['changeClass']) && $_POST['changeClass'] == true && $course->is_ajax()){
		$id = $course->prn($_POST['id']);
		$class = $_POST['class'];
		$sql = "UPDATE course SET class = '{$class}' WHERE id = {$id}";
		if($database->query($sql)){
			//update user activity
			$course->update_activity($id);
			echo 'Updated';
		}
	}

//update department
	if(isset($_POST['changeDepartment']) && $_POST['changeDepartment'] == true && $course->is_ajax()){
		$id = $course->prn($_POST['id']);
		$department = $_POST['department'];
		$sql = "UPDATE course SET department = '{$department}' WHERE id = {$id}";
		if($database->query($sql)){
			//update user activity
			$course->update_activity($id);
			echo 'Updated';
		}
	}

//update duration
		if(isset($_POST['update_duration']) && $_POST['update_duration'] == true && $course->is_ajax()){
		$id = $_POST['id'];
		$duration = $_POST['duration'];
		$split = explode(':' , $duration);
		if($split[0]  > 5){
			echo "Maximum Of 5 Hours Allowed";
			return false;
		}
		if($split[1]  > 59){
			echo "Cannot Have More Than 59 Minutes";
			return false;
		}
		//check if minutes is in range
		$range = range(0,60,5);
		if(!in_array($split[1] , $range)){
			echo "Value Should Be A Multiple Of 5. E.g 5,10,15...";
			return false;
		}
		
		if($split[1] < 5 && $split[0] == 0){
			echo "Test Duration Too Small";
			return false;
		}
		if($split[1] < 5 && $split[0] == 0){
			echo "Test Minute Duration Too Small";
			return false;
		}
		$sql = "UPDATE course SET duration = '{$duration}' WHERE id = {$id}";

		 if($database->query($sql)){
		 	$course->update_activity($id);
		echo true;
		 }

	}


//update month of the course 

	if(isset($_POST['update_date']) && $_POST['update_date'] == true && $course->is_ajax()){
		$id = $_POST['id'];
		$date = $_POST['date'];
		$split = explode('/' , $date);
		$today = strtotime(date('m/d/Y' , time()));
		$course_date = strtotime($date);
		if($split[1]  > 31){
			echo "Cannot Have More Than 31 Days";
			return false;
		}
		if($split[0]  > 12){
			echo "Cannot Have More Than 12 Months";
			return false;
		}
		if($today > $course_date){
			echo "You Have Selected A Date In The Past";
			return false;
		}
		$sql = "UPDATE course SET course_date = '{$date}' WHERE id = {$id}";

		 if($database->query($sql)){
		 	//update user activity
		 	$course->update_activity($id);
		 echo (int)'1';
		 return true;
		 }else{
		 	//error
		 }

	}


//get courses for the day 
if(isset($_POST['find_course']) && $_POST['find_course'] == true && $course->is_ajax()){
	//confirm the students name in the database and the student is in a class
	if($_POST['class_id'] !== "" && $_POST['class_id'] !== 0){
	$sql = "SELECT id FROM users WHERE (full_name = '{$_POST['name']}' AND department = '{$_POST['department']}' AND class_id  = {$_POST['class_id']}) AND (user_type = 'student') ";
	$query = $database->query($sql);
	if($database->affected_rows() !== 1){
		echo "<span class=\"error\">Sorry your Name is Not On The Database. Meet Your Class Teacher.</span>";
		return false;
	}else{
		//found name and class
		//get the class name
		$sql = "SELECT class_name,id FROM myclass WHERE id = {$_POST['class_id']}";
		$found_class = array_shift(myclass::find_by_sql($sql));
		//get the students department
		$sqldept = "SELECT department FROM users WHERE (full_name='{$_POST['name']}' AND class_id='{$found_class->id}' AND department  = '{$_POST['department']}') AND user_type='student'";
		$found_dept = array_shift(user::find_by_sql($sqldept));
	}

}else{
	echo "<span class=\"error\">Sorry Can't Find Your Name Or Class. Meet Your Class Teacher Thank You.</span>";
	return false;
}
	
	//get the class name 

	//display courses for student
	$date =  date('m/d/Y');
	$zero = "1";
	$sql = "SELECT * FROM course WHERE (class LIKE '%{$found_class->class_name}%' AND (department LIKE '%{$found_dept->department}%' OR department LIKE '%{$zero}%') AND course_date = '{$date}') AND activate = 1";
	$result  = course::find_by_sql($sql);

	//display only one
	if(count($result) > 1){
		echo "<span class=\"error\">Results Shows That You Currently Have multiple Tests Activated. Notify Your Invigilator Thank You.</span><br />";
		foreach($result as $one){
			echo "<span>" . ucwords($one->course_name). "</span><br />";
			echo "<br />";
		}
		return false;
	}

	//send result
	if(!empty($result)){
	$time = "";
	$hour = "";
	$minute ="";
	$output ="";
	foreach($result as $one){
		$time = "";
		$split = explode(":" , $one->duration);
		//get the hours
		if($split[0] !== "0"){
			if($split[0] =="1"){
				$hour = $split[0] . " hour ";
		}else{
			$hour = $split[0] . " hours ";
		}
	}else{
		$hour = "";
	}
		//get the minutes
		if($split[1] !== "0"){
			if($split[1] =="1"){
				$minute = $split[1] . " minute ";
		}else{
			$minute = $split[1] . " minutes ";
		}
	}else{
		$minute = "";
	}

	$time = $hour . $minute;


		$output .="<a href=\"starttest.php?course_id={$one->id}&class={$found_class->class_name}\" target=\"_BLANK\">"  . strtoupper($one->course_name)  . " - duration : " . $time . "</a>";
	}
}else{
	$output  = "<span class=\"error\">Sorry No Test Found</span>";
	}

	//store the students details for further reference
	$_SESSION['student_name'] = $_POST['name'];
	$_SESSION['student_class']  = $found_class->class_name;
	echo $output;

}	

//get student name based on search result 
if(isset($_POST['get_name']) && $_POST['get_name'] == true && $question->is_ajax()){
		$name =$_POST['student_name'];
		$sql = "SELECT full_name ,class_id, department FROM users WHERE (MATCH (full_name) AGAINST ('{$name}')) AND user_type='student' LIMIT 3";
		$result = user::find_by_sql($sql);
		if(!empty($result)){
		$output = "<span class=\"click_name\">Click On Your Name</span>";
		}else{
			$output = "<span><i class=\"fa fa-spin fa-spinner\"></i> Searching...</span>";
		}
	foreach($result as $one) {
		//get the department name 
		$foundDept = departments::find_by_id($one->department);
		//get class
		$sql = "SELECT class_name FROM myclass WHERE id = {$one->class_id}";
		$class = array_shift(myclass::find_by_sql($sql));
		$the_class = (!empty($class)) ? $class->class_name : "";

	$output .="<span class=\"show_name\" data-name=\"{$one->full_name}\" data-classid=\"{$one->class_id}\" data-department=\"{$one->department}\">{$one->full_name} - {$the_class} - {$foundDept->name}</span>";
	}		
	if(!empty($output)){
		echo $output;
	}
	}






//edit teacher details accordingly 
 if(isset($_POST['edit_user_id']) && $question->is_ajax()){
if($_POST['type'] == "teacher"){
 	//get teachers value
 	$teacher = user::find_by_id($_POST['edit_user_id']);

//get courses by teacher
$sql = "SELECT COUNT(id) AS id FROM course WHERE teacher_id = " . $teacher->id;

	$output  = "<div data-type=\"teacher\" class=\"edit\" data-id=\"$teacher->id\">";
	$output .="<input type=\"text\" name=\"username\" id=\"username\" value=\"$teacher->username\"  />";
	$output .= "<input type=\"text\" name=\"full_name\" id=\"full_name\" value=\"$teacher->full_name\"  />";
	$output .="<select name=\"title\" id=\"title\">";
	if($teacher->title == "Mr") {
	$output .="<option value=\"Mr\">Mr</option>";
	$output .="<option value=\"Mrs\">Mrs</option>";
	$output .="<option value=\"Miss\">Miss</option>";

	}else{
	$output .="<option value=\"Mrs\">Mrs</option>";
	$output .="<option value=\"Mr\">Mr</option>";
	$output .="<option value=\"Miss\">Miss</option>";
}
	$output .="</select>";
	$output .="</div><!-- end of edit teacher -->";
	$output .="<div class=\"seperate\">";
	$output .="<a data-id=\"$teacher->id\">Reset Password</a>";
	$output .="</div><!-- end of seperate -->";
	}


if($_POST['type'] == "student"){
 	//get teachers value
 	$student = user::find_by_id($_POST['edit_user_id']);
 	//get department
 	$sql = "SELECT * FROM departments ORDER BY id = {$student->department} DESC";
 	$foundDepts = departments::find_by_sql($sql);

	$output  = "<div data-type=\"student\" class=\"edit\" data-id=\"$student->id\">";
	$output .= "<input type=\"text\" name=\"full_name\" id=\"full_name\" value=\"$student->full_name\" placeholder=\"Full Name\"  />";
	$output .="<input type=\"text\" name=\"admission_number\" id=\"admission_number\" value=\"$student->admission_number\" placeholder=\"Admission Number\"  />";
		$output .="<select id=\"department\" name=\"department\">";
		foreach($foundDepts as $depts){
			$output .="<option value=\"{$depts->id}\">{$depts->name}</option>";
		}
	$output .="</select>";
	$output .="</div><!-- end of edit teacher -->";
	}
	$output .= "<button id=\"update_user\">update</button>";
	echo $output;
}

//update description
	if(isset($_POST['u_description']) && $_POST['u_description'] == true && $question->is_ajax()){
		if($admin->update_school_description($_POST['description']) == true){
			echo true;
		}else{
			echo "Error Updating School Description";
		}
	}


//update school name
	if(isset($_POST['us_name']) && $_POST['us_name'] == true && $question->is_ajax()){
		if($admin->update_school_name($_POST['school_name']) == true){
			echo true;
		}else{
			echo "Error Updating School Name";
		}
	}

//update class
	if(isset($_POST['update_class']) && $_POST['update_class'] == true && $question->is_ajax()){
			$myclass->id = $_POST['class_id'];
			$myclass->class_name = $_POST['new_class'];
			$myclass->teacher_id = $_POST['class_teacher'];
		//check if new name
			if(trim($myclass->class_name) !== trim($_POST['oldval'])){
				$sql = "UPDATE myclass SET class_name = '{$myclass->class_name}' WHERE id = {$myclass->id}";
				$database->query($sql);
				echo true;
				return false;
			}
		//check if teach is teaken
		$sql = "SELECT id,class_name FROM myclass WHERE teacher_id = '{$myclass->teacher_id}'";
		$foundTeacher = array_shift(myclass::find_by_sql($sql));
		//echo $sql;
		if($foundTeacher){
			echo "Sorry This Teacher is Already Assigned To " .  strtoupper($foundTeacher->class_name);
			return false;
		}
		//create class 
			$myclass->update();
			//update class id for the teacher
			$myclass->update_classID($myclass->teacher_id , $myclass->id);
			echo true;
	}	
//delete class
	if(isset($_POST['delete_class']) && $_POST['delete_class'] == true && $question->is_ajax()){
			$myclass->id = $_POST['class_id'];
			//delete students from database
			$sql ="DELETE FROM users WHERE class_id = {$myclass->id} AND user_type = 'student'";
			$database->query($sql);
			//delete class
			$myclass->delete();
			//update teachers class id to 0
			if($_POST['teacher_id'] !== "0"){
			$sql = "UPDATE users SET class_id = 0 WHERE id  = {$_POST['teacher_id']}";
			$database->query($sql);
			}
		}
	

//add class
	if(isset($_POST['add_class']) && $_POST['add_class'] == true && $question->is_ajax()){
		$myclass->class_name = strtoupper($_POST['new_class']);
		//check if class name already exist
		if($myclass->check_class_name($myclass->class_name) == true){
			echo "Class Name Already Exist";
			return false;
		}
		$myclass->teacher_id = $_POST['class_teacher'];
		//check if theacher is already assign to a different class
		if($myclass->teacher_id !== ""){
		$sql = "SELECT class_name FROM myclass WHERE teacher_id = {$myclass->teacher_id}";

		$found = array_shift(myclass::find_by_sql($sql));
		}else{
			$found = "";
		}
		if(empty($found)){
			//create class else alert
			$myclass->create();
			//update class id for the teacher
			if($myclass->teacher_id !== ""){
			$myclass->update_classID($myclass->teacher_id);
			}
			echo true;
		}else{
			echo "Sorry This Teacher Is Already Asssigned To {$found->class_name}";
			return false;
		}
	}

//delete course 
if(isset($_POST['del_course']) && $_POST['del_course'] == true && $question->is_ajax()){
	$course->id = $_POST['id'];
	$id = $course->id;
	//delete questions first
	$sql = "DELETE FROM question WHERE course_id = {$id}";
	$database->query($sql);
	//delete course second
	$course->delete();
}
//update answer
	if(isset($_POST['update_answer']) && $_POST['update_answer'] == true && $question->is_ajax()){
		$id = $question->prn($_POST['id']);
		$answer =  $question->prl($_POST['answer']);
		$sql="UPDATE question SET answer ='{$answer}' WHERE id = {$id}";
		if($database->query($sql)){
			echo "yes";
			//update user activity
			$course->update_activity($_POST['course_id']);
		}
	}
//delete question 
	if(isset($_POST['delete_question']) && $_POST['delete_question'] == true && $question->is_ajax()){
		$question->id = $_POST['id'];
		$question->delete();
		echo "yes deleted";
		//update activity
		$course->update_activity($_POST['course_id']);
	}


//update the option to a question 
	if(isset($_POST['update_option']) && $_POST['update_option'] == true && $question->is_ajax()){
		$id = $_POST['question'];
		$escape_value = $database->escape_value($_POST['new_value']);
		$_POST['new_value'] = $escape_value;

			switch ($_POST['option']) {
				case 'a':
					$value = $_POST['new_value'];
					$sql = "UPDATE question SET option_a  = '{$value}' WHERE id = {$id}";
					break;
					case 'b':
					$value = $_POST['new_value'];
					$sql = "UPDATE question SET option_b = '{$value}' WHERE id = {$id}";
					break;
					case 'c':
					$value = $_POST['new_value'];
					$sql = "UPDATE question SET option_c  = '{$value}' WHERE id = {$id}";
					break;
					case 'd':
					$value = $_POST['new_value'];
					$sql = "UPDATE question SET option_d  = '{$value}' WHERE id = {$id}";
					break;
			}
			$database->query($sql);
			$course->update_activity($_POST['course_id']);
	};

//update the question 
	if(isset($_POST['update_question']) && $_POST['update_question']== true && $question->is_ajax()){
		$id = $_POST['question_id'];
		$value = $database->escape_value($_POST['value']);
		$sql= "UPDATE question SET question='{$value}' WHERE id={$id}";
		if($database->query($sql)){
			echo true;
			//update user activity
			$course->update_activity($_POST['course_id']);
		}else{
			echo "Not Updated";
		}
	}


//update course name
	if(isset($_POST['name_edit']) && $_POST['name_edit']== true && $question->is_ajax()){
		
		$id = $_POST['id'];
		$name = $database->escape_value($_POST['name_value']);
		$sql= "UPDATE course SET course_name='{$name}' WHERE id={$id}";
		if($database->query($sql)){
			echo true;
			//this is a course so use its id directly
			$course->update_activity($_POST['id']);
		}else{
			echo "Not Updated";
		}
	}

//update question
if(isset($_POST['update_question_form']) && $_POST['update_question_form']== true && $question->is_ajax()){
		//get course id 
		$question->id      = 	$_POST['question_id'];
		$question->course_id= 	$_POST['course_id'];
		$question->question = htmlentities($_POST['question']);
		$question->answer = 	htmlentities($_POST['answer']);
		$question->option_a = htmlentities($_POST['option_a']);
		$question->option_b = htmlentities($_POST['option_b']);
		$question->option_c = htmlentities($_POST['option_c']);
		$question->option_d = htmlentities($_POST['option_d']);
		$question->special_character  = $question->prn($_POST['special_character']);
		if($question->answer == ""){
			echo "Please Select The Answer Option";
			return false;
		}
			if($question->update()){
				//update user activity
				$course->update_activity($question->course_id);
			echo true;
			}else{
			echo "No Change Occured!!!";
			}
	}

//add question 
	if(isset($_POST['submit_question']) && $_POST['submit_question']== true && $question->is_ajax()){
		//get course id 
		$question->course_id= $_POST['course_id'];
		$question->question = $database->escape_value($_POST['question']);
		$question->answer =   $database->escape_value($_POST['answer']);
		$question->option_a = $database->escape_value($_POST['option_a']);
		$question->option_b = $database->escape_value($_POST['option_b']);
		$question->option_c = $database->escape_value($_POST['option_c']);
		$question->option_d = $database->escape_value($_POST['option_d']);
		$question->special_character  = $question->prn($_POST['special_character']);
		if($question->answer == ""){
			echo "Please Select The Answer Option";
			return false;
		}
		if($question->if_avialable($question->question , $question->course_id , 'course_id') == true){	
		$question->create();
		//update user activity
		$course->update_activity($question->course_id);
		echo true;
		//question added succesfully
		}else{
			echo "Question Already Exist For This Test";
		}
	}

//add course
	if(isset($_POST['add_course']) && $user->is_ajax()){
		$course->course_name =  $database->escape_value(ucwords($_POST['course_name']));
		if($course->course_name == ""){
			echo "Please Enter A Test Name";
			return false;
		}
		$course->class = strtoupper($_POST['classes']);
		$course->department = $_POST['departments'];
		if($_POST['department'] == ""){
			echo "Please Select A Department";
			return false;
		}
		//prevent other processes if false
		if($course->if_avialable($course->course_name , $course->class , 'class') == false){
			echo "{$course->course_name} Already Created For {$course->class}";
			return false;
		}
		//else create the course
		$course->teacher_id = $_POST['teacher'];
		//get  teacher name
		$sql="SELECT title,full_name FROM users WHERE id  = {$course->teacher_id} LIMIT 1";
		$found = array_shift(user::find_by_sql($sql));
		$course->teacher = $found->title . " " . $found->full_name;
		$course->duration = $_POST['duration_h'] . ":" . $_POST['duration_mins'];
		//check that all course date input is correct
		if($_POST['day'] =="" || $_POST['month'] ==""){
			echo "Date Of Test Incomplete";
			return false;
		}
		

		$course->course_date = $_POST['month']."/".$_POST['day']. "/" . $_POST['year'];

		//check if the date the teacher is specifying i passed already
		$today = strtotime(date('m/d/Y'));
		$course_date = strtotime($course->course_date);
		if($today > $course_date){
			echo "You Have Selected A Date In The Past";
			return false;
		}
		$course->create();
		//course created succesfully
		//return the id of the course for the questions page
		echo $database->insert_id();

	}

//generete teachers class details
	if(isset($_POST['teacher_id']) && $_POST['teacher_id'] !== "" && $user->is_ajax()){
		$teacher = user::find_by_id($_POST['teacher_id']);
		if(!empty($teacher)){
		$available_class = myclass::find_all();
		$output = "<form>";
		$output .= "<input type=\"text\" value=\"{$teacher->username}\" data-id=\"{$teacher->id}\">";
		$output.="<select id=\"classValue\">";
		foreach($available_class as $one){
			$output.="<option value=\"{$one->id}\">" . ucfirst($one->class_name). "</option>";
		}
		$output.="</select>";
		$output.="<button>Assign</button>";
		$output.="</form>";
		echo $output;
	}
}

//login user
	if(isset($_POST['login']) && $_POST['login']  == true && $user->is_ajax()){
		$found = user::authenticate($_POST['username'] , $_POST['password']);
		if($found->id){
			$session->login($found);
			echo true;
		}else{
			echo false;
		}
	}

//register user
	if(isset($_POST['register']) && $_POST['register'] == true && $user->is_ajax()){
		$user->username = isset($_POST['username']) ? $_POST['username'] : "";	
		$user->password = isset($_POST['password']) ? $_POST['password'] : "";
		$user->user_type = $_POST['user_type'];
		$user->title = isset($_POST['title']) ? $_POST['title'] : "";
		$user->full_name = ucwords($_POST['full_name']);
		$user->admission_number = strtoupper($_POST['admission_number']);
		$user->class_id = isset($_POST['class']) ? $_POST['class'] : "";
		$user->department = isset($_POST['department']) ? $_POST['department'] : "";
		if($user->user_type == "student"){
		$user->teacher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
		}else{
			$user->teacher_id = "";
		}
		
		$names = explode(' ', $user->full_name);
		//check class in their
		if($user->user_type == "student" && $user->class_id == ""){
			echo "Please Create A Class First";
			return false;
		}
		//check if that name already exist for that class
		if($user->user_type == "student"){
			$sql = "SELECT full_name FROM users WHERE (full_name = '{$user->full_name}'AND department = '{$user->department}' AND class_id = {$user->class_id}) AND user_type='student'";
			$query = $database->query($sql);
			if($database->affected_rows() == 1){
				echo "This Student's Name Already Exist For This Class And Department";
				return false;
			}
		}
		//check full name
		if(!isset($names[1])){
			echo "Full Name Not Complete";
			return false;
		}
		//check that department is selected
		if($user->user_type == "student" && $user->department == ""){
			echo "Please Select A Department";
			return false;
		}
		$user->new_user();
		if($user->return_error() !== false){
			//ther is an error
			echo $user->return_error();
		}else{
			//no error
			echo true;
			//dont login user cause only admin has to right to create user
		}
	}


?>
