function AddGuaranteeToCart(button, callback) {
    var path = button.attr('data-guarantee-path');

    if (path) {
        $.get(path, function() {
            callback();
        });
    } else {
        callback();
    }
}

function AddGiftWrapToCart(button, callback) {
    var path = button.attr('data-gift-wrap-path');

    if (path) {
        $.get(path, function() {
            callback();
        });
    } else {
        callback();
    }
}

function AddGoodToCart(button) {
    var path = button.attr('data-path');

    $.fancybox.showLoading();

    OpenPopupCart(button);

	/*
    $.get(path, function() {
        AddGuaranteeToCart(button, function() {
            AddGiftWrapToCart(button, function() {
                BX.onCustomEvent(window, 'OnBasketChange');
            });
        });
    });
	*/
	$.ajax({
		type: "GET",
		url: path,
		complete: function(){
			AddGuaranteeToCart(button, function() {
				AddGiftWrapToCart(button, function() {
					BX.onCustomEvent(window, 'OnBasketChange');
				});
			});
		}
	});
}

function OpenPopupCart(button) {
    var id = button.attr('data-id'),
        html = GetListTemplateItem(button);

    $('#popup-cart table tbody').html(html);

    $.fancybox.open({
        title: 'Оформление заказа',
        href: '#popup-cart',
        maxWidth : 944,
        fitToView : false,
        autoSize : true,
        closeClick : false,
        openEffect : 'none',
        closeEffect : 'none',
        openSpeed: 0,
        afterShow: function() {
            $('#popup-cart-more-goods').html('');
            $.post('/ajax/popup-cart-more-goods.php', {id : id}, function(data) {
                if (data.length) {
                    $('#popup-cart-more-goods').html(data);
                    $('.slider-more-goods').slick({
                        slidesToShow: 4,
                        slidesToScroll: 4
                    });

                    $('.slider-more-good-item-add-to-cart').click(function() {
                        var path = $(this).attr('data-path'),
                            html = GetListTemplateItem($(this));

                        $('#popup-cart table tbody').append(html);
                        $.fancybox.update();

                        $.get(path, function() {
                            BX.onCustomEvent(window, 'OnBasketChange');
                        });

                        return false;
                    });

                    $.fancybox.update();
                }
            });

            $('a[href="#fancybox-close"]').click(function() {
                $.fancybox.close();
                return false;
            });
        },
    });
}

function OpenPopupCartLast()
{
    $.fancybox.open({
        title: 'Оформление заказа',
        href: '#popup-cart',
        maxWidth : 680,
        fitToView : false,
        autoSize : true,
        closeClick : false,
        openEffect : 'none',
        closeEffect : 'none',
        openSpeed: 0,
        beforeShow  : function(){
            audio('wuf-4');
        },
        beforeClose : function(){
            audio('wuf-3');
        },
    });
}

function GetListTemplateItem(button)
{
    var name = button.attr('data-name'),
        picture = button.attr('data-picture'),
        vendor = button.attr('data-vendor'),
        price = button.attr('data-price'),
        price_html = '';
        html = '';

    price_html = '<td class="popup-cart-good-color-red">';
    price_html += '<big>'+price+'</big> руб.';

    price_html += '</td>';

    html = '<tr>'+
            '<td width="100">'+
                '<div class="popup-cart-good-picture" style="background: url('+picture+')"></div>'+
            '</td>'+
            '<td>'+
                '<div class="popup-cart-product">'+name+'</div>'+
            '</td>'+
            price_html+
        '</tr>';

    return html;
}