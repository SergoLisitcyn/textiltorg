<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="region-select">
<!--    <?/*if (!$arResult["IS_CONFIRMED"]):*/?>
        <div id="popup_geo">
            Вы находитесь тут: <br /><span class="geo_name"><?/*=$arResult["GEO_REGION_CITY_NAME"]*/?></span><br />Все верно?<br />
            <div class="_margintop_5">
            <button id="button_geo_yes">Да</button>
            <button id="button_geo_no">Нет</button>
            </div>
        </div>
    --><?/*endif*/?>
    <div style="line-height: 16px;">
        <span style="font-size: 11px; font-weight: bold;">Ваш город:</span><span class="point_geo"></span>
        <a href="#" onclick='show_hide_geo(1);return false;' class="link_ch_city">
            <!--noindex--><span class="geo_name"><span class="b"><?=$arResult["GEO_REGION_CITY_NAME"]?></span></span><!--/noindex-->
        </a>
    </div>
</div>