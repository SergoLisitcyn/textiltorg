<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$title = ($arParams["COMPONENT_TITLE"]) ? $arParams["COMPONENT_TITLE"] : "Не забудьте купить";
$description = ($arParams["COMPONENT_DESCRIPTION"]) ? $arParams["COMPONENT_DESCRIPTION"] : "Не забудьте приобрести расходные материалы и аксессуары для вашей новой швейной машины";
?>
<?if ($arResult["ITEMS"]):?>
    <div class="catalog-section-more-goods-sidebar box_block">
        <div class="canbeint2">
			<div class="box_head"><?if ($arResult["ARTICLE"]):?><a class="fancybox-popup-article-sidebar" href="#article-popup-sidebar" title="<?=$arResult["ARTICLE"]["NAME"]?>"><?endif?><?=$title?><?if ($arResult["ARTICLE"]):?></a><?endif?>
				<div class="question tooltip-message" data-tooltipe-text="<?=$description?>"></div>
			</div>

            <?foreach ($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => "Будет удалена вся информация, связанная с этой записью. Продолжить?"));
                ?>
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
                            <button onmousedown="try { rrApi.addToBasket(<?=$arResult["ID"]?>) } catch(e) {}" class="inyourcart" type="submit" data-id="<?=$arItem["ID"]?>" data-path="<?=$arItem["ADD_URL"]?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>"<?=Helper::GetYandexCounter("Open_Cart")?>>
                                <i class="fa fa-shopping-cart"></i> Добавить
                            </button>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            <?endforeach?>
        </div>
    </div>

    <?if ($arResult["ARTICLE"]):?>
        <div class="popup-article-sidebar" id="article-popup-sidebar">
            <?=$arResult["ARTICLE"]["DETAIL_TEXT"]?>
        </div>
    <?endif?>
<?endif?>