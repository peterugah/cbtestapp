<?php
require_once('include/session.php');
require_once('include/generalFunc.php');
if($session->is_logged_in() == false){
	echo "Please Login And Try Again";
	die();
}
unset($_SESSION['instruction']);
$compId = "";
$instruction = "";
$comprehension = "";
if(isset($_SESSION['compId']) && isset($_SESSION['comprehension']) && isset($_SESSION['instruction'])){
	$compid = $_SESSION['compId'];
	$instruction = $generalFunc->excapeQuote($_SESSION['instruction']);
	$comprehension = $generalFunc->excapeQuote($_SESSION['comprehension']);
}elseif(isset($_GET['compid']) && (!isset($_SESSION['compId']) || !isset($_SESSION['Comprehension']) || !isset($_SESSION['instruction']))){
	//fetch from the database;
	require 'include/question.php';
	$found = $question->find_by_id($_GET['compid']);
	if($found){
		$compId = $found->id;
		$instruction = $generalFunc->excapeQuote($found->instruction);
		$comprehension = $generalFunc->excapeQuote($found->comprehension);
	}else{
		//error loading content
		echo "<span class=\"error\">Comprehension Not Found.</span>".
		die();
	}
}else{
	echo "<span class=\"error\">Could Not Load Up Comprehension. Please Press The Back Button And Try Again</span>".
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Comprehension Edit</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js?date=<?= date(time()) ?>""></script>
	<script type="text/javascript" src="script/tinymce/tinymce.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<?php require 'script/mathjax.php'; ?>	
	<script>
			//initialize tinymce
			//menubar 'file edit view',
			tinymce.init({
				selector : '#comprehension',
				 menubar: false,
				  theme: 'modern',
				  width: '100%',
	   		 	  height: '300',
	   		 	  statusbar: false,
	   		 	   browser_spellcheck: true,
	  				contextmenu: false,
	   		 	 // content_css: 'css/style.css',
				  plugins : 'advlist lists charmap print preview table',
				  toolbar:  'undo redo | styleselect | bold italic | alignleft aligncenter alignright table'
	 
			});

		</script>
</head>
<body>

<div class="container">
<div id="toolbar">
<button type="submit" id="update_comprehension"><i class="fa fa-plus"></i> Update</button>
<button type="button" class="PreviewWysiwyg"><i class="fa fa-eye"></i> Preview</button>
<button type="button" class="gobackUpdate"><i class="fa fa-arrow-left"></i> Back</button>
</div><!-- end of toolbar -->
<div class="godown">
<form action="comprehension" name="updateComprehension" class="wysiwyg" id="updateComprehension" data-id="<?= $compId ?>">
<div class="specifyInstruction">
<label for="instruction"><i class="fa fa-info"></i> Specify An Instruction</label>
<textarea name="instruction" id="instruction"><?= $instruction ?></textarea>
</div>
<textarea name="comprehension" id="comprehension"><?= $comprehension ?></textarea>
</form>
</div>
<div id="previewContent">
</div>
</div><!-- end of container -->


	<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
</body>

</html>