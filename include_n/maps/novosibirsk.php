<div class="container-magazin-map">
	<a href="javascript:" class="showbottom">подробная схема проезда</a>

	<div class="cent blue contdown" style="display: none;" id="Moscow_cart_v">
		<div class="container-magazin-left">
			<?$APPLICATION->IncludeComponent(
                "custom:map.yandex.view",
                "",
                array(
                    "CONTROLS" => array(
						0 => "ZOOM",
                        1 => "SMALLZOOM",
                        2 => "SCALELINE",
                    ),
                    "INIT_MAP_TYPE" => "MAP",
                    "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.0327209114852;s:10:\"yandex_lon\";d:82.90211725735001;s:12:\"yandex_scale\";i:18;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:82.90205288433364;s:3:\"LAT\";d:55.03274402047304;s:4:\"TEXT\";s:71:\"г. Новосибирск, улица Ленина д.55, Россия\";}}}",
                        "MAP_HEIGHT" => "450",
                        "MAP_ID" => "1",
                        "MAP_WIDTH" => "730",
                        "OPTIONS" => array(
                            0 => "ENABLE_DBLCLICK_ZOOM",
                            1 => "ENABLE_DRAGGING",
                        ),
                       
                    ),
                    false
                );?>
		</div>
		<div class="container-magazin-right">
		</div>
		<div class="clear"></div>
	</div>
</div>