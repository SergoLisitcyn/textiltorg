<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);


?>
<?if ($arResult["ITEMS"]):?>

<div class="product-slider-wrap n_index_catalog_clider">
    <div class="slider-title <?=$arParams["COMPONENT_TITLE_COLOR"]?>"><?=$arParams["COMPONENT_TITLE"]?></div>
    <ul class="product-slider">
        <?foreach ($arResult["ITEMS"] as $arItem):?>
            <li class="slick-slide">
                <div class="product-slider-item">
                    <div class="product-slider-item-border">
                        <div class="title-product">
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                        </div>
                        <div class="product-slider-item-img">
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="product-slider-item-picture">
                                <img src="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" title="<?=$arItem["NAME"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arItem["RESIZE_PICTURE"]["WIDTH"]?>" height="<?=$arItem["RESIZE_PICTURE"]["HEIGHT"]?>">
                            </a>
                        </div>
                        <div class="product-slider-item-price">
                            Цена: <span><span class="price"><?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?></span> руб.</span>
                        </div>
                        <? $dataPath = $arItem["ADD_URL"]; $dataPath = $arItem['DETAIL_PAGE_URL'].'?action=ADD2BASKET&action_catalog=ADD2BASKET&id='.$arItem["ID"]; ?>
                        <div class="product-slider-item-btn">
                            <a href="#popup-cart" class="product-slider-item-add-to-cart is-active" onmousedown="try { rrApi.addToBasket(<?=$arItem["ID"]?>) } catch(e) {}" data-id="<?=$arItem["ID"]?>" <?=Helper::GetYandexCounter("Open_Cart")?> data-id="<?=$arItem["ID"]?>" data-path="<?=$dataPath?>" data-name="<?=$arItem["NAME"]?>" data-picture="<?=$arItem["RESIZE_PICTURE"]["SRC"]?>" data-vendor="<?=$arItem["PROPERTIES"]["VENDOR_CODE"]["VALUE"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-price-rb="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>">Купить</a>
                            <a href="#buy-one-click-form" class="product-slider-item-one-click-order buy-one-click" onclick="yaCounter1021532.reachGoal('zakaz_1_click'); return true;" data-good-id="<?=$arItem["ID"]?>" data-price="<?=$arItem["REGION_PRICE"]["DISCOUNT_VALUE"]?>" data-currency="RUB" data-good-name="<?=$arItem["NAME"]?>" data-good-url="<?=$dataPath?>" title="Оформление заказа в 1 клик">Купить в 1 клик</a>
                        </div>
                    </div>
                </div>
            </li>
        <?endforeach?>
    </ul>
</div>





<?endif?>