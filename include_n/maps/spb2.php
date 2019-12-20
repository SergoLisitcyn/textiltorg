<div class="container-magazin-map">
	<a href="javascript:" class="showbottom">подробная схема проезда</a>

	<div class="cent blue contdown" style="display: none;" id="Moscow_cart_v">
		<div class="container-magazin-left">
			<?$APPLICATION->IncludeComponent(
				"custom:map.yandex.view",
				".default",
				array(
					"CONTROLS" => array(
						0 => "ZOOM",
						1 => "SMALLZOOM",
						2 => "SCALELINE",
					),
					"INIT_MAP_TYPE" => "MAP",
					"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:60.01775506406433;s:10:\"yandex_lon\";d:30.320530499999997;s:12:\"yandex_scale\";i:14;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:30.320530499999997;s:3:\"LAT\";d:60.01775506406433;s:4:\"TEXT\";s:72:\"ул. Елецкая, д. 3, Санкт-Петербург, Россия\";}}}",
					"MAP_HEIGHT" => "450",
					"MAP_ID" => "1",
					"MAP_WIDTH" => "730",
					"OPTIONS" => array(
						0 => "ENABLE_DBLCLICK_ZOOM",
						1 => "ENABLE_DRAGGING",
					),
					"COMPONENT_TEMPLATE" => ".default"
				),
				false
			);?>
		</div>
		<div class="container-magazin-right">
		</div>
		<div class="clear"></div>
	</div>
</div>