var map;
var count = 0;

$(document).ready(function() {
    $('a[href="#as-stores-popup"]').fancybox({
        maxWidth    : "100%",
        maxHeight   : 400,
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        afterShow : function() {
            if (AS_STORE_MAP_GROUP !== undefined) {
                map.container.fitToViewport();
                map.setBounds(map.geoObjects.getBounds(),{
                    checkZoomRange:true,
                    callback: function(){
                        if (map.getZoom() > 10)
                        {
                            map.setZoom(10);
                        }
                    }
                });
            }
        },
        afterClose:function() {
            BX.onCustomEvent(window, 'OnMapCloseFancybox');
        }
    });

    // tooltip
    var container = $('.tooltip-message-stores'),
        hint = $('#as-stores-hint');

    container.add(hint)
        .mouseover(function() {
            hint
                .css({
                    top: container.position().top - hint.innerHeight(),
                    left: container.position().left
                })
                .addClass('show');
        })
        .mouseout(function() {
            hint.removeClass('show');
        });

    ymaps.ready(init);

    function init() {
        if (AS_STORE_MAP_GROUP === undefined) {
            return false;
        }

        $('#as-stores-hint ul li').click(function() {
            var address = $(this).text();
            $.fancybox('#as-stores-popup', {
                maxWidth    : "100%",
                maxHeight   : 400,
                fitToView   : false,
                autoSize    : true,
                closeClick  : false,
                openEffect  : 'none',
                closeEffect : 'none',
                afterShow : function() {
                    if (AS_STORE_MAP_GROUP !== undefined) {
                        map.container.fitToViewport();
                        $('.as-stores-popup-menu-container li:contains("'+address+'")').click();
                    }
                },
                afterClose:function() {
                    BX.onCustomEvent(window, 'OnMapCloseFancybox');
                }
            });
        });

        var center;

        $.each(AS_STORE_MAP_GROUP, function (index, group) {
            $.each(group.items, function (index, item) {
                center = item.center;
            });
        });

        // Создание экземпляра карты.
        map = new ymaps.Map('as-stores-popup-map', {
            center: center,
            zoom: 10,
            controls: ['zoomControl']
        });

        // Контейнер для меню.
        var menu = $('<ul class="nav nav-list"/>');

        // Перебираем все группы.
        $.each(AS_STORE_MAP_GROUP, function (index, group) {
            // DOM-представление группы.
            var menuItem = $('<li class="nav-header '+group.style+'">' + group.name + '</li>'),
                // Создадим коллекцию для геообъектов группы.
                collection = new ymaps.GeoObjectCollection(null, { preset: group.preset });

            // Добавляем коллекцию на карту.
            map.geoObjects.add(collection);

            menuItem
                // Добавляем пункт в меню.
                .appendTo(menu)
                // Навешиваем обработчик клика по пункту меню.
                .on('click', function (e) {
                    // Скрываем/отображаем пункты меню данной группы.
                    $(this)
                        .nextUntil('.nav-header')
                        .removeClass('active')
                        .slideToggle('fast');

                    // Скрываем/отображаем коллекцию на карте.
                    if(collection.getParent()) {
                        map.geoObjects.remove(collection);
                    }
                    else {
                        map.geoObjects.add(collection);
                    }
                });

            // Перебираем элементы группы.
            $.each(group.items, function (index, item) {
                // DOM-представление элемента группы.
                var menuItem = $('<li>' + item.address + '</li>'),
                    // Создаем метку.
                    placemark = new ymaps.Placemark(item.center, {
                        balloonContentHeader: item.address,
                        balloonContent: item.time + '<br>' + item.phone,
                        balloonContentFooter: item.hint
                    });

                count++;

                // Добавляем метку в коллекцию.
                collection.add(placemark);

                menuItem
                    // Добавляем пункт в меню.
                    .appendTo(menu)
                    // Навешиваем обработчик клика по пункту меню.
                    .on('click', function (e) {
                        // Отменяем основное поведение (переход по ссылке)
                        e.preventDefault();

                        // Выставляем/убираем класс active.
                        menuItem
                            .toggleClass('active')
                            .siblings('.active')
                            .removeClass('active');

                        // Открываем/закрываем баллун у метки.
                        if(placemark.balloon.isOpen()) {
                            placemark.balloon.close();
                        }
                        else {
                            // Плавно меняем центр карты на координаты метки.
                            placemark.balloon.open();
                        }
                    });

                BX.addCustomEvent(window, 'OnMapCloseFancybox', function(){
                    placemark.balloon.close();
                    $('.as-stores-popup-menu-container li').removeClass('active');
                });
            });
        });

        // Добавляем меню в сайдбар.
        menu.appendTo($('.as-stores-popup-menu-container'));
    }
});

