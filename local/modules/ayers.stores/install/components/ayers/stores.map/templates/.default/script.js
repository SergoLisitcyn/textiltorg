var map;

ymaps.ready(init);

function init() {
    if (AS_STORE_MAP_GROUP === undefined) {
        return false;
    }

    var center;

    $.each(AS_STORE_MAP_GROUP, function (index, group) {
        $.each(group.items, function (index, item) {
            center = item.center;
        });
    });

    // Создание экземпляра карты.
    map = new ymaps.Map('as-stores-map', {
        center: center,
        zoom: 10,
        controls: ['zoomControl']
    });

    // Перебираем все группы.
    $.each(AS_STORE_MAP_GROUP, function (index, group) {
        // DOM-представление группы.

        // Создадим коллекцию для геообъектов группы.
        var collection = new ymaps.GeoObjectCollection(null, { preset: group.preset });

        // Добавляем коллекцию на карту.
        map.geoObjects.add(collection);

        // Перебираем элементы группы.
        $.each(group.items, function (index, item) {
            // DOM-представление элемента группы.

            // Создаем метку.
            var placemark = new ymaps.Placemark(item.center, {
                    balloonContentHeader: item.address,
                    balloonContent: item.time + '<br>' + item.phone,
                    balloonContentFooter: item.hint
                });

            // Добавляем метку в коллекцию.
            collection.add(placemark);
        });
    });

    $('.as-show-load-map').addClass('show');

    $('a[href="#as-stores-map"]').click(function() {
        $(this).parent().remove();
        $('#as-stores-map').addClass('show');

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
        return false;
    });
}