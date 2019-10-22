
//------------------------------------
$(function () {
	//track questions in ttinymce
window.$questionTracker = {};
////////////////////////////////////
var $location  = window.location.href;
//show techers panel in login 
if($location.includes('register.php?teacher')){
		$('#utype').prop('selectedIndex' , 1);
		setTimeout(function(){
			$('#utype').trigger('change');
		} , 10);
}
if($location.includes('starttest.php')){
	// //prevent the right click
	 $(document).bind("contextmenu", function(e) {
       e.preventDefault();
    });
	$(document).bind('keydown' , function (e){
		return false;
	});	
}


//lunch full screen
function exitFullscreen() {
  if(document.exitFullscreen) {
    document.exitFullscreen();
  } else if(document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
  } else if(document.webkitExitFullscreen) {
    document.webkitExitFullscreen();
  }
}

	function launchFullScreen(element) {
	  if(element.requestFullScreen) {
	    element.requestFullScreen();
	  } else if(element.mozRequestFullScreen) {
	    element.mozRequestFullScreen();
	  } else if(element.webkitRequestFullScreen) {
	    element.webkitRequestFullScreen();
	  }
	}


//insert content to tinymce
$(document).on('click' , '#complexDiv #iconsymbols span[data-value]' , function(){
	$content = $(this).attr('data-value');
	tinymce.activeEditor.execCommand('mceInsertContent', false, $content);
	//update question tracker
	$current = $('#toolbar').attr('data-current');
    $content = tinyMCE.activeEditor.getContent();
    window.$questionTracker[$current] = $content;
});

	//on click load full screen
	$(document).on('click' , '#fullscreen', function () {
		var $this = $(this);
		if($this.attr('data-fs') == "yes"){
		launchFullScreen(document.documentElement); // the whole page
		$this.attr('data-fs' , "no");
		}else if($this.attr('data-fs') == "no"){
		exitFullscreen(document.documentElement);
		$this.attr('data-fs' , "yes");
		}
	});
//load acknowledge
$(document).on('click', '#ack_student' , function() {
$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , 'receive.php').removeClass('hide');
	return false;
});
		//load complex quesitons section
		$(document).on('click' ,'#loadComplex' ,function(e){
			$link = $(this).attr('data-link');
			$('#iframe' ,parent.document).attr('src' , $link);
		});	
		//load comprehension
		$(document).on('click' , '#loadComprehension', function () {
		$('.oneatatime.active').removeClass('active');
		$('#comprehensionContainer').addClass('active');
		$('html').scrollTop(0);
		});

//update department 
$(document).on('click' , '.individual_department .edit_sec i.fa-pencil' ,function(){
	$parent  = $(this).parents('.individual_department');
	$parentHtml = $parent.html();
	$name = $(this).attr('data-name');
	$new_val = prompt('Enter The New Value'  , $name);
	if($new_val.trim() == $name.trim()){
		return false;
	}
		$parent.html('<i class="fa fa-spin fa-spinner"></i>').css({
			'padding' : '.35rem',
			'color'  : 'green',
		});
		$.post('include/process.php' , {update_dept : true  , old_val : $name, new_val : $new_val} , function(result){
			if(result.trim() == "done"){
		$htmlfix  = '<div class="individual_department">';
		$htmlfix += '<span data-value="'+ $new_val.trim()+'">'+ $new_val.trim() + '</span>';
		$htmlfix += '<div class="edit_sec"><i class="fa fa-remove" data-name="'+ $new_val.trim()+'"></i> <i class="fa fa-pencil" data-name="'+ $new_val.trim()+'"></i></div><!-- end of edit sec -->';
		$htmlfix += '</div>';
		$parent.replaceWith($htmlfix);
				$parent.fadeOut('fast' , function(){
					$(this).fadeIn('fast');
				});
			}else{
				alert(result);
				$parent.html($parentHtml);
			}
		});
});

//remove department 
$(document).on('click' , '.individual_department .edit_sec i.fa-remove' ,function(){
	$confirm = confirm('Are You Sure?');
	$confirm  = confirm('Please Note That Deleting This Department Will Return All Current Students In This Department To General.');
	$parent  = $(this).parents('.individual_department');
	$parentHtml = $parent.html();
	$name = $(this).attr('data-name');
	if($confirm == true){
		$parent.html('<i class="fa fa-spin fa-spinner"></i>').css({
			'padding' : '.35rem',
			'color'  : 'red',
		});
		$.post('include/process.php' , {delete_dept : true  , name : $name} , function(result){
			if(result.trim() == "done"){
				$parent.fadeIn('fast' , function(){
					$(this).fadeOut('fast' , function(){
						$(this).remove();
					});
				});
			}else{
				alert(result);
				$parent.html($parentHtml);
			}
		});
	}
});

	//preview all 
	$(document).on('click' , '.PreviewAll' ,function(){
		$content = tinyMCE.activeEditor.getContent();
		if($questionTracker['question'] && ($questionTracker['opt_a'] || $questionTracker['opt_b']) || $questionTracker['opt_c'] || $questionTracker['opt_d']){
			//show full
			var $output  = '<div class="displayComplexQuestions">';
			    $output += "<div class=\"quesiton\">"+ $questionTracker['question']+"</div>";
			   	$output += "<div class=\"answerDiv\">";
			$.each($questionTracker , function(key , value){
				$option = "";
				if(key !== "question" && value.trim() !==""){
				if(key == "opt_a"){
					$option = "a.) ";
				}else if(key == "opt_b"){
					$option = "b.) ";
				}else if(key == "opt_c"){
					$option = "c.) ";
				}else if(key == "opt_d"){
					$option = "d.) ";
				}
	$output += "<div class=\"answers\"><span class=\"optis\">"+ $option + "</span>"+ value + "</div>";
				}
			});
			$output +="</div><!-- end of answer div -->";
			$output += '</div><!-- end of display complex questions -->';
			$('#popup .content').html('');
			$('#popup .content').html($output);
			$('#popup').removeClass('hide');
				MathJax.Hub.Queue(["Typeset",MathJax.Hub,"popup"]);
		}else if($content.trim() !== ""){
			var $output ='<div class="displayComplexQuestions">';
			  	$output += "<div class=\"quesiton\">"+ $content+"</div>";
				$output += '</div><!-- end of display complex questions -->';
			$('#popup .content').html('');
			$('#popup .content').html($output);
			$('#popup').removeClass('hide');
				MathJax.Hub.Queue(["Typeset",MathJax.Hub,"popup"]);
		}
	});

	//save complex question
	$(document).on('click' , '#addComplex' ,function(){
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
		//check if answer has been selected
		if($answer.trim() == ""){
			alert('Please Select Your Answer');
			return false;
		}
		$course_id = $this.attr('data-courseid');
		$question = $questionTracker['question'];
		$opt_a = $questionTracker['opt_a'];
		$opt_b = $questionTracker['opt_b'];
		$opt_c = ($questionTracker['opt_c']) ? $questionTracker['opt_c'] : "";
		$opt_d = ($questionTracker['opt_d']) ? $questionTracker['opt_d'] : "";

		$data = {new_complex_question : true , question : $question , opt_a : $opt_a , opt_b : $opt_b , opt_c : $opt_c , opt_d : $opt_d , course_id : $course_id  , answer : $answer};
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : $data,
			beforeSend : function (){
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function(result){
				if(result.trim() == "yes"){
					$('#opts button').removeClass('selected');
					$('#question').addClass('selected');
					$('#correctAnswer').prop('selectedIndex' , 0);
		$('.ComplexLabel').text('Question').fadeOut('fast' , function(){
			$(this).fadeIn('fast');
		});
					//$this.html("<i class=\"fa fa-check\"></i> Added");
					$this.html($buttonhtml);
					tinyMCE.activeEditor.setContent('');
					$('#previewContent').html('');
					window.$questionTracker = {};
					window.location.reload();
				}else{
					alert(result);
					$this.html($buttonhtml);
				}
			}
		});
	});
		//close popup
		function closePopUp(){
			$('#popup').animate({
				opacity : 0,
			} , 1000 , function (){
				$(this).addClass('hide');
				alert('yes');
			});
		}


	//track and save question data to session variable
	$(document).on('click' , '#opts button' , function(e){
		//save previous
		var $thisButton = $(this);
		var $name = $(this).attr('data-name');
		$('.ComplexLabel').text($name).fadeOut('fast' , function(){
			$(this).fadeIn('fast');
			$('#toolbar button').removeClass('selected');
			$thisButton.addClass('selected');
		});
		var $content = tinyMCE.activeEditor.getContent();
		tinyMCE.activeEditor.setContent('')
		$current = $('#toolbar').attr('data-current');
		$id = this.id;
		if($content.trim() !== ""){
		$.post('include/process.php' , {save_state : true , content : $content , current : $current} , function(result){
			$questionTracker[$current] = result;
			$('#toolbar').attr('data-current' , $id);
		});
		}
		//get content from questio tracker
		if($questionTracker[$id]){
			tinyMCE.activeEditor.setContent($questionTracker[$id]);
			$('.PreviewWysiwyg').trigger('click');
		}
	});
	//preview and save data to the database
	$(document).on('click' , '#complexDiv .PreviewWysiwyg' ,function(){
		$current = $('#toolbar').attr('data-current');
		var $content = tinyMCE.activeEditor.getContent();
		//save to a session variable
		if($content.trim() !== ""){
		$.post('include/process.php' , {save_state : true , content : $content , current : $current} , function(result){
			$questionTracker[$current] = result;
		});
		}
	});
	//preview tinymce content
	$('.PreviewWysiwyg').on('click' , function (e) {
		var ed = tinyMCE.activeEditor.getContent();
		var $previewContent = $('#previewContent');
		$previewContent.html("");
		var $instruction = $('#instruction').val();
		if($instruction == undefined){
			$instruction = "";
		}
		if($instruction.trim() !== ""){
		$previewContent.prepend("<div class='ins_div'>" + $instruction+ "</div>");	
		}
		$previewContent.append(ed);
		 MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathOutput"]);
	});

//close popup
$(document).on('click' , '#popup' ,function(e){
	if(e.target == this){
		$(this).addClass('hide');
		$(this).find('.content').html('');
	}
});

//edit comprehension
$(document).on('click' , '#edit_comprehension_popup' , function(){
	$id = $('#previewContent').attr('data-compId');
	$('#popup' , parent.document).addClass('hide');
	//$('#popup .content' , parent.document).html('');
	$('#iframe').attr('src' , 'comp.php?comp=edit&compid=' + $id+'').removeClass('hide');
});
//go back from comprehension
$(document).on('click', '#comprehensionContainer .goback', function() {
	$('#comprehensionContainer').removeClass('active');
	$('#generalQuestions').addClass('active');
});
//go back complex page
$(document).on('click' , '#gobackComplex' , function(){
	window.history.go(-1);
});

//newly added comprehension
$(document).on('click' , '#newlyAddedComp' , function(){
	var $popupContent = $('#popup .content' , parent.document);
	var $popup = $('#popup' , parent.document);
	$popup.removeClass('hide');
	$popupContent.load('include/process.php?show_comprehension=true' , function(){
				MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathOutput"]);
	});
});

//prevent loading of external links in comp div
$(document).on('click' , '.CompContent a' , function(e){
	e.preventDefault();
});

//update comprehension
$(document).on('click' , '#update_comprehension' , function(){
	var $content = tinyMCE.activeEditor.getContent();
	var $button = $(this);
	var $buttonhtml = $button.html();
	var $instruction = $('#instruction').val();
	var $id = $('#updateComprehension').attr('data-id');
	if($content.trim() == "" || $content.trim() == " "){
		alert('Please Enter Your Comprehension');
		return false;
	}
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {update_comprehension : true , comprehension : $content , id : $id , instruction : $instruction},
		beforeSend : function(){
			$button.html('<i class="fa fa-spin fa-spinner"></i> Updating...');
		},
		success : function (result){
		if(result.trim() == "yes"){
		$button.html('<i class="fa fa-check"></i> Updated');
		setTimeout(function(){
		window.history.go(-1);
		} , 300);
		}else{
			//error
			alert(result);
			$button.html($buttonhtml);
		}
		}
	});
});

//save comprehension
$(document).on('click' , '#add_comprehension' , function(){
	$('body, html , #iframe').scrollTop($('.specifyInstruction').offset().top);
	var $content = tinyMCE.activeEditor.getContent();
	var $button = $(this);
	var $buttonhtml = $button.html();
	var $instruction = $('#instruction').val();
	var $id = $('#course_id').val();
	if($content.trim() == "" || $content.trim() == " "){
		alert('Please Enter Your Comprehension');
		return false;
	}
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {add_comprehension : true , comprehension : $content , id : $id , instruction : $instruction},
		beforeSend : function(){
			$button.html('<i class="fa fa-spin fa-spinner"></i> Adding...');
		},
		success : function (result){
		if(result.trim() == "yes"){
		$button.html('<i class="fa fa-check"></i> Added');
		setTimeout(function(){
			//go back to basic question
			$button.html($buttonhtml);
			$('#comprehensionContainer').removeClass('active');
			$('#generalQuestions').addClass('active');
			tinymce.activeEditor.setContent(''); 
			$('#previewContent').html('');
			$('#instruction').html('');
			//show notice
			$('.notices').html('');
			$('.notices').html('<button id="newlyAddedComp"><i class="fa fa-info-circle"></i> Your Comprehension Has Been Added Succesfully</span>').delay(3000).fadeOut('fast' , function(){
				//add comprehension to question bar
				$html ="<button id=\"newlyAddedComp\"> click here to view your newly added comprehension...</button>";
				$(this).html($html).show();
			});
		} , 300);	
		}else{
			//error
			alert(result);
			$button.html($buttonhtml);
		}
		}
	});
});

//load refused
$(document).on('click' ,'.r_student', function () {
	$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , 'refused.php').removeClass('hide');
	setTimeout(function (){
		var $this = $('.r_student');
		var $class = $this.attr('data-class');
		$.ajax({
			url : 'include/process.php',
			data: {refused: true , class : $class},
			type: 'POST',
			success: function (result) {
				if(parseInt(result) == 1){
					$this.remove();
				}else{
					alert(result);
				}
			}
		});	
	}  , 1000);
		return false;
});//load trasnfer
$(document).on('click' ,'#trans_student', function () {
	$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , 'transfer.php').removeClass('hide');
	return false;
});

//nav reload
function nav_reload(){
	$('.nav_reload' , parent.document).load(parent.document.URL + ' #navigation');
}

//acknowledge individaul
$(document).on('click' , '.acceptInd' , function () {
	var $this = $(this);
	var $id = $this.attr('data-id');
	var $prevhtml = $this.html();
		$.ajax({
			url : 'include/process.php',
			data:{acknowledge_individual: true , id : $id},
			type: 'POST',
			beforeSend : function () {
			$this.html(' <i class="fa fa-spin fa-spinner"></i>');
			},	
			success: function (result) {
				if(parseInt(result) == 1){
					$('#class').prop('selectedIndex' , 0);
					$this.html($prevhtml);
					$this.parents('.student_container').remove();
					if($('.trans #body').children().length == 0){
				$('.trans #body').append('<span>All Students Acknowledged Succesfully</span>');
			}
					nav_reload();
				}else{
					alert(result);
				}
			}
		});	
});

//transfer individual
$(document).on('click' , '.transferInd' , function () {
	var $this = $(this);
	var $class = $('#class').val();
	if($class == ""){
		alert('Please Select A Class');
		return false;
	}
	var $id = $this.attr('data-id');
	var $name = $this.attr('data-name');
	var $class_name = $('#class option:selected').text();
	var $former_class = $this.attr('data-formerclass');
	var $confirm = confirm('Transfer ' + $name + ' to ' + $class_name + "?");
	if($confirm == true){
	var $prevhtml = $this.html();
		$.ajax({
			url : 'include/process.php',
			data:{transfer_individual: true , id : $id , class: $class , former_class : $former_class},
			type: 'POST',
			beforeSend : function () {
			$this.html(' <i class="fa fa-spin fa-spinner"></i>');
			},	
			success: function (result) {
				if(parseInt(result) == 1){
					$('#class').prop('selectedIndex' , 0);
					$this.html($prevhtml);
					$this.parents('.student_container').remove();
					if($('.trans #body').children().length == 0){
				$('.trans #body').append('<span>All Students Transfered Succesfully</span>');
			}
					nav_reload();
				}else{
					alert(result);
				}
			}
		});	
	}
});

//reject individual
$(document).on('click' , '.reject' ,function(){
	var $this = $(this);
	var $name = $this.attr('data-name');
	var $propmpt  = confirm('Are You Sure You Want To Reject ' + $name + "?");
	if($propmpt == true){
	var $id = $this.attr('data-id');
	var $prevhtml = $this.html();
	$.ajax({
	url : 'include/process.php',
	data:{reject: true , id : $id},
	type: 'POST',
	beforeSend : function () {
		$this.html(' <i class="fa fa-spin fa-spinner"></i>');
	},	
	success: function (result) {
		if(parseInt(result) == 1){
			$('#class').prop('selectedIndex' , 0);
			$this.html($prevhtml);
			$this.parents('.student_container').remove();
			$('.trans #body').append('<span>Done Succesfully</span>');
			nav_reload();
		}
	}
});
}
});
//ignore students

$(document).on('click' , '.ignore' ,function(){
	var $this = $(this);
	var $name = $this.attr('data-name');
	var $propmpt  = confirm("Are You Sure You Want '" + $name + "' To Remain In Your Class?");
	if($propmpt == true){
	var $id = $this.attr('data-id');
	var $prevhtml = $this.html();
	$.ajax({
	url : 'include/process.php',
	data:{ignore: true , id : $id},
	type: 'POST',
	beforeSend : function () {
		$this.html(' <i class="fa fa-spin fa-spinner"></i>');
	},	
	success: function (result) {
		if(parseInt(result) == 1){
			$('#class').prop('selectedIndex' , 0);
			$this.html($prevhtml);
			$this.parents('.student_container').remove();
			$('.trans #body').append('<span>Done Succesfully</span>');
			nav_reload();
		}
	}
});
}
});
//acknowledge all
$(document).on('click','#head #acknowledge', function () {
	//check the at least one student is selected
	var $selected = $('.inputcheck:checked').length;
	if($selected == 0){
		alert('Please Select A Student');
		return false;
	}

	var $this = $(this);
	var $prevhtml = $this.html();
	var $checks = $('.inputcheck:checked').serialize() + "&acknowledgeselected=true&";
	
	$.ajax({
	url : 'include/process.php',
	data:$checks,
	type: 'POST',
	beforeSend : function () {
		$this.append(' <i class="fa fa-spin fa-spinner"></i>');
	},	
	success: function (result) {
		if(parseInt(result) == 1){
			$this.html($prevhtml);
			$('.inputcheck:checked').parents('.student_container').remove();
			//check if all have been Acknowledge
			if($('.trans #body').children().length == 0){
				$('.trans #body').append('<span>All Students Acknowledged Succesfully</span>');
			}
			nav_reload();
		}
	}
});
});
//transfer students 
$(document).on('click','#head #trasnfer', function () {
	//check the at least one student is selected
	var $selected = $('.inputcheck:checked').length;
	if($selected == 0){
		alert('Please Select A Student');
		return false;
	}
	//check class
	var $class = $('#head #class').val();
	if($class == ""){
		alert('Please Select A Class');
		return false;
	}
	var $this = $(this);
	var $classtext  = $('#class option:selected').text();
	var $former_class = $this.attr('data-formerclass');
	var $prevhtml = $this.html();
	var $checks = $('.inputcheck:checked').serialize() + "&transferselected=true&class="+$class+"&former_class="+$former_class;
	var $confirm = confirm('Are You Sure You Want To Transfer The Selected Students To ' + $classtext + "?");
	if($confirm == true){
	$.ajax({
	url : 'include/process.php',
	data:$checks,
	type: 'POST',
	beforeSend : function () {
		$this.append(' <i class="fa fa-spin fa-spinner"></i>');
	},	
	success: function (result) {
		if(parseInt(result) == 1){
			$this.html($prevhtml);
			$('#class').prop('selectedIndex' , 0);
			$('.inputcheck:checked').parents('.student_container').remove();
			//check if all have been transfered
			if($('.trans #body').children().length == 0){
				$('.trans #body').append('<span>All Students Transfered Succesfully</span>');
			}
			nav_reload();
		}
	}
});
}//end of confirm
});
//deselect all for transfr
$(document).on('click' , '#untick' , function () {
	$('.inputcheck').removeAttr('checked');
	return false;
});
//select all for transfer
$(document).on('click' , '#tickall' , function () {
	$('.inputcheck').prop('checked' ,true).attr('checked' , true);
	return false;
});	
//error on term day
$('#term_day').on('change' , function () {
	$(this).css('border-color' , '#ccc');
});

//update term date
$(document).on('click'  , '#update_termdate' , function () {
	var $this = $(this);
	var $day = $('#term_day').val();
	if($day == "00"){
		$('#term_day').css('border-color' , 'red');
		alert('Please Select A Day');
		return false;
	}
	var $month = $('#term_month').val();
	var $year = $('#term_Year').val();
	var $date = $month + "/" + $day + "/" + $year;
	var $buttonhtml = $this.html();
	$.ajax({
	url : 'include/process.php',
	data: {update_termdate : true , date : $date},
	type: 'POST',
	beforeSend : function () {
		$this.html('<i class="fa fa-spin fa-spinner"></i>');
	},	
	success: function (result) {
		$this.html('<i class="fa fa-check"></i>');
		setTimeout(function () {
			$this.html($buttonhtml);
		}, 1000);
		if(result.trim() == "You Have Selected A Date In The Past"){
			alert(result);
		}else{
			//update term date div
			$('.e_term' , parent.document).text("Next Session : " + result.trim());

	}
}
});
	return false;
});

//remove last update
$(document).on('click' , '#last_edit  .hide', function () {
	var $this = $(this);
	$this.parents('#last_edit').animate({
		opacity : 0
	} , 500 , function () {
		$(this).remove();
	});
});
//add classes to write test
$('#select_test_class , #change_class').on('change', function () {
	var $this = $(this);
	var $class = $this.val();
	if($class == "" || $class == "---"){
		return false;
	}
	var $flag = true;
	var $html ="";
		$html += "<div class=\"individual_class\">";
	 	$html += "<span data-value=\""+ $class +"\">" + $class + "</span>";
	 	$html +="<span class=\"delete_one\"><i class=\"fa fa-remove\"></i></span>";
	 	$html +="</div>";
	 	//prevent it from adding twice
	 	$('#add_classes span[data-value]').each(function () {
	 		var $this = $(this);
	 		if($this.attr('data-value') == $class){
	 			$flag = false;
	 		}
	 	});
	 		if($flag == true){
	 		$('#add_classes').append($html);
	 		}
});
//add department
$('#department_select_ce  , #change_department').on('change', function () {
	var $this = $(this);
	var $dept = $this.val();
	var $text = $this.find('option:selected').text();
	//format the text
	if($dept == "" || $dept == "---"){
		return false;
	}
	var $flag = true;
	var $html ="";
		$html += "<div class=\"individual_class\">";
	 	$html += "<span data-value=\""+ $dept +"\">" + $text + "</span>";
	 	$html +="<span class=\"delete_one\"><i class=\"fa fa-remove\"></i></span>";
	 	$html +="</div>";
	 	//prevent it from adding twice
	 	$('#add_departments span[data-value]').each(function () {
	 		var $this = $(this);
	 		if($this.attr('data-value') == $dept){
	 			$flag = false;
	 		}
	 	});
	 		if($flag == true){
	 		$('#add_departments').append($html);
	 		}
});

//delete class display on click
$(document).on('click', '#add_classes .delete_one , #add_departments  .delete_one' , function() {
	var $this = $(this);
	$this.parents('.individual_class').remove();
});

//show deleted test results
var $count = 0;
$('#version').on('click' , function () {
	var $this = $(this);
	var $allow = $this.attr('data-allow');
	//online click if not logged in
	if($allow == "false"){
		return false;
	}
	$count++;
	if($count == 10){
		var $password = prompt('Enter Password');
		if($password == "cbt1234"){
		$.ajax({
		url : 'include/process.php',
		data: {get_scores : true},
		type: 'GET',
		success: function (result) {
			if(result.trim() == ""){
				alert('No Deleted Score Found');
			}else{
				//display deleted scores
				$('#description').remove();
				$('#iframe_container').html( "<div class=\"holder\">" + result + "</div>");
				$('#iframe_container').prepend('<span class="error">Please Click The Home Button When Done. Thank You.</span>');
			}
			
		}
	});	
	}else{
			alert('Wrong Password Try Again');
			$count = 0;
		}
	}
});

//display html fromat on the display div
function showHtmlFormat(first){
	if(!first){
	$text = $('#viewsymbols').html($currentInput.val()).text();
	$('#viewsymbols').text($text).slideDown('fast').addClass('show');
	}else{
		$text = $('#viewsymbols').html($('#question').val()).text();
		$('#viewsymbols').text($text).slideDown('fast').addClass('show');
	}
}
//get the current input in use
var $currentInput = "";
$("#questionDiv").on('keyup , focus' , 'input , textarea' , function () {
	$text = "";
	$currentInput = $(this);
	if($currentInput.val() !== ""){
	showHtmlFormat();
	}else{
	$('#viewsymbols').text($text).slideUp('fast').removeClass('show');
	}
});



//add symbols to the text area
$('#iconsymbols').on('click' , 'span' , function () {
	var $this = $(this);
	var $character = $this.attr('data-value');
	if($.type($currentInput) == "string"){
		var $question = $('#question');
		//incase a symbol is put first still display it first
		$question.val($character);
		$currentInput = $question;
		showHtmlFormat(true);
		$question.focus();
	}else{
		//get the cursor position 
	var cursorPos = $currentInput.prop('selectionStart');
    var v = $currentInput.val();
    var textBefore = v.substring(0,  cursorPos);
    var textAfter  = v.substring(cursorPos, v.length);
    $currentInput.val(textBefore + $character + textAfter);
	showHtmlFormat();
	$currentInput.focus();

	}
	//set the special character value for editing 
	$('#special_character').attr('value' , 1);
});


//scroll down on click
$('#symbolbuttons button').on('click' , function() {
	var $offset = $('#not').offset();
	$('.notices').html('');
	$(window).scrollTop($offset.top);
});
//show icons on click
$('#symbolbuttons').on('click' , 'button' , function () {
	var $this = $(this);
	var $div = $this.attr('data-id');
	$('#iconsymbols div').each(function () {
		var $this = $(this);
		if($this.attr('id') == $div){
			$this.addClass('show').slideDown('fast');
		}else{
			$this.removeClass('show').slideUp('fast');
		}
	});
	//show this to the user on the sidebar of question page
	showHtmlFormat();
	return false;
});



//show or hide icons on click
$('#symtext').on('click' , function (){
	$('#iconsymbols').slideToggle('fast');
});
//reload sidebar on update
$(document).on('click' , '#withoutadnumber  .up_admin' , function () {
		//update the sidebar
		var $count = 0;
		var $left = $('#withoutadnumbercount', parent.document);
		var $prevhtml = $left.html();
		$('#withoutadnumber input').each(function () {
			var $this = $(this);
			if($this.val() == ""){
				$count++;
			}
		});
			if($count == 0){
				$left.replaceWith('<i  class="fa fa-check"></i>');
				setTimeout(function () {
					$('#withoutnumber' , parent.document).remove();
				}, 300);	
			}else {
				//$left.text($count);
			}
});
//trigger submit
$(document).on('keyup' , '#withoutadnumber  input' , function (e) {
	if(e.keyCode  === 13){
		$(this).siblings('.up_admin').trigger('click');
	}

	});
//update students without admission number
$(document).on('click' , '#withoutadnumber  .up_admin' , function () {
	var $this = $(this).siblings('input');
	var $value  = $this.val();
	if($value == ""){
		return false;
	}
	var $id = $this.attr('data-id');
	var $admission_number = $this.val();
	var $loader = $this.siblings('.loader');
	var $loaderClass = $loader.attr('class');

	$.ajax({
	url : 'include/process.php',
	data: {updateadmissionnumber : true , id : $id , admission_number : $admission_number},
	type: 'POST',
	beforeSend : function () {
			$loader.removeClass('hide');
	},
	success: function (result) {
		if(result == 1){
		$loader.attr('class' , 'fa fa-check').css('color' , 'green');
		setTimeout(function () {
			$loader.attr('class' , $loaderClass).css('color' , 'orange').addClass('hide');
			$this.parents('.show').remove();
		} , 500);
	}else{
		$loader.addClass('hide');
		$this.val("");
		alert(result);
		$this.focus();
	}
}

});
	
});

//custom navigation
$('#navigation #back').on('click' ,  function() {
	window.history.go(-1);
	if(window.history.length == 0){
	}
});
$('#navigation #front').on('click' ,  function() {
	window.history.go(+1);
});

//lead nest terms date
$('.e_term').on('click' , function(){
	$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , 'term_date.php').removeClass('hide');
});

//load developer
$('#developer').on('click' , function () {
		$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , 'developer.html').removeClass('hide');
	return false;
});

//load links into the iframe
$('#indexpage #sidebar a[data-link]').on('click' , function () {
	var $link = $(this).attr('data-link');
	$('div.indexdesc').css('display'  , 'none');
	$('#iframe').attr('src' , $link).removeClass('hide');
});

//print
$(document).on('click' , '#print' , function() {
	window.print();
});

//hide all neccesary divs
$('#searchval').on('focus' , function () {
$('#classTeacher').css('display' , 'none');

});

//delete score
$(document).on('click' ,'#removeScore', function () {
var $this = $(this);
var $id = $this.attr('data-id');
var length = $('#searchresult').children().length;
var $confirm = confirm('Are You Sure?');
var $student_name = $this.siblings('.c1').text();
var $test = $this.siblings('.c2').text();
var $class = $this.siblings('.c3').text();
var $score = $this.siblings('.c5').text();
if($confirm == true){
	var reason = prompt('Please State Your Reason.');
	if(reason !== "" && reason !== null){
		$.post('include/process.php', {deleteScore : true , id : $id , reason : reason , student_name : $student_name , test : $test , class : $class , score : $score }, function(result) {
				//remove table from display
				if(parseInt(result) == 1) {
					$this.parents('.displayscores').fadeOut('fast' , function() {
						$(this).remove();
						$previoushtmlfromsearch = $('#searchresult').html();
						//update improper submission
						if(length == 1){
						$('#improper'  , parent.document).remove();
						}
					});
				}
		});
	}
}
});	
//search for students score 
$previoushtmlfromsearch = $('#searchresult').html();
$('#searchval').on('keyup' , function () {
var $this = $(this);
var $value = $this.val();
var $notice = $this.attr('data-notice');
$value = $value.trim();
//display the current showingresults
$('#msg > span').text('showing results for '  + '' + $value + '');
if(!$value){
$(document).attr('title', 'score');
}else{
$(document).attr('title', 'score');
}
//return previous html if value is empty
if($value == ""){
	$('#searchresult').html($previoushtmlfromsearch);
}else{

$.ajax({
	url : 'include/process.php',
	data: {searchscore : true , value : $value , notice : $notice},
	type: 'POST',
	beforeSend : function () {
			//$('#searchresult').html('<i class="fa fa-spin fa-spinner"></i>');
	},
	success: function (result) {
		$('#searchresult').html(result);
	}

});
}

});
//new window
$(document).on('click' , '  #displaycourses a' , function () {
	var $link = $(this).attr('href');
	window.open($link , "Test page" , "resizable=no,menubar=no,toolbar=no,scrollbars=yes,fullscreen=yes,close=no,minimizable=no,maximizable=no,height=1200px,width=2400px");
	
	return false;
});
//view course
$('#past_date').on('click' , function () {
	return false;
});
//close div after submission
if($('#mytimmer').length > 0){
	setTimeout(function () {
		window.close();
	} , 5000);
}
//submit test
$('#submitTest').on('submit' , function (){
	//check if student can submit
	var $allow = $('#timediv').attr('data-allow').replace(':',"");
	var $current_time = $('#timediv').text().replace(':',"");
	if((parseInt($allow) > parseInt($current_time)) && $('#timediv').attr('data-autosubmit') == "0"){
		$('#warning').removeClass('hide').text("Sorry you have not used up 90% of your time").fadeIn(1000).delay(5000).fadeOut(1000 , function () {
			$(this).empty().addClass('hide');

		});
		return false;
	}
	var $scores = $(this).serialize();
	var $course_id = $('#center').attr('data-id');
	var $course_name = $('#center').attr('data-courseName');
	var $data = $scores + "&submitTest=true&course_id=" + $course_id + "&course_name=" +$course_name;
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : $data,
		success : function (result) {
			var split = result.split('/');
			if(split[0].trim() == "success"){
				//reload page and show result
				window.location.href = window.location.href + "&hide=true&score=" + split[1] + " out of " + split[2];
				window.opener.location.reload(true);
		
			}else{
				alert(result);
				if(result == "sorry you have already written this test"){
				window.close();
				}
			}
		}
	});
	return false;
});

//edit course instruction 
$(document).on('focus' ,'#course_instruction', function () {
	var $this = $(this);
	if($this.attr('data-replace') == "0"){
		$this.val('');
	}


});
var $labelddd = $('#course_ins');
var $prevHtmlddd = $labelddd.html();
$(document).on('keyup' ,'#course_instruction', function () {
	var $this = $(this);
	var $value = $this.val();
	var $id = $this.attr('data-id');
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {update_instruction : true , instruction  : $value  , id : $id},
		beforeSend : function(){
		$labelddd.html('<i class="fa fa-spin fa-spinner"></i> saving...');
		},
		success : function (result) {
		$labelddd.html($prevHtmlddd);
		}
	});
});



//load edit duration adn class setion 
$(document).on('click','#class_ce , #duration_ce , #course_date_ce', function () {
	var $id = $(this).parent().attr('data-id');
	var $url = window.location.href;
	var $load = "";
	if($url.indexOf('course_id') >= 0){
	  $load =  window.location.href + "&edit=" + $id;
	}else{
	  $load =  window.location.href + "?edit=" + $id + "&course_id=" + $id;
	}
	window.location.href = $load;
})

//update class
$(document).on('click' ,'#update_class_ce', function () {
	var $confirmthis = confirm('Are You Sure?');
	var $this = $(this);
	var $currentHtml = $this.html();
	if($confirmthis == true){
		var $id = $this.attr('data-id');
		var $classes = "";
		$('#add_classes span[data-value]').each(function () {
			$classes += $(this).attr('data-value') + ",";
		});
		if($classes  == ""){
			alert('Please Select A Class');
			return false;
		}
		$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {changeClass : true , id  : $id  , class : $classes},
		beforeSend : function(){
			$this.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success : function (result) {
			$this.html('<i class="fa fa-check"></i>');
			setTimeout(function () {
			$this.html($currentHtml);
			} , 300);
			$('#sidebar').load(document.URL  + ' .teachers_course');
		}
		});
	}
});

//update department
$(document).on('click' ,'#update_department_ce', function () {
	var $confirmthis = confirm('Are You Sure?');
	var $this = $(this);
	var $currentHtml = $this.html();
	if($confirmthis == true){
		var $id = $this.attr('data-id');
		var $department = "";
		$('#add_departments span[data-value]').each(function () {
			$department += $(this).attr('data-value') + ",";
		});
		if($department  == ""){
			alert('Please Select A Department');
			return false;
		}
		$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {changeDepartment : true , id  : $id  , department : $department},
		beforeSend: function(){
			$this.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success : function (result) {
			$this.html('<i class="fa fa-check"></i>');
			setTimeout(function () {
			$this.html($currentHtml);
			} , 300);
			$('#sidebar').load(document.URL  + ' .teachers_course');
		}
		});
	}
});

//update duration
$(document).on('click' ,'#update_duration', function () {
	var $currentHtml = $(this).html();
	var $this = $(this);
	var $hour = ($('input[name=hour]').val().trim() > 0) ? $('input[name=hour]').val().trim() : 0;
	var $minute = ($('input[name=minute]').val().trim() > 0) ? $('input[name=minute]').val().trim() : 0;
	var $duration  = $hour + ":" + $minute;
	var $id = $(this).parent().attr('data-id');
	//return false if duration is lower that 5 mins
	if(parseInt($('input[name=minute]').val()) < 5 && parseInt($('input[name=hour]').val()) == 0){
		alert('Test Duration Too Small');
		return false;
	}
	if(parseInt($('input[name=minute]').val()) < 5 && parseInt($('input[name=hour]').val()) == 0){
		alert('Test Minute Duration Too Small');
		return false;
	}
		$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {'update_duration' : true , 'duration' : $duration , 'id' : $id},
		beforeSend : function () {
			$this.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success : function (result) {
			$this.html('<i class="fa fa-check"></i>');
			setTimeout(function () {
			$this.html($currentHtml);
			} , 300);
			if(result == 1){
			$('#sidebar').load(document.URL  + ' .teachers_course');
			}else{
				alert(result);
			}
		}
	});

});
//end of update duration

//show check 

function showcheck($div , $value){
	$('$div').html('<i class="fa fa-check" aria-hidden="true"></i>');
	setTimeout(function () {
		$('$div').html('$value');
	} , 1000);
}
//update course date
$(document).on('click' , '#update_date' ,  function () {
	var $currentHtml = $(this).html();
	var $this  = $(this);
	var $month = $('input[name=month]').val().trim();
	if($month < 10){
		$month = 0  + $month;
	}
	var $day = $('input[name=day]').val().trim();
	if($day < 10){
		$day  = 0 + $day;
	}
	var $date  = $month + "/" + $day + "/" + $('input[name=year]').val().trim();
	var $id = $(this).parent().attr('data-id');
		$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {'update_date' : true , 'date' : $date , 'id' : $id},
		beforeSend : function () {
			$this.html('<i class="fa fa-spin fa-spinner"></i>');
		},
		success : function (result) {
			$this.html('<i class="fa fa-check"></i>');
			setTimeout(function () {
			$this.html($currentHtml);
			} , 300);
			
			if(result.trim() !== "1"){
				alert(result);
			}else{
			$('#sidebar').load(document.URL  + ' .teachers_course');
			}
		}
	});

});
//end of update course date

//start test
$('#course_instruction #start').on('click' , function () {
	launchFullScreen(document.documentElement);
	$reload = window.location.href + "&start";
	window.location.href  = $reload;
});

//get values and print students result
$('#showtestform').on('submit' , function () {
	var $this = $(this);
	var $student_name = $('#enternameinput').val();
	var $class_id = $('#enternameinput').attr('data-classid');
	var $department = $('#enternameinput').attr('data-department');
	var $split = $student_name.split(" ");
	if(!$split[1]){
		alert('Please Complete Your Full Name');
		$('#enternameinput').focus();
		return false;
	}
	var $data = {'name' : $student_name , 'find_course' : true, 'class_id' : $class_id , department : $department};
	if($student_name !==""){
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : $data,
		beforeSend : function () {
			$this.parents('#entername').slideUp('fast');
			$('#displaycourses').html('<i class="fa fa-spin fa-spinner"></i>').css('display' , 'block');
		},
		success : function (result) {
			$('#displaycourses').html(result);
		}
	});
}else{
	alert('Please Input Your Name');
}	
	return false;
});

//go back button
$('div.back_btn').on('click'  , function(){
	$(this).parent().slideUp();
	$(this).parent().prev('div').slideDown('fast');
});

	//insert student name and admission number if avialable 
	$('#display_names').on('click' , 'span.show_name', function() {
		var $this = $(this);
		var $name = $this.attr('data-name');
		var $class_id = $this.attr('data-classid');
		var $department = $this.attr('data-department');
		$this.parent().slideUp('fast');
		$('#enternameinput').val($name).attr({
			'data-classid' : $class_id,
			'data-department' : $department,
	
  		});
	});
	//hide display name on focus	
	$('#admission_numberinput').on('focus' , function () {
		$('#display_names').hide();
	});

	//get students name as he types 
	$('#entername').on('keyup'  , '#enternameinput' , function () {
		var $this = $(this);
		var $value = $this.val().trim();
		if($value.length < 5){
			$('#display_names').css('display' , 'none');
		}else{
	$.post('include/process.php' , {'student_name' : $value , 'get_name' : true}, function (data)  {
		$('#display_names').css('display' ,'block').html(data);
	});
}
	});

	$('#selectClass').on('click'  , 'button' , function () {
		$(this).parent().slideUp('fast');
		$('#entername').css('display' , 'block').slideDown(400);
	});
	//scroll to admin section
	$(document).on('click' , '.total_users a' , function(){
		var $scrollTop = $(this).offset().top + 20;
		$('html,body').animate({
			scrollTop : $scrollTop,
		} , 1000);
	});

	//add Departments
	$(document).on('click' , '#addDepartment' , function(){
		var $this = $(this);
		var $buttonhtml = $this.html();
		var $prompt = prompt('Enter The Name Of The Department' , '');
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : {new_department : true , name : $prompt.trim()},
			beforeSend : function(){
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function (result){
			if(result.trim() !== "done"){
				alert(result);
			}else{
				//add to the department list
				$htmlfix  = '<div class="individual_department">';
				$htmlfix += '<span data-value="'+ $prompt+'">'+ $prompt + '</span>';
				$htmlfix += '<div class="edit_sec"><i class="fa fa-remove" data-name="'+ $prompt+'"></i> <i class="fa fa-pencil" data-name="'+ $prompt+'"></i></div><!-- end of edit sec -->';
				$htmlfix += '</div>';
				$('#mangeDepts').append($htmlfix).fadeOut('fast' , function(){
					$(this).fadeIn('fast');
				});
			}
			$this.html($buttonhtml);
			}
		});
	});

	//delete the user
		$('.display').on('click' , '#tdiv .remove', function () {
			var $this = $(this);
			var id = $this.attr('data-id');
			var permission = confirm('Are You Sure?');
			if(permission == true){
				//delete record from the database
				$.post('include/process.php', {id: id , delete_user : true}, function(data , status) {
					if(data.trim() == 1){
						$this.parents('.details').fadeOut(300, function() {
							$(this).remove();
							$('.total_users').load(document.URL + " #th");
							//window.location.reload();
						});
					}else{
						alert(data);
					}
				});
			}else{
			}
		});

	//trigger update
	$('#UpNewPass').on('keypress' , function(e){
		if(e.which == 13){
			$('#UpPass').trigger('click');
		}
	});

	//update password in homePage
		$(document).on('click' , '#UpPass', function () {
		$this = $(this);
		$html = $this.html();
		var password = $('#UpNewPass').val();
		if(password.length < 4){
			alert('Password Too Short');
			$('#UpNewPass').focus();
			return false;
		}
		var id = $this.attr('data-id');
		data = {password : password , update_password : true , id : id , update_new : true};
	
		if(password){
		$.ajax({
			  url: 'include/process.php',
			type: 'POST',
			data: data,

			beforeSend : function () {
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function (info) {
			 	$this.html('<i class="fa fa-check"></i>');
				setTimeout(function () {
				$this.html($html);
				} , 300);
				$('#updatePasswordDiv H1').addClass('green').text('Update Successful');
				$('#updatePasswordDiv p').remove('');
				$('#updatePasswordDiv input').remove('');
				$this.remove();
				setTimeout(function (){
					window.location.reload();
				} , 2000);
			}
		});
	}
	});


	//reset the password of the user
	$('.display').on('click' , '#tdiv .seperate a', function () {
		var password = prompt('Enter New Password');
		var id = $(this).attr('data-id');
		data = {password : password , update_password : true , id : id};
		$this = $('#update_user');
		if(password){
		$.ajax({
			  url: 'include/process.php',
			type: 'POST',
			data: data,

			beforeSend : function () {
				$html = $this.html();
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function (info) {
			 	$this.html('<i class="fa fa-check"></i>');
				setTimeout(function () {
				$this.html($html);
				} , 300);
			}
		});
	}
	});

	//update username , fullname and title and admission number and department 
	$('.display').on('click' , '#tdiv #update_user', function () {
		var $this = $(this);
		var $type = $this.siblings('.edit').attr('data-type');
		var $id = 	$this.siblings('.edit').attr('data-id');
		//if teacher
		if($type == "teacher"){
			var username =  $('#username').val();
			var full_name = $('#full_name').val();
			var title = 	$('#title').val();
			var data = {username : username , full_name : full_name , title : title , type: 'teacher' , update_user_in_admin : true , id : $id};
			if(username.trim() == ""){
			alert('Please Enter User Name');
			$('#full_name').focus();
			return false;
		}
		}
			//if stundent
			if($type == "student"){
			var admission_number =  $('#admission_number').val();
			var full_name = $('#full_name').val();
			var department = $('#department').val();
			var data = {admission_number : admission_number , full_name : full_name , type:'student' , update_user_in_admin : true, id : $id , department : department};
		
		}
			if(full_name.trim() == ""){
			alert('Please Enter Full Name');
			$('#full_name').focus();
			return false;
		}
		
		$.ajax({
			  url: 'include/process.php',
			type: 'POST',
			data: data,

			beforeSend : function () {
				$html = $this.html();
				$this.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success : function (info) {
			if(info != 1){
				if(info.trim() == "Admission Number Already Exist"){
					$('#admission_number').val('').focus();
				}
				alert(info);
				
			}
			$this.html('<i class="fa fa-check"></i>');
			setTimeout(function () {
			$this.html($html);
			} , 300);
			
			}
		});

		
	});
var $previousHtmlAdmin ="";
$(document).on('focus' , '#searchdivall input' , function(){
	$previousHtmlAdmin = $('#detailsContainer').html();
});

$(document).on('keyup' , '#searchdivall input' , function(){
	var $this = $(this);
	var $val = $(this).val();
	var $who  = $this.attr('data-who');
	var $data = {searchUser: true , who : $who , value : $val };
	if($val){
			 $.ajax({
                url: 'include/process.php', 
                data: $data,                      
                type: 'POST',
                beforeSend : function () {
        
                },
                success: function(response){
              
             			$('#detailsContainer').html(response);
                }
     			});
			}else{
				//show previous html
				$('#detailsContainer').html($previousHtmlAdmin);
			}

})

//load teacher information
	$(document).on('click' , '#all_t' , function () {
		$('div.display').load('all.php?view=teacher #tdiv');
		return false;
	});
//load student information
	$(document).on('click' , '#all_st' , function () {
		$('div.display').load('all.php?view=student #tdiv');
		return false;
	});
//load admin information
$(document).on('click' , '#all_ad' , function () {
		$('div.display').load('all.php?view=admin #tdiv');
		return false;
	});

//lead searched user information
$('.display').on('click' , '.displayAll'  , function () {
	var $id = $(this).children('span').attr('data-id');
	$('#detailsContainer').load('all.php?single=' + $id + ' .show');
});

//load teacher information
$('.display ').on('click' , '#tdiv a.a_me', function () {

	var $id = $(this).attr('data-id');
	var $type = $(this).attr('data-type');
	$.post('include/process.php' , {'edit_user_id' : $id , 'type': $type}, function (data)  {
		$('#tdiv').html(data);
	});
	return false;
});

//transfer admin
//load teacher information
$('.display ').on('click' , '#tdiv button.transferAdmin', function () {
	var $confirm = confirm('Are You Sure?');
	if($confirm == true){
		$again = confirm('Please Note That You Will No Longer Have Master Administrators Priviledge');
	}
	if($again == true){
	var $id = $(this).attr('data-id');

	$.post('include/process.php' , {'transfer_admin' : true , 'id': $id}, function (data)  {
		window.location.reload();
	});
}
	return false;
});

	//update schools name
	$(document).on('keyup' ,'#admin_form #school_name', function () {
		var $this = $(this);
		var $value = $this.val();
		var $data = {'us_name' : true , 'school_name' : $value};
		$.post('include/process.php' , $data , function (result) {

		});
	});

	//prevent admin form from submitting it self
	$('#admin_form').on('submit' , function () {
		var $formerHtml = $('#school_logo + label').html();
		$.ajax({
                url: 'include/process.php',
                cache: false,
                contentType: false,
                processData: false,
                data: new FormData(this),                         
                type: 'POST',
                beforeSend : function () {
                	$('#school_logo + label i').attr('class' , 'fa fa-spinner fa-spin').text('');
                },
                success: function(response){
                   $('#school_logo + label').html($formerHtml);
                   var $html = '<img src="admin/'+response+'"/>';
         
                   $('.display_logo').html($html);
                }
     });
		return false;
	})
	//display selected image
	$("#school_logo").change(function(){
		var $this = $(this);
		if($this.attr('data-uplaod') == "1"){
			var sure = confirm('Are You Sure?');
			if(sure == true){
				$('#admin_form').trigger('submit');
			}
		}else{
		$('#admin_form').trigger('submit');
		}
	});

	function readURL(input) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('img.display_image').attr('src', e.target.result).css('display' , 'block');
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	//update class
	$(document).on('submit' , '#update_class' , function() {
		var $this = $(this);
		var $classId = $this.attr('data-classId');
		var $oldval = $this.attr('data-oldval');
		var $data = $this.serialize() + "&update_class=true&class_id="+$classId+"&oldval="+ $oldval;
		var $htmlbtn = $this.html();
		var $button = $this.find('button[name="update_class"]');
		var $buttonhtml = $button.html();
		$button.html('<i class="fa fa-spin fa-spinner"></i>');
		$.post('include/process.php' , $data , function (result) {
			if(result == 1){
			$('#class_sidebar').load(document.URL + ' .reload');
			$('#class_sidebar').fadeOut(200).fadeIn(300);
			}else{
				alert(result);
			}
			$button.html($buttonhtml);
		});
		return false;
	});


	//delete class
	$(document).on('click' , '#delete_class' , function() {
		var $confirm =  confirm('Are You Sure?');
		if($confirm == true){
		var $classId = $(this).parents('#update_class').attr('data-classId');
		var $teacher_id = $(this).attr('data-id');
		$(this).html('<i class="fa fa-spin fa-spinner"></i>');
		var $data = "delete_class=true&class_id="+$classId+"&teacher_id=" + $teacher_id;
		$.post('include/process.php' , $data , function (result) {
			window.location.href = "class.php";
		
			
		});
	}
		return false;
	});

	//edit class
	$(document).on('click' , '.class_details' , function () {
		var $url = $(this).attr('data-id');
		window.location.href = "?class_id=" + $url;
	});

	//add class
	$('#addClass').on('submit', function () {
	var $this = $(this);
	var $data = $this.serialize() + '&add_class=true';
		$.post('include/process.php' , $data, function (data) {
			if(data == 1){
				$('span.notice').replaceWith('<a class="a_button" href="class.php">New Class</a>');
				//reload sidebar
				$('#class_sidebar').load(document.URL + ' .reload');
				$this[0].reset();
				$('#new_class').focus();
				//update display on sidebar
				var $withoutclass = $('#withoutclass' , parent.document);
				var $withoutadclasscount = $('#withoutadclasscount' , parent.document).attr('data-count');
				var $withouthtml = $('#withoutadclasscount' , parent.document);
				var $text =  parseInt($withoutadclasscount);
				var $result = $text - 1;
				//set the new val
				$withouthtml.attr('data-count' , $result);
				
				if($result == 0){
					//remove from sidebar
					$withouthtml.replaceWith('<i class="fa fa-check"></i>');
					setTimeout(function () {
						$withoutclass.remove();
					} , 1000);
				}else{
					//show the new numbe
				}
			}else{
				//alert error
				alert(data);
			}
		});
		return false;
	});

	//delete course 	
	$(document).on('click' , '.course_name_sidebar .del_course' , function () {
		var $confirm = confirm('Are You Sure?');
		if($confirm == true){
			var $this = $(this);
			var $id = $this.attr('data-id');
		$.post('include/process.php' , {'del_course' : true , 'id' : $id}, function (data) {
			//reload entire page
			window.location.href="courseedit.php";
});
	
		}
	});
	//update the answer of a question 
	$(document).on('change' ,'.posible_options select' ,  function (){
		var $windows = $('html').scrollTop();
		var $this = $(this);
		var $id = $this.parents('#result_title').find('input[class=questions]').attr('data-questionId');
		if($id == undefined){
			//check if its a complex question
			$id = $this.parents('.posible_options').attr('data-id'); 
		}
		var $answer = $this.val();
		var $course_id = $this.parents('.posible_options').attr('data-courseid');
		var $i = $this.siblings('i');
		$i.attr('class' , 'fa fa-spin fa-spinner');
	$.post('include/process.php' , {'update_answer' : true , 'id' : $id , 'answer' : $answer , 'course_id': $course_id }, function (data) {
		//reload center div
		$('#center').load(document.URL + " .reload" , function(){
			MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathOutput"]);
			$('html').animate({
				scrollTop : $windows,
			} , 500);
		});

		});
		
	});
	//update the options 
	$(document).on('click' ,'[data-valedit]', function() {
		var $this = $(this);
		var $previousHtmldd = $this.html();
		var $datas  = $(this).attr('data-value').split('_');
		var $id = $datas[2];
		var $course_id = $this.parents('#result_options').attr('data-courseid');
		var $option = $datas[1];
		var $value = $this.text().split(')')[1];
		var $prompt= prompt('Option ' + $option , $value.trim());
		$this.text('saving...');
		//update value in real time
		if($prompt.trim().length == 0 || $prompt.trim() == "null"){
			$this.html($previousHtmldd);
			return false;
		}
		$postvalue = {'option' : $option , 'question' : $id , 'new_value' : $prompt , 'update_option' : true , 'course_id' : $course_id};
		$.post('include/process.php' , $postvalue , function (data) {
			$this.text($option + ") "  + $prompt);
		});

});

	//go back on comprehension edit page
	$(document).on('click' , '.gobackUpdate' , function(){
		window.history.go(-1);
	});
	//delete comprehension
	$(document).on('click' ,'.compDisplay .delete', function () {
		var $this = $(this);
		var $question_id = $this.attr('data-id');
		var $comfirm = confirm('Are You Sure?');
		if($comfirm == true){
			//delete question
			var $course_id = $this.attr('data-courseid');
			$.post('include/process.php' , {'delete_question' : true , 'id' : $question_id ,'course_id' : $course_id }, function (data) {
				$this.parents('.compDisplay').fadeOut('fast' , function(){
					$(this).remove();
				});
			});
		}
	});
		//delete complex question
	$(document).on('click' ,'.courseditcomplex .delete', function () {
		var $this = $(this);
		var $question_id = $this.attr('data-id');
		var $comfirm = confirm('Are You Sure?');
		if($comfirm == true){
			//delete question
			var $course_id = $this.attr('data-courseid');
			$.post('include/process.php' , {'delete_question' : true , 'id' : $question_id ,'course_id' : $course_id }, function (data) {
				$this.parents('.closer').fadeOut('fast' , function(){
					$(this).remove();
				});
				 //update respective divs 
			 		$('#sidebar').load(document.URL + " .teachers_course");
			 	//$('#center').load(document.URL + " #center");
			});
		}
	});
	//delete question 
	$(document).on('click' ,'#result_title .delete', function () {
		var $this = $(this);
		var $question_id = $this.parent().prev('input').attr('data-questionId');
		var $comfirm = confirm('Are You Sure?');
		if($comfirm == true){
			//delete question
			var $course_id = $this.attr('data-courseid');
			$.post('include/process.php' , {'delete_question' : true , 'id' : $question_id ,'course_id' : $course_id }, function (data) {
				$this.parents('#result_div').fadeOut('fast', function() {
					$(this).remove();
				});
			 //update respective divs 
			 		$('#sidebar').load(document.URL + " .teachers_course");
			 	//$('#center').load(document.URL + " #center");
			});
		}
	});

	//update questions 
	//get the previous value
	var $prevquestionval ="";
	$(document).one('focus' , '.question' , function () {
		$prevquestionval = $(this).val();
	});
	$(document).on('keyup' ,'.questions', function () {
		var $this = $(this);
		var $value = $this.val();
		if($this.val().length == 0){
			$this.val($prevquestionval);
			return false;
		}
		var $id = $this.attr('data-questionId');
		var $course_id = $this.attr('data-courseid');
		var $edit_section = $this.siblings('.edit_section');
		var $pq = $edit_section.html();
		var data = {'update_question' : true , 'value' : $value , 'question_id' : $id  , 'course_id' : $course_id};
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : data , 
			beforeSend : function (){
	
			},
			success: function (data){
			
			}
		});
	});	
	//edit input fields
	$(document).on('click' , '[data-penEdit]', function () {
		$(this).prev('input').removeAttr('disabled').focus();
	});

	//set attr back for the input filed 
	$(document).on('blur' ,'#course_name_ce', function(){
		$(this).attr('disabled' , 'true');
	});
	$(document).on('keypress' , '#course_name_ce' , function(e){
		if(e.which == 13){
			$(this).trigger('blur');
		}
	});

	//submit edited value for course name
	$(document).on('change' , '#course_name_ce', function (e) {
		var $this = $(this);
		var $value = $this.val();
		if($value.trim() == "" || !$value){
			alert('Please Enter A Test Name');
			return false;

		}
		var $id = $this.attr('data-id');
		var $previousHtml = $this.next('span').html();
		var $data = {'name_edit' : true , 'name_value' : $value , 'id' : $id};
		$.ajax({
			url : 'include/process.php',
			data: $data,
			type : 'POST',
			beforeSend  : function () {
				$this.next('span').html('<i class="fa fa-spinner fa-spin"></i>');
			},
			success: function (data) {
				if(data == 1){
				$this.next('span').html('<i class="fa fa-check"></i>');
				$this.next('span').html($previousHtml);
				//update the value on the sidebar
				$('#sidebar').load(document.URL + " .teachers_course");
			}else{
				alert(data);
			}
		}
		});
	});

	//focus question div
	$('#question').focus();

	//stop browser from storing data
	$('form , input[type="text"], textarea').attr({
		'autocomplete' : "off",
		'spellcheck' : false,
		'accept-charset' : 'UTF-8',
		'autocorrect' : "off",
		'autocapitalize' : "off"
	});
	//update question form
		$('#questionRegistrationUpdate').on('submit' , function () {
		var data = $(this).serialize() + '&update_question_form=true';
		var $button = $('#submit_questions');
		var $buttonhtml = $button.html();
		$button.html('<i class="fa fa-spin fa-spinner"></i>');
		$.post('include/process.php' , data, function (data){
			if(data == 1){
				$('#submit_questions').html('<i class="fa fa-check"></i>');
				setTimeout(function () {
					window.history.go(-1);
				} , 300);
			//success 
			}else{
				//error
				$button.html($buttonhtml);
				alert(data);

			}
		});

		return false;
	})
	//add questions
	$('#questionRegistration').on('submit' , function () {
		var $button = $('#submit_questions');
		var $buttonhtml = $button.html();
		var data = $(this).serialize() + '&submit_question=true';
		$button.html('<i class="fa fa-spin fa-spinner"></i>');
		$.post('include/process.php' , data, function (data){
			if(data == 1){
			var current_num = $('#num_of_questions').text();
			var num  = current_num.split(' ');
			var num = parseInt(num);
			var sum = num + 1;
			var text = "";
			if(sum == 1){
				 text = sum  + " Question"; 
			}else{
				text = sum + " Questions";
			}
			$('#num_of_questions').fadeOut('slow').fadeIn('fast').text(text).addClass('blink');
			$('#question').val('').focus();
			$('#option_a').val('');
			$('#option_b').val('');
			$('#option_c').val('');
			$('#option_d').val('');
			$('#answer').val('');
			$('#special_character').attr("value" , '0');
			$(window).scrollTop($('#not').offset().top);
			}else{
				alert(data);
			}
			$button.html($buttonhtml);
		});

		return false;
	})


	//add course
	$('#courseRegistration').on('submit' , function (){
		var classes = ""; 
		var $departments = "";

		//check course name
		if($('#course_name').val() == ""){
			alert('Please Enter A Test Name');
			return false;
		}
		//get classes
		$('#add_classes span[data-value]').each(function (index,element) {
			classes += $(this).attr('data-value') + ",";
		});
		//get departments
		$('#add_departments span[data-value]').each(function (index,element) {
			$departments += $(this).attr('data-value') + ",";
		});
	
		var data = $(this).serialize() + '&add_course=add_course&classes=' + classes +"&departments="+ $departments;
		//check if the class has been selected 
		if($('#add_classes').children().length <= 0){
			alert('Please Select A Class');
			return false;
		}
		//check that department is selected
		if($('#add_departments').children().length <= 0){
			alert('Please Select A Department');
			return false;
		}

		//check duration
		if(parseInt($('#d_h').val()) == 0 && parseInt($('#d_min').val()) < 5){
			alert('Test Duration Too Small');
			return false;
		}
		if(parseInt($('#d_h').val()) == 0 && parseInt($('#d_min').val()) < 5){
			alert('Test Minute Duration Too Small');
			return false;
		}
		$.ajax({
			url : 'include/process.php',
			data : data,
			type :'POST',
			success : function (data){
				if($.isNumeric(data)){
					window.location.href="question.php?course_id=" + data;
				}else{
					alert(data);
				}
			}
		});
		return false;
	});
	//change input display if student
			$('#register .username , #register .password , #register .sel_title').hide();
	$('select[name="user_type"]').on('change',  function () {
		var $this = $(this);
		var $value = $this.val();
		if($value.toLowerCase() == "student"){
			//hide username, password and title
			$('.username , .password , .sel_title').hide();
				$('.admission_number , #class_section , #department').show();
		}else{
			$('.username , .password , .sel_title').show();
			$('.admission_number , #class_section , #department').hide();
		}
	});

	//register user
	$(document).on('submit' ,'#register', function (e) {
		var $this = $(this);
		var username = $('[name=username]').val();
		var password = $('[name=password]').val();
		var $user_type = $('[name=user_type]').val();
		var $title = $('[name=title]').val();
		var $full_name = $('[name=full_name]').val();
		var $button  = $this.children('.register');
		var $bthmlt = $button.html();
		var $admission_number = $('[name=admission_number]').val();
		var register    = true;
		//check class
		if($('#class_section').children().length > 0){
			var $class = $('#select_class').val();
		}else{
			$class = "";
		}
		if($user_type == "student" && $class == ""){
			$class = "";
			alert('Please Select A Class');
			return false;
		}
		if($user_type == "student" && $class == undefined){
			$class = "";
			alert('Please Create A Class First');
			return false;
		}
		//check department
		var $department  = $('#department_select').val();
		if($user_type == "student" && $department == ""){
			alert('Please Select A Department');
			return false;
		}
		
		data = {'username' : username , 'password' : password , 'register' : register , 'user_type' : $user_type , 'title' : $title , 'full_name' : $full_name , 'admission_number' : $admission_number , 'class' : $class  , 'department'  : $department};
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : data,
			beforeSend : function(){
				$button.html('<i class="fa fa-spinner fa-spin"></i>');
			},
			success: function (data) {
			if(data == 1){
				$button.html('<i class="fa fa-check"></i>');
				setTimeout(function () {
				$('#select_class').prop('selectedIndex' , 0);
				$('#department_select').prop('selectedIndex' , 0);
				$this.children('input').each(function () {
					$(this).val('');
				});
				$button.html($bthmlt);
				} , 200);
			}else{
				alert(data);
				$button.html($bthmlt);
			}
			}
		});
		e.preventDefault();
		return false;
	});
	//login user
	$('#loginform').on('submit' , function () {
		var $this = $(this);
		var username = $('[name=username]').val();
		var password = $('[name=password]').val();
		var login    = true;
		var $button = $this.children('.register');
		var $buttonhtml = $button.html();
		data = {'username' : username , 'password' : password , 'login' : login};
		$.ajax({
			url : 'include/process.php',
			type : 'POST',
			data : data,
			beforeSend : function(){
				$button.html('<i class="fa fa-spin fa-spinner"></i>');
			},
			success: function (data) {
				if(data == 1){
					window.location.href="index";
				}else{
					$('#loginDiv').prepend('<span class="error">incorrect username and password combination</span>');
				}
				$button.html($buttonhtml);
			}
		});
		return false;
	});

	//color tab on focus
	$(document).on('focus blur', 'input[type=text]' , function () {
		$('span.error').remove();
	})
});
//improper submission
$(document).on('click' , '.impropersubmit #removeScore' , function() {
	var length = $('#searchresult').children().length;

	if(length == 1){
	}
});	
$(document).on('change' , '#activate input' , function () {
	//update the change
	var $this = $(this);
	var $id = $this.attr('data-id');
	var $checked = $this.prop("checked");
	if($checked == true){
		$this.siblings('span').text('activated').css('color' , 'green');
	}else{
		$this.siblings('span').text('activate').css('color' , '#1e3f58')
	}
	$.ajax({
		url : 'include/process.php',
		type : 'POST',
		data : {update_activate : true , id : $id , value : $checked}
	});

});
//trggger submits for sec1 section
$(document).on('keypress' ,' .sec1  input[type=text]' , function (e) {
	var $this = $(this);
	if(e.which == 13){
		$this.siblings('button').trigger('click');
	}

});	