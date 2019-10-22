<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');
class admin extends generalFunc {
	
	protected static $table_name="admin_panel";
	protected static $db_fields = array('school_name', 'school_logo', 'description','term_date');
	
	public $school_name;
	public $school_logo;
	public $description;
	public $term_date;

	

	public function update_school_name($name){
		global $database;
		$name = ucwords($this->prl($name));
		if($this->check_profile() == false){
		$sql = "INSERT INTO " . self::$table_name . "(school_name) VALUES('{$name}')";
		}else{
		$sql = "UPDATE " . self::$table_name . " SET school_name = '{$name}'";
		}
		if($database->query($sql)){
			return true;
		}else{
			return false;
		}
	}


	private function check_profile(){
		global $database;
		$sql= "SELECT * FROM admin_panel";
		$query = $database->query($sql);
		if($database->affected_rows() == 1){
			return true;
		}else{
			return false;
		}
	}

	public function update_school_description($name){
		global $database;
		$name = $database->escape_value($name);
		if($this->check_profile() == false){
		$sql = "INSERT INTO " . self::$table_name . "(description) VALUES('{$name}')";
		}else{
			$sql = "UPDATE " . self::$table_name . " SET description = '{$name}'";
		}
		if($database->query($sql)){
			return true;
		}else{
			return false;
		}
	}

	public function update_school_logo($file){
		global $database;
		$sql = "UPDATE " . self::$table_name . " SET school_logo = '{$file}'";
		if($database->query($sql)){
			return true;
		}else{
			return false;
		}
	}
}
$admin = new admin();

?>
