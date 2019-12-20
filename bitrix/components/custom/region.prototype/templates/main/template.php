<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<div class="popup__into_overlay"></div>
<div id="win_choose_region" style="display:none">
    <div class="inner_container">
        <p>Внимание! От вашего региона будет зависеть цена на товары и стоимость доставки. </p>
        <p>Пожалуйста введите ваш город:</p>
        <div class="form">
            <input class="select_city" id="input_city" type="text" placeholder="<?=$arResult["GEO_REGION_CITY_NAME"]?>" value=""/>
            <input value="<?=$arResult["GEO_REGION_CITY_ID"]?>" name="city_id" id="hid_city_id" type="hidden" />
            <button id="do_choose" class="red_button">Выбрать</button>
        </div>
        <!--noindex-->
            <div class="list_of_famous_citys">
                <ul class="listcity">
                    <?foreach ($arResult["ITEMS"]["DEFAULT"] as $arItem):?>
	                     <?if ($arItem["IS_HOUSE"]):?><li class="house"><span data-href="<?=$arItem["SET_CITY_URL"]?>" class="region_city" data-city-id="<?=$arItem["ID"]?>"><?=$arItem["CITY_NAME"]?></span></li><?endif?>
                    <?endforeach?>
                    <?foreach ($arResult["ITEMS"]["DEFAULT"] as $arItem):?>
	                     <?if (!$arItem["IS_HOUSE"]):?><li ><span data-href="<?=$arItem["SET_CITY_URL"]?>" class="region_city" data-city-id="<?=$arItem["ID"]?>"><?=$arItem["CITY_NAME"]?></span></li><?endif?>
                    <?endforeach?>
                </ul>
            </div>
        <!--/noindex-->
    </div>
</div>