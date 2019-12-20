var map;
var count = 0;

$(document).ready(function() {

    if ($('.store-address').height() > 600) {
        $('.store-address').css('height', '600px');
        $('.store-address').css('overflow', 'auto');
    }

    $(".header-full-menu-button").hover(
        function() {
            $('.store-address').show();
        }, function() {
            $('.store-address').hide();
        }
    );

    $(".store-address").hover(
        function() {
            $('.store-address').show();
        }, function() {
            $('.store-address').hide();
        }
    );

    // Пункт выдачи в карточке товара
    var pointAddress = $.cookie('pointAddress');
    var pointCity = $.cookie('pointCity');
    if (pointAddress && pointCity == GEO_REGION_CITY_NAME) {
        setTimeout(function (){
            $("body").find(".chosen-single span").text(pointAddress);
        }, 1000);
    } else {
        $.cookie('pointAddress', null, { path: '/' });
    }

    // Set default pickup address value for msk
    set_point();

    $('a[href="#as-stores-popup-header"]').fancybox({
        maxWidth    : 1000,
        maxHeight   : 900,
        fitToView   : false,
        autoSize    : true,
        closeClick  : false,
        openEffect  : 'none',
        closeEffect : 'none',
        beforeLoad: function() {
            $.post('/bitrix/components/ayers/stores.product/store_map_data.php', { city: GEO_REGION_CITY_NAME }, function(data) {
                AS_STORE_MAP_GROUP = data;
                ymaps.ready(init(''));
            });
        },
        afterClose:function() {
            //BX.onCustomEvent(window, 'OnMapCloseFancybox');
            location.reload();
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

    function init(mapAddress) {

        return function () {
            if (typeof AS_STORE_MAP_GROUP === 'undefined') {
                return false;
            }

            $('#as-stores-hint ul li').click(function () {
                var address = $(this).text();
                $.fancybox('#as-stores-popup-header', {
                    maxWidth: 1000,
                    maxHeight: 900,
                    fitToView: false,
                    autoSize: true,
                    closeClick: false,
                    openEffect: 'none',
                    closeEffect: 'none',
                    afterShow: function () {
                        if (AS_STORE_MAP_GROUP !== undefined) {
                            map.container.fitToViewport();
                            $('.as-stores-popup-menu-container-header-header li:contains("' + address + '")').click();
                        }
                    },
                    afterClose: function () {
                        //BX.onCustomEvent(window, 'OnMapCloseFancybox');
                    }
                });
            });

            var center;

            $.each(AS_STORE_MAP_GROUP, function (index, group) {
                $.each(group.items, function (index, item) {
                    if (item.city == GEO_REGION_CITY_NAME) {
                        if (item.city == 'Москва') { // Set center for Moscow
                            center = ["55.75399399999374", "37.62209300000001"];
                        } else {
                            center = item.center;
                        }
                    }
                });
            });

            // Создание экземпляра карты.
            map = new ymaps.Map('as-stores-popup-map-header', {
                center: center,
                zoom: 10,
                controls: ['routePanelControl'] // zoomControl, typeSelector
            })

            // Получим ссылку на элемент управления.
            control = map.controls.get('routePanelControl');

            if (mapAddress !== '') {
                // Установим начальную точку маршрута.
                control.routePanel.state.set('from', mapAddress);
            }

            // map.controls.add('routePanelControl');

            // Контейнер для меню.
            var menu = $('<ul class="nav nav-list"/>');

            // Перебираем все группы.
            $.each(AS_STORE_MAP_GROUP, function (index, group) {
                // DOM-представление группы.
                var menuItem = $('<li class="nav-header ' + group.style + '">' + group.name + '</li>'),
                    // Создадим коллекцию для геообъектов группы.
                    collection = new ymaps.GeoObjectCollection(null, {preset: group.preset});

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
                        if (collection.getParent()) {
                            map.geoObjects.remove(collection);
                        }
                        else {
                            map.geoObjects.add(collection);
                        }
                    });

                // Перебираем элементы группы.
                $.each(group.items, function (index, item) {

                    // DOM-представление элемента группы.
                    var menuItem = $('<li>' + item.address + '<br><i>' + item.metro + '</i></li>'),
                    //var menuItem = $('<li>' + item.address + '</li>'),
                        // Создаем метку.
                        placemark = new ymaps.Placemark(item.center, {
                            balloonContentHeader: item.address,
                            balloonContent: item.metro + '<br>' + item.time + '<br>' + item.phone,
                            balloonContentFooter: item.hint
                        });

                    count++;

                    // Добавляем метку в коллекцию.
                    collection.add(placemark);

                    if (GEO_REGION_CITY_NAME == item.city) {
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
                                if (placemark.balloon.isOpen()) {
                                    placemark.balloon.close();
                                }
                                else {
                                    // Плавно меняем центр карты на координаты метки.
                                    placemark.balloon.open();
                                }
                                // Передаем точку А и отрисовываем карту
                                var mapAddress = GEO_REGION_CITY_NAME +', '+ $(this).text();
                                //console.log(mapAddress);
                                $( "ymaps" ).remove();
                                $( ".nav.nav-list" ).remove();
                                $( ".ajax-city-show" ).remove();
                                ymaps.ready(init(mapAddress));
                            });
                    }

                    BX.addCustomEvent(window, 'OnMapCloseFancybox', function () {
                        placemark.balloon.close();
                        $('.as-stores-popup-menu-container-header li').removeClass('active');
                    });


                });
            });

            var btn = $('<span class="ajax-city-show" onclick="show_hide_geo(1);return false;">Выбрать другой город</span>');
            btn.appendTo($('.as-stores-popup-menu-container-header')).on('click', function (e) {
                $.cookie('headerStoreBlock', 1);
            });

            // Добавляем меню в сайдбар.
            menu.appendTo($('.as-stores-popup-menu-container-header'));
        };
        // end init
    }
});

function chose_point() {
    // Запоминаемем выбранный пункт
    $.cookie('pointCity', GEO_REGION_CITY_NAME, { path: '/' });
    $.cookie('pointAddress', $(".tmpAddress").text(), { path: '/' });
    location.reload();
}

function set_point() {
    // Set default value for msk
    if ($.cookie('pointAddress') == null) {
        var address;
        switch (GEO_REGION_CITY_NAME) {
            case 'Москва':
                address = "улица Трофимова, д. 3";
                break;
            case 'Санкт-Петербург':
                address = "Вознесенский проспект д. 21";
            case 'Екатеринбург':
                address = "ул. Энгельса 21";
                break;
            case 'Нижний Новгород':
                address = "ул. Ильинская д.100";
            case 'Ростов-на-Дону':
                address = "ул. Максима Горького 55";
                break;
            case 'Новосибирск':
                address = "ул. Ленина 55";
        }
        setTimeout(function () {
            $("body").find(".chosen-single span").text(address);
        }, 1000);
    }
}