$(function(){
    $('.product-slider-item-add-to-cart').on( "click", function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),
            guaranteePath = button.attr('data-guarantee-path'),
            giftWrapPath = button.attr('data-gift-wrap-path');

        $("#popup-cart div[data-retailrocket-markup-block]").attr("data-product-id", id);

        AddGoodToCart(button);

        return false;
    });
/*
    var deviceType = /iPad/.test(navigator.userAgent) ? "m" : /webOS|Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
    var countItems = 4;

    if (deviceType == 'm') {
        countItems = 1;
    }
*/
    var countItems = 4;

    if (screen.width <= '768') {
        countItems = 1;
    }


    console.log(deviceType);

    $('.product-slider').slick({
        slidesToShow: countItems,
        slidesToScroll: 1,
        lazyLoad: 'progressive',
    });

    $('.product-slider-wrap').show();

    $('.fancybox-popup-article').fancybox({
        maxWidth    : '100%',
        maxHeight   : '100%',
        width       : 'auto',
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        beforeShow  : function(){
            audio('wuf-4');
        },
        beforeClose : function(){
            audio('wuf-3');
        },
        afterClose: function() {
            setTimeout(function() {
                OpenPopupCartLast();
            }, 50);
        }
    });

    // $('.product-slider-item-add-to-cart').click(function() {
    //     var button = $(this),
    //         path = button.attr('data-path');
    //
    //     $.get(path, function() {
    //         BX.onCustomEvent(window, 'OnBasketChange');
    //     });
    //
    //     return false;
    // });
});