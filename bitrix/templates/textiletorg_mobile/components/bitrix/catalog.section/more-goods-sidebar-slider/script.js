$(function(){
    $('.add-cart').on( 'click', function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),

            popup = $('#add_basket'),
            title = button.parents('.shop_item').find('h2').text(),
            image = button.parents('.shop_item').find('.img_block img').attr('src'),
			price = button.parents('.shop_item').find('.list-price').text(),
            price_rb = button.parents('.shop_item').find('.list-price-rb').text();

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
            location.reload();
        });

        return false;
    });

    $("#add-products-button").click(function() {
        $("#add-products").toggleClass("open");
        if ($("#add-products").hasClass("open")) {
            $("#add-products").slideDown();
        } else {
            $("#add-products").slideUp();
        }
    });
    $("#click-send-form").click(function() {
        $("#confirm_order_btn").click();
    });

});