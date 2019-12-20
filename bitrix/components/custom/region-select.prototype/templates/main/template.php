<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="region-select">

<!--    <a href="#" onclick='show_hide_geo(1);return false;' class="link-ch-city">-->
    <a href="#win_choose_region" class="link-ch-city show-geo-block" title="Пожалуйста укажите где вы находитесь">
        <span class="tt-icons geo-icon"></span>
        <!--noindex-->
        <span class="geo-name">
            <?=$arResult["GEO_REGION_CITY_NAME"]?>
            <div style = "width:105%">
                <span class="text">Ваш город</span>
            </div>
        </span>
        <!--/noindex-->
    </a>
</div>