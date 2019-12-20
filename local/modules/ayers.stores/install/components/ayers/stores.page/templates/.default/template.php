<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult['ITEMS']):?>
    <div id="as-stores-page">
        <div class="as-stores-page-list">
            <?if ($arResult['ITEMS']['HOME']):?>
                <div class="as-stores-page-title shop">Магазины</div>
                <?foreach ($arResult['ITEMS']['HOME'] as $nItem => $arItem):?>
                    <div class="as-stores-page-item">
                        <div>
                            Адрес: <?=$arItem['SHORT_ADDRESS']?>
                        </div>
                        <?if ($arItem['METRO']):?>
                            <div class="icon-metro"><?=$arItem['METRO']?></div>
                        <?endif?>
                        <?if ($arItem['TIME']):?>
                            <div class="as-stores-page-item-time">
                                Режим работы:<br>
                                <?foreach ($arItem['TIME'] as $time):?>
                                    <small><?=$time?></small>
                                <?endforeach?>
                            </div>
                        <?endif?>
                    </div>
                <?endforeach?>
            <?endif?>

            <?if ($arResult['ITEMS']['STORES']):?>
                <div class="as-stores-page-title">Пункты выдачи <span class="red">*</span></div>
                <?foreach ($arResult['ITEMS']['STORES'] as $nItem => $arItem):?>
                    <?if ($nItem == $arResult['COUNT_SHOW'] && count($arResult['ITEMS']['STORES']) > $arResult['COUNT_SHOW']):?>
                        <div id="as-stores-page-hide" class="hide">
                    <?endif?>

                    <div class="as-stores-page-item">
                        <div>
                            Адрес: <?=$arItem['SHORT_ADDRESS']?>
                        </div>
                        <?if ($arItem['METRO']):?>
                            <div class="icon-metro"><?=$arItem['METRO']?></div>
                        <?endif?>
                        <?if ($arItem['TIME']):?>
                            <div class="as-stores-page-item-time">
                                Режим работы:<br>
                                <?foreach ($arItem['TIME'] as $time):?>
                                    <small><?=$time?></small>
                                <?endforeach?>
                            </div>
                        <?endif?>
                    </div>
                <?endforeach?>
            <?endif?>

            <?if (count($arResult['ITEMS']['STORES']) > $arResult['COUNT_SHOW']):?>
                </div>
                <a href="#as-stores-page-hide">развернуть</a>
            <?endif?>

        </div>
    </div>

    <p><span class="red">*</span> Уважаемые клиенты, обратите внимание на то, что демонстрация возможна только в магазинах, в пунктах выдачи демонстрация не производится.</p>
<?endif?>