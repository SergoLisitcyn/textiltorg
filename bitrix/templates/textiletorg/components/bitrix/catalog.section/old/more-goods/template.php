<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if ($arResult["ITEMS"]):?>
    <div class="description_block">
        <div class="button red acc">Вам точно потребуется<br /> к этому товару:</div>
        <div class="description_block_content">
            <div class="items_2">
                <?foreach ($arResult["ITEMS"] as $arItem):?>
                    <?
                    $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
                    ?>
                    <div class="item">
                        <div>
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                                <p class="title"><?=$arItem["NAME"]?></p>
                                <div class="block-img">
                                    <img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
                                </div>
                            </a>
                            <div class="price_block">
                                <strong>Цена:</strong>
                                <span class="sum more-price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?></span>
                                <span class="red-text">руб.</span>

                            </div>

                            <div class="buttons">
                                <a href="<?=$arItem["ADD_URL"]?>" class="button red buy" <?=Helper::GetYandexCounter("Open_Cart")?>>В корзину</a>
                            </div>
                        </div>
                    </div>
                <?endforeach?>
            </div>
        </div>
    </div>

    <div id="add_basket" class="fancy_block form">
        <div class="info_block">
            <div class="img">
                <img src="#" alt="#">
            </div>
            <div class="text"></div>
        </div>
        <a href="#close-fancybox" class="button yellow">Продолжить покупки</a>
        <a href="/cart/" class="button red">Оформить заказ</a>
    </div>
<?endif?>