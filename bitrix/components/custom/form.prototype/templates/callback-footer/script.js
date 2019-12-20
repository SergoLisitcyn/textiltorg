$(document).ready(function() {
	var isSend = true,
		form = $('.callback-form-footer');

	$.validateExtend({
		name : {
			required : true
		},
		phone : {
			required : true,
			pattern : /^.{18}$/
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
						if (status == 'success' && data.status == 'success') {
							if(siteDomen == "spb.textiletorg.ru") {
								yaCounter48343148.reachGoal('callMe');
								window.dataLayer = window.dataLayer || [];
								dataLayer.push({
									"event": "consultationBottom", 
								});
							} else
							if(siteid == 's1'){							
								yaCounter1021532.reachGoal('callMe');
							}
							else if(siteid == 'tp'){
								yaCounter46320975.reachGoal('callMe_tp');
							}
								
							ga('send', 'event', 'callbackForm', 'call');
							console.log(data);
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

	form.find('input[data-validate="phone"]').formatter({
		pattern: '+7 ({{999}}) {{999}}-{{99}}-{{99}}'
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

	$(".form_callback_block input[data-validate=\"phone\"]").click(function(){
		if ($(this).val() == "") {
			$(this).val("+7 (");
		}
	});
	$(".form_callback_block input[data-validate=\"phone\"]").focusout(function(){
		if ($(this).val() == "+7 (") {
			$(this).val("");
		}
	});
});