$(function(){

    $('.more-goods-product-page').slick({
        slidesToShow: 4,
        slidesToScroll: 4,
        variableWidth: true,
    });

    $('.slider-more-good-item-add-to-cart').click(function() {
        var button = $(this),
            path = button.attr('data-path');

        if (button.hasClass('is-active'))
        {
            $.get(path, function() {
                BX.onCustomEvent(window, 'OnBasketChange');
            });
        }

        return false;
    });
});