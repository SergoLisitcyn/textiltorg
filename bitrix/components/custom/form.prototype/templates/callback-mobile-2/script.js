$(document).ready(function() {
	var isSend = true,
		form = $('.callback-form');

	$.validateExtend({
		name : {
			required : true
		},
		phone : {
			required : true,
			//pattern : /^.{18}$/
		},
	});

	form.validate({
		onKeyup : false,
		sendForm : false,
		eachValidField : function() {
			$(this).removeClass('error').addClass('success');
		},
		eachInvalidField : function() {
			$(this).removeClass('success').addClass('error');
		},
		valid: function() {
			if (isSend)
			{
				isSend = false;
				form.ajaxSubmit({
					success: function(data, status) {
							if(siteDomen == "spb.textiletorg.ru") {
								yaCounter48343148.reachGoal(yaSendCatalogDetail);
							} else
						if(siteid == 's1'){
								yaCounter1021532.reachGoal(yaSendCatalogDetail);
							}
							else if(siteid == 'tp'){
								yaCounter46320975.reachGoal(yaSendCatalogDetail);
								
							}
							
							
							ga('send', 'event', 'footerCallbackForm', 'call');
						if (status == 'success' && data.status == 'success') {
							ShowMessage('Заявка принята', data.message);
							form.resetForm();
						} else {
							ShowMessage('Ошибка', data.message);
							console.error(data.message);
						}
						isSend = true;
					},
					error: function() {
						ShowMessage('Ошибка', 'При отправке данных возникла ошибка, просьба сообщить об этом по электронной почте, указанной в контактах.');
						console.error('Error sending data');
						isSend = true;
					}
				});
			}
		}
	});

	function ShowMessage(title, message) {
		$.fancybox( '<p>' + message + '</p>', {
			maxWidth	: 400,
			fitToView	: false,
			autoSize	: true,
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none',
			beforeLoad: function() {
				this.title = title
			}
		});
	}

	// form.find('input[data-validate="phone"]').formatter({
	// 	pattern: '+7 ({{999}}) {{999}}-{{99}}-{{99}}'
	// });
	// $(".callback-form input[data-validate=\"phone\"]").click(function(){
	// 	if ($(this).val() == "") {
	// 		$(this).val("+7 (");
	// 	}
	// });
	// $(".callback-form input[data-validate=\"phone\"]").focusout(function(){
	// 	if ($(this).val() == "+7 (") {
	// 		$(this).val("");
	// 	}
	// });
});