<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');
class myclass extends generalFunc {
	
	protected static $table_name="myclass";
	protected static $db_fields = array('id', 'teacher_id', 'class_name');
	protected static $checkfield = "class_name";
	
	public $id;
	public $teacher_id;
	public $class_name;

	public function check_class_name($name){
		global $database;
		$sql = "SELECT id FROM myclass WHERE class_name = '{$name}'";
		$query  = $database->query($sql);
		if($database->affected_rows() == 1){
			//classname is taken
			return true;
		}
	}

	public function update_classID($Teacher_id , $class_id=""){
		global $database;
		if($class_id !== ""){
		$sql ="UPDATE users SET class_id = {$class_id} WHERE id={$Teacher_id} AND user_type = 'teacher'";
		}else{
		$sql ="UPDATE users SET class_id = {$database->insert_id()} WHERE id={$Teacher_id} AND user_type = 'teacher'";
		}
		if($database->query($sql)){
			return true;
		}

	}
	
}
$myclass = new myclass();

?>
