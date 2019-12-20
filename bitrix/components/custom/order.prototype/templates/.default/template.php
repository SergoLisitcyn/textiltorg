<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if ($arResult["BASKET_COUNT_PRODUCT"]):?>
    <div class="cart-full-container">
        <?if ($arResult["ERROR"]):?>
            <div class="error-message">
                <?=$arResult["ERROR"]?>
            </div>
        <?endif?>
        <div class="cart-full-container-left">
            <h3 class="cart-full-title"><i class="tt-icons cart-1"></i>Корзина покупок</h3>
            <div class="cart-full-content no-bottom-padding" data-height-group="2">
                <?$APPLICATION->IncludeComponent(
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
                );?>
            </div>

            <?if ($arResult["ELEMENTS_CART_ID"]):?>
                <?$APPLICATION->IncludeComponent(
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
                );?>

                <?
                // rb
                if ($arResult["IS_PRICE_RB"])
                {
                    $GLOBALS["arrFilterMoreGoodsCart"][] = array(
                        ">CATALOG_CATALOG_GROUP_ID_11" => 0,
                        "CATALOG_CURRENCY_11" => "BYN"
                    );
                }
                ?>

            <?endif?>
        </div>
        <form action="<?=$arParams["FORM_ACTION"]?>" method="post" id="order-form" class="clearfix">
            <?=bitrix_sessid_post()?>
            
            <input type="text" class="skritoe_pole" name="skritoe_pole" value="">
            <input type="hidden" name="FORM_ACTION" value="ORDER">
            <input type="hidden" name="IS_MOBILE" value="0">
            <input type="hidden" id="radio-user-type-1" name="PERSON_TYPE_ID" value="1" checked="checked"/>
			
            <div class="cart-full-container-left">
                <h3 class="cart-full-title"><i class="tt-icons cart-2"></i>Покупатель</h3>
                <div class="cart-full-content small-bottom-padding">
                    <div id="type-user-1">
                        <div class="cart-full-content-left">
                            <div class="cart-full-content-el-row">
                                <div><label for="phone">Кнтактный телефон <span class="red">*</span></label></div>
                                <div><input type="text" name="PHONE" id="phone" placeholder="<?=(SITE_ID == "s1") ? "+79261234567" : "+375123456789" ?>" data-validate="phone" autocomplete="off"<?if(defined("IS_MOBILE")):?>style="position: absolute;top: -207px;text-align: left;border: 2px solid #d8d9d9;border-radius: 3px;margin-bottom: 0;padding: 5px 10px;width: 100%;line-height: 24px;box-sizing: border-box;"<?endif;?>></div>
                            </div>
                            <div class="cart-full-content-el-row">
                                <div><label for="name">Контактное лицо <span class="red">*</span></label></div>
                                <div><input type="text" name="NAME" id="name" placeholder="Иванов Иван" data-validate="name" autocomplete="off"<?if(defined("IS_MOBILE")):?>style="position: absolute;top: -288px;text-align: left;border: 2px solid #d8d9d9;border-radius: 3px;margin-bottom: 0;padding: 5px 10px;width: 100%;line-height: 24px;box-sizing: border-box;"<?endif;?>></div>
                            </div>
                        </div>
                        <div class="cart-full-content-right">
                            <div class="cart-full-content-el-row">
                                <div><label for="email">E-mail</label></div>
                                <div><input type="text" name="EMAIL" id="email" placeholder="sample@sample.<?=(SITE_ID == "s1") ? "ru" : "by" ?>" data-validate="email" autocomplete="off"></div>
                            </div>
                            <div class="cart-full-content-el-row">
                                <div><label for="city">Город</label></div>
                                <div>
                                    <div class="custom-select">
                                        <select name="CITY" id="city">
                                            <?foreach ($arResult["LOCATIONS"] as $arLocation):?>
                                                <option value="<?=$arLocation["ID"]?>" <?if ($arLocation["SELECTED"] == "Y"):?>selected<?endif?>><?=$arLocation["CITY_NAME"]?></option>
                                            <?endforeach?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cart-full-content-el-row">
                        <div class="w-25"><label for="comment">Ваш комментарий</label></div>
                        <div class="w-75">
                            <textarea name="COMMENT" id="comment" placeholder="Не смогла выбрать нужную лапку..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="cart-full-content-columns clearfix">
                    <div class="cart-full-content-column">
                        <h3 class="cart-full-title"><i class="tt-icons cart-3"></i>Получение</h3>
                        <div class="cart-full-content no-bottom-margin small-bottom-padding" data-height-group="1">

                            <div id="deliveryes-container">
                                <?
                                $iDelivery = 0;
                                foreach ($arResult["DELIVERYES"][$arResult["CITY_DELIVERY"]] as $deliveryId => $deliveryName):
                                    $iDelivery++;
                                    ?>
                                     <div class="custom-radio">
                                        <input type="radio" id="radio-del-<?=$deliveryId?>" value="<?=$deliveryId?>" name="DELIVERY" <?if ($iDelivery <= 1):?>checked="checked"<?endif?> />
                                        <label for="radio-del-<?=$deliveryId?>"><span></span><?=$deliveryName?></label>
                                    </div>
                                <?endforeach?>
                            </div>


                            <?if ($arResult["STORES"]):?>
                                <div class="cart-full-content-el-row" id="delivery-stores">
                                    <label for="store" class="top">
                                        Пункт выдачи:
                                        <a href="#as-stores-popup-header" class="" title="Адреса пунктов выдачи г. <?=$GLOBALS['GEO_REGION_CITY_NAME']?>">Показать на карте</a>
                                    </label>
                                    <select name="STORE" id="store" class="chosen-select chosen-icon" data-placeholder="Выберите пункт выдачи" tabindex="-1">
                                        <option value="Не выбран"></option>
                                        <?if ($arResult["STORES"]["HOME"]):?>
                                            <optgroup label="Магазины" class="shop">
                                                <?foreach ($arResult["STORES"]["HOME"] as $arStore):?>
                                                    <option value="<?=$arStore["ADDRESS"]?> (<?=$arStore["TYPE"]?>)"><?=$arStore["SHORT_ADDRESS"]?></option>
                                                <?endforeach?>
                                            </optgroup>
                                        <?endif?>
                                        <?if ($arResult["STORES"]["STORES"]):?>
                                            <optgroup label="Пункты выдачи *">
                                                <?foreach ($arResult["STORES"]["STORES"] as $arStore):?>
                                                    <option value="<?=$arStore["ADDRESS"]?> (<?=$arStore["TYPE"]?>)"><?=$arStore["SHORT_ADDRESS"]?></option>
                                                <?endforeach?>
                                            </optgroup>
                                        <?endif?>
                                    </select>
                                </div>
                            <?endif?>

                            <div class="cart-full-content-el-row <?if ($arResult["IS_ADDRESS_SHOW"] != "Y"):?>hidden<?endif?>" id="delivery-address">
                                <div class="w-100"><label for="address" class="top">Адрес доставки</label></div>
                                <div class="w-100"><input type="text" name="ADDRESS" id="address" autocomplete="off" placeholder="ул. <?=(SITE_ID == "s1") ? "Уличная" : "Свердлова" ?>, 44, кв. 5"></div>
                            </div>

                        </div>
                    </div>
                    <div class="cart-full-content-column">
                        <?if ($arResult["PAY_SYSTEMS"]):?>
                            <h3 class="cart-full-title"><i class="tt-icons cart-4"></i>Оплата</h3>
                            <div class="cart-full-content no-bottom-margin" data-height-group="1">
                                <?foreach ($arResult["PAY_SYSTEMS"] as $payId => $payName):?>
                                    <div class="custom-radio">
                                        <? $payType = (!$arResult["MRC"]) ? 2 : $payId; ?>
                                        <input type="radio" id="radio-pay-<?=$payId?>" data-type="<?=$payType?>" value="<?=$payId?>" name="PAYMENT" <?if ($payId == '2'):?>checked="checked"<?endif?> />
                                        <label for="radio-pay-<?=$payId?>"><span></span><?=$payName?></label>
                                        <?if ($arResult["HELP_PAY_SYSTEMS"][$payId]):?>
                                            <i class="cart-full-content-help-icon tooltip-message" data-tooltipe-text="<?=$arResult["HELP_PAY_SYSTEMS"][$payId]?>"></i>
                                        <?endif?>
                                    </div>
                                <?endforeach?>
                            </div>
                        <?endif?>
                    </div>
                    <? if(count($arResult["GIFTS"]) > 0): ?>
                        <div class="cart-full-container-left hide1 your-present">
                            <h3 class="cart-full-title red"><i class="tt-icons cart-5"></i>Ваш подарок</h3>
                            <div class="cart-full-content small-bottom-padding">
                                <div class="cart-full-content-center">
                                    <div class="cart-full-content-el-row">
                                        <div class="w-left"><label for="gift">Выберите подарок</label></div>
                                        <div class="w-right">
                                            <div class="custom-select">
                                                <select name="GIFT_BAS" id="gift">
                                                    <? foreach ($arResult["GIFTS"] as $idGift => $nameGift): ?>
                                                        <option value="<?=$idGift?>"><?=$nameGift?></option>
                                                    <? endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
            <div class="cart-full-container-right" id="cart-full-sum">
                <h3 class="cart-full-title tt-icons icon-3">ИТОГО:</h3>
                <div class="cart-full-content no-bottom-margin">
                    <div>
                        <p>Товаров</p>
                        <span class="field-wrap"><?=$GLOBALS['NUM_PRODUCTS']?> шт.</span>
                    </div>
                    <p>Сумма</p>
                    <div class="cart-full-summ">
                        <span class="field-wrap" data-sum="<?=$arResult["BASKET_SUM"]?>"><?=$arResult["BASKET_SUM_FORMAT"]?> руб.</span>
                    </div>
                    <?if (isset($arResult["DELIVER"])):?>
                        <div id="calc-container" class="hidden">
                            <p>Доставка:</p>
                            <div class="cart-calc-sum">
                                <?if ($arResult['IS_DELIVER_FREE'] || $arResult["DELIVER"] === 0):?>
                                    Стоимость: <strong>бесплатно</strong>
                                <?else:?>
                                    Стоимость: <strong><?=$arResult["DELIVER"]?></strong> руб.
                                <?endif?>
                                <br>
                                <?if ($arResult["DELIVER_PERIOD"]):?>
                                    Срок: <strong><?=$arResult["DELIVER_PERIOD"]?></strong> <?=$arResult['DELIVER_PERIOD_SIGN']?>
                                <?endif?>
                            </div>
                        </div>
                    <?endif?>
                    <?if ($arResult['DELIVER_EXPRESS']):?>
                        <div id="calc-container-express" class="hidden">
                            <p>Доставка:</p>
                            <div class="cart-calc-sum">
                                Стоимость: <strong><?=$arResult["DELIVER_EXPRESS"]?></strong> руб.
                                <br>
                                <?if ($arResult["DELIVER_EXPRESS_PERIOD"]):?>
                                    Срок: <strong><?=$arResult["DELIVER_EXPRESS_PERIOD"]?></strong> <?=$arResult['DELIVER_EXPRESS_PERIOD_SIGN']?>
                                <?endif?>
                            </div>
                        </div>
                    <?endif?>

                    <input id="default-order-btn" <?=defined("IS_MOBILE") ? "class=\"red_button_mob\"" : "class=\"red_button\"" ?> type="submit" value="Оформить заказ">
                    <button type="button" id="sberbank-order-btn" <?=defined("IS_MOBILE") ? "class=\"red_button_mob\"" : "class=\"red_button\"" ?> onclick="orderMake(0)">Оформить заказ</button>
                    <button type="button" id="tinkoff-order-btn"  <?=defined("IS_MOBILE") ? "class=\"red_button_mob\"" : "class=\"red_button\"" ?> onclick="orderMake(1)">Оформить заказ</button>


                    <?if (defined("IS_MOBILE")):?>
						<script>
							$(".send_mobile").click(function(event) {
								var stop = 0;
								$("#type-user-1 #name").css("border-color", "#d8d9d9");
								$("#type-user-1 #phone").css("border-color", "#d8d9d9");
								if($("#type-user-1 #name").val() == "") {
									event.preventDefault();
									$("#type-user-1 #name").css("border-color", "#ff0000");
									$('body, html').animate({scrollTop:420},1000);
									stop = 1;
								}
								if($("#type-user-1 #phone").val() == "") {
									event.preventDefault();
									$("#type-user-1 #phone").css("border-color", "#ff0000");
									if(stop == 0)
										$('body, html').animate({scrollTop:490},1000);
								}
							});
						</script>
					<?endif?>
                    <div class="offer-block-order">
                        Нажимая на кнопку &laquo;Оформить заказ&raquo;,
                        вы принимаете условие <a href="/politika/oformlenie-zakaza-korzina" target="_blank">Публичной оферты</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?$APPLICATION->IncludeComponent(
        "custom:targetmail.prototype",
        "",
        array(
            "ID" => "3077731",
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
    );?>

    <?$APPLICATION->IncludeComponent(
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
    );?>


<?else:?>
    Ваша корзина пуста.
<?endif?>

<script>
    ORDER_DELIVERYES = JSON.parse('<?=json_encode($arResult["DELIVERYES"])?>');
    <?if ($arResult["IS_KLADR"]):?>
        IS_KLADR = true;
    <?else:?>
        IS_KLADR = false;
    <?endif?>
</script>
<style>input[name='ADDRESS']{padding:0 10px !important}.suggestions-constraints{display:none !important}</style>