$(document).ready(function() {
	var isSend = true,
		form = $('#footer-callback-form');

	$.validateExtend({
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
				tagManager = $(this).attr("id");
				form.ajaxSubmit({
					success: function(data, status) {
						if (status == 'success' && data.status == 'success') {
							if(siteDomen == "spb.textiletorg.ru") {
								//yaCounter48343148.reachGoal(yaSendCatalog);
								window.dataLayer = window.dataLayer || [];
								dataLayer.push({
									"event": "consultationBottom", 
								});
							} else
							if(siteid == 's1'){
								//yaCounter1021532.reachGoal(yaSendCatalog);
							}
							else if(siteid == 'tp'){
								//yaCounter46320975.reachGoal(yaSendCatalog);
								
							}
							
							
							//ga('send', 'event', 'footerCallbackForm', 'call');
							/*yaCounter1021532.reachGoal('callMe_Send');*/
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

	form.find('input.phone-ru').formatter({
		pattern: '+7 ({{999}}) {{999}}-{{99}}-{{99}}'
	});

	form.find('input.phone-by').formatter({
		pattern: '+3{{99}} {{999999999}}'
	})

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
    $("#footer-callback-form .i_phone.input_callback.phone-ru").click(function(){
        if ($(this).val() == "") {
            $(this).val("+7 (");
        }
    });
    $("#footer-callback-form .i_phone.input_callback.phone-ru").focusout(function(){
        if ($(this).val() == "+7 (") {
            $(this).val("");
        }
    });
});