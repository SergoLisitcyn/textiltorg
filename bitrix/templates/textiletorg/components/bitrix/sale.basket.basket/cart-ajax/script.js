var box = $('#box-full-cart');
var updateCart = 0;

BX.addCustomEvent(window, 'OnBasketChange', function(){
    var update = 0;
    updateCart++;
    update = updateCart;

    $.get(location.href, {'ajax_action_full_cart': 'Y'}, function(data) {
        if (box.length && update == updateCart) {
            box.html(data);
        }
    });
});

var countTimer;

$('body').on('click', '.countbox .confirm_cart_cursor_left', function() {
    var input = $(this).parent().find('input'),
        count = parseInt(input.val()),
        id = input.attr('data-id');

    clearInterval(countTimer);

    if (count > 1)
    {
        input.val(count - 1);
    }

    countTimer = setTimeout(function() {
        updateCount(id, input.val());
    }, 500);

    return false;
});

$('body').on('click', '.countbox .confirm_cart_cursor_right', function() {
    var input = $(this).parent().find('input'),
        count = parseInt(input.val()),
        id = input.attr('data-id');

    clearInterval(countTimer);

    if (count < 99)
    {
        input.val(count + 1);
    }

    countTimer = setTimeout(function() {
        updateCount(id, input.val());
    }, 500);

    return false;
});

function updateCount(id, count)
{
    var postData = {
            action: 'quantity',
            id: id,
            count: count
        };

    BX.ajax({
        url: '/bitrix/templates/textiletorg/components/bitrix/sale.basket.basket/cart-ajax/ajax.php',
        method: 'POST',
        data: postData,
        onsuccess: function(result)
        {
            location.reload();
            //BX.onCustomEvent(window, 'OnBasketChange');
        }
    });
}
