$(function(){
    /*
    * Scroll to top
    * */
    $(window).scroll(function() {
        if($(this).scrollTop() != 0) {
            $('#toTop').fadeIn();
        } else {
            $('#toTop').fadeOut();
        }
    });
    $('#toTop').click(function() {
        $('body,html').animate({scrollTop:0},800);
    });

    /*
    * Product page, tabs
    * */
    var tabContainers = $('div.tabs > div'); // получаем массив контейнеров
    tabContainers.hide().filter(':first').show(); // прячем все, кроме первого
    if( location.hash == '#comment' ){
        tabContainers.hide();
        $('#item_rev').show();
    }
    // далее обрабатывается клик по вкладке
    $('div.tabs ul.tabNavigation a').click(function () {
        tabContainers.hide(); // прячем все табы
        tabContainers.filter(this.hash).show(); // показываем содержимое текущего
        $('div.tabs ul.tabNavigation a').removeClass('selected'); // у всех убираем класс 'selected'
        $(this).addClass('selected'); // текушей вкладке добавляем класс 'selected'
        return false;
    }).filter(':first').click();
    if(location.hash == '#comment'){
        $('div.tabs ul.tabNavigation a').filter('.otzuv').click();
    }

    $(".showbottom").click( function() {
        if ($(this).next('div').css('display') == 'none') {
            $(this).next('div').slideDown();
        } else {
            $(this).next('div').slideUp();
        }

        if ($(this).parent().next('div').css('display') == 'none') {
            $(this).parent().next('div').slideDown();
        } else {
            $(this).parent().next('div').slideUp();
        }

        return false;
    });

    $('.tooltip-message')
        .mouseover(function() {
            var text = $(this).attr('data-tooltipe-text'),
                message = $('<div class="tooltip-message-container"/>');

            message
                .attr('id', 'tooltipe-message')
                .html(text);

            $(this).append(message);
        })
        .mouseout(function() {
            var message = $('#tooltipe-message');
            message.remove();
        });

    $('.livetex-btn').click(function(event) {
        LiveTex.showWelcomeWindow();
        console.log(456456);
    });

    $('.tooltip-message-click').click(function(event) {
        var text = $(this).attr('data-tooltipe-text'),
            message = $('<div class="tooltip-message-container"/>');

        $('#tooltipe-message').remove();

        message
            .attr('id', 'tooltipe-message')
            .html(text);

        $(this).append(message);
        event.stopPropagation();
    });

    $('body').on('click', '.tooltip-message-click-fixed', function(event) {
        var text = $(this).attr('data-tooltipe-text'),
            message = $('<div class="tooltip-message-container"/>'),
            offset = $(this).attr('data-tooltipe-offset');

        $('#tooltipe-message').remove();

        message
            .attr('id', 'tooltipe-message')
            .html(text)
            .css({
                top : $(this).offset().top - $(window).scrollTop(),
                left : $(this).offset().left,
                bottom: 'auto',
                position: 'fixed'
            });

        $('body').append(message);

        $('#tooltipe-message').css({
            top: $('#tooltipe-message').offset().top - $('#tooltipe-message').innerHeight() - $(window).scrollTop() - offset
        });

        event.stopPropagation();
    });

    $('body').click(function(e) {
        var message = $('#tooltipe-message');
        if (message.length) {
            message.remove();
        }
    });
});

//Музыка при наведениии на эелементы
function audio(audio){
    if ($(".custom-musiconoff-default").hasClass("off")) {
        return;
    }

    $("#audio").html('<source src="/bitrix/templates/textiletorg/audio/'+audio+'.mp3"></source>');
    $("#audio").attr({
        'src':'/bitrix/templates/textiletorg/audio/'+audio+'.mp3',
        'volume':0.5,
        'autoplay':'autoplay'
    });
};

//Окошки
$('.fancybox').fancybox({
    maxWidth	: '100%',
    maxHeight	: '100%',
    width  		: 'auto',
    fitToView	: false,
    autoSize	: true,
    closeClick	: false,
    openEffect	: 'none',
    closeEffect	: 'none',
    afterLoad   : function(current, previous){
        //ckesh();
    },
    beforeShow	: function(){
        audio('wuf-4');
    },
    beforeClose	: function(){
        audio('wuf-3');
    },
});
$('a[href="#close-fancybox"]').click(function() {
    $.fancybox.close();
    return false;
});

