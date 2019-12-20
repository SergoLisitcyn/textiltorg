<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="addr_5" class="contacts addr_cont" style="margin-top: 10px;">
    <div>
        <b>Горьковская, улица Ильинская д.100</b> <i>(Ежедневно с 09:00 до 21:00)</i>
        <br>5 мин. пешком от метро
        <span class="text-red text-small showContacts"><i>(подробная схема проезда)</i></span>
        <div class="cent blue contdown" id="N_nov_cart" style="display: none;">
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
                    "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:56.31598173768665;s:10:\"yandex_lon\";d:43.98656099867244;s:12:\"yandex_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:43.98720472883604;s:3:\"LAT\";d:56.315623975198655;s:4:\"TEXT\";s:79:\"Ильинская улица, 100, Нижний Новгород, Россия\";}}}",
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
</div>