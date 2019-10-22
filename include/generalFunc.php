<?php
require_once('database.php');
class generalFunc {

		public $error = array();
		protected static $table_name;
		protected static $db_fields = array();
		protected static $checkfield;

	public function __construct(){
		date_default_timezone_set('Africa/Lagos');
	}

		public function ifMathJax($value){
			$regex = "/($)+([*]) + ([$]+)/";
			if(preg_match($regex , $value)){
				return true;
			}
		}

		public function excapeQuote($value = ""){
			$string =  str_replace("\'" , "'",  $value);
			$string = str_replace('\"' , '"' , $string);
			$string = str_replace("\\\\", "\\", $string);
			$string = str_replace('\r\n' , "" , $string);	
			$string = str_replace('\n' , "" , $string);	
			return $string;
		}

		public function wirte_score_log($content){
			if(file_exists('scorelog.txt')){
				$previous = file_get_contents('scorelog.txt');
				$append = $content . $previous;
				file_put_contents('scorelog.txt', $append);
			}else{
				$handler = fopen('scorelog.txt' , 'w');
				fwrite($handle , $content);
				fclose($handle);
				chmod('scorelog.txt' , 0766);
			}
		}	

		public function size_as_text() {
		if($this->size < 1024) {
			return "{$this->size} bytes";
		} elseif($this->size < 1048576) {
			$size_kb = round($this->size/1024);
			return "{$size_kb} KB";
		} else {
			$size_mb = round($this->size/1048576, 1);
			return "{$size_mb} MB";
		}
	}

	public static function check_if($checkval , $value){
		$value = strtolower($value);
		$sql = "SELECT {$checkval} FROM " . static::$table_name . " WHERE " . static::$checkfield . "= '{$value}' LIMIT 1";
		$result = array_shift(static::find_by_sql($sql));
		return $result;
	}

	public function activateUrlStrings($str){
    $find = array('`((?:https?|ftp)://\S+[[:alnum:]]/?)`si', '`((?<!//)(www\.\S+[[:alnum:]]/?))`si');
    $replace = array('<a href="$1" target="_blank">$1</a>', '<a href="http://$1" target="_blank">$1</a>');
    return preg_replace($find,$replace,$str);
	}


		public function prl($value){
			return preg_replace("/[^A-Za-z., ]/",'',$value);
		}
		public function prn($value){
			return preg_replace("/[^0-9]/",'',$value);
		}


		public function return_error(){
			if($this->error){
			return join('<br />' , $this->error);
			}else{
				return false;
			}
	}
		function is_ajax(){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
			}else{
				return false;
			}
		}


		function strip_zeros_from_date( $marked_string="" ) {
		  // first remove the marked zeros
		  $no_zeros = str_replace('*0', '', $marked_string);
		  // then remove any remaining marks
		  $cleaned_string = str_replace('*', '', $no_zeros);
		  return $cleaned_string;
		}

		function redirect_to( $location = NULL ) {
		  if ($location != NULL) {
		    header("Location: {$location}");
		    exit;
		  }
		}

		function output_message($message="") {
		  if (!empty($message)) { 
		    return "<p class=\"message\">{$message}</p>";
		  } else {
		    return "";
		  }
		}

		function __autoload($class_name) {
			$class_name = strtolower($class_name);
		  $path = LIB_PATH.DS."{$class_name}.php";
		  if(file_exists($path)) {
		    require_once($path);
		  } else {
				die("The file {$class_name}.php could not be found.");
			}
		}

		function include_layout_template($template="") {
			include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
		}

		function log_action($action, $message="") {
			$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
			$new = file_exists($logfile) ? false : true;
		  if($handle = fopen($logfile, 'a')) { // append
		    $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
				$content = "{$timestamp} | {$action}: {$message}\n";
		    fwrite($handle, $content);
		    fclose($handle);
		    if($new) { chmod($logfile, 0755); }
		  } else {
		    echo "Could not open log file for writing.";
		  }
		}

		function datetime_to_text($datetime="") {
		  $unixdatetime = strtotime($datetime);
		  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
		}


	// Common Database Methods
	public static function find_all() {
		return static::find_by_sql("SELECT * FROM ".static::$table_name);
 	 }
  
  public static function find_by_id($id=0) {
    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = static::instantiate($row);
    }
    return $object_array;
  }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".static::$table_name;
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

		public static function count_filed($field , $value) {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".static::$table_name . " WHERE {$field} = '{$value}'";
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    $object = new static;
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
	  foreach(static::$db_fields as $field) {
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
	  $sql = "INSERT INTO ".static::$table_name." (";
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

	private function if_exit($username){
		global $database;
		$sql= "SELECT id FROM users WHERE username ='{$username}' LIMIT 1";
		$database->query($sql);
		if($database->affected_rows() == 1){
			return true;
		}else{
			return false;
		}
	}

	public static function if_avialable($value="" ,$value2="" ,  $filed2 = "" , $condition=""){
		global $database;
		if($filed2 == ""){
			//checking 1 field;
		$sql = "SELECT id FROM " . static::$table_name . " WHERE " . static::$checkfield .   " = '{$value}' LIMIT 1";
		}elseif($condition == true){
			//one or two fields
			$sql = "SELECT id FROM " . static::$table_name . " WHERE " . static::$checkfield .   " = '{$value}' OR {$filed2} = {$value2} LIMIT 1";

		}else{
			//checking two fields
			$sql = "SELECT id FROM " . static::$table_name . " WHERE " . static::$checkfield .   " = '{$value}' and {$filed2} = '{$value2}' LIMIT 1";
		}
		$database->query($sql);
		if($database->affected_rows() == 1){
			return false;
		}else{
			return true;
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
		$sql = "UPDATE ".static::$table_name." SET ";
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
	  $sql = "DELETE FROM ".static::$table_name;
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
$generalFunc = new generalFunc();

?>