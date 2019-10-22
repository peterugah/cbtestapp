<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once('session.php');
require_once('database.php');
require_once('generalFunc.php');

class departments extends generalFunc {
	
	protected static $table_name="departments";
	protected static $db_fields = array('id','name');
	protected static $checkfield = "name";
	public $id;
	public $name;
}
$departments = new departments();
?>
