$(document).ready(function(){
	$('.cercle').mouseenter(function(){
		var margeV = (-15);
		var margeH = 0;

		if($(this).attr('cy') < 40){ margeV = +25; }

		$('#tooltip').text($(this).attr('title'));
	});

	$('.cercle').mouseleave(function(){
		$('#tooltip').text('');
	})
});