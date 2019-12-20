$(function(){
    $('#gift_wrap_cart .buy_button').on( "click", function() {
        var button = $(this);

        $.fancybox.close();
        AddProduct(button);

        return false;
    });
});