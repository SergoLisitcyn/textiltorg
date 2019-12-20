<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="addr_4" class="contacts addr_cont" style="margin-top: 10px;">
    <div>
        <b>улица Фурманова д.103</b> <i>(Ежедневно с 09:00 до 21:00)</i>
        <br>5 мин. пешком от метро Чкаловская
        <span class="text-red text-small showContacts"><i>(подробная схема проезда)</i></span>
        <div class="cent blue contdown" id="Ekb_cart" style="display: none;">
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
                    "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:56.814366283638215;s:10:\"yandex_lon\";d:60.60147263893116;s:12:\"yandex_scale\";i:14;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:60.595721982803305;s:3:\"LAT\";d:56.81427213437326;s:4:\"TEXT\";s:115:\"улица Фурманова, 103, Екатеринбург, Свердловская область, Россия\";}}}",
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