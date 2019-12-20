$(function(){
	
	$('#lay_body').on('click', '.inner_sub li', function(){
			$(this).parent('.inner_sub').find('li').removeClass('active');
			$(this).addClass('active');
			$(this).parent('.inner_sub').parent('.inner').find('.eshop-item-small__img').attr({
				src: $(this).find('a').attr('data-detail'),
				href: $(this).find('a').attr('href'),
			});
			return false;
		});
	
    $('#lay_body').on("click",'input.inyourcart', function() {
        var button = $(this);

        AddGoodToCart(button);

        return false;
    });

    $('#lay_body').on( "click",'.add-compare-button', function() {
        var button = $(this),
            path = $(this).attr('href');

        if (button.hasClass('add-compare')) {
            path = button.attr('data-add-compare-url');
            button.text('Удалить из сравнения777');
        }

        if (button.hasClass('delete-compare')) {
            path = button.attr('data-delete-compare-url');
            button.text('Добавить к сравнению');
        }

        button
            .toggleClass('add-compare')
            .toggleClass('delete-compare');

        $.get(path, function() {
            BX.onCustomEvent(window, 'OnCompareChange');
        });

        return false;
    });

	$('.buy-button').fancybox({
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
	
	$(".watch-video").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		beforeShow  : function(){
			audio('wuf-4');
		},
		beforeClose : function(){
			audio('wuf-3');
		},
	});
	
	$(".test-drive").fancybox({
		maxWidth	: 1024,
		maxHeight	: 780,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
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

        $.get(path);

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

});