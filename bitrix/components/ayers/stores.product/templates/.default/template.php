<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult['ITEMS']):?>
    <div id="as-stores-hint">
        <div class="as-stores-hint-inner">
            <strong>Самовывоз в г. <?=$arResult['CITY']?></strong>

            <?if ($arResult['ITEMS_HINT']['HOME']):?>
                <div class="as-stores-hint-icon-title as-icon-home">Магазины</div>
                <ul>
                    <?foreach ($arResult['ITEMS_HINT']['HOME'] as $arItem):?>
                        <li><?=$arItem['SHORT_ADDRESS']?></li>
                    <?endforeach?>
                </ul>
            <?endif?>
            <?if ($arResult['ITEMS_HINT']['STORES']):?>
                <div class="as-stores-hint-icon-title as-icon-stores">Пункты выдачи <span class="red">*</span></div>
                <ul>
                    <?foreach ($arResult['ITEMS_HINT']['STORES'] as $arItem):?>
                        <li><?=$arItem['SHORT_ADDRESS']?></li>
                    <?endforeach?>
                </ul>
            <?endif?>

            <?if ($arResult['IS_BUTTON_POPUP']):?>
                <p><a href="#as-stores-popup" class="dotted" title="Адреса пунктов выдачи г. <?=$arResult['CITY']?>">Показать весь список</a></p>
            <?endif?>
            <!--<p><small><span class="red">*</span> Уважаемые клиенты, обратите внимание на то, что демонстрация возможна только в магазинах, в пунктах выдачи демонстрация не производится.</small></p>-->
            <p><small><span class="red">*</span>Перед самовывозом необходимо забронировать товар.</small></p>
        </div>
    </div>

    <div id="as-stores-popup">
        <div class="as-stores-popup-container">
            <div class="as-stores-popup-menu-container">

            </div>
            <div class="as-stores-popup-map-container">
                <div id="as-stores-popup-map"></div>
            </div>
        </div>
    </div>

    <script>
        GEO_REGION_CITY_NAME = '<?=$GLOBALS['GEO_REGION_CITY_NAME']?>';
    </script>
<?endif?>