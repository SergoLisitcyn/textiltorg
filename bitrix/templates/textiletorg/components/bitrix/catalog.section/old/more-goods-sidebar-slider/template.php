<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?/*if ($arResult["ITEMS"]):?>
    <!--div id="add-products-button">
        Не забудьте купить!
    </div-->
    <div class="shop_items" id="add-products">
        <?foreach ($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
            ?>
            <div class="shop_item" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                <div class="title_block">
                    <h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h2>
                    <?if ($arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]):?>
                        <p>Артикул <?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?></p>
                    <?endif?>
                </div>
                <?if ($arItem["RESIZE_PICTURE"]["SRC"]):?>
                    <div class="img_block">
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="fancybox"><img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>"></a>
                    </div>
                <?endif?>
                <div class="info_block">
                    <div class="items_2">
                        <div class="item">
                            <div class="price_block">
                                <?if ($arItem["REGION_PRICE"]["DISCOUNT_VALUE"]):?>
                                    <strong>Цена:</strong> <span class="sum list-price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?></span> <span class="text-red">руб.</span>
                                <?else:?>
                                    <strong>Цена:</strong> <span class="sum">На заказ</span>
                                <?endif?>
                            </div>
                            <?if ($arItem["COMMENTS_COUNT"]):?>
                                <div class="rew_block">
                                    <strong>Отзывы:</strong> <span class="text-red"><?=$arItem["COMMENTS_COUNT"]?> шт.</span>
                                </div>
                            <?endif?>
                            <?if ($arItem["RATING"] && $arItem["COMMENTS_COUNT"]) {?>
                                <div class="rating">
                                    <strong>Рейтинг:</strong> <span class="<?=$arItem["RATING"]["CLASS"]?>"></span>
                                </div>
                            <?}?>
                        </div>
                        <div class="item">
                            <div class="bay_block">
                                <a href="<?=$arItem["ADD_URL"]?>" class="button buy red add-cart" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" <?=Helper::GetYandexCounter("Open_Cart")?>>В корзину</a>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">Подробнее..</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?endforeach?>
        <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
            <?=$arResult["NAV_STRING"]?>
        <?endif;?>
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

<?endif*/?>

<div class="basket-form button red red_button" style="background:#ff0000 !important; width:100% !important;" id="click-send-form">
    Оформить заказ
</div>