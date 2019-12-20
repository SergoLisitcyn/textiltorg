$(function(){
    $('#lay_body').on('click', '.inner_sub li', function(){
        $(this).parent('.inner_sub').find('li').removeClass('active');
        $(this).addClass('active');
        $(this).parent('.inner_sub').parent('.inner').find('.eshop-item-small__img').attr({
            src: $(this).find('a').attr('href')
        });

        return false;
    });

    $('#lay_body').on("click",'button.inyourcart', function() {
        var button = $(this);

        AddGoodToCart(button);

        return false;
    });



    $(".n_help").hover(function () {
            var el = $(this);
            text = el.attr('data-tool-text');
            el.html('<div class = "n_help_tool">' + text + '</div>');
        },
        function () {
            var el = $(this);
            el.html('');
        }
    );


    $('#lay_body').on( "click",'.add-compare-button', function() {
        var button = $(this),
            path = $(this).attr('href');

        if (button.hasClass('add-compare')) {

            if (button.hasClass('n_c_el')) {
                path = button.attr('data-add-compare-url');
                button.html('<div class = "n_catalog_iz_img_red"></div>');

            } else {
                path = button.attr('data-add-compare-url');
                button.text('Удалить из сравнения');
            }

        }

        if (button.hasClass('delete-compare')) {

            if (button.hasClass('n_c_el')) {
                path = button.attr('data-add-compare-url');
                button.html('<div class = "n_catalog_iz_img"></div>');
            } else {
                path = button.attr('data-delete-compare-url');
                button.text('Добавить к сравнению');
            }

        }

        button
            .toggleClass('add-compare')
            .toggleClass('delete-compare');

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnCompareChange');
        });

        return false;
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
        },
        beforeShow  : function(){
            audio('wuf-4');
        },
        beforeClose : function(){
            audio('wuf-3');
        },
    });

    $('.popup-offers .buy_button').click(function() {
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

    $('a[href="#open-delimiter"]').click(function() {
        if (!$(this).hasClass('show'))
        {
            $(this).prev('.desc-delimiter').addClass('show');
            $(this).text('Скрыть').addClass('show');
        }
        else
        {
            $(this).prev('.desc-delimiter').removeClass('show');
            $(this).text('Подробнее...').removeClass('show');
        }

        return false;
    });
});

$(document).ready(function(){

    $(".grid-btn").click(function(){

        $('.grid-list .itemgrid .name').matchHeight();
        $('.grid-list .itemgrid .item_content').matchHeight();

        $('.grid-list .item').css({
            'height':'auto'
        });
    });

    $("#n_c_h_catalog").click(function() {
        $("#n_c_h_catalog_content").removeClass('hide');
        $("#n_c_h_vibor_content").addClass('hide');
        $("#n_c_h_proiz_content").addClass('hide');

        $("#n_c_h_catalog").addClass('n_c_h_active');
        $("#n_c_h_vibor").removeClass('n_c_h_active');
        $("#n_c_h_proiz").removeClass('n_c_h_active');

        $("#catalog-sort").removeClass('hide');
        $(".n_call_line").removeClass('hide');
    });

    $("#n_c_h_vibor").click(function() {
        $("#n_c_h_catalog_content").addClass('hide');
        $("#n_c_h_vibor_content").removeClass('hide');
        $("#n_c_h_proiz_content").addClass('hide');

        $("#n_c_h_catalog").removeClass('n_c_h_active');
        $("#n_c_h_vibor").addClass('n_c_h_active');
        $("#n_c_h_proiz").removeClass('n_c_h_active');

        $("#catalog-sort").addClass('hide');
        $(".n_call_line").addClass('hide');
    });

    $("#n_c_h_proiz").click(function() {
        $("#n_c_h_catalog_content").addClass('hide');
        $("#n_c_h_vibor_content").addClass('hide');
        $("#n_c_h_proiz_content").removeClass('hide');

        $("#n_c_h_catalog").removeClass('n_c_h_active');
        $("#n_c_h_vibor").removeClass('n_c_h_active');
        $("#n_c_h_proiz").addClass('n_c_h_active');

        $("#catalog-sort").addClass('hide');
        $(".n_call_line").addClass('hide');


    });

});