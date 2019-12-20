$(function() {
    $('.flexslider').flexslider({
        animation: "slide",
        animationLoop: true,
        start: function(slider){
            $('body').removeClass('loading')
        }
    });
});