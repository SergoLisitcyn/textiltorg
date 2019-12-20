$(function() {
    BX.addCustomEvent(window, 'OnBasketChange', function(){
        $.get(location.href, {'ajax_action_cart': 'Y'}, function(data) {
            var box = $('#box-header-cart');

            if (box.length)
                 box.html(data);
        });
    });
});