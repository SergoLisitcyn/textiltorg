<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if ($arResult["ITEMS"]):?>
    <div class="action-products-container">
        <p><strong>*Данное предложение действует только на следующие товары:</strong></p>

        <div class="action-products">
            <?foreach ($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
                ?>
                <div class="action-products-item" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                    <img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" width="<?=$arItem["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arItem["RESIZE_PICTURE"]["HEIGHT"]?>" alt="">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="ar-black" target="_blank">
                        <?=$arItem["NAME"]?> -
                        <span class="big_red">Цена:</span> <?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <span class="red">руб.</span>
                    </a>
                </div>
            <?endforeach?>
        </div>
    </div>

    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
<?endif?>

