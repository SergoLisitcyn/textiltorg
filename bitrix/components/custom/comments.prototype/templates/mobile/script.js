$(document).ready(function() {    
	var isSend = true,
		form = $('#comments-form');

	$.validateExtend({
		name : {
			required : true,
			pattern : /^[а-я\s]{2,}$/i
		},
		question : {
			required : true
		}
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
							ShowMessage('Комментарий оставлен', data.message);
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
	}).find('input[name="PHONE"]').formatter({
		pattern: '+{{999999999999999}}'
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
    
    $('.tabs .item-tab').click(function() {
        var open = $(this).attr("data-open");
        $('.tabs .item-tab').removeClass("selected");
        $(this).addClass("selected");
        
        $(".description_block_content").removeClass("open");
        $(".description_block_content."+open).addClass("open");
    });        
    
});

$(document).ready(function() {
	$('.description_block_content .rew_item:eq(3)').nextAll('.rew_item').addClass('hide');

	if ($('.description_block_content .rew_item.hide').length) {
		$('.rew_sub .rew_all').removeClass('hide');
	} else {
		$('.rew_sub .rew_all').addClass('hide');
	}

	$('.rew_sub .rew_all').click(function() {
		$('.description_block_content .rew_item').removeClass('hide');
		$('.rew_sub .rew_all').addClass('hide');
		return false;
	});
});