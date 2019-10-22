	<?php
	require_once('session.php');
	require_once('database.php');
  require_once('class.php');
  require_once('score.php');
  require_once('fpdf/fpdf.php');
	if(!isset($_SESSION['user_type'])){
		$generalFunc->redirect_to('../login.php');
	}
  //download scores by admin
if($_SESSION['user_type'] == "admin" && isset($_GET['type']) && $_GET['type'] == "result"){
  //specify the right sql
  if(isset($_GET['user']) && $_GET['user'] !== ""){
    //get result by student name
    $sql = "SELECT * FROM score WHERE student_name = '{$_GET['user']}'";
    $FILENAME = "{$_GET['user']}_Result.pdf";
    $TITLE = "{$_GET['user']} Test Results";
  }if(isset($_GET['class']) && isset($_GET['user'])){
    //get result by student name and class
      $sql = "SELECT * FROM score WHERE student_name = '{$_GET['user']}' AND class= '{$_GET['class']}'";
      $FILENAME = "{$_GET['user']}_{$_GET['class']}_Result.pdf";
      $TITLE = "{$_GET['user']} {$_GET['class']} Test Results";
  }

  //download by test
  if(isset($_GET['test']) && $_GET['test'] !== ""){
      $sql = "SELECT * FROM score WHERE course_name = '{$_GET['test']}'";
      $FILENAME = "{$_GET['test']}_Result.pdf";
      $TITLE = "{$_GET['test']} Test Results";
  }
  if(!isset($_GET['class']) && !isset($_GET['user']) && !isset($_GET['test'])){
    //get all test results
    $sql = "SELECT * FROM score";
    $FILENAME = "All_Test_Results.pdf";
    $TITLE = "All Test Results";
  }
  //get all scores
  $foundScores = myscore::find_by_sql($sql);

  if(empty($foundScores)){
    echo "<span class=\"error\" style=\"color:red;\">No Test Result Found</span>";
    return false;
  }
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(10,10,$TITLE, 0,1,'L');
  $pdf->SetFont('Arial','',10);
  foreach($foundScores as $one){
    $pdf->Cell(0,5,strtoupper($one->student_name) . " - " . strtoupper($one->class) . " - " . ucwords($one->course_name) . " - " . date('j M, Y' , $one->score_date) . " at " . date('g:ia' , $one->score_date) . " - " . $one->score . "/" . $one->max_score,0,1);
      $pdf->Ln();
  }
  //DOWNLOAD THE FILE
  $pdf->Output('D' , $FILENAME);
}//end for mater admin
//download scores by teacher
elseif($_SESSION['user_type'] == "teacher" && $_SESSION['class_id'] !== 0 && isset($_GET['type']) && $_GET['type'] == "result"){
  //get class for the teacher
  $sql = "SELECT class_name FROM myclass WHERE id = {$_SESSION['class_id']}";
  $class = array_shift(myclass::find_by_sql($sql));
  //get scores for teacher
    //specify the right sql
  if(isset($_GET['user']) && $_GET['user'] !== ""){
    //get result by student name
    $sql = "SELECT * FROM score WHERE student_name = '{$_GET['user']}' AND class = '{$class->class_name}'";
    $FILENAME = "{$_GET['user']}_Result.pdf";
    $TITLE = "{$_GET['user']} Test Results";
  }if(isset($_GET['class']) && isset($_GET['user'])){
    //get result by student name and class
      $sql = "SELECT * FROM score WHERE (student_name = '{$_GET['user']}' AND class= '{$_GET['class']}') AND class= '{$class->class_name}'";
      $FILENAME = "{$_GET['user']}_{$_GET['class']}_Result.pdf";
      $TITLE = "{$_GET['user']} {$_GET['class']} Test Results";
  }
 //download by test
  if(isset($_GET['test']) && $_GET['test'] !== ""){
      $sql = "SELECT * FROM score WHERE course_name = '{$_GET['test']}' AND class = '{$class->class_name}'";
      $FILENAME = "{$class->class_name}_{$_GET['test']}_Result.pdf";
      $TITLE = "{$class->class_name} {$_GET['test']} Test Results";
  }
  if(!isset($_GET['class']) && !isset($_GET['user']) && !isset($_GET['test'])){
    //get all test results
    $sql = "SELECT * FROM score WHERE class= '{$class->class_name}' ORDER BY student_name ASC";
    $FILENAME = "{$class->class_name}_Test_Results.pdf";
    $TITLE = "{$class->class_name} Test Results";
  }
  $foundScores = myscore::find_by_sql($sql);



  if(empty($foundScores)){
    echo "<span class=\"error\" style=\"color:red;\">No Test Result Found</span>";
    return false;
  }
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(10,10,$TITLE, 0,1,'L');
  $pdf->SetFont('Arial','',10);
  foreach($foundScores as $one){
    $pdf->Cell(0,5,strtoupper($one->student_name) . " - " . ucwords($one->course_name) . " - " . date('j M, Y' , $one->score_date) . " at " . date('g:ia' , $one->score_date) . " - " . $one->score . "/" . $one->max_score,0,1);
      $pdf->Ln();
  }
  //DOWNLOAD THE FILE
  $pdf->Output('D' , $FILENAME);
  }
	?>