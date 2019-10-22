<?php
require_once('generalFunc.php');

class Photograph extends generalFunc {
	
	public $filename;
	public $type;
	public $size;
	public $caption;
	public $extention;
	public $new_name;
	protected $max_size = 1000000000000000000000000;
	
	public 		$temp_path;
  	protected 	$upload_dir="uploads";
  	public 		$errors=array();
  
  protected $upload_errors = array(
		// http://www.php.net/manual/en/features.file-upload.errors.php
	  UPLOAD_ERR_OK 				=> "No errors.",
	  UPLOAD_ERR_INI_SIZE  			=> "Larger than upload_max_filesize.",
	  UPLOAD_ERR_FORM_SIZE 			=> "Larger than form MAX_FILE_SIZE.",
	  UPLOAD_ERR_PARTIAL 			=> "Partial upload.",
	  UPLOAD_ERR_NO_FILE 			=> "No file.",
	  UPLOAD_ERR_NO_TMP_DIR 		=> "No temporary directory.",
	  UPLOAD_ERR_CANT_WRITE 		=> "Can't write to disk.",
	  UPLOAD_ERR_EXTENSION 			=> "File upload stopped by extension."
	);

public function resize($target, $newcopy, $w, $h, $ext) {
	if(!$w){
		$w = 100;
	}
	if(!$h){
		$h = 100;
	}

	if(!$newcopy){
		//$newcopy = 
	}
    list($w_orig, $h_orig) = getimagesize($target , $quality = 100);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) {
           $w = $h * $scale_ratio;
    } else {
           $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 
      $img = imagecreatefrompng($target);
    } else { 
      $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    imagejpeg($tci, $newcopy, $quality);
}

	// Pass in $_FILE(['uploaded_file']) as an argument
  public function attach_file($file) {
		// Perform error checking on the form parameters
		if(!$file || empty($file) || !is_array($file)) {
		  // error: nothing uploaded or wrong argument usage
		  $this->errors[] = "No file was uploaded.";
		  return false;
		} elseif($file['error'] != 0) {
		  // error: report what PHP says went wrong
		  $this->errors[] = $this->upload_errors[$file['error']];
		  return false;
		} else {
			// Set object attributes to the form parameters.
		  $this->temp_path  = $file['tmp_name'];
		  $this->filename   = basename($file['name']);
		  $this->type       = $file['type'];
		  $this->size       = $file['size'];
		  $this->extention 	= pathinfo($this->filename , PATHINFO_EXTENSION);
			// Don't worry about saving anything to the database yet.
			return true;

		}
	}
  
	public function save() {
			
			// Can't save if there are pre-existing errors
		 	if(!empty($this->errors)) { return false; }
		  
		
		  // Can't save without filename and temp location
		  if(empty($this->filename) || empty($this->temp_path)) {
		    $this->errors[] = "The file location was not available.";
		    return false;
		  }

		  //check file size
		  if($this->size > $this->max_size){
		  	$this->error[] = "the file is greater than 200kb";
		  	return false;
		  }
			
			// Determine the target_path
		$target_path ="../admin/school_logo." .$this->extention;
		$png ="../admin/school_logo.png";
		$jpg ="../admin/school_logo.jpg";
		  
		  // Make sure a file doesn't already exist in the target location by deleting it 
		  if(file_exists($target_path)) {
		    $this->destroy($target_path);
		  }elseif(file_exists($png)){
		  	$this->destroy($png);
		  }elseif(file_exists($jpg)){
		  	$this->destroy($jpg);
		  }
		
			// Attempt to move the file 
			if(move_uploaded_file($this->temp_path, $target_path)) {
		  	// Success
				$this->new_name = $target_path;
				return true;
			} else {
				// File was not moved.
		    $this->errors[] = "The file upload failed, possibly due to incorrect permissions on the upload folder.";
		    return false;
			}
	}
	
	public function destroy($target_path) {
		// First remove the database entry
			return unlink($target_path) ? true : false;
	}	
}

$photo = new photograph();
?>

