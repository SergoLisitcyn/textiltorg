$(window).scroll(function() {
    if ($(this).scrollTop() > 50){
        $('.textiletorg-basket-fixed-prototype-default').fadeIn();
    } else {
        $('.textiletorg-basket-fixed-prototype-default').fadeOut();
    }
});