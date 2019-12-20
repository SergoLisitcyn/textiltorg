BX.addCustomEvent(window, 'OnBasketChange', function(){
    var box = $('#box-line-cart');

    $.get(location.href, {'ajax_action_line_cart': 'Y'}, function(data) {
        if (box.length) {
            box.html(data);
        }
    });
});