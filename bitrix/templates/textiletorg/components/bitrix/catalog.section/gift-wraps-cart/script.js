$(function(){
    $('#gift_wrap_cart .buy_button').on( "click", function() {
        var button = $(this),
            path = button.attr('data-path');

        $.fancybox.close();

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnBasketChange');
        });

        return false;
    });
});