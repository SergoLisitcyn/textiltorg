$(function(){
    $('.items_2 .item .button.buy').on( "click", function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),

            popup = $('#add_basket'),
            title = button.parents('.item').find('p').first().text(),
            image = button.parents('.item').find('img').attr('src'),
            price = button.parents('.item').find('.more-price').text(),
            price_rb = button.parents('.item').find('.more-price-rb-text').text();

        popup.find('.info_block .text').text(title);
        popup.find('.info_block .text').append('<div class="popup-price"><big>'+price+"</big> руб.</div>");
        if (price_rb.length) {
            popup.find('.info_block .text').append('<div class="popup-price-rb">'+price_rb+" <small>руб.<small></div>");
        }
        popup.find('.info_block .img img').attr('src', image).attr('alt', title);

        $.fancybox(popup, {
            maxWidth    : 800,
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

        setTimeout(function() {
            $message.remove();
        }, 50000);

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnBasketChange');
        });

        return false;
    });

    $('a[href="#close-fancybox"]').click(function() {
        $.fancybox.close();
        return false;
    });
});