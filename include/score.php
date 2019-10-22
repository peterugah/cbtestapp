<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');

class myscore extends generalFunc {
	
	protected static $table_name="score";
	protected static $db_fields = array('id','course_name' , 'student_name','score' , 'class' , 'score_date' , 'course_id' , 'max_score', 'notice');
	protected static $checkfield = "student_name";
	public $id;
	public $course_name;
	public $student_name;
	public $score;
	public $class;
	public $score_date;
	public $course_id;
	public $max_score;
	public $notice;
}
$myscore = new myscore();
?>
