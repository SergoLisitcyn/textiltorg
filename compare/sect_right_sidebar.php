<div class="box">
    <div class="box_head">Информация</div>
    <div class="box_block">
        <div class="grafik">
            <b>График работы:</b><br>
            <span class="red">Круглосуточно,</span> без выходных
        </div>
    </div>
    <div class="box_block">
        <a class="box_addres" href="/kontakty/">
            Адреса наших<br /> магазинов
        </a>
    </div>
    <?$APPLICATION->IncludeComponent(
        "custom:online-pay.prototype",
        "",
        array(
            "URL" => "https://pay.textiletorg.ru/create/"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>

    <?$APPLICATION->IncludeComponent("bitrix:catalog.compare.list",
        "compare",
        array(
            "ACTION_VARIABLE" => "action",  // Название переменной, в которой передается действие
            "AJAX_MODE" => "N", // Включить режим AJAX
            "AJAX_OPTION_ADDITIONAL" => "", // Дополнительный идентификатор
            "AJAX_OPTION_HISTORY" => "N",   // Включить эмуляцию навигации браузера
            "AJAX_OPTION_JUMP" => "N",  // Включить прокрутку к началу компонента
            "AJAX_OPTION_STYLE" => "Y", // Включить подгрузку стилей
            "COMPARE_URL" => "/compare/",  // URL страницы с таблицей сравнения
            "DETAIL_URL" => "", // URL, ведущий на страницу с содержимым элемента раздела
            "IBLOCK_ID" => "8", // Инфоблок
            "IBLOCK_TYPE" => "catalog", // Тип инфоблока
            "NAME" => "CATALOG_COMPARE_LIST",   // Уникальное имя для списка сравнения
            "POSITION" => "top left",   // Положение на странице
            "POSITION_FIXED" => "Y",    // Отображать список сравнения поверх страницы
            "PRODUCT_ID_VARIABLE" => "id",  // Название переменной, в которой передается код товара для покупки
        ),
        false
    );?>

    <div class="box_block">
        <div class="payment-methods">
            Принимаем к оплате
        </div>
    </div>
</div>

<div class="box reviews">
    <div class="box_head">Отзывы</div>
    <div class="box_block">
        Отзывы наших клиентов
        <a href="/otzyvy" class="button" target="_blank">читать</a>
    </div>
</div>

<div class="box end"></div>
