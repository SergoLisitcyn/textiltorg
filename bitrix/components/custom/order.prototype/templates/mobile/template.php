<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if ($arResult["BASKET_COUNT_PRODUCT"]): ?>
    <div class="cart-full-container">
        <? if ($arResult["ERROR"]): ?>
            <div class="error-message">
                <?= $arResult["ERROR"] ?>
            </div>
        <? endif ?>
        <div class="cart-full-container-left">
            <div class="cart-full-content no-bottom-padding" data-height-group="2">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket",
                    "cart-ajax",
                    array(
                        "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
                        "COLUMNS_LIST" => array(
                            0 => "NAME",
                            1 => "PROPS",
                            2 => "DELETE",
                            3 => "PRICE",
                            4 => "QUANTITY",
                            5 => "SUM",
                            6 => "PROPERTY_VENDOR_CODE",
                        ),
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "PATH_TO_ORDER" => "/order/",
                        "HIDE_COUPON" => "Y",
                        "QUANTITY_FLOAT" => "N",
                        "PRICE_VAT_SHOW_VALUE" => "N",
                        "SET_TITLE" => "Y",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "OFFERS_PROPS" => "",
                        "COMPONENT_TEMPLATE" => "cart",
                        "USE_PREPAYMENT" => "N",
                        "ACTION_VARIABLE" => "action",
                        "AUTO_CALCULATION" => "Y",
                        "USE_GIFTS" => "Y",
                        "GIFTS_PLACE" => "BOTTOM",
                        "GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
                        "GIFTS_HIDE_BLOCK_TITLE" => "N",
                        "GIFTS_TEXT_LABEL_GIFT" => "Подарок",
                        "GIFTS_PRODUCT_QUANTITY_VARIABLE" => "undefined",
                        "GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
                        "GIFTS_SHOW_OLD_PRICE" => "N",
                        "GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
                        "GIFTS_SHOW_NAME" => "Y",
                        "GIFTS_SHOW_IMAGE" => "Y",
                        "GIFTS_MESS_BTN_BUY" => "Выбрать",
                        "GIFTS_MESS_BTN_DETAIL" => "Подробнее",
                        "GIFTS_PAGE_ELEMENT_COUNT" => "4",
                        "GIFTS_CONVERT_CURRENCY" => "N",
                        "GIFTS_HIDE_NOT_AVAILABLE" => "N",
                        "TEMPLATE_THEME" => "red"
                    ),
                    false
                ); ?>
            </div>

            <? if ($arResult["ELEMENTS_CART_ID"]): ?>
                <? $APPLICATION->IncludeComponent(
                    "custom:catalog.propfilter.prototype",
                    "",
                    array(
                        "IBLOCK_ID" => 8,
                        "ELEMENT_ID" => $arResult["ELEMENTS_CART_ID"],
                        "PROPERTY_CODE" => "MORE_GOODS",
                        "FILTER_NAME" => "arrFilterMoreGoodsCart"
                    ),
                    false,
                    array(
                        "HIDE_ICONS" => "Y"
                    )
                ); ?>

                <?
                // rb
                if ($arResult["IS_PRICE_RB"]) {
                    $GLOBALS["arrFilterMoreGoodsCart"][] = array(
                        ">CATALOG_CATALOG_GROUP_ID_11" => 0,
                        "CATALOG_CURRENCY_11" => "BYN"
                    );
                }
                ?>

            <? endif ?>
        </div>


        <div class="form">
            <h3 class="bf-header">Оформление заказа:</h3>
            <form action="<?= $arParams["FORM_ACTION"] ?>" method="post" id="order-form">
                <?= bitrix_sessid_post() ?>

                <input type="hidden" name="skritoe_pole" value="">
                <input type="hidden" name="FORM_ACTION" value="ORDER">
                <input type="hidden" name="PERSON_TYPE_ID" value="1">
                <input type="hidden" name="IS_MOBILE" value="1">

                <div id="confirm_self_data" class="for_kredit_btn">
                    <div class="div_confirm_row">
                        <div class="div_confirm_column_ownd_l">
                            <table cellpadding="5">
                                <tbody>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_l">
                                        <label for="confirm_name">Контактное лицо:<span class="red">*</span></label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <input id="name" name="NAME" placeholder="Иванов Иван" class="input_confirm_cart" data-validate="name" type="text" autocomplete="off">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_l">
                                        <label for="confirm_phone">Ваш телефон:<span class="red">*</span></label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <input id="phone" name="PHONE" class="input_confirm_cart" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+375123456789" ?>" data-validate="phone" type="text" autocomplete="off">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_l">
                                        <label for="confirm_emails">Ваш e-mail:</label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <input id="email" name="EMAIL" class="input_confirm_cart input_confirm_cart_focus" data-validate="email" placeholder="sample@sample.<?=(SITE_ID == "s1") ? "ru" : "by" ?>" type="text" autocomplete="off">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_l">
                                        <label for="confirm_city">Ваш город:</label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <div class="custom_select">
                                            <input type="text" class="select_view<?=$GLOBALS["GEO_REGION_CITY_NAME"] ? " select_view_choose" : ""?>" value="<?=$GLOBALS["GEO_REGION_CITY_NAME"] ? $GLOBALS["GEO_REGION_CITY_NAME"] : "Выберите город"?>" style="background:url(/bitrix/templates/textiletorg_mobile/img/select_green.png);background-repeat:no-repeat;background-position:205px">
                                            <div class="select_options">
                                                <?$city_id = 0;?>
                                                <?foreach ($arResult["LOCATIONS"] as $key => $arLocation):?>
                                                    <div data-value="<?=$arLocation["ID"]?>"<?=($GLOBALS["GEO_REGION_CITY_NAME"] == $arLocation["CITY_NAME"]) ? "class=\"option-selected\"" : ""?>><a href="?SET_CITY=<?=$arLocation["CITY_NAME"]?>"><?=$arLocation["CITY_NAME"]?></a></div>
                                                    <?if($GLOBALS["GEO_REGION_CITY_NAME"] == $arLocation["CITY_NAME"])
                                                        $city_id = $arLocation["ID"]?>
                                                <?endforeach?>
                                            </div>
                                            <input type="hidden" name="CITY" value="<?=$city_id?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_r">
                                        <label for="confirm_delivery">Способ получения:</label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <div class="custom_select">
                                            <input type="hidden" id="delivery_item" name="DELIVERY" value="2">
                                            <div class="select_view">Выберите способ получения</div>
                                            <div id="delivery_type" class="select_options">
                                                <?foreach ($arResult["DELIVERYES"][$arResult["CITY_DELIVERY"]] as $deliveryId => $deliveryName):?>
                                                    <div data-value="<?=$deliveryId?>" class="select_delivery"><?=$deliveryName?></div>
                                                <?endforeach?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="choose_store">
                                    <td class="txt_left_cf txt_left_cf_r">
                                        <label for="confirm_delivery">Пункт выдачи:</label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <?if ($arResult["STORES"]):?>
                                            <div class="custom_select">
                                                <input type="hidden" id="store_item" name="STORE" value="">
                                                <div class="select_view">Выберите пункт выдачи</div>
                                                <div id="store_type" class="select_options">
                                                    <?foreach ($arResult["STORES"]["HOME"] as $arStore):?>
                                                        <div style="background-image:url(<?=SITE_TEMPLATE_PATH?>/img/shop.png);background-repeat: no-repeat; background-position: left center;padding-left: 35px;" data-value="<?=$arStore["ADDRESS"]?> (<?=$arStore["TYPE"]?>)"><?=$arStore["SHORT_ADDRESS"]?><br><span style="background-image: url(/bitrix/templates/textiletorg_mobile/img/metro.png);background-repeat: no-repeat;background-position: left center;padding-left: 15px;color: rgb(85, 85, 85);font-size: .9em;"><?=$arStore["METRO"]?></span></div>
                                                    <?endforeach?>
                                                    <?foreach ($arResult["STORES"]["STORES"] as $arStore):?>
                                                        <div style="background-image:url(<?=SITE_TEMPLATE_PATH?>/img/location.png);;background-repeat: no-repeat; background-position: left center;padding-left: 35px;" data-value="<?=$arStore["ADDRESS"]?> (<?=$arStore["TYPE"]?>)"><?=$arStore["SHORT_ADDRESS"]?></div>
                                                    <?endforeach?>
                                                </div>
                                            </div>
                                        <?endif?>
                                    </td>
                                </tr>
                                <tr class="choose_address">
                                    <td class="txt_left_cf txt_left_cf_r">
                                        <label for="confirm_address">Ваш адрес:</label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <input id="address" name="ADDRESS" class="input_confirm_cart input_confirm_cart_focus" value="" placeholder="ул. <?=(SITE_ID == "s1") ? "Уличная" : "Свердлова" ?>, 44, кв. 5"  type="text">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="txt_left_cf txt_left_cf_r">
                                        <label for="forma_pokypki">Способ оплаты:<span class="question open_popup"><span class="message popup" style="left:-100px;width:230px">Вы можете оформить покупку в онлайн кредит, для этого потребуется лишь Ваше желание!<br>Наш специалист свяжется с Вами  после оформления заказа и проведет все необходимые процедуры.<br>После одобрения заявки (ожидание от 30 до 90 мин.) банком партнером и поступления оплаты, Вы получите свой заказ!<br>Процент одобрения высокий, так как мы работам с несколькими банками партнерами!</span></span></label>
                                    </td>
                                    <td class="txt_left_cf">
                                        <div class="custom_select">
                                            <input type="hidden" id="payment_type" name="PAYMENT" value="2">
                                            <div class="select_view">Выберите способ оплаты</div>
                                            <div id="pay_type" class="select_options">
                                                <?foreach ($arResult["PAY_SYSTEMS"] as $payId => $payName):?>
                                                    <? $payType = (!$arResult["MRC"]) ? 2 : $payId; ?>
                                                    <div data-value="<?=$payId?>" data-type="<?=$payType?>"><?=$payName?></div>
                                                <?endforeach?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="cart-full-container-right" id="cart-full-sum">
                    <div class="cart-full-content no-bottom-margin" data-height-group="2">

                        <input id="default-order-btn" class="red_button_mob"
                               type="submit" value="Оформить заказ">
                        <button type="button"
                                id="sberbank-order-btn" class="red_button_mob"
                                onclick="orderMake(0)">Оформить заказ
                        </button>
                        <button type="button"
                                id="tinkoff-order-btn" class="red_button_mob"
                                onclick="orderMake(1)">Оформить заказ
                        </button>

                        <script>
                            $(".send_mobile").click(function (event) {
                                var stop = 0;
                                $("#type-user-1 #name").css("border-color", "#d8d9d9");
                                $("#type-user-1 #phone").css("border-color", "#d8d9d9");
                                if ($("#type-user-1 #name").val() == "") {
                                    event.preventDefault();
                                    $("#type-user-1 #name").css("border-color", "#ff0000");
                                    $('body, html').animate({scrollTop: 420}, 1000);
                                    stop = 1;
                                }
                                if ($("#type-user-1 #phone").val() == "") {
                                    event.preventDefault();
                                    $("#type-user-1 #phone").css("border-color", "#ff0000");
                                    if (stop == 0)
                                        $('body, html').animate({scrollTop: 490}, 1000);
                                }
                            });
                        </script>

                        <div class="offer-block-order" style="display: none">
                            Нажимая на кнопку &laquo;Оформить заказ&raquo;,
                            вы принимаете условие <a href="/politika/oformlenie-zakaza-korzina" target="_blank">Публичной
                                оферты</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="offer-block-callback">
            Нажимая на кнопку «Жду звонка!», <br>
            вы принимаете условие <a href="/politika/zakazat-zvonok" target="_blank">Публичной оферты</a>
        </div>

        <form id="tinkoff" action="https://loans.tinkoff.ru/api/partners/v1/lightweight/create" method="post">
            <input name="shopId" value="textiletorg" type="hidden"/>
            <input name="sum" value="<?=$arResult["allSum"]?>" type="hidden" id="tinkoff-form-sum"/>
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

    </div>

    <? $APPLICATION->IncludeComponent(
        "custom:targetmail.prototype",
        "",
        array(
            "ID" => "2791918",
            "PAGE_CATEGORY" => "/catalog/index.php",
            "PAGE_PRODUCT" => "/catalog/detail/index.php",
            "PAGE_CART" => "/^\/cart/",
            "PAGE_PURCHASE" => "/^\/order/",
            "PRODUCT_ID" => $arResult["ELEMENTS_CART_XML_ID"],
            "TOTAL_VALUE" => $arResult["BASKET_SUM_FORMAT"],
            "LIST" => "1"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>

    <? $APPLICATION->IncludeComponent(
        "custom:gdeslon.prototype",
        "",
        array(
            "MID" => "88568",
            "PAGE_CATEGORY" => "/catalog/index.php",
            "PAGE_PRODUCT" => "/catalog/detail/index.php",
            "PAGE_CART" => "/^\/cart/",
            "PAGE_PURCHASE" => "/^\/order/",
            "PAGE_SEARCH" => "/^\/search/",
            "CODES" => $arResult["GDESLON_CODES"],
            "CAT_ID" => ""
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    ); ?>


<? else: ?>
    Ваша корзина пуста.
<? endif ?>

<script>
    ORDER_DELIVERYES = JSON.parse('<?=json_encode($arResult["DELIVERYES"])?>');
    <?if ($arResult["IS_KLADR"]):?>
    IS_KLADR = true;
    <?else:?>
    IS_KLADR = false;
    <?endif?>
</script>
<style>input[name='ADDRESS'] {
        padding: 0 10px !important
    }

    .suggestions-constraints {
        display: none !important
    }</style>