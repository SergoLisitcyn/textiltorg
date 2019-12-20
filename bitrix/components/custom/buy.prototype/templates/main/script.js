$(document).ready(function() {
	var isSend = true,
		form = $('.buy-one-click-form');

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
							fbq('track', 'Purchase', {currency: 'RUB', value: data.price}); // Facebook Purchase
							if(siteDomen == "spb.textiletorg.ru") {
								window.dataLayer = window.dataLayer || [];
								dataLayer.push({
									"event": "order1click",
								});
							} else
							if(siteid == 's1'){
								//yaCounter1021532.reachGoal(yaSend);
							}
							else if(siteid == 'tp'){
								//yaCounter46320975.reachGoal(yaSend);
							}
							ShowMessage('Заявка принята', data.message);
							form.resetForm();

							// retail rocket markap
							(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
								try {
									rrApi.order({
										transaction: data.transaction,
										items: [
											{ id: data.id, qnt: data.qnt,  price: data.price}
										]
									});
								} catch(e) {}
							});
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
			maxWidth	: 900,
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

	$('.buy-one-click').fancybox({
		maxWidth    : 944,
		fitToView   : false,
		autoSize    : true,
		closeClick  : false,
		openEffect  : 'none',
		closeEffect : 'none',
		afterLoad : function() {
			$('.buy-one-click-form input[name="GOOD_ID"]').val($(this.element).attr('data-good-id'));
		}
	});

	$(".buy_one_click_block input[data-validate=\"phone\"]").click(function(){
		if ($(this).val() == "") {
			$(this).val("+7 (");
		}
	});
	$(".buy_one_click_block input[data-validate=\"phone\"]").focusout(function(){
		if ($(this).val() == "+7 (") {
			$(this).val("");
		}
	});
});