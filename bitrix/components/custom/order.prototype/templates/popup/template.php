<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?$city_id = 0;?>
<?foreach ($arResult["LOCATIONS"] as $key => $arLocation):?>
    <?if($GLOBALS["GEO_REGION_CITY_NAME"] == $arLocation["CITY_NAME"])
        $city_id = $arLocation["ID"]?>
<?endforeach?>
<?
?>
<script src="https://securepayments.sberbank.ru/payment/docsite/_prod/assets/js/ipay.js"></script>
<script>
    var ipay = new IPAY({
        api_token : "itqeerhi81441fl1hba6vo99t8" // v0vnvf6i55kkokqrlhqdqu2k3n
    });
</script>

<div class="cart-full-container">
    <div class="form">
        <h3 class="bf-header">Оформление заказа:</h3>
        <form action="<?= $arParams["FORM_ACTION"] ?>" method="post" id="order-form">
            <?= bitrix_sessid_post() ?>

            <input type="hidden" name="skritoe_pole" value="">
            <input type="hidden" name="FORM_ACTION" value="ORDER">
            <input type="hidden" name="PERSON_TYPE_ID" value="1">
            <input type="hidden" name="CITY" value="<?=$city_id?>">

            <div id="confirm_self_data" class="for_kredit_btn">
                <div class="div_confirm_row">
                    <div class="div_confirm_column_ownd_l">
                        <div class="left1">
                            <div class="txt_left_cf">
                                <label for="confirm_name">Контактное лицо:<span class="red">*</span></label>
                                <input id="name" name="NAME" placeholder="Иванов Иван" class="input_confirm_cart" data-validate="name" type="text" autocomplete="off">
                            </div>

                            <div class="txt_left_cf">
                                <label for="confirm_phone">Ваш телефон:<span class="red">*</span></label>
                                <input id="phone" name="PHONE" class="input_confirm_cart" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+375123456789" ?>" data-validate="phone" type="text" autocomplete="off">
                            </div>

                            <div class="txt_left_cf">
                                <label for="confirm_emails">Ваш e-mail:</label>
                                <input id="email" name="EMAIL" class="input_confirm_cart input_confirm_cart_focus" data-validate="email" placeholder="sample@sample.<?=(SITE_ID == "s1") ? "ru" : "by" ?>" type="text" autocomplete="off">
                            </div>
                        </div>

                        <div class="left2">
                            <div class="txt_left_cf">
                                <label for="confirm_delivery">Способ получения:</label>
                                <div class="custom_select">
                                    <input type="hidden" id="delivery_item" name="DELIVERY" value="2">
                                    <div class="select_view">Выберите способ получения</div>
                                    <div id="delivery_type" class="select_options">
                                        <?foreach ($arResult["DELIVERYES"][$arResult["CITY_DELIVERY"]] as $deliveryId => $deliveryName):?>
                                            <div data-value="<?=$deliveryId?>" class="select_delivery"><?=$deliveryName?></div>
                                        <?endforeach?>
                                    </div>
                                </div>
                            </div>

                            <div class="choose_address">
                                <div class="txt_left_cf">
                                    <label for="confirm_address">Ваш адрес:</label>
                                    <input id="address" name="ADDRESS" class="input_confirm_cart input_confirm_cart_focus" value="" placeholder="ул. <?=(SITE_ID == "s1") ? "Уличная" : "Свердлова" ?>, 44, кв. 5"  type="text">
                                </div>
                            </div>

                            <div class="choose_store">
                                <div class="txt_left_cf">
                                    <?if ($arResult["STORES"]):?>
                                        <label for="confirm_delivery">Пункт выдачи:</label>
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
                                </div>
                            </div>

                            <div class="txt_left_cf">
                                <label for="forma_pokypki">Способ оплаты:</label>
                                <div class="custom_select">
                                    <input type="hidden" id="payment_type" name="PAYMENT" value="2">
                                    <div class="select_view">Выберите способ оплаты</div>
                                    <div id="pay_type" class="select_options">
                                        <?foreach ($arResult["PAY_SYSTEMS"] as $payId => $payName):?>
                                            <div data-value="<?=$payId?>"><?=$payName?></div>
                                        <?endforeach?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="popup-cart-bottom-button">
                <a href="#fancybox-close" class="silver_button">Продолжить покупки</a>
                <div id="cart-full-sum">
                    <div class="cart-full-content no-bottom-margin" data-height-group="2">
                        <?
                        $yaCounter = "1021532";
                        if ($_SERVER["SERVER_NAME"] == "spb.textiletorg.ru") {
                            $yaCounter = "48343148";
                        }
                        ?>
                        <input id="default-order-btn" class="red_button"
                               onclick="<? if (SITE_ID == "s1"): ?>yaCounter<?= $yaCounter ?>.reachGoal('oformit_zakaz_korzina'); return true;<? elseif (SITE_ID == "tp"): ?>yaCounter46320975.reachGoal('oformit_zakaz_korzina_tp'); return true;<? endif; ?>"<? /*=Helper::GetYandexCounter("oformit_zakaz_korzina")*/ ?>
                               type="submit" value="Оформить заказ">
                        <button type="button"
                                id="sberbank-order-btn" class="red_button"
                                onclick="orderMake(0)">Оформить заказ
                        </button>
                        <button type="button"
                                id="tinkoff-order-btn" class="red_button"
                                onclick="orderMake(1)">Оформить заказ
                        </button>

                        <div class="offer-block-order" style="display: none">
                            Нажимая на кнопку &laquo;Оформить заказ&raquo;,
                            вы принимаете условие <a href="/politika/oformlenie-zakaza-korzina" target="_blank">Публичной
                                оферты</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

<script>
    PAY_SYSTEMS = JSON.parse('<?=json_encode($arResult["PAY_SYSTEMS"])?>');
    REGION_CITY_NAME = "<?=$GLOBALS["GEO_REGION_CITY_NAME"]?>";
    ORDER_DELIVERYES = JSON.parse('<?=json_encode($arResult["DELIVERYES"])?>');
    <?if ($arResult["IS_KLADR"]):?>
    IS_KLADR = true;
    <?else:?>
    IS_KLADR = false;
    <?endif?>
</script>