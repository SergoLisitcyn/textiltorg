$(document).ready(function() {
	var form = $('.textiletorg-fotm-resilt-new-konkursy');

	$.validateExtend({
		name : {
			required : true
		},
		phone : {
			required : true
		},
		email : {
			required : true,
			pattern : /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
		},
	});

	form.find('#SIMPLE_QUESTION_872').formatter({
		pattern: '+{{9999999999999}}'
	});


});