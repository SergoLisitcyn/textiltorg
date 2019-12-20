$(function(){
    $('.inyourcart').on( "click", function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path');

        AddProduct(button);

        return false;
    });
});