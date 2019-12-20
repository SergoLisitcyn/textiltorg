<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="popup__into_overlay"></div>
<div id="win_choose_region" style="display:none">
    <div class="h2_head_confirm tahoma"></div>
    <a class="close_win _close" title="Закрыть">&nbsp;</a>
    <div class="inner_container">
        <p>Внимание! От вашего региона будет зависеть цена на товары и стоимость доставки.</p>
        <p>Пожалуйста введите ваш город:</p>
        <input class="select_city width250" id="input_city" type="text" value="<?=$arResult["GEO_REGION_CITY_NAME"]?>"/>
        <input value="<?=$arResult["GEO_REGION_CITY_ID"]?>" name="city_id" id="hid_city_id" type="hidden" />
        <button id="do_choose">Выбрать</button><br />
        <!--noindex-->
            <div class="list_of_famous_citys">
                <span class="header_01">Популярные города</span>
                <ul class="listcity">
                    <?foreach ($arResult["ITEMS"]["DEFAULT"] as $arItem):?>
	                     <?if ($arItem["IS_HOUSE"]):?><li class="house"><span data-href="<?=$arItem["SET_CITY_URL"]?>" class="region_city" data-city-id="<?=$arItem["ID"]?>"><?=$arItem["CITY_NAME"]?></span></li><?endif?>
                    <?endforeach?>
                    <?foreach ($arResult["ITEMS"]["DEFAULT"] as $arItem):?>
	                     <?if (!$arItem["IS_HOUSE"]):?><li ><span data-href="<?=$arItem["SET_CITY_URL"]?>" class="region_city" data-city-id="<?=$arItem["ID"]?>"><?=$arItem["CITY_NAME"]?></span></li><?endif?>
                    <?endforeach?>
                </ul>
            </div>
            <div class="list_of_all_citys">
                <span class="header_01">Все города</span>
                <ul class="listalfabet">
                    <?foreach($arResult["ABC"] as $id => $simbol):?>
                        <li><a class="alfabet" data-letter="<?=$id?>"><?=$simbol?></a></li>
                    <?endforeach?>
                </ul>

                <?foreach ($arResult["ITEMS"]["ABC"] as $id => $arItems):?>
                    <ul class="listcity allcity" id="abc-<?=$id?>">
                        <?foreach ($arItems as $arItem):?>
                            <li><span class="region_city" data-city-id="<?=$arItem["ID"]?>" data-href="<?=$arItem["SET_CITY_URL"]?>"><?=$arItem["CITY_NAME"]?></span></li>
                        <?endforeach?>
                    </ul>
                <?endforeach?>
            </div>
        <!--/noindex-->
        <div class="wrap_show_all_city">
            <span class="show_all_city">Показать все города</span>
            <div></div>
        </div>
    </div>
</div>