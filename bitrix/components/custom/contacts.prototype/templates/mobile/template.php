<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (count($arResult["ITEMS"]) > 0)
{ ?>
    <div class="textiletorg-contacts-prototype-default2">
	<div class="overlay"></div>
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <div class="item">
                <div class="headerp2">Консультация и прием заказов:<br><b><?=$arItem["PHONE"]["VALUE"]?></b><br>(круглосуточно, без выходных)</div>
                <div class="container-contacts">
                    <div class="shop-img">
                        <img alt="" src="<?=CFile::GetPath($arItem["PREVIEW_PICTURE"]);?>" />
                    </div>
                    <div class="shop-info">
                        <div class="shop-town">Адрес нашего магазина:<br><b><?=$arItem["ADDRESS"]["VALUE"]?></b></div>
                    </div>
                </div>				
                <? if (!empty($arItem["METRO"]["VALUE"])): ?>
                    <div class="shop-adr"><span class="icon-metro"><?=$arItem["METRO"]["VALUE"]?></span></div>
                <? endif; ?>
                <div class="shop-graphic">Работаем: <?=$arItem["TIME"]["VALUE"]?></div>
				
				<div class="clear"></div>
				<div class="map-3d">
					<? if (!empty($arItem["MAP"]["VALUE"])): ?>
						<div data-open="map" class="show-map show-container">Посмотреть на карте</div>
					<? endif; ?>
					
					<? if (!empty($arItem["TOUR3D"]["VALUE"])): ?>
						<div data-open="tour" class="show-3d-tour show-container">3D-тур по магазину</div>
					<? endif; ?>
				</div>
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
									"MAP_HEIGHT" => "320",
									"MAP_ID" => "",
									"MAP_WIDTH" => "100%",
									"OPTIONS" => array("ENABLE_DBLCLICK_ZOOM","ENABLE_DRAGGING")
								),
								false
							);?>
						</div>
					</div>
				<? endif; ?>
				
				<? if (!empty($arItem["TOUR3D"]["VALUE"])): ?>
					<div class="clear"></div>
					<div class="container-showhide tour">
						<iframe src="<?=$arItem["TOUR3D"]["VALUE"]?>" width="100%" height="320">
							Ваш браузер не поддерживает плавающие фреймы!
						 </iframe>
					</div>
				<? endif; ?>
				
				<? if (count($arItem["PHOTOGALLERY"]) > 0): ?>
					<div class="clear"></div>
					<div class="header-photo">Фотографии магазина в г. <b><?=$arItem["CITY"]["VALUE"] ?></b></div>
					<ul class="photogallery">
						<? foreach ($arItem["PHOTOGALLERY"] as $arPhoto): ?>
							<li><a href="<?=$arPhoto["BIG"];?>" class="open_slider"><img alt="" src="<?=$arPhoto["SMALL"];?>" /></a></li>
						 <? endforeach; ?>
					</ul>					
					<div class="popup_slider" style="top:300px">			
						<div class="popup_slider_header"><div class="popup_slider_close"></div></div>
						<ul class="popup_slider_title_img_block_slider">
							<? foreach ($arItem["PHOTOGALLERY"] as $arPhoto): ?>
								<li><img src="<?=$arPhoto["BIG"]?>" alt="<?=$arResult["NAME"]?>" class="title_img"></li>
							<?endforeach?>
						</ul>
					</div>
				<? endif; ?>
				
            </div>
        <? endforeach; ?>

    </div>
<? }?>
