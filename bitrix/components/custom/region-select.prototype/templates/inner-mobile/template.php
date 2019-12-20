<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="tel tel_bl_u">
    <div class="select-region-container">
        <b>Ваш город:</b> <a href="#open-inner-city" ><!--noindex--><b><span class="geo_name"><?=$arResult["GEO_REGION_CITY_NAME"]?></span></b><!--/noindex--></a>
        <select class="select-region-hidden">
            <?=$GLOBALS["REGION_OPTIONS"]?>
        </select>
    </div>
</div>