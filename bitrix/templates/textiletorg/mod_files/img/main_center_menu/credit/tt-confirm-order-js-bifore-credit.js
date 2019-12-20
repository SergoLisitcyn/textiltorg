var error_messages = {
    'SCR_ERROR_CTX': 'Ошибка определения контекста. Пожалуйста, включите куки и перезагрузите страницу. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>',
    'SCR_ERROR_PROPID': 'Данный подвид товара не существует. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>',
    'SCR_ERROR_ITEMID': 'Товар не существует. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>',
    'SCR_ERROR_INPUTDATA': 'Переданы не все данные. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>',
    'SCR_ERROR_UNVALIDATE_INPUTDATA': 'Переданы неверные данные. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>',
    'SCR_ERROR_PLUSCOUNT': 'Отрицательное значение при увеличении товара. Если эта ошибка повторяется, пожалуйста, <a href="javascript://" onclick="show_bbform();">свяжитесь с нами.</a>'
};

function show_popup_confirm_add_item() {
    if (!confirm_add_item_popup_timer) {
        window.confirm_add_item_popup_timer = setTimeout(function() {
            $('.popup_confirm_add_item').hide(1000);
        }, confirm_add_item_popup_interval);
    } else {
        clearTimeout(confirm_add_item_popup_timer);
        window.confirm_add_item_popup_timer = null;
        window.confirm_add_item_popup_timer = setTimeout(function() {
            $('.popup_confirm_add_item').hide(1000);
        }, confirm_add_item_popup_interval);
    }
}

// функция показа всплывающего окна
function show_confirm_win() {    
    p = $('.popup__overlay');
    p.css('display', 'block');
    p.css('top', ($(window).scrollTop() + 40) + 'px');
    $('.popup__into_overlay').css('display', 'block');
    get_cart_info();
    light_active_dop_items();
    return true;
}

// закрытие окна
function confirm_popup_close() {
    p.css('display', 'none');
    $('.popup__into_overlay').css('display', 'none');
    return false;
}

// ajax запрос на удаление товара из корзины
function del_cart_item(item_id, prop_id) {
    $.post('/confirm_order/cart.php', 
    {
        item_id: item_id,
        prop_id: prop_id,
        del: 1
    },
    function(data) {
        get_cart_info()
    }
    );
}

// ajax запрос на добавление товара в корзину
function buy_dop_item(item_id, prop_id, plus, count) {

    $.post('/confirm_order/cart.php', 
    {
        item_id: item_id,
        prop_id: prop_id,
        plus: plus,
        count: count
    }
    ,function(data) {
                get_cart_info();
            }
    );
}

// функция ajax запроса о доп.товарах и формирование их вывода в окне
function reload_dop_items(cart) {
    elem = $('.dop_items_confirm_slides');
    if (cart == null || typeof cart == 'undefined') {
        $('#dop_items').hide();
        return false;
    }
    $('#dop_items').show();
    elem.html('<div class="confirm_dop_load_gif"><img src="/_img/load.gif" id="load_price"></div>');
    arr = {};
    for (i = 0; i < cart.length; i++) {
        arr[i] = cart[i].id;
    }

    $.post(
            '/confirm_order/get_dop_item.php',
            {data: arr},
    function(data) {
        err_reg = new RegExp('^SCR','i');
        if (data != 0 && data != '' && data != '[]' && !err_reg.test(data)) {
            array_items = JSON.parse(data);
            cont = '';
            for (i = 0; i < array_items.length; i++) {
                cont += '<li>';
                cont += '<div class="item confirm_dop_i" style=""><div class="name" style="height: 28px; width: 106px; margin: 0 auto;"><a href="' + array_items[i].link + '" style="color: #16118B !important;" target="_blank" title="' + array_items[i].hint + '">' + array_items[i].name + '</a>';
                cont += '</div><div class="img_related_item" style="margin: 7px 0;">';
                if (array_items[i].image != '' && array_items[i].min_image != '')
                    cont += '<a id="prev" href="' + array_items[i].image + '"><img alt="" src="' + array_items[i].min_image + '" alt="" height="50" style="max-width: 128px;"></a>';
                cont += '</div><div><div>Цена:<span class="confirm_dop_price"> ' + array_items[i].price + ' руб.</span></div><div class="buybtn">';
                cont += '<a onClick="buy_dop_item(' + array_items[i].id + ', 0, 1, 1)" href="javascript:" align="center" style="color: #16118B !important;" class="incart_dop_item" title="Добавить в корзину">Купить</a>';
                cont += '</div></div><div class="clear"></div></div>';
                cont += '</li>';
            }
            elem.html(cont);
            dop_item_interface();
            light_active_dop_items();
        } else {
            $('#dop_items').hide();
            //if(err_reg.test(data)) alert('Ошибка определения геоположения. Пожалуйста, перезагрузите страницу.');
        }
    }
    );
}

// инициализация работы при загрузки страницы
$(window).load(function() {
// инициализация всплывающих сообщение о покупки товара    
    window.confirm_add_item_popup_interval = 3000; // время показа окошка после покупки товара
    window.confirm_add_item_popup_timer = null; // id таймера скрытия окошек после покупки товара
    window.is_dop_item = 0; // флаг последний купленный товар дополнительный или нет

    $('.popup_confirm_add_item').mouseenter(function() {
        if (confirm_add_item_popup_timer) {
            clearTimeout(confirm_add_item_popup_timer);
            confirm_add_item_popup_timer = null;
        }
    });
    $('.popup_confirm_add_item').mouseleave(function() {
        if (!confirm_add_item_popup_timer) {
            window.confirm_add_item_popup_timer = setTimeout(function() {
                $('.popup_confirm_add_item').hide(1000);
            }, confirm_add_item_popup_interval);
        }
    });
// id последнего купленого товара
    window.id_last_item = null;
// инициализация переменных для js интерфейса показа доп. товаров    
    dop_item_interface();
// установка хука на прокрутку колесика в поле доп. товаров
    $(".dop_items_confirm_slides").mousewheel(function(event, delta) {
        if (delta < 0)
            animate_dop_items(-1);
        else
            animate_dop_items(1);
        return false;
    });

// настраиваем форму оформления заказа:	
// выбор города и способа доставки в зависимости от геоположения
	//console.log(dataGeo);
	$('#confirm_city').children('option[value="' + dataGeo.city_id + '"]').attr('selected', 'selected');
	
    //$('#confirm_city').children('option[value="' + dataGeo.id + '"]').attr('selected', 'selected');
    delivery_chooses($('#confirm_city').val());
// вставляем ранее сохраненные данные
    var data_user_str = $.cookie('conf_dtu');
    if (data_user_str != null)
    {
        data_user = JSON.parse(data_user_str);
        window.confirm_order_form.name.value = data_user['name'];
        window.confirm_order_form.phone.value = data_user['phone'];
        window.confirm_order_form.email.value = data_user['email'];
        window.confirm_order_form.address.value = data_user['address'];
    }

// показ окна для кнопки
    $('#popup__toggle').click(function() {
        show_confirm_win();
    });

// хук на крестик
    $('.popup__close').click(function() {
        confirm_popup_close();
    })
});

// функция показа возможных способов доставки в зависимости от города
function delivery_chooses(id) {
	regex = new RegExp(';'+id+';','g');
    //arr = ';524925;536199;524894;536203;1486209;1490542;559838;';
    // POINT! This is city id, see - geoip/ctx_reg.php
	arr = ';569591;468250;555746;461920;566854;490996;563522;508101;516215;504042;565380;533543;531820;472433;542463;527955;504576;526651;502018;579528;515024;526565;495518;559853;554233;563524;546230;568808;540030;560756;496879;565614;551964;502965;581321;534595;571557;489162;496527;497534;534015;496638;532615;503977;535741;547560;523553;543731;484910;532657;500303;857689;464687;579464;535695;562319;463355;547523;482260;486968;6417459;520068;473778;471622;523426;462755;536206;564719;542374;538601;565955;503761;555111;492448;512023;495344;473984;471656;563523;523811;524712;469655;563705;548392;483019;548442;496519;509597;472722;513898;470546;471101;470368;472357;490172;561887;533690;463829;481608;500591;550280;524901;495260;498817;496478;1502061;542199;1504826;1492663;1497795;520204;1487277;510808;1490686;1486209;1510203;1487281;829005;538340;1497393;1502060;1503335;1511330;1509888;580798;554599;520494;502011;1494573;1490402;572665;579514;827329;559678;575560;511002;539555;520555;497450;496012;478757;532715;523198;563708;464101;498525;470444;540103;496802;580724;;501165;518970;468307;484396;565348;578740;497218;462522;579771;553399;528495;541404;525162;514796;552006;472761;545277;580054;502185;480876;473998;553427;578155;501774;463637;583673;517963;496015;499161;558082;554482;481036;501175;570021;474059;526558;501405;484907;568429;';
    if (!(regex.test(arr))) {
		$("#debug_opt").val('2 \\'+window.dataGeo.id );
        $('#confirm_delivery').html('<option value="3">Транспортная компания</option>');
    } else {
		$("#debug_opt").val('1 \\'+window.dataGeo.id);
        $('#confirm_delivery').html('<option selected value="1">Доставка курьером</option><option value="0">Самовывоз</option>');
	}
}

// показывает подсказку или скрывает её в элементах ввода контактной информации
function sh_h_title_input(obj, title, focus) {

    if (focus)
        if ($(obj).val() == title) {
            $(obj).val('');
        }
    if (!focus)
        if ($(obj).val() == '') {
            $(obj).val(title);
        }

}

// инициализация переменных для показа доп товаров
function dop_item_interface() {
    window.corusel_confirm = {};
    window.corusel_confirm.const_off = parseInt($(".dop_items_confirm_slides").children('li').eq(0).css('width'));
    window.corusel_confirm.current_pos = 0;
    window.corusel_confirm.count_li = $(".dop_items_confirm_slides").children().length;
    window.corusel_confirm.client = Math.round(640 / window.corusel_confirm.const_off) * window.corusel_confirm.const_off;

}

// активность кнопок прокрутки доп. товаров
function light_active_dop_items() {

    if (!(window.corusel_confirm.current_pos + window.corusel_confirm.const_off <= 0)) {
        $('.left_cursor_dop_items').css('opacity', '0.4');
    } else
        $('.left_cursor_dop_items').css('opacity', '1');

    if (!(-window.corusel_confirm.current_pos < window.corusel_confirm.count_li * window.corusel_confirm.const_off - window.corusel_confirm.client)) {
        $('.right_cursor_dop_items').css('opacity', '0.4');
    } else
        $('.right_cursor_dop_items').css('opacity', '1');

}

// анимация прокрутки доп. товаров
function animate_dop_items(side) {

    if (side < 0 && -window.corusel_confirm.current_pos < window.corusel_confirm.count_li * window.corusel_confirm.const_off - window.corusel_confirm.client) {

        window.corusel_confirm.current_pos = ((window.corusel_confirm.const_off * side) + window.corusel_confirm.current_pos);
        //$(".dop_items_confirm_slides").css('-webkit-transform', 'translate3d('+window.corusel_confirm.current_pos+'px, 0, 0)');
        $(".dop_items_confirm_slides").css('margin-left', window.corusel_confirm.current_pos + 'px');
    }

    if (side > 0 && window.corusel_confirm.current_pos + window.corusel_confirm.const_off <= 0) {

        window.corusel_confirm.current_pos = ((window.corusel_confirm.const_off * side) + window.corusel_confirm.current_pos);
        //$(".dop_items_confirm_slides").css('-webkit-transform', 'translate3d('+window.corusel_confirm.current_pos+'px, 0, 0)');
        $(".dop_items_confirm_slides").css('margin-left', window.corusel_confirm.current_pos + 'px');
    }
    light_active_dop_items();
    return true;
}

// ajax пересчет корзины
function ajax_recalc_cart()
{
    if (window.timer_id)
        clearTimeout(window.timer_id);
    $('#entryFrm').ajaxSubmit(
            {
                success: function(data) {
                    get_cart_info();
                },
                beforeSubmit: function() {
                    $('.sector_confirm_cart').html('<img src="/_img/load.gif" id="load_price">');
                }
            });

}

// установка таймера на пересчет корзины
function RunTimer()
{
    if (window.timer_id)
        clearTimeout(window.timer_id);
    window.timer_id = setTimeout(ajax_recalc_cart, 2500);
}

// функция получения товаров в корзине и их вывод
function get_cart_info() {
    $('.sector_confirm_cart').html('<img src="/_img/load.gif" id="load_price">');
    $.post(
            'confirm_order/get_cart.php',
            {},
            function(data) {
                err_reg = new RegExp('^SCR','i');
                if ( !err_reg.test(data) && data!='0' && data!='') {
                    count_item = 0;
                    sum_item = 0;
                    row_count = 0;
                    cur_props_len = 0;
                    cart = JSON.parse(data);
                    table_cart = '<form id="entryFrm" action="/confirm_order/cart.php" method="post">';
                    table_cart += '<table class="table_confirm_cart" border="0" cellspacing="0"><tr class="grad_silver_confirm"><td width="65%" class="font_confirm_bold">Товар</td><td width="10%" class="font_confirm_bold" style="text-align:center;">Шт.</td><td width="15%" class="font_confirm_bold" style="text-align:center;">Сумма</td><td width="34">&nbsp;</td></tr>';
                    var mixmarket = [];
                    for (i = 0; i < cart.length; i++) {
                        row_count++;
                        count_item += +cart[i].count;
                        sum_item += +cart[i].count * cart[i].price;
						if( typeof cart[i].img != 'undefined' )
							bg = '<div class="square_color"><div><img src="/'+cart[i].img+'"></div></div>';
						else bg = '';
                        table_cart += '<tr><td><a href="' + cart[i].sublink + '" target="_blank">' + cart[i].name + bg + '</a>';
                        if(typeof cart[i].can_get_paper != 'undefined'){
                            table_cart += '<div class="pad_bottom_5"><span class="active_link paper_show_incart" data-id="'+cart[i].id+'">Выберите подарочную упаковку</span></div>';                            
                        }
                        table_cart += '<span class="blue_confirm_font">Артикул: </span>' + cart[i].article;
                        table_cart += '<input type="hidden" name="item_id_' + i + '" value="' + cart[i].id + '">';
                        table_cart += '<input type="hidden" name="prop_id_' + i + '" value="' + cart[i].prop_id + '">';                        
                        table_cart += '</td><td style="text-align:center;"><div class="countbox"><a href="javascript://" title="Уменьшить" class="confirm_cart_cursor_left">-</a>';
                        table_cart += '<input type="text" name="count_' + i + '" value="' + cart[i].count + '" id="amount" class="confirm_cart_count" readonly="">';
                        table_cart += '<a href="javascript://" title="Увеличить" class="confirm_cart_cursor_right">+</a>';//href="/confirm_order/cart.php?item_id=' + cart[i].id + '&prop_id=' + cart[i].prop_id + '&del=1"

                        mixmarket.push(cart[i].id);
                        //table_cart += "<script>var __mixm__ = __mixm__ || []; __mixm__.push(['basket',{skulist:"+strt+"}]);</script>";
                        /* POINT! Show add mark (metr) for clothe price */
                        var price_info;
                        var catid = cart[i].category;
                        if(catid == 20376 || catid == 20377 || catid == 20378 || catid == 20379 || catid == 20380 || catid == 20385 || catid == 20387 || catid == 20399 || catid == 20400 || catid == 20401 || catid == 20402 || catid == 20403 || catid == 20404){price_info = 'руб./метр'}else{price_info = 'руб.'}
                        table_cart += '</div></td><td style="text-align:center;">' + cart[i].count * cart[i].price + '&nbsp;'+price_info+'</td><td><a onclick="del_cart_item(' + cart[i].id + ', '+cart[i].prop_id+');" class="dela_img"><img src="/_img/del.png" height="25" alt="Удалить из корзины"></a></td></tr>';

                        if (cart[i]['props'].length > 0)
                        {
                            
                            for (j = 0; j < cart[i]['props'].length; j++) {
                                if( typeof cart[i]['props'][j].img != 'undefined' )
							bg_pr = '<img src="/'+cart[i]['props'][j].img+'">';
						else bg_pr = '';
                                row_count++;
                                table_cart += '<input type="hidden" name="item_id_' + (cart.length + cur_props_len + j) + '" value="' + cart[i].id + '">';
                                table_cart += '<input type="hidden" name="prop_id_' + (cart.length + cur_props_len + j) + '" value="' + cart[i]['props'][j].id + '">';
                                table_cart += '<tr style="border-top: 1px solid rgb(235, 235, 235);"><td>'+ bg_pr + ' ' + cart[i]['props'][j].name;
                                table_cart += '</td><td style="text-align:center;"><div class="countbox"><a href="javascript://" title="Уменьшить" class="confirm_cart_cursor_left">-</a>';
                                table_cart += '<input type="text" name="count_' + (cart.length + cur_props_len + j) + '" value="' + cart[i]['props'][j].count + '" id="amount" class="confirm_cart_count" readonly="">';
                                table_cart += '<a href="javascript://" title="Увеличить" class="confirm_cart_cursor_right">+</a>';//href="/confirm_order/cart.php?item_id=' + cart[i].id + '&prop_id=' + cart[i]['props'][j].id + '&del=1"
                                table_cart += '</div></td><td style="text-align:center;">' + cart[i]['props'][j].count * cart[i]['props'][j].price + '&nbsp;руб.</td><td><a onclick="del_cart_item(' + cart[i].id + ', '+cart[i]['props'][j].id+');" class="dela_img"><img src="/_img/del.png" height="25" alt="Удалить из корзины"></a></td></tr>';
                                sum_item += +cart[i]['props'][j].count * cart[i]['props'][j].price;
                            }
                            
                            cur_props_len += cart[i]['props'].length;
                        }
                    }
                    //table_cart += mixmarket.join(';');
                    table_cart += '</table>' + '<input type="hidden" name="row_count" value="' + row_count + '">' + '</form>';
                    $('#eshop_cart_count').html(count_item);
                    $('#eshop_cart_total').html(sum_item + ' руб.');
                    $('#count_item_cart').html(count_item);
                    $('#total_item_cart').html(sum_item);
                    $('.sector_confirm_cart').html(table_cart);
                    $('.paper_show_incart').on('click', onevent_clickbuy_paper);
					edit_sum_credit(sum_item);
                    $('.confirm_cart_cursor_left, .confirm_cart_cursor_right').click(function() {
                        var action = $(this).attr('class');
                        var block = $(this).closest('div.countbox').find('input#amount');

                        if (action == 'confirm_cart_cursor_right') {
                            block.val(+block.val() + 1);
                            RunTimer();
                        }
                        else if (action == 'confirm_cart_cursor_left' && block.val() > 1) {
                            block.val(+block.val() - 1);
                            RunTimer();
                        }

                    });
                    $('.confirm_order_button_confirm').show();
                    $('#confirm_self_data').show();
					if(sum_item>=3000){
						$('.credit_rassrochka').css("display", "block"); // New credit and rassrochka
						$('#credit_').show();						
						toggle_credit_field();
						$('#credit_text_val').hide();
						} else {
							$('.credit_rassrochka').css("display", "none");
							$('#credit_').hide();
							$('#credit_info_c').hide();
							$('input[name="credit_checked"]').prop('checked',false);
							$('.credit_field').addClass('nonedisp');
							$('#credit_text_val').show();
							//$('.tooltip_credit_text_val').html('Для оформления заказа в кредит не хватает: <span class="red">'+(3000-sum_item)+'</span> руб. <i class="ico_ques"></i>');
						}					
                } else if (data == '0' || data=='') {
                    $('#confirm_self_data').hide();
                    table_cart = '<span class="">Ваша корзина пуста</span>';
                    $('.sector_confirm_cart').html(table_cart);
                    $('#eshop_cart_count').html('0');
                    $('#eshop_cart_total').html('0 руб.');
                    $('#count_item_cart').html('0');
                    $('#total_item_cart').html('0');					
					$('#credit_info_c').hide();
                    $('.sector_confirm_cart').html(table_cart);
                    $('.confirm_order_button_confirm').hide();
                    cart = null;
                } else {
                    $('#confirm_self_data').hide();
                    //table_cart = '<span class="">Извините, проблемы со связью. Пожалуйста, попробуйте перезагрузить страницу.</span>';
                    table_cart = error_messages[data];
                    $('.sector_confirm_cart').html(table_cart);
                    $('#eshop_cart_count').html('0');
                    $('#eshop_cart_total').html('0 руб.');
                    $('#count_item_cart').html('0');
                    $('#total_item_cart').html('0');
					$('#credit_info_c').hide();
                    $('.sector_confirm_cart').html(table_cart);
                    $('.confirm_order_button_confirm').hide();
                    cart = null;
                }
                reload_dop_items(cart);
                return cart;
            }
    );
}

// функции для кредита
function toggle_credit_field(){
	if($('input[name="credit_checked"]').prop('checked')){
		$('.credit_field').removeClass('nonedisp');	
		$('#credit_info_c').show();		
	} else {
		$('#credit_info_c').hide();
		$('.credit_field').addClass('nonedisp');
		}
}

function edit_sum_credit(sum){
var percent_first = +$('select[name="first_pay"]').val();
$('#fullcredit_sum').html(+sum+Math.round(sum*0.05));
$('#sum_firstpay').html(Math.round(+sum*0.1));
$('#sum_paymonth').html(Math.round(+sum*0.102));
}
// функция проверки телефона
function confirm_valid_phone(id)
{
    var flag = true;
    var s = $('#' + id).val();
    if (!s)
        flag = false;
    s = s.replace('/[\(\)-\s]/gi', '');
    if (s.length > 10 && s.length < 13)
    {
        if (!isNaN(s))
        {
            if (s.indexOf('+7') == 0) {
                s = s.substr(2, s.length - 1);
            } else
            if (s[0] == '8')
            {
                s = s.substr(1, s.length - 1);
            }
            if (s.indexOf('+') != -1 || s.length > 10)
            {
                flag = false;
            }
        } else
        {
            flag = false;
        }
    } else
    if (s.length != 10 || isNaN(s))
        flag = false;
    if (!flag) {
        return false;
    }
    else
    {
        $('#' + id).attr('value', s);
        return true;
    }
}

// функция проверки вводимых данных
function confirm_validate_order() {
    message = '';
    message += 'Ошибка заполнения данных!';
    $('.callback_error').hide();
    error = false;

    if (window.confirm_order_form.name.value == 'Имя Фамилия' || window.confirm_order_form.name.value.trim().length == '') {
        error = true;
        message += ' Введите Ваше имя и фамилию.';
    }
    if (window.confirm_order_form.phone.value == 'Телефон' || window.confirm_order_form.phone.value.trim().length == '') {
        error = true;
        message += ' Введите Ваш номер телефона. ';
    }

    var regCheck_phone = new RegExp("^((8|\\+7)[\\- ]?)?(\\(?\\d{3}\\)?[\\- ]?)?[\\d\\- ]{7,10}$");
    if (!regCheck_phone.test(window.confirm_order_form.phone.value)) {    
        message += ' Некорректный мобильный номер. Необходимо корректно ввести номер в международном формате, например +7(926)000-00-00.';
        error = true;
    }

    if ((window.confirm_order_form.email.value != 'E-mail'||$('input[name="credit_checked"]').prop('checked')) && window.confirm_order_form.email.value.trim().length != '') {
        var regCheck = new RegExp("^([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$");
        if (!regCheck.test(window.confirm_order_form.email.value)) {
            message += ' Введите верный e-mail.';
            error = true;
        }
    }
	
       //     if($('input[name="credit_checked"]').prop('checked')){
	//	if(!$('input[name="agreement_credit"]').prop('checked')){
	//	 message += ' Для оформления заказа в кредит необходимо согласиться с условиями соглашения';
	//	 error = true;
	//	}
	//}
    // запись введенных данных в куку
    var data_user = {
        'name': window.confirm_order_form.name.value,
        'phone': window.confirm_order_form.phone.value,
        'email': window.confirm_order_form.email.value,
        'address': window.confirm_order_form.address.value
    };
    // ---------

    var data_user_str = JSON.stringify(data_user);
    $.cookie('conf_dtu', data_user_str, {path: "/"}); //запись в куки

    if (error) {
        $('.callback_error').html(message).show();
        return false;
    } else
        return true;
}

// --------------------------------------

function ajax_buy_item(item_id, prop_id, plus, count, ing)
{
    arg = {item_id: item_id,
        prop_id: prop_id,
        plus: plus,
        count: count};
    if(ing = 1)
        arg['ing'] = 1;
	if( typeof(prop_id) == 'undefined')
		arg['prop_id'] = 0;
    $.post('/confirm_order/cart.php', 
    arg
    ,function(data) {
                    if (data == 'true') {
                        if (window.oneclick != true) {
                            // если это обычная покупка
                            if (!$.cookie('confirm_sh') && window.is_dop_item != 1) {
                                var date = new Date();
                                date.setTime(date.getTime() + (60 * 1000));
                                $.cookie("confirm_sh", "87945389843541987462184", {expires: date, path: '/'});
                                show_confirm_win(); // показ окна для кнопки
                            } else if (window.id_last_item != null) {
                                window.is_dop_item = 0;
                                $('#confirm_add_popup_' + window.id_last_item).slideToggle();
                                show_popup_confirm_add_item();
                            }
                        }else {
                        // если это покупка в один клик
                        oneClickOrder('');
                    }
                    var data_cart = $.cookie('user_session');
                    if (data_cart != null){
                        data_cart = unserialize(data_cart);
                        $('#eshop_cart_count').html(data_cart.eshop_cart_count);
                        $('#eshop_cart_total').html(data_cart.eshop_cart_total_plain+' руб.');
                    } else {
                        $('#eshop_cart_count').html('0');
                        $('#eshop_cart_total').html('0 руб.'); 
                    }
                    } else {                        
                        alert(error_messages[data]);
                    }
                });

}

function google_cart(){
    $.post('confirm_order/get_cart_google.php',
        {},
        function(data) {
            if(data!=''){
                $('#google_cart').html(data);
            }
        });
}

function ajax_buy_item_row(data_item, plus, ing, callback){
    arg = data_item;    
    arg['plus'] = plus;        
    if(ing == 1)
        arg['ing'] = 1;
	
    $.post('/confirm_order/cart.php', 
    arg
    ,function(data) {
                    if (data == 'true') {
                        if(typeof callback == 'undefined'){
                        if (window.oneclick != true) {
                            // если это обычная покупка
                            if (!$.cookie('confirm_sh') && window.is_dop_item != 1) {
                                var date = new Date();
                                date.setTime(date.getTime() + (60 * 1000));
                                $.cookie("confirm_sh", "87945389843541987462184", {expires: date, path: '/'});
                                show_confirm_win(); // показ окна для кнопки
                            } else if (window.id_last_item != null) {
                                window.is_dop_item = 0;
                                $('#confirm_add_popup_' + window.id_last_item).slideToggle();
                                show_popup_confirm_add_item();
                            }
                        }else {
                        // если это покупка в один клик
                        oneClickOrder('');
                        }
                    } else callback();
                    var data_cart = $.cookie('user_session');
                    if (data_cart != null){
                        data_cart = unserialize(data_cart);
                        $('#eshop_cart_count').html(data_cart.eshop_cart_count);
                        $('#eshop_cart_total').html(data_cart.eshop_cart_total_plain+' руб.');
                    } else {
                        $('#eshop_cart_count').html('0');
                        $('#eshop_cart_total').html('0 руб.'); 
                    }
                    
                    } else {                        
                        alert(error_messages[data]);
                    }
                });

}

function merge_data_item(arrays){
    if(arguments.length < 1) return false;
    var result = {};
    var count_row = 0;
    for(var i=0; i<arguments.length; i++) {
        if(!(arguments[i] instanceof Object) || arguments[i].length < 1)
            continue;
        if(typeof arguments[i]['item_id'] == 'undefined' || typeof arguments[i]['prop_id'] == 'undefined' || typeof arguments[i]['count'] == 'undefined')
            continue;
        for(var key in arguments[i])
            result[key+'_'+count_row] = arguments[i][key];
        count_row++;
    }
    result['row_count'] = count_row;
    return result;
}

$(document).ready(function(){

    $(".left_cursor_dop_items").hover(function(){
        $( ".left_cursor_dop_items_show" ).css( "display", "block" );
    }, function(){
        $( ".left_cursor_dop_items_show" ).css( "display", "none" );
    });

    $(".right_cursor_dop_items").hover(function(){
        $( ".right_cursor_dop_items_show" ).css( "display", "block" );
    }, function(){
        $( ".right_cursor_dop_items_show" ).css( "display", "none" );
    });

});