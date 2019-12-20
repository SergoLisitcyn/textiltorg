<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="region-select-prototype-mobile-header">
    <div class="sity select-region-container">
        <div class="select-city"><span><div><?=$arResult["GEO_REGION_CITY_NAME"]?></div></span></div>
    </div>
</div>
<div class="select-region-hidden" id="delect-region-search">
    <div class="close"></div>
    <div class="wrap-input">
        <input type="text" />
    </div>
    <div class="no-result">Город не найден</div>
    <?=$GLOBALS["REGION_OPTIONS"]?>
</div>