<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<?if ($arResult["ITEMS"]):?>
    <div class="catalog-section-more-goods-cart">
        <div class="popup-cart-more-goods-title">
            <?=$arResult["TITLE"]?>
        </div>
        <div class="popup-cart-more-goods">
            <?if ($arResult["HELP_ARTICLES"]):?>
                <?foreach ($arResult["HELP_ARTICLES"] as $arArticle):?>
                    <div class="popup-cart-more-goods-hint">
                        <a class="fancybox-popup-article" href="#article-popup-<?=$arArticle["ID"]?>"><span><?=$arArticle["NAME"]?></span></a>
                    </div>
                <?endforeach?>
            <?endif?>

            <ul class="slider-more-goods">
                <?foreach ($arResult["ITEMS"] as $arItem):?>
                    <li>
                        <div class="slider-more-good-item">
                            <div class="slider-more-good-item-border">
                                <div class="title-product"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["TITLE_NAME"]?></a><i class="slider-more-good-item-help-icon tooltip-message-click-fixed" data-tooltipe-text="<?=$arItem["PREVIEW_TEXT"]?>" data-tooltipe-offset="20"></i></div>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="slider-more-good-item-picture" style="background: url(<?=$arItem["RESIZE_PICTURE"]["SRC"]?>)"></a>
                                <div class="slider-more-good-item-price">
                                    Цена: <span style="font-size: 1.2em"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?></span> руб.
                                </div>
                                <? global $USER;
                                $dataPath = $arItem["ADD_URL"];
                               // if ($USER->IsAdmin()):
$dataPath = $arItem['DETAIL_PAGE_URL'].'?action=ADD2BASKET&action_catalog=ADD2BASKET&id='.$arItem["ID"];
                            //    endif; ?>
                                <a href="#add-to-cart" onmousedown="try { rrApi.addToBasket(<?=$arItem["ID"]?>) } catch(e) {}" class="slider-more-good-item-add-to-cart is-active" data-id="<?=$arItem["ID"]?>" <?=Helper::GetYandexCounter("Open_Cart")?> data-id="<?=$arItem["ID"]?>" data-path="<?=$dataPath?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>">
                                    <i class="fa fa-shopping-cart"></i> Добавить
                                </a>
                            </div>
                        </div>
                    </li>
                <?endforeach?>
            </ul>
        </div>
        <?foreach ($arResult["HELP_ARTICLES"] as $arArticle):?>
            <div class="popup-article" id="article-popup-<?=$arArticle["ID"]?>">
                <?=$arArticle["DETAIL_TEXT"]?>
            </div>
        <?endforeach?>
    </div>
<?endif?>