$(function(){
    $('.add-cart').on( 'click', function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),

            popup = $('#add_basket'),
            title = button.parents('.product').find('.product__title').text(),
            image = button.parents('.product').find('.product__image img').attr('src'),
			price = button.parents('.product').find('.product__price .price_num').text(),
            price_rb = button.parents('.product').find('.product__price').text();

        popup.find('.info_block .text').text(title);
		popup.find('.info_block .text').append('<div class="popup-price"><big>'+price+"</big> руб.</div>");
        /*if (price_rb.length) {
            popup.find('.info_block .text').append('<div class="popup-price-rb">'+price_rb+" <small>руб.<small></div>");
        }*/
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

    $('#open-filter').click(function() {
        $('#catalog-sort').hide();
        $('#catalog-items').hide();
        $('.pagi').hide();
        $('#catalog-filter').show();
        return false;
    });

    $('#close-filter').click(function() {
        $('#catalog-sort').show();
        $('#catalog-items').show();
        $('.pagi').show();
        $('#catalog-filter').hide();
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
});