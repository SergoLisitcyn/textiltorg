<?
define("HIDE_BOTTOM_PANEL", true);
define("HIDE_PAGE_TITLE", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Ваша корзина");
?>

    <script src="https://securepayments.sberbank.ru/payment/docsite/_prod/assets/js/ipay.js"></script>

    <script>
        var ipay = new IPAY({
            api_token : "itqeerhi81441fl1hba6vo99t8" // v0vnvf6i55kkokqrlhqdqu2k3n
        });
    </script>

<?if (defined("IS_MOBILE")):?>
    <?$APPLICATION->IncludeComponent(
        "custom:order.prototype",
        "mobile",
        array(
            "COUNTRY_NAME_ORIG" => $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "DELIVERYES" => array(
                "Регион" => array(
                    "4" => "Доставка",
                    "14" => "Экспресс доставка"
                ),
                "Москва" => array(
                    "3" => "Доставка курьером",
                ),
                "Санкт-Петербург" => array(
                    "3" => "Доставка курьером",
                ),
                "Екатеринбург" => array(
                    "3" => "Доставка курьером",
                ),
                "Нижний Новгород" => array(
                    "3" => "Доставка курьером",
                ),
                "Ростов-на-Дону" => array(
                    "3" => "Доставка курьером",
                ),
                "Минск" => array(
                    "3" => "Доставка курьером",
                ),
            ),
            "PAY_SYSTEMS" => array(
                "2" => "Наличными при получении",
                "3" => array(
                    "NAME" => "В кредит или рассрочку",
                    "KREDIT" => 3000,
                    "HELP" => "Вы можете оформить покупку в онлайн кредит, для этого потребуется лишь Ваше желание!<br>Наш специалист свяжется с Вами  после оформления заказа и проведет все необходимые процедуры.<br>После одобрения заявки (ожидание от 30 до 90 мин.) банком партнером и поступления оплаты, Вы получите свой заказ!<br>Процент одобрения высокий, так как мы работам с несколькими банками партнерами!"
                ),
                "6" => "Банковской картой онлайн"
            ),
            "DELIVERY_STORE" => array(
                "2" => "Самовывоз"
            ),
            "FORM_ACTION" => "/order/"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
<?else:?>
    <?$APPLICATION->IncludeComponent(
        "custom:order.prototype",
        "",
        array(
            "COUNTRY_NAME_ORIG" => $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "DELIVERYES" => array(
                "Регион" => array(
                    "4" => "Доставка",
                    "14" => "Экспресс доставка"
                ),
                "Москва" => array(
                    "3" => "Доставка курьером",
                ),
                "Санкт-Петербург" => array(
                    "3" => "Доставка курьером",
                ),
                "Екатеринбург" => array(
                    "3" => "Доставка курьером",
                ),
                "Нижний Новгород" => array(
                    "3" => "Доставка курьером",
                ),
                "Ростов-на-Дону" => array(
                    "3" => "Доставка курьером",
                ),
                "Минск" => array(
                    "3" => "Доставка курьером",
                ),
            ),
            "PAY_SYSTEMS" => array(
                "2" => "Наличными при получении",
                "3" => array(
                    "NAME" => "В кредит или рассрочку",
                    "KREDIT" => 3000,
                    "HELP" => "Вы можете оформить покупку в онлайн кредит, для этого потребуется лишь Ваше желание!<br>Наш специалист свяжется с Вами  после оформления заказа и проведет все необходимые процедуры.<br>После одобрения заявки (ожидание от 30 до 90 мин.) банком партнером и поступления оплаты, Вы получите свой заказ!<br>Процент одобрения высокий, так как мы работам с несколькими банками партнерами!"
                ),
                "6" => "Банковской картой онлайн"
            ),
            "DELIVERY_STORE" => array(
                "2" => "Самовывоз"
            ),
            "FORM_ACTION" => "/order/"
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
<?endif?>

<?if (!defined("IS_MOBILE")):?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "gift-wraps-cart",
        array(
            "ACTION_VARIABLE" => "action_wraps",
            "ADD_PICT_PROP" => "-",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "ADD_SECTIONS_CHAIN" => "Y",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BACKGROUND_IMAGE" => "-",
            "BASKET_URL" => "/cart/",
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "N",
            "CONVERT_CURRENCY" => "N",
            "DETAIL_URL" => "",
            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "NAME",
            "ELEMENT_SORT_FIELD2" => "ASC",
            "ELEMENT_SORT_ORDER" => "SORT",
            "ELEMENT_SORT_ORDER2" => "ASC",
            "FILTER_NAME" => "",
            "HIDE_NOT_AVAILABLE" => "N",
            "IBLOCK_ID" => "10",
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_SUBSECTIONS" => "A",
            "LABEL_PROP" => "-",
            "LINE_ELEMENT_COUNT" => "3",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_LIMIT" => "5",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "modern",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "50",
            "PARTIAL_PRODUCT_PROPERTIES" => "Y",
            "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
            "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
            "REGION_PRICE_CODE_DEFAULT" => "Москва",
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPERTIES" => array(),
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "",
            "PRODUCT_SUBSCRIPTION" => "N",
            "PROPERTY_CODE" => array(
                0 => "PHOTOS",
                1 => "",
            ),
            "SECTION_CODE" => "",
            "SECTION_ID" => "",
            "SECTION_ID_VARIABLE" => "",
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "UF_LINK",
                1 => "",
            ),
            "SEF_MODE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "Y",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "SHOW_CLOSE_POPUP" => "N",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_OLD_PRICE" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "TEMPLATE_THEME" => "blue",
            "USE_MAIN_ELEMENT_SECTION" => "N",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "COMPONENT_TEMPLATE" => "catalog"
        ),
        false
    );?>
<?endif?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>