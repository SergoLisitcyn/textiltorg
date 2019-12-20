$(function(){
    $('button.inyourcart').on( "click", function() {
        var button = $(this);

        AddGoodToCart(button);

        return false;
    });

    $('.fancybox-popup-article-sidebar').fancybox({
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
        }
    });
});