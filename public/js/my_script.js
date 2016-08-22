// My script

$(document).ready(function(){
	alert_message();
});

function alert_message(){
	$('input').click(function(){
		$('.alert').slideUp();
	});
}