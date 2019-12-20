<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (count($arResult["ITEMS"]) > 0)
{ ?>
    <div class="textiletorg-contacts-prototype-default">
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <div class="item">

                <div class="left">
                    <p class="title"><?=$arItem["CITY"]["VALUE"] ?></p>
                    <ul>
                        <li>Консультация и прием заказов:</li>
                        <li><?=$arItem["PHONE"]["VALUE"]?></li>
                        <li>(круглосуточно, без выходных)</li>
                    </ul>

                    <? if (!empty($arItem["MAP"]["VALUE"])): ?>
                        <div data-open="map" class="show-map show-container">Посмотреть на карте</div>
                    <? endif; ?>
                    <? if (!empty($arItem["MAP"]["VALUE"])): ?>
                        <div class="clear"></div>
                        <?
                        $coord = explode(",", $arItem["MAP"]["VALUE"]);
                        $mapData = 'a:4:{s:10:"yandex_lat";d:'.$coord[0].';s:10:"yandex_lon";d:'.$coord[1].';s:12:"yandex_scale";i:15;s:10:"PLACEMARKS";a:1:{i:0;a:3:{s:3:"LON";d:'.$coord[1].';s:3:"LAT";d:'.$coord[0].';s:4:"TEXT";s:24:"Текстильторг";}}}';
                        ?>
                        <div class="container-showhide map">
                            <div class="map">
                                <?$APPLICATION->IncludeComponent(
                                    "custom:map.yandex.view",
                                    "",
                                    Array(
                                        "CONTROLS" => array(
                                            0 => "ZOOM",
                                            1 => "SMALLZOOM",
                                            2 => "SCALELINE",
                                        ),
                                        "INIT_MAP_TYPE" => "MAP",
                                        "MAP_DATA" => $mapData,
                                        "MAP_HEIGHT" => "450",
                                        "MAP_ID" => "",
                                        "MAP_WIDTH" => "588",
                                        "OPTIONS" => array("ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING")
                                    ),
                                    false
                                );?>
                            </div>
                        </div>
                    <? endif; ?>
                </div>

                <div class="right">
                    <p class="title">Адреса наших магазинов:</p>
                    <ul>
                        <li>Адрес нашего магазина:</li>
                        <li><?=$arItem["ADDRESS"]["VALUE"]?>. <?=$arItem["METRO"]["VALUE"]?></li>
                        <li>График работы:</li>
                        <li><?=$arItem["TIME"]["VALUE"]?></li>
                    </ul>

                    <? if (!empty($arItem["VIDEO_MAGAZINA"]["VALUE"])): ?>
                        <div data-open="map" class="show-map show-container"><span></span>Как добраться (видео)</div>
                    <? endif; ?>
                    <? if (!empty($arItem["VIDEO_MAGAZINA"]["VALUE"])): ?>
                        <div class="clear"></div>
                        <div class="container-showhide map">
                            <iframe src="<?=$arItem["VIDEO_MAGAZINA"]["VALUE"]?>" width="100%" height="450">
                                Ваш браузер не поддерживает плавающие фреймы!
                            </iframe>
                        </div>
                    <? endif; ?>
                </div>

				<? if (count($arItem["PHOTOGALLERY"]) > 0): ?>
					<div class="clear"></div>
					<div class="header-photo">Фотографии нашего магазина в г. <?=$arItem["CITY"]["VALUE"] ?></div>
					<ul class="photogallery">
						<? foreach ($arItem["PHOTOGALLERY"] as $arPhoto): ?>
							<li><a href="<?=$arPhoto["BIG"];?>" class="fancybox" rel="gallery"><img alt="" src="<?=$arPhoto["SMALL"];?>" /></a></li>
						 <? endforeach; ?>
					</ul>
				<? endif; ?>

            </div>
        <? endforeach; ?>
    </div>
<? }?>
