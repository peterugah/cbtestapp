<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');
class course extends generalFunc {
	
	protected static $table_name="course";
	protected static $db_fields = array('id', 'teacher_id', 'class', 'duration', 'course_date'  , 'course_name', 'course_instruction' , 'teacher' , 'activate' , 'status','department','admin','update_user','update_date');
	protected static $checkfield = "course_name";
	public $id;
	public $teacher_id;
	public $class;
	public $duration;
	public $course_date;
	public $course_name;
	public $course_instruction;
	public $teacher;
	public $activate;
	public $status;
	public $update_user;
	public $update_date;
	public $department;
	public $admin;

	//format durations if not above 10 minutes and hour is >= 1
	public function below_duration_and_1_hour($format , $minute_check ){
			if((int)$format[0] >= 1 && (int)$format[1] <= $minute_check){
			$initminute  = (int)$format[1];
			$knowndiff = (60 - $minute_check) + $initminute;
			//check if eactly one hour left
			if($knowndiff == 60 && (int)$format[0] >= 1){
				$returned = "{$format[0]}:00:00";
			}
			//check if less than 60 minutes
			if($knowndiff < 60 ){
				//format the minue
				$min = strlen($knowndiff == 1) ? "0{$knowndiff}" : $knowndiff;
				$subs  = (int)$format[0] - 1;
				$returned = "0{$subs}:{$min}:00";
			}	
		}else{
			$returned = "";
		}
		return $returned;
	}
	public function update_activity($id){
		global $database;
		//if admin
		$time = strtotime('now');
		if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin"){
			if($_SESSION['master_admin'] == 1){
			$sql = "UPDATE course SET update_user='{$_SESSION['title']} {$_SESSION['full_name']}',update_date={$time},admin = 2 WHERE id={$id}";
			}else{
			$sql = "UPDATE course SET update_user='{$_SESSION['title']} {$_SESSION['full_name']}',update_date={$time},admin = 1 WHERE id={$id}";
			}
		}
		//if teacher
		if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "teacher"){
		$sql = "UPDATE course SET update_user='{$_SESSION['title']} {$_SESSION['full_name']}',update_date={$time},admin = 0 WHERE id={$id}";
		}
		$database->query($sql);
	}
}
$course = new course();
?>
