<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');
class User extends generalFunc {
	
	protected static $table_name="users";
	protected static $checkfield="full_name";
	protected static $db_fields = array('id', 'username', 'user_type', 'reg_date' , 'last_seen' , 'title' , 'is_online' , 'full_name' , 'admission_number' , 'master_admin' , 'class_id' , 'teacher_id' , 'department'  ,'ignore_transfer','reject','new','former_class','acknowledge');
	
	public $id;
	public $username;
	public $password;
	public $user_type;
	public $last_seen;
	public $reg_date;
	public $title;
	public $is_online;
	public $full_name;
	public $admission_number;
	public $master_admin;
	public $class_id;
	public $teacher_id;
	public $department;
	public $ignore_transfer;
	public $reject;
	public $new;
	public $former_class;
	public $acknowledge;

  public function full_name() {
    if(isset($this->first_name) && isset($this->last_name)) {
      return $this->first_name . " " . $this->last_name;
    } else {
      return "";
    }
  }

  public static function check_if_student($checkval , $value){
		$value = strtolower($value);
		$sql = "SELECT {$checkval} FROM " . self::$table_name . " WHERE " . self::$checkfield . "= '{$value}' AND user_type = 'student' LIMIT 1";
		$result = array_shift(static::find_by_sql($sql));
		return $result;
	}

	public static function authenticate($username="", $password="") {
    global $database;
    $username = $database->escape_value($username);
    $password = hash('sha512' , $database->escape_value($password));

    $sql  = "SELECT * FROM users ";
    $sql .= "WHERE username = '{$username}' ";
    $sql .= "AND hashed_password = '{$password}' ";
    $sql .= "LIMIT 1";
    $result_array = self::find_by_sql($sql);
		$output =  !empty($result_array) ? array_shift($result_array) : false;
		//update the is online and last seen 
		if($output !== false){
		self::update_last_seen($output->id);
		return $output;
		}
	}


	private function count_admin($value="admin"){
		global $database;
		$sql="select COUNT(id) from " . self::$table_name . " WHERE user_type='{$value}'";
		$result = $database->query($sql);
		return array_shift($database->fetch_array($result));
	}

	private static function update_last_seen($id){
		global $database;
		$time = time();
		$sql = "UPDATE " . self::$table_name . " SET last_seen ={$time} ,  is_online = 1 WHERE id = {$id}";
		$database->query($sql);
	}


	public static function update_logout($id){
		global $database;
		$sql = "UPDATE " . self::$table_name . " SET is_online = 0 WHERE id={$id}";
		$database->query($sql);
	}
	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }
  
  public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
  }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    $object = new self;
		// Simple, long-form approach:
		// $object->id 				= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
	  $sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
	  $sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
	  if($database->query($sql)) {
	    $this->id = $database->insert_id();
	    return true;
	  } else {
	    return false;
	  }
	}

	public function new_user(){
		global $database;
		$user_type = $database->escape_value($this->user_type);
		//if stundent, sturcture the username as the index count
		if($user_type == "student"){
			$sql = " SELECT MAX(id) FROM users WHERE user_type = 'student'";
			//echo $sql;
			$query  = $database->query($sql);
			$result = $database->fetch_array($query);
			if(!empty($result)){
			///	print_r($result);
			$value  = array_shift($result);
			$username  = (int)$value;
			$username++;
			}else{
				$username = 1;
			}
		}else{
			$username = $database->escape_value($this->username);
		}

		//if username alaredy exit
		if($this->if_exit($username) == true){
			$this->error[] = "Username Already Exist.";
			return false;
		}
		
		//if admin is more than 3
		if($this->count_admin() >= 3 && $user_type !== "student" && $user_type !== "teacher"){
			$this->error[] = "Sorry You Cannot Register More Than 2 Administrators";
			return false;
		}
		if($user_type !== "student"){
		$password = $database->escape_value(hash('sha512' , $this->password));
		
		}else{
			$password="";
		}

		$last_seen = $reg_date =time();
		$full_name = $this->prl($this->full_name);
		if($user_type !== "student"){
		$title = $this->prl($this->title);
		}else{
			$title = "";
		}
		if($user_type == "student" && $this->admission_number !==""){
		$admission_number = $database->escape_value($this->admission_number);
		//if exit admission number
		$sql = " SELECT id FROM users WHERE admission_number  = '{$admission_number}' ";
		$query = $database->query($sql);
		if($database->affected_rows() == 1){
			$this->error[] ="Student With Admission Number ". strtoupper($admission_number) . " Already Exist";
			return false;
		}
		
	}else{
			$admission_number = "";
		}
	
		if($this->master_admin !==""){
			$master_admin = $this->prn($this->master_admin);
		}
		$teacher_id = $this->teacher_id;
		$class_id = $this->class_id;

		if($this->user_type == "teacher" || $this->user_type == "admin"){
			$new = 1;
		}else{
			$new = 0;
		}
		$sql = "INSERT INTO " . self::$table_name . "(username , hashed_password , user_type , last_seen , full_name , title,reg_date, admission_number , master_admin , teacher_id , class_id,department,new) VALUES ('{$username}' , '{$password}' , '{$user_type}' , '{$last_seen}' , '{$full_name}' , '{$title}','{$reg_date}' , '{$admission_number}' ,' {$master_admin}' , '{$teacher_id}' , '{$class_id}' , '{$this->department}' , {$new})";
		//echo $sql;
		$database->query($sql);
	}

	public function if_exit($username){
		global $database;
		$sql= "SELECT id FROM users WHERE username ='{$username}' LIMIT 1";
		$database->query($sql);
		if($database->affected_rows() == 1){
			return true;
		}else{
			return false;
		}
	}

	public function update() {
	  global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
	  $sql = "DELETE FROM ".self::$table_name;
	  $sql .= " WHERE id=". $database->escape_value($this->id);
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}

}
$user = new User();
?>