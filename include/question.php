<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');
class question extends generalFunc {
	
	protected static $table_name="question";
	protected static $db_fields = array('id', 'course_id', 'option_a', 'option_b' , 'option_c' , 'option_d' , 'answer' , 'question' , 'special_character','comprehension','instruction' , 'complex');
	protected static $checkfield = "question";
	public $id;
	public $course_id;
	public $question;
	public $option_a;
	public $option_b;
	public $option_c;
	public $option_d;
	public $answer;
	public $comprehension;
	public $instruction;
	public $complex;
	public $special_character;


	public function limitComprehension(){
		$sql = "SELECT COUNT(id) AS id FROM " . self::$table_name . " WHERE comprehension IS NOT NULL and course_id = {$this->course_id}";
		$result = self::find_by_sql($sql);
		$id = array_shift($result);
		return $id->id;
	}

	public function comprehensionExist(){
		global $database;
		$id = $this->course_id;
		$comprehension = $this->comprehension;
		$instruction = $this->instruction;
		$sql  = "SELECT id FROM " . self::$table_name . " WHERE comprehension = '{$comprehension}'AND instruction = '{$instruction}' AND course_id = {$id}";
		$query = $database->query($sql);
		if($database->affected_rows() >= 1){
			return true;
		}else{
			return false;
		}
	}
	

	public static function get_sum_question($course_id){
		$sql= "SELECT COUNT(id) AS id FROM " . self::$table_name . " WHERE course_id = {$course_id} and comprehension =TRIM(\"\") ";
		$result = self::find_by_sql($sql);
		$id = array_shift($result);
		return $id->id;
	}
}

$question = new question();


/*
$question->course_id = 1;
$question->question = "what is your name";
$question->answer = "a";
$question->option_a = "peter";
$question->option_b = "tunde";
$question->option_c = "joy";
$question->option_d = "mike";
if($question->if_avialable($question->question , $question->course_id , 'course_id') == true){
$question->create();
}else{
	echo "question already exits";
}
*/
?>
