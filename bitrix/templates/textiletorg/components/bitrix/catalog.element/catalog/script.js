$(function() {

    $(".3d-popup").fancybox({
        autoSize    : true,
        autoWidth    : true,
        fitToView	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none',
        titleShow	: false,
        'title'		: "3D",
        width : '1200px'
    });

    $('.multiple-items').slick({
        infinite: true,
        slidesToShow: 7,
        slidesToScroll: 7,
    })


    $('.readmore').readmore({
        speed: 75,
        maxHeight: 76,
        moreLink: '<a href="#">Читать далее »</a>',
        lessLink: ''
    });

    $('#incart-button').on( "click", function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path'),
            guaranteePath = button.attr('data-guarantee-path'),
            giftWrapPath = button.attr('data-gift-wrap-path');

		$("#popup-cart div[data-retailrocket-markup-block]").attr("data-product-id", id);

        AddGoodToCart(button);

        return false;
    });

    function formatTitle() {
        return 'Описание'; // + $(this).data("caption");
    }


    $(".prop-popup").fancybox({
        maxWidth	: 600,
        maxHeight	: 500,
        autoSize    : true,
        fitToView	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none',
        titleShow	: false,
        'title'		: formatTitle,
    });

    $('.buy-one-click').fancybox({
        maxWidth    : 944,
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        afterLoad : function() {
            $('.buy_one_click input[name="GOOD_ID"]').val($(this.element).attr('data-good-id'));
        }
    });

    var incart = $('#incart-button');

    $('#guarantee').change(function() {
        var select = $(this),
            option = select.find('option:selected');

        $('#guarantee-price').text(option.attr('data-print-price'));

        if (parseInt(option.attr('data-price')) > 0) {
            incart.attr('data-guarantee-path', option.attr('data-path'));
        } else {
            incart.attr('data-guarantee-path', '');
        }
    });

    $('a[href="#close-fancybox"]').click(function() {
        $.fancybox.close();
        return false;
    });

    $('#gift_wrap .gift-wrap-button').click(function() {
        var button = $(this);

        $('#gift-wrap-picture img').attr('src', button.attr('data-picture'));
        $('#gift-wrap-price').text(button.attr('data-print-price'));
        $('#gift-wrap-data').addClass('show');

        incart.attr('data-gift-wrap-path', button.attr('data-path'));

        $.fancybox.close();
        return false;
    });

    $('#popup-offers .buy_button').click(function() {
        var button = $(this),
            id = button.attr('data-id'),
            path = button.attr('data-path');

        $(button).closest(".gift_block").addClass("add-overlay");
        $.get(path, {}, function(){
            BX.onCustomEvent('OnBasketChange');
            $(button).closest(".gift_block").removeClass("add-overlay");
        });

        return false;
    });
	$('.fancybox-gall').fancybox({
         maxWidth  : '100%',
         maxHeight : '100%',
         autoSize : true
    });

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
            OpenPopupCartLast();
            return false;
        }
    });

    $('.fancybox-popup-article-item').fancybox({
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

    $('.left_block_cart .picture_gallery img').first().elevateZoom({
        responsive: true,
        gallery:'multiple-items',
        galleryActiveClass: 'focus_img',
        zoomType: "inner",
        cursor: "crosshair",
        zoomWindowFadeIn: 500,
        zoomWindowFadeOut: 750
   });
});

$(function(){
    /*Форма расчета доставки*/
    $('#pv_category').change(function() {
        if($(this).val()!='' && $(this).val()!=0){
            $('.pv_block_brend').css({display:'inline-block'});
        }else{
            $('.pv_block_brend').hide();
            $('.pv_block_model').hide();
        }
        $.fancybox.update();
    });
    $('#pv_brend').change(function() {
        if($(this).val()!='' && $(this).val()!=0){
            $('.pv_block_model').css({display:'inline-block'});
        }else{
            $('.pv_block_model').hide();
        }
        $.fancybox.update();
    });
});
setInterval(function(){
    $("#colorBlink").toggleClass("color_red");
},1000)
