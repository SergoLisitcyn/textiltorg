<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if ($arResult["ITEMS"]):?>
    <div id="gift_wrap_cart" class="fancybox_block">
        <div class="gift_blocks">
            <?foreach ($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
                ?>
                <div class="gift_block" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                    <div class="img"><img src="<?=$arItem["RESIZE_PICTURE"]["BIG"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" /></div>
                    <div class="name"><?=$arItem["NAME"]?></div>
                    <div class="price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> руб.</div>
                    <a class="buy_button incart_input scale-decrease gift-wrap-button" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" href="<?=$arItem["ADD_URL"]?>">
                        <span class="acs-bay-btn"><i class="fa fa-shopping-cart"></i> Купить</span>
                    </a>
                </div>
            <?endforeach?>
            <div class="footer_button_block">
                <a href="#close-fancybox" class="button">Продолжить покупки</a>
                <a href="/cart/" class="red_button">Оформить заказ</a>
            </div>
        </div>
    </div>
<?endif?>