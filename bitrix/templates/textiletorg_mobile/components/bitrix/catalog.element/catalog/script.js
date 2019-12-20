$(function() {

    $('.buy-cart-button').on( 'click', function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),

            popup = $('#add_basket'),
            title = $('.shop_item h1').text(),
            image = $('.shop_item .img_block .title_img').attr('src');
			price = button.parents('.shop_item').find('.element-price').text(),
            price_rb = button.parents('.shop_item').find('.element-price-rb').text();

        popup.find('.info_block .text').text(title);
		popup.find('.info_block .text').append('<div class="popup-price"><big>'+price+"</big> руб.</div>");
        if (price_rb.length) {
            popup.find('.info_block .text').append('<div class="popup-price-rb">'+price_rb+" <small>руб.<small></div>");
        }
        popup.find('.info_block .img img').attr('src', image).attr('alt', title);

        $.fancybox(popup, {
            maxWidth    : '100%',
            maxHeight   : 600,
            fitToView   : false,
            width       : '100%',
            height      : 'auto',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none',
            title: 'Товар добавлен в корзину'
        });

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnBasketChange');
        });

        return false;
    });

    $('#popup-offers .buy_button').on( 'click', function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),

            popup = $('#add_basket'),
            title = $('.shop_item h1').text(),
            image = $('.shop_item .img_block .title_img').attr('src');
			price = button.parents('.shop_item').find('.element-price').text(),
            price_rb = button.parents('.shop_item').find('.element-price-rb').text();

        popup.find('.info_block .text').text(title);
		popup.find('.info_block .text').append('<div class="popup-price"><big>'+price+"</big> руб.</div>");
        if (price_rb.length) {
            popup.find('.info_block .text').append('<div class="popup-price-rb">'+price_rb+" <small>руб.<small></div>");
        }
        popup.find('.info_block .img img').attr('src', image).attr('alt', title);

        $.fancybox(popup, {
            maxWidth    : '100%',
            maxHeight   : 600,
            fitToView   : false,
            width       : '100%',
            height      : 'auto',
            autoSize    : false,
            closeClick  : false,
            openEffect  : 'none',
            closeEffect : 'none',
            title: 'Товар добавлен в корзину'
        });

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnBasketChange');
        });

        return false;
    });

    $('a[href="#close-fancybox"]').click(function() {
        $.fancybox.close();
        return false;
    });

    $('.buy-one-click').fancybox({
        maxWidth    : '100%',
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        width       : 'auto',
        openEffect  : 'none',
        closeEffect : 'none',
        afterLoad : function() {
            $('#buy_one_click input[name="GOOD_ID"]').val($(this.element).attr('data-good-id'));
            $('#buy_one_click input[name="GOOD_NAME"]').val($(this.element).attr('data-good-name'));
            $('#buy_one_click input[name="GOOD_URL"]').val($(this.element).attr('data-good-url'));
            $('#buy_one_click input[name="PRICE"]').val($(this.element).attr('data-price'));
            $('#buy_one_click input[name="CURRENCY"]').val($(this.element).attr('data-currency'));
        }
    });

    $('.help-info').fancybox({
        type        : 'ajax',
        maxWidth    : '100%',
        fitToView   : false,
        autoSize    : true,
        openEffect  : 'none',
        closeEffect : 'none',
        afterShow : function() {
            $('a[href="#close-fancybox"]').click(function() {
                $.fancybox.close();
                return false;
            });
        }
    });
});