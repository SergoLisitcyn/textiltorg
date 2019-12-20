$(document).ready(function() {

    // On/Off Sberbank online pay button
    $("#pay_type div").click(function() {
        var target = $(this).data("value");
        var payType = $(this).data("type");
        $("#payment_type").val(target);
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

    $("#delivery_type div").click(function() {
        var target = $(this).data("value");
        $("#delivery_item").val(target);
    });
    $("#store_type div").click(function() {
        var target = $(this).data("value");
        $("#store_item").val(target);
    });

    InitOrderForm();
    if (IS_KLADR == true)
    {
        $("input[name='ADDRESS']").keyup(function(){InitKladr('ADDRESS');});
    }
});

function orderMake(payType) {
    var arrTypeName = ['Сбербанк', 'Тинькофф'];
    var arrTypeComment = [
        'Онлайн оплата банковской картой Сбербанк (мобильная версия)',
        'Оформление кредита Тинькофф (мобильная версия)'
    ];
    var form = $('#order-form');
    var sum = $('#allSum_FORMATED').text();
    sum = parseInt(sum.replace(/\s/g, ''), 10);
    var phone = form.find('input[name="PHONE"]').val();
    var name = form.find('input[name="NAME"]').val();
    var orderMark = true;
    var data = {
        phone: phone,
        name:  name,
        email: form.find('input[name="EMAIL"]').val(),
        sity: $('input[name="CITY"]').val(),
        sum: Math.round(parseInt(sum)),
        delivery: form.find('input[name="DELIVERY"]').val(),
        payment: form.find('input[name="PAYMENT"]').val(),
        comment: arrTypeComment[payType]
    };

    if (phone == '') {
        $('#phone').removeClass('success').addClass('error');
        scroll_to(".error");
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
                $("#tinkoff-form-sum").val(Math.round(sum));
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

function scroll_to(target) {
    $('html,body').animate({
        scrollTop: $(target).offset().top
    }, 'slow');
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
            //pattern : /^.{18}$/
        },
        email : {
            required : false,
            pattern : /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/
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
            scroll_to(".error");
        }
    });

    // form.find('input[data-validate="phone"]').formatter({
    //     pattern: '+7 ({{999}}) {{999}}-{{99}}-{{99}}'
    // });
    //
    // $("#order-form input[data-validate=\"phone\"]").click(function(){
    //     if ($(this).val() == "") {
    //         $(this).val("+7 (");
    //     }
    // });
    // $("#order-form input[data-validate=\"phone\"]").focusout(function(){
    //     if ($(this).val() == "+7 (") {
    //         $(this).val("");
    //     }
    // });


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

    if (SupportsHtml5Storage) {
        var fields = [
            'NAME',
            'EMAIL',
            'PHONE',
            'ADDRESS'
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

    form.find('select[NAME="ADRESS"]').change(function() {
        var $this = $(this),
            $delivery = $(form.find('select[NAME="DELIVERY"]')),
            deliveryes;

        $delivery.find('option').each(function() {
            $(this).remove();
        });

        if (ORDER_DELIVERYES[$this.val()]) {
            deliveryes =  ORDER_DELIVERYES[$this.val()];
        } else {
            deliveryes =  ORDER_DELIVERYES['Регион'];
        }



        $.each(deliveryes, function(key, value) {
            $delivery
                .append($("<option></option>")
                    .attr('value', key)
                    .text(value));
        });
    });

    form.find('input[name="PHONE"], input[name="NAME"], input[name="EMAIL"]').change(function(){
        var sum = $('#allSum_FORMATED').text();
        var data = {
            phone: form.find('input[name="PHONE"]').val(),
            name: form.find('input[name="NAME"]').val(),
            email: form.find('input[name="EMAIL"]').val(),
            sity: $('input[name="CITY"]').val(),
            sum: parseFloat(sum.replace(/\s+/g, '')),
            delivery: form.find('input[name="DELIVERY"]').val(),
            payment: form.find('input[name="PAYMENT"]').val(),
            isMobile: 1
        };

        if (form.find('input[name="PHONE"]').val()) {
            $.post('/ajax/pending_order/handler.php', data);
        }
    });
}

function SupportsHtml5Storage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}
function InitKladr(address_name) {
    var $city = $('#order-form input[name="CITY"]'),
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
            query: $city.parents(".custom_select").find('.select_view').val(),
            limit: 1,
        }
    }).done(function(data) {
        if (data.result.length)
        {
            $city.attr('data-id', data.result[0].id);

            $address.kladr({
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
                        query: $city.parents(".custom_select").find('.select_view').val(),
                        limit: 1,
                    }
                }).done(function(data) {
                    if (data.result.length) {
                        $city.attr('data-id', data.result[0].id);

                        $address.kladr({
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
}