<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult['ITEMS']):?>
<div class="header-catalog-menu mn_content">
    <div id="header-drop-down-menu">
        <a href="#as-stores-popup-header" class="header-full-menu-button" title="Адреса пунктов выдачи г. <?=$arResult['CITY']?>" data-city="<?=$arResult['CITY']?>">
            г. <?=$arResult['CITY']?> <div>(<?=$GLOBALS["ITEMS_STORE_COUNT"]?> <?=$GLOBALS["ITEMS_STORE_COUNT_TEXT"]?>)</div>
        </a>
        <div class="container-header-menu">
            <div id="as-stores-popup-header">
                <div class="as-stores-popup-container">
                    <div class="as-stores-popup-menu-container-header">

                    </div>
                    <div class="as-stores-popup-map-container">
                        <div id="as-stores-popup-map-header"></div>
                    </div>
                </div>
            </div>
            <script>
                GEO_REGION_CITY_NAME = '<?=$GLOBALS['GEO_REGION_CITY_NAME']?>';
            </script>
        </div>
        <?php //error_log(print_r($arResult, TRUE), 3, "/home/bitrix/www/tmp.textiletorg.ru/bitrix/components/ayers/stores.product/templates/list/array2.log"); ?>
        <!--noindex-->
            <div class="store-address">
            <ul>
                <?if($arResult['ITEMS']['HOME']):?>
                    <li class="nav-header shop"> Магазины:</li>
                <?endif?>

                <?foreach ($arResult['ITEMS']['HOME'] as $id => $arItem):?>
                    <?if($arResult["CITY"] == $arItem['CITY']):?>
                        <li><?=$arItem['SHORT_ADDRESS'];?></li>
                    <?endif?>
                <?endforeach?>

                <?if($arResult['ITEMS']['STORES']):?>
                    <li class="nav-header stores">Пункты выдачи:</li>
                <?endif?>

                <?foreach ($arResult['ITEMS']['STORES'] as $id => $arItem):?>
                    <?if($arResult["CITY"] == $arItem['CITY']):?>
                        <li><?=$arItem['SHORT_ADDRESS'];?></li>
                    <?endif?>
                <?endforeach?>
            </ul>
        </div>
        <!--/noindex-->
    </div>
</div>
<?else:?>
<div class="htb_soc">
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => "/include/header-social.php",
            "EDIT_TEMPLATE" => "text.php"
        )
    );?>
</div>
<?endif?>