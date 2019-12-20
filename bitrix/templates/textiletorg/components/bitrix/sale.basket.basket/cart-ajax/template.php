<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$isAjax = (isset($_REQUEST["ajax_action_full_cart"]) && $_REQUEST["ajax_action_full_cart"] == "Y");

$idFullCart = 'fullCart'.$this->randString();
?>

<?if ($arResult["WARNING_MESSAGE"]):?>
    <p><?=$arResult["WARNING_MESSAGE"]?></p>
<?endif?>

<div id="box-full-cart">

<?
if ($isAjax)
{
    $APPLICATION->RestartBuffer();
}

$frame = $this->createFrame($idFullCart)->begin('');
?>
    <div class="sector_confirm_cart" style="max-height: none;">
        <?if($arResult["ITEMS"]["AnDelCanBuy"]):?>
            <form id="entryFrm" action="/confirm_order/cart.php" method="post">
                <table class="table_confirm_cart" cellspacing="0">
                    <thead>
                        <tr class="grad_silver_confirm">
                            <td class="font_confirm_bold table_confirm_cart_item">Товар</td>
                            <td class="font_confirm_bold table_confirm_cart_lenght">Количество</td>
                            <td class="font_confirm_bold table_confirm_cart_sub">Сумма</td>
                            <td>&nbsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $arItem):?>
                            <tr data-id="<?=$arItem["PRODUCT_ID"]?>">
                                <td class="table_confirm_cart_1">
                                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arItem["NAME"]?></a>
                                    <?if ($arItem["IS_GIFT_WRAPS"] && $arResult["IS_GIFT_WRAPS"]):?>
                                        <div class="pad_bottom_5">
                                            <a href="#gift_wrap_cart" class="active_link paper_show_incart dashed fancybox" data-id="<?=$arItem["ID"]?>" title="Выберите подарочную упаковку для товара">Выберите подарочную упаковку</a>
                                        </div>
                                    <?endif?>
                                    <?if ($arItem["PROPERTY_VENDOR_CODE_VALUE"]):?>
                                        <span class="blue_confirm_font">Артикул: </span><?=$arItem["PROPERTY_VENDOR_CODE_VALUE"]?>
                                    <?endif?>
                                    <? // Если торговое предложение, ты выведем каритнку SKU
                                    if ($arItem["IBLOCK_ID"] == 25):?>
                                        <div class="sku-image" title="Выбранный цвет" style="background-image: url('<?=$arItem["DETAIL_PICTURE_SRC"]?>')"></div>
                                    <?endif?>
                                </td>
                                <td class="table_confirm_cart_2">
                                    <div class="countbox">
                                        <a href="javascript://" title="Уменьшить" class="confirm_cart_cursor_left"><i class="tt-icons cart-cursor-left"></i></a>
                                        <input name="count_0" value="<?=$arItem["QUANTITY"]?>" data-id="<?=$arItem["ID"]?>" id="amount" class="confirm_cart_count" name="QUANTITY_INPUT_<?=$arItem["ID"]?>" readonly="" type="text">
                                        <a href="javascript://" title="Увеличить" class="confirm_cart_cursor_right"><i class="tt-icons cart-cursor-right"></i></a>
                                    </div>
                                    <?if ($arItem["DISCOUNT_PRICE_PERCENT"]):?>
                                        <div class="red discount">Скидка <?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?></div>
                                    <?endif?>
                                </td>
                                <td class="uz_cart_price table_confirm_cart_3">
                                    <?if ($arItem["FULL_SUM"] != $arItem["SUM"]):?>
                                        <span class="old_price"><?=$arItem["FULL_SUM"]?></span><br>
                                    <?endif?>
                                    <?=$arItem["SUM"]?>

                                </td>
                                <td class="table_confirm_cart_4">
                                    <a href="/cart/?action=delete&id=<?=$arItem["ID"]?>" class="dela_img scale-decrease delete-item <?if (count($arResult["ITEMS"]["AnDelCanBuy"]) <= 1):?>is-one-good<?endif?>">
                                        <i class="tt-icons cart-cursor-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        <?endforeach?>
                    </tbody>
                </table>
                <input name="row_count" value="1" type="hidden">
            </form>

            <form id="tinkoff" action="https://loans.tinkoff.ru/api/partners/v1/lightweight/create" method="post">
                <input name="shopId" value="textiletorg" type="hidden"/>
                <input name="sum" value="<?=$arResult["allSum"]?>" type="hidden"/>
                <input name="orderNumber" value="" type="hidden" id="tinkoff-form-order"/>
                <?php $i = 0; ?>
                <?foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $arItem):?>
                    <input name="itemName_<?=$i?>" value="<?=$arItem["NAME"]?>" type="hidden"/>
                    <input name="itemPrice_<?=$i?>" value="<?=$arItem["PRICE"]?>" type="hidden"/>
                    <input name="itemQuantity_<?=$i?>" value="<?=$arItem["QUANTITY"]?>" type="hidden"/>
                    <input name="itemVendorCode_<?=$i?>" value="<?=$arItem["PROPERTY_VENDOR_CODE_VALUE"]?>" type="hidden"/>
                    <?php $i++; ?>
                <?endforeach?>
                <input name="customerEmail" value="" type="hidden" id="tinkoff-form-email"/>
                <input name="customerPhone" value="" type="hidden" id="tinkoff-form-phone"/>
            </form>
        <?else:?>
            <span class="">Ваша корзина пуста</span>
        <?endif?>
    </div>
	
    <script>
        window.criteo_q = window.criteo_q || [];
       var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
		window.criteo_q.push(
			{ event: "setAccount", account: 38714 },
			{ event: "setHashedEmail", email: "<? echo $USER->GetEmail(); ?>" },
			{ event: "setSiteType", type: deviceType },
            { event: "viewBasket", item: [
                <? foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arItem): ?>
                { id: <?=(!empty($arItem["PRODUCT_XML_ID"])) ? $arItem["PRODUCT_XML_ID"] : $arItem["PRODUCT_ID"]?>, price: <?=$arItem["PRICE"]?>, quantity: <?=$arItem["QUANTITY"]?>},
                <? endforeach;?>
            ]}
        );
    </script>

<?
$frame->end();

if ($isAjax)
    die();
?>

</div>

<?
$arProductsRetail = array();
foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $arItem) {
    $arProductsRetail[] = $arItem["PRODUCT_ID"];
}
?>