$(function () {
	var $30mins = $('#timediv').attr('data-30mins');
	var $10mins = $('#timediv').attr('data-10mins');
	var $5mins  = $('#timediv').attr('data-5mins');
	var $2mins  = $('#timediv').attr('data-2mins');
	var $1min   = $('#timediv').attr('data-1mins');
	var $allow  = $('#timediv').attr('data-allow');
	//state the timer
	timer();
	//check to displauy warning msgs
setInterval(function () {
	var $timer = $('#timediv').text();
	//alert once its 30m
	if($30mins !==""){
	if($30mins.toString() === $timer.toString()){
		warning('30 minutes left');
	}
	}
	//alert once its 10m
	if($10mins !==""){
	if($10mins.toString() === $timer.toString()){
		warning('10 minutes left');
	}
	}
	//alert once its 5m
	if($5mins !==""){
	if($5mins.toString() === $timer.toString()){
		warning('5 minutes left');
	}
	}
	//alert once its 2m
	if($2mins !==""){
	if($2mins.toString() === $timer.toString()){
		warning('2 minutes left');
	}
	}
	//alert once its 1m
	if($1min !==""){
	if($1min.toString() === $timer.toString()){
		$('#warning').html('<i class="fa fa-spin fa-spinner"></i> submitting in less than a minute...').removeClass('hide').fadeIn(1000);
		}
	}

	//check if the user previously wanter to submit and allow when time
	if($allow.toString() == $timer.toString()){
			warning('You can now submit if you wish to.');
	}
},1000);

function timer(e) {
var $time = $('#timediv').attr('data-time');
$('#timediv').timer({
	format: '%H:%M:%S',  //Display time as 00:00:00,
	duration : $time,
	callback: function () {
		$('#timediv').timer('remove');
		$('#submitTest').submit();

	}
});
}

function warning(text){
	$('#warning').removeClass('hide').text(text).fadeIn(1000).delay(5000).fadeOut(1000 , function () {
			$(this).empty().addClass('hide');
		});
}

});

