
$(document).ready(function() {

    // On/Off Sberbank online pay button
    $('input[type=radio][name=PAYMENT]').change(function() {
        var payType = $(this).data("type");
        if (payType == 6) {
            $( "#default-order-btn" ).hide();
            $( "#tinkoff-order-btn" ).hide();
            $( "#sberbank-order-btn" ).show();

            // OFF sberbank, default рфи банк
            // $( "#sberbank-order-btn" ).hide();
            // $( "#tinkoff-order-btn" ).hide();
            // $( "#default-order-btn" ).show();
        } else if (payType == 3) {
            $( "#default-order-btn" ).hide();
            $( "#sberbank-order-btn" ).hide();
            $( "#tinkoff-order-btn" ).show();
        } else {
            $( "#sberbank-order-btn" ).hide();
            $( "#tinkoff-order-btn" ).hide();
            $( "#default-order-btn" ).show();
        }
    });
    
	BX.addCustomEvent(window, 'OnBasketChange', function(){
        var ids = [];

        $.post('/bitrix/components/custom/order.prototype/ajax.php', {'ACTION': 'GET_SUM'}, function(data) {
            $('#cart-full-sum .cart-full-summ').html(data);
        });

        $('#box-full-cart table tbody tr').each(function() {
            var id = $(this).attr('data-id');
            ids.push(id);
        });

        $.post('/ajax/popup-cart-more-goods.php', {id : ids.join()}, function(data) {
            if (data.length) {
                $('#cart-more-goods').html(data);

                $('.slider-more-goods').slick({
                    slidesToShow: 4,
                    slidesToScroll: 4
                });

                $('.slider-more-good-item-add-to-cart').click(function() {
                    var button = $(this),
                        path = button.attr('data-path');

                    if (button.hasClass('is-active'))
                    {
                        button.removeClass('is-active').text('...');

                        $.get(path, function() {
                            BX.onCustomEvent(window, 'OnBasketChange');
                            button.addClass('is-active').text('Добавить');
                        });
                    }

                    return false;
                });
            }
        });
    });

    $('.chosen-select').chosen({
        disable_search: true
    }).on('chosen:showing_dropdown', function(evt, params) {
        $('.chosen-results .group-result').each(function() {
            var text = $(this).text().replace(/\*$/, '<span class="red">*</span>');
            $(this).html(text);
        });
    });



    $('.fancybox-popup-article').fancybox({
        maxWidth    : '100%',
        maxHeight   : '100%',
        width       : 'auto',
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        beforeShow  : function(){
            audio('wuf-4');
        },
        beforeClose : function(){
            audio('wuf-3');
        },
    });

    if ($('#order-form').length)
    {

        var r = $('#cart-full-sum'),
            h = $( '.cart-full-container' ).height(),
            distance = r.offset().top,
            $window = $(window);

        $window.scroll(function() {
            if ( $window.scrollTop() >= distance ) {
                r.addClass('fixed');
            } else {
                r.removeClass('fixed');
            }
            if ($window.scrollTop() >= h) {
                r.addClass('bottom');
            } else {
                r.removeClass('bottom');
            }
        });

        InitOrderForm();

        InitDadata();

        if (IS_KLADR == true)
        {
            InitKladr('ADDRESS');
            InitKladr('UR_CONTACT_ADDRESS');
        }
    }

});

function InitDadata() {
    var city = $('select[name="CITY"] option:selected').text();
    $("#address").suggestions({
        token: "e64dc6e92582a8c3b8bc9d9a028ee3b3298c38fa",
        type: "ADDRESS",
        count: 5,
        constraints: {
            label: "",
            // ограничиваем поиск Новосибирском
            locations: {
                //region: "Новосибирская",
                city: city
            },
            // даем пользователю возможность снять ограничение
            deletable: true
        },
        // в списке подсказок не показываем область и город
        restrict_value: true
    });
}

function orderMake(payType) {
    var arrTypeName = ['Сбербанк', 'Тинькофф'];
    var arrTypeComment = [
        'Онлайн оплата банковской картой Сбербанк (основная версия)',
        'Оформление кредита Тинькофф (основная версия)'
    ];
    var form = $('#order-form');
    var sum = $('#cart-full-sum .cart-full-summ span').attr('data-sum');
    var phone = form.find('input[name="PHONE"]').val();
    var name = form.find('input[name="NAME"]').val();
    var orderMark = true;
    var data = {
        phone: phone,
        name:  name,
        email: form.find('input[name="EMAIL"]').val(),
        sity: $('select[name="CITY"]').val(),
        sum: Math.round(sum),
        delivery: form.find('input[name="DELIVERY"]:checked').val(),
        payment: form.find('input[name="PAYMENT"]:checked').val(),
        comment: arrTypeComment[payType]
    };

    if (phone == '') {
        $('#phone').removeClass('success').addClass('error');
        orderMark = false;
    } else {
        $('#phone').removeClass('error').addClass('success');
        orderMark = true;
    }

    if (orderMark == true) {
        if (payType == 0) { // Sberbank
            $.post('/ajax/sber/order.php', data, function( orderId ) {
                ipayCheckout({
                        amount: Math.round(sum),
                        currency:'RUB',
                        order_number:orderId,
                        description: ''},
                    function(order) { showSuccessfulPurchase(order) },
                    function(order) { showFailurefulPurchase(order) }
                );
            });
        } else if (payType == 1) { // Tinkoff
            $.post('/ajax/sber/order.php', data, function( orderId ) {
                $("#tinkoff-form-phone").val(form.find('input[name="PHONE"]').val());
                $("#tinkoff-form-email").val(form.find('input[name="EMAIL"]').val());
                $("#tinkoff-form-order").val(orderId);

                $( "#tinkoff" ).submit();
            });
        }
    }

}

function showSuccessfulPurchase(order) {
    $.post('/ajax/sber/basket.php');
    setTimeout(function(){document.location.href = "https://www.textiletorg.ru/order/?ORDER_ID="+order.orderNumber;}, 5000);
}

function InitOrderForm()
{
	var form = $('#order-form');

	$.validateExtend({
		name : {
			required : true,
		},
		phone : {
			required : true,
            pattern : /^.{18}$/
		},
		email : {
			required : false,
			pattern : /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[-_a-zA-Z0-9]{2,}\.[a-zA-Z09]+$/
		},
	});

	form.validate({
		onKeyup : false,
		sendForm : true,
		eachValidField : function() {
			$(this).removeClass('error').addClass('success');
		},
		eachInvalidField : function() {
			$(this).removeClass('success').addClass('error');
		},
		valid: function(e) {

			// Если способ оплаты в кредит
			if ($('input[name="PAYMENT"]:checked').val() == 3) {

			}
		}
	});

    form.find('input[data-validate="phone"]').formatter({
        pattern: '+7 ({{999}}) {{999}}-{{99}}-{{99}}'
    });

    $("#order-form input[data-validate=\"phone\"]").click(function(){
        if ($(this).val() == "") {
            $(this).val("+7 (");
        }
    });
    $("#order-form input[data-validate=\"phone\"]").focusout(function(){
        if ($(this).val() == "+7 (") {
            $(this).val("");
        }
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

	if (SupportsHtml5Storage()) {
		var fields = [
			'NAME',
			'EMAIL',
			'PHONE',
			'ADDRESS',
            'UR_NAME',
            'UR_EMAIL',
            'UR_PHONE',
            'UR_CONTACT_ADDRESS',
            'UR_ORG',
            'UR_INN',
            'UR_KPP',
            'UR_BIK',
            'UR_BANK'
		];

		for (var i = 0; i < fields.length; i++) {
			var field = fields[i];
				value = localStorage.getItem('ORDER_FIELD_' + field),
				element = form.find('input[name="' + field + '"]');


			if (value) {
				element.val(value);
			}

			element.change(function() {
				var field = $(this).attr('name');

				localStorage.setItem('ORDER_FIELD_' + field, $(this).val());
				return true;
			});
		}
	}

	form.find('input[name="PHONE"], input[name="NAME"], input[name="EMAIL"]').change(function(){
		var sum = $('#cart-full-sum .cart-full-summ span').attr('data-sum');
		var data = {
				phone: form.find('input[name="PHONE"]').val(),
				name: form.find('input[name="NAME"]').val(),
				email: form.find('input[name="EMAIL"]').val(),
				sity: $('select[name="CITY"]').val(),
				sum: parseFloat(sum.replace(/\s+/g, '')),
				delivery: form.find('input[name="DELIVERY"]:checked').val(),
				payment: form.find('input[name="PAYMENT"]:checked').val(),
                isMobile: 0
			};

		if (form.find('input[name="PHONE"]').val()) {
			$.post('/ajax/pending_order/handler.php', data);
		}
	});

    $('#order-form').on('click', 'label[for="radio-del-2"]', function() {
        $('#delivery-stores').removeClass('hidden');
        $('#delivery-address').addClass('hidden');
        $('#calc-container').addClass('hidden');
        $('#calc-container-express').addClass('hidden');
    });

    $('#order-form').on('click', 'label[for="radio-del-3"]', function() {
        $('#delivery-stores').addClass('hidden');
        $('#delivery-address').removeClass('hidden');
        $('#calc-container').addClass('hidden');
        $('#calc-container-express').addClass('hidden');
    });

    $('#order-form').on('click', 'label[for="radio-del-4"]', function() {
        $('#delivery-stores').addClass('hidden');
        $('#delivery-address').removeClass('hidden');
        $('#calc-container').removeClass('hidden');
        $('#calc-container-express').addClass('hidden');
    });

    $('#order-form').on('click', 'label[for="radio-del-14"]', function() {
        $('#delivery-stores').addClass('hidden');
        $('#delivery-address').removeClass('hidden');
        $('#calc-container').addClass('hidden');
        $('#calc-container-express').removeClass('hidden');
    });

    $('label[for^="radio-user-type-"]').click(function() {
        var id = $(this).parent().find('input').val();
        $('div[id^="type-user-"]').hide();
        $('#type-user-' + id).show();
    });

    $('input[name="UR_BIK"]').change(function() {
        var value = $(this).val();

        if (value.length) {
            $.getJSON('/proxy/bik-info.php?bik=' + value, function( data ) {
                if (!data.error) {
                    if (data.name.length && data.city.length) {
                        var bank = data.city +', '+ data.name;
                        $('input[name="UR_BANK"]').val(bank);

                        if (SupportsHtml5Storage()) {
                            localStorage.setItem('ORDER_FIELD_UR_BANK', bank);
                        }
                    }
                    $('input[name="UR_BIK"]').removeClass('error');
                } else {
                    $('input[name="UR_BIK"]').addClass('error');
                }
            });
        }
    });

    $('input[data-validate="phone"]').keyup(function() {
        $('input[data-validate="phone"]').not(this).val($(this).val());

        if (SupportsHtml5Storage()) {
            localStorage.setItem('ORDER_FIELD_PHONE', $(this).val());
            localStorage.setItem('ORDER_FIELD_UR_PHONE', $(this).val());
        }
    });

    $('input[data-validate="name"]').keyup(function() {
        $('input[data-validate="name"]').not(this).val($(this).val());

        if (SupportsHtml5Storage()) {
            localStorage.setItem('ORDER_FIELD_NAME', $(this).val());
            localStorage.setItem('ORDER_FIELD_UR_NAME', $(this).val());
        }
    });

    $('input[data-validate="email"]').keyup(function() {
        $('input[data-validate="email"]').not(this).val($(this).val());

        if (SupportsHtml5Storage()) {
            localStorage.setItem('ORDER_FIELD_EMAIL', $(this).val());
            localStorage.setItem('ORDER_FIELD_UR_EMAIL', $(this).val());
        }
    });

    $('#order-form select[name="CITY"]').change(function() {
        $('#order-form select[name="UR_CITY"]').val($(this).val());
        var loc = $('.list_of_all_citys span[data-city-id="'+$(this).val()+'"').attr('data-href');
        window.location.replace(loc);
    });

    $('#order-form select[name="UR_CITY"]').change(function() {
        $('#order-form select[name="CITY"]').val($(this).val()).change();
        var loc = $('.list_of_all_citys span[data-city-id="'+$(this).val()+'"').attr('data-href');
        window.location.replace(loc);
    });

    $('input[name="UR_NAME"]').val($('input[name="NAME"]').val());
    $('input[name="UR_PHONE"]').val($('input[name="PHONE"]').val());
    $('input[name="UR_EMAIL"]').val($('input[name="EMAIL"]').val());


    function HeightGroup(id, max) {
        var height = 0;
        $('div[data-height-group="'+id+'"]').each(function() {
            if ($(this).innerHeight() - 40 > height)
            {
                height = $(this).innerHeight() - 40;
            }
        });

        $('div[data-height-group="'+id+'"]').each(function() {
            if ($(this).hasClass('small-bottom-padding'))
            {
                if (height + 15 < max)
                {
                    $(this).css('min-height', height + 15);
                }
            } else if ($(this).hasClass('no-bottom-padding')){
                if (height + 20 < max)
                {
                    $(this).css('min-height', height + 20);
                }
            } else {
                $(this).css('min-height', height);
            }
        });
    }

    HeightGroup(1, 1000);
    HeightGroup(2, 251);
}

function GetCustomRadioTemplate(name, value, id, title, checked)
{
    return '<div class="custom-radio">'+
                '<input type="radio" id="'+id+':"'+value+'" name="'+name+'" '+checked+'>'+
                '<label for="'+id+'"><span></span>'+title+'</label>'+
            '</div>';
}

function SupportsHtml5Storage() {
	try {
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

function InitKladr(address_name) {
	var $city = $('#order-form select[name="CITY"]'),
		$address = $('#order-form input[name="'+address_name+'"]');

	$.kladr.setDefault({
		verify: false,
		noResultText: false,
		token: '57df9e410a69def3788b4577',
		valueFormat: function (obj, query) {
			var objs,
				label = '';


            if (obj.hasOwnProperty('parents')) {
				objs = [].concat(obj.parents);
				objs.push(obj);

				$.each(objs, function(i, obj) {
					if (obj.contentType == 'street')
					{
						label += obj.typeShort + '. ' + obj.name;
					}

					if (obj.contentType == 'building')
					{
						label += ', ' + obj.typeShort + '. ' + obj.name;
					}
				});

				return label;
			}

			return (obj.typeShort ? obj.typeShort + '. ' : '') + obj.name;
		}
	});

    $.ajax({
        type: 'GET',
        dataType: 'jsonp',
        callbackParameter: 'callback',
        url: 'https://kladr-api.ru/api.php',
        data : {
            contentType: 'city',
            query: $city.find('option:selected').text(),
            limit: 1,
        }
    }).done(function(data) {
        if (data.result.length)
        {
            $city.attr('data-id', data.result[0].id);

            $address.kladr({
                // parentId: $city.attr('data-id'),
                // parentType: $.kladr.type.region,
                // oneString: true
                type: $.kladr.type.street,
                parentId: $city.attr('data-id'),
                parentType: $.kladr.type.city,
                withParents: true
            });

            $city.change(function() {
                $.ajax({
                    type: 'GET',
                    dataType: 'jsonp',
                    callbackParameter: 'callback',
                    url: 'https://kladr-api.ru/api.php',
                    data : {
                        contentType: 'city',
                        query: $city.find('option:selected').text(),
                        limit: 1,
                    }
                }).done(function(data) {
                    if (data.result.length) {
                        $city.attr('data-id', data.result[0].id);

                        $address.kladr({
                            // parentId: $city.attr('data-id'),
                            // parentType: $.kladr.type.region,
                            // oneString: true
                            type: $.kladr.type.street,
                            parentId: $city.attr('data-id'),
                            parentType: $.kladr.type.city,
                            withParents: true
                        });
                    }
                }).fail(function() {
                    console.error('error');
                });


            });
        }
    }).fail(function() {
        console.error('error');
    });
};