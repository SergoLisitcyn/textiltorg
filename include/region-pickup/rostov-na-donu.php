<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="addr_6" class="contacts addr_cont" style="margin-top: 10px;">
    <b>Соборный переулок, д. 34</b> <i>(Ежедневно с 09:00 до 21:00)</i>
    <br><span class="text-red text-small showContacts"><i>(подробная схема проезда)</i></span>
    <div class="cent blue contdown" id="Rnd_cart" style="display: none;">
        <?$APPLICATION->IncludeComponent(
            "bitrix:map.yandex.view",
            ".default",
            array(
                "CONTROLS" => array(
                    0 => "ZOOM",
                    1 => "SMALLZOOM",
                    2 => "SCALELINE",
                ),
                "INIT_MAP_TYPE" => "MAP",
                "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:47.223828074283446;s:10:\"yandex_lon\";d:39.700118499999896;s:12:\"yandex_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:39.700118499999896;s:3:\"LAT\";d:47.223828074283446;s:4:\"TEXT\";s:76:\"Россия, Ростов-на-Дону, Максима Горького 55\";}}}",
                "MAP_HEIGHT" => "450",
                "MAP_ID" => "1",
                "MAP_WIDTH" => "680",
                "OPTIONS" => array(
                    0 => "ENABLE_DBLCLICK_ZOOM",
                    1 => "ENABLE_DRAGGING",
                ),
                "COMPONENT_TEMPLATE" => ".default"
            ),
            false
        );?>
    </div>
</div>