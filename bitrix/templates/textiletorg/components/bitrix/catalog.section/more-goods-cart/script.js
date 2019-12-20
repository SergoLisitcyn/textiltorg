$(function(){
    $('.slider-more-goods').slick({
        slidesToShow: 4,
        slidesToScroll: 4
    });

    $('.slider-more-good-item-add-to-cart').click(function() {
        var button = $(this),
            path = button.attr('data-path');

        if (button.hasClass('is-active'))
        {
            button.removeClass('is-active').text('...');

            $.get(path, function() {
                BX.onCustomEvent(window, 'OnBasketChange');
                button.addClass('is-active').text('Добавить');
            });
        }

        return false;
    });
});