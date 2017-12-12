(function(){
	var $content = $('.crystal-test-info').detach();
	$('#getcrystalized').on('click', function(){
		modal.open({content: $content, width: 340, height:300});
	});
}());