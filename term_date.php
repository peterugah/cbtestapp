<?php
	require_once('include/session.php');
	if(!isset($_SESSION['user_type']) && $_SESSION['master_admin'] == 0){
		$generalFunc->redirect_to('regadmin.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<?php if(isset($_SESSION['full_name'])) : ?>
	<title>welcome <?php echo $_SESSION['full_name']; ?></title>
<?php else : ?>
	<title>welcome</title>
<?php endif; ?>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/script.js"></script>
</head>
<body>
	 <div id="formatermdate">
	 <span id="tdate">Specify Next Session's Date </span>
		<select name="term_day" id="term_day">
		<?php for($x=0; $x<32; $x++) :?>
			<?php if($x < 10)  : ?>
				<option value="<?php echo "0$x"; ?>"><?php echo $x; ?></option>
			<?php else : ?>
				<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
			<?php endif; ?>
		
		<?php endfor; ?>
		</select>
		<select name="term_month" id="term_month">
		<?php
			$month = array('01'=>'jan','02'=>'feb','03'=>'march','04'=>'april','05'=>'may','06'=>'june','07'=>'july','08'=>'aug','09'=>'sept','10'=>'oct','11'=>'nov','12'=>'dec');
		?>
		<?php foreach($month as $key=>$val) :?>
		<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
		<?php endforeach; ?>
		</select>
		<select name="term_Year" id="term_Year">
		<?php
			$year = (int)date('Y',time());
		?>
		<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
		<option value="<?php echo $year + 1; ?>"><?php echo $year + 1; ?></option>
		</select>
		<div>
		<button type="button" id="update_termdate">Update</button>
		</div>
	</div><!-- end of format term date --> 
</body>
</html>