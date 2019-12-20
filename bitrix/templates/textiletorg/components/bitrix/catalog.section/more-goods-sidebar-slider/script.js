$(function(){
    $('button.inyourcart').on( "click", function() {
        var button = $(this);

        AddGoodToCart(button);

        return false;
    });

    $('.slider1').bxSlider({
        slideWidth: 212,
        minSlides: 3,
        maxSlides: 3,
        mode: 'vertical',
        pager: false
    });
});