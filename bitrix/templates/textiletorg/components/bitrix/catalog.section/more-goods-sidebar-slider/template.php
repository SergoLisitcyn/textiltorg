<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if ($arResult["ITEMS"]):?>
    <div class="catalog-section-more-goods-sidebar box_block no-border">
        <div class="canbeint2">
			<div class="box_head">Не забудьте купить
				<div class="question tooltip-message" data-tooltipe-text="Не забудьте приобрести расходные материалы и аксессуары для вашей новой швейной машины"></div>
			</div>
            <div class="border-left-bottom">
                <div class="slider1">
                    <?foreach ($arResult["ITEMS"] as $arItem):?>
                        <?
                        $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
                        ?>
                        <div class="slide">
                            <div class="item" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                                <div class="name">
                                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="linked_item_title" target="_blank"><?=$arItem["NAME"]?></a>
                                </div>
                                <div class="img_related_item">
                                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" target="_blank"><img alt="<?=$arItem["NAME"]?>" src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" style="max-width: 200px;" height="55"></a>
                                </div>
                                <div>
                                    <div class="price">
                                        <span>Цена:</span> <?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?> <small>руб.</small>
                                    </div>

                                    <div class="buybtn">
                                        <button onmousedown="try { rrApi.addToBasket(<?=$arItem["ID"]?>) } catch(e) {}" class="inyourcart" type="button" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" onclick="<?if(SITE_ID == "s1"):?>yaCounter1021532.reachGoal('Open_Cart');<?endif;?>">
                                            <i class="fa fa-shopping-cart"></i> Купить
                                        </button>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    <?endforeach?>
                </div>
            </div>
        </div>
    </div>
<?endif?>