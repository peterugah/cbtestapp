<script>
$(function(){
	//show default preview
	$content = $('#questionTextArea').val();
	$('#previewContent').html($content);
});
//triger click
//check if the user change the answer
window.isAnswerChange = 0;
$(document).on('change' , '#correctAnswer' , function(){
	isAnswerChange++;
});
//populate the question tracker
	$(document).on('click' , '#opts button' , function(){
		var $id = $(this).attr('id');
		var $tracker = window.$questionTracker[$id];
		if($tracker == undefined){
			//load content from the database;
			if($id == "opt_a"){
				window.$questionTracker[$id] = '<?= ($optAExcaped) ?>';
				return false;
			}
			if($id == "opt_b"){
				window.$questionTracker[$id] = '<?= ($optBExcaped) ?>';
				return false;
			}
			if($id == "opt_c"){
				window.$questionTracker[$id] = '<?= ($optCExcaped) ?>';
				return false;
			}
			if($id == "opt_d"){
				window.$questionTracker[$id] = '<?= ($optDExcaped) ?>';
				return false;
			}
		}

	});
	//update complex equation
	$(document).on('click' , '#updateComplex' ,function(){
		setupdates();
		var $this = $(this);
		var $buttonhtml = $this.html();
		var $answer = $('#correctAnswer').val();
		if(!$questionTracker['question']){
			alert('Please Create  A Question.');
			return false;
		}
		if(!$questionTracker['opt_a']){
			alert('Please Enter A Value For Option A.');
			return false;
		}
		if(!$questionTracker['opt_b']){
			alert('Please Enter A Value For Option B.');
			return false;
		}
		//make sure the user wants to use the same old answer
		if(isAnswerChange == 0){
			$confirm = confirm('Are You Sure You Dont Want To Change Your Answer?');
			if($confirm == false){
				return false;
			}
		}
		$question_id = $this.attr('data-questionid');
		$course_id = $this.attr('data-courseid');
		$question = $questionTracker['question'];
		$opt_a = $questionTracker['opt_a'];
		$opt_b = $questionTracker['opt_b'];
		$opt_c = ($questionTracker['opt_c']) ? $questionTracker['opt_c'] : "";
		$opt_d = ($questionTracker['opt_d']) ? $questionTracker['opt_d'] : "";

		$data = {update_complex_question : true , question : $question , opt_a : $opt_a , opt_b : $opt_b , opt_c : $opt_c , opt_d : $opt_d , course_id : $course_id  , answer : $answer , question_id : $question_id};
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : $data,
			beforeSend : function (){
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function(result){
				if(result.trim() == "yes"){
					$this.html("<i class=\"fa fa-check\"></i> Updated");
					setTimeout(function(){
					window.history.back();
					} , 500);
					
				}else{
					alert(result);
					$this.html($buttonhtml);
				}
			}
		});
	});

	$(document).on('click' , '.PreviewAll' ,function(){
		setupdates();
	});

	function setupdates(){
		//set updates if not set
		if(window.$questionTracker['question'] == undefined){
			window.$questionTracker['question'] = '<?= ($questionExcaped) ?>';
		}
		if(window.$questionTracker['opt_a'] == undefined){
			window.$questionTracker['opt_a'] = '<?= ($optAExcaped) ?>';
		}
		if(window.$questionTracker['opt_b'] == undefined){
			window.$questionTracker['opt_b'] = '<?= ($optBExcaped) ?>';
		}
		if(window.$questionTracker['opt_c'] == undefined){
			window.$questionTracker['opt_c'] = '<?= ($optCExcaped) ?>';
		}
		if(window.$questionTracker['opt_d'] == undefined){
			window.$questionTracker['opt_d'] = '<?= ($optDExcaped) ?>';
		}
	}
	</script>