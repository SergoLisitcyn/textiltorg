<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск по каталогу");

// sort filter status + sku
CModule::includeModule('catalog');

$arrFilter = array();
$arSubQueryUp = array("IBLOCK_ID" => 25, ">CATALOG_QUANTITY" => 0);

if ($_GET["status"] == "in-stock")
    $arrFilter[] = array(
        "LOGIC" => "OR",
        array("ID" => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQueryUp)),
        array(">CATALOG_QUANTITY" => 0)
    );

if ($_GET["status"] == "to-order")
    $arrFilter[] = array(
        "LOGIC" => "AND",
        array("!ID" => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQueryUp)),
        array("CATALOG_QUANTITY" => 0)
    );

$GLOBALS["arrFilterCatalogSearch"][] = $arrFilter;
?>

<?if (!defined("IS_MOBILE")):?>
    <?$APPLICATION->IncludeComponent(
        "custom:catalog.sort.prototype",
        "",
        array(
            "ITEMS" => array(
                array(
                    "TITLE" => "Сортировать",
                    "CODE" => "sort",
                    "OPTIONS" => array(
                        "search" => "По результатам поиска",
                        "price-asc" => "Сначала дешевле",
                        "price-desc" => "Сначала дороже",
                        "rating" => "По рейтингу",
                        "rating-price" => "По рейтингу и цене",
                        "availability" => "По наличию",
                    ),
                    "DEFAULT" => "search"
                ),
                array(
                    "TITLE" => "Товаров на странице",
                    "CODE" => "page-count",
                    "OPTIONS" => array(
                        "16" => "16",
                        "28" => "28",
                        "40" => "40",
                        "60" => "60",
                        "100" => "100"
                    ),
                    "DEFAULT" => "16"
                ),
                array(
                    "TITLE" => "Товар на складе",
                    "CODE" => "status",
                    "OPTIONS" => array(
                        "in-stock" => "В наличии",
                        "to-order" => "На заказ",
                        "all" => "Все",
                    ),
                    "DEFAULT" => "all"
                ),
            ),
            "FILTER_NAME" => "arrFilterCatalogSearch",
            "PRICE_ID" => $_SESSION["PRICE_ID"]
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>

<?else:?>
    <?$APPLICATION->IncludeComponent(
        "custom:catalog.sort.prototype",
        "mobile",
        array(
            "ITEMS" => array(
                array(
                    "TITLE" => "Сортировать",
                    "CODE" => "sort",
                    "OPTIONS" => array(
                        "section" => "По результатам поиска",
                        "price-asc" => "Сначала дешевле",
                        "price-desc" => "Сначала дороже",
                        "rating" => "По рейтингу",
                        "rating-price" => "По рейтингу и цене",
                        "availability" => "По наличию",
                    ),
                    "DEFAULT" => "search"
                )
            ),
            "FILTER_NAME" => "arrFilterCatalogSearch",
            "PRICE_ID" => $_SESSION["PRICE_ID"]
        ),
        false,
        array(
            "HIDE_ICONS" => "Y"
        )
    );?>
<?endif?>

<?
if (empty($_REQUEST["QUERY"]) && empty($_REQUEST["ACTION"]))
{
    LocalRedirect("/");
}

$arWord = explode(" ", trim($_REQUEST["QUERY"]));
$filterName = array("LOGIC" => "AND");
foreach ($arWord as $wordName) {
    $filterName[] = array("%NAME" => trim($wordName));
}

$GLOBALS["arrFilterCatalogSearch"][] = array(
    array(
        "LOGIC" => "OR",
        array("PROPERTY_VENDOR_CODE" => $_REQUEST["QUERY"]),
        array($filterName)
    )
);
?>

<?
// ru
$GLOBALS["arrFilterCatalogSearch"][] = array(
    "PROPERTY_VIEW_SITE_RU_VALUE" => "Да"
);

$arSearch = array("CATALOG_PRICE_1", "CATALOG_PRICE_2", "CATALOG_PRICE_4", "CATALOG_PRICE_5", "CATALOG_PRICE_6", "CATALOG_PRICE_11");
$arReplace = array("PROPERTY_MIN_PRICE_MSK", "PROPERTY_MIN_PRICE_SPB", "PROPERTY_MIN_PRICE_EKB", "PROPERTY_MIN_PRICE_NOV", "PROPERTY_MIN_PRICE_RND", "PROPERTY_MIN_PRICE_MINSK");

$elementSortField = str_replace($arSearch, $arReplace, $GLOBALS["ELEMENT_SORT_FIELD"]);
$elementSortField2 = str_replace($arSearch, $arReplace, $GLOBALS["ELEMENT_SORT_FIELD2"]);
$elementSortOrder = $GLOBALS["ELEMENT_SORT_ORDER"];
$elementSortOrder2 = $GLOBALS["ELEMENT_SORT_ORDER2"];

?>
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "catalog",
    array(
        "ACTION_VARIABLE" => "action",
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
        "CACHE_TYPE" => "A",
        "CONVERT_CURRENCY" => "N",
        "DETAIL_URL" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => $elementSortField,
        "ELEMENT_SORT_FIELD2" => $elementSortField2,
        "ELEMENT_SORT_ORDER" => $elementSortOrder,
        "ELEMENT_SORT_ORDER2" => $elementSortOrder2,
        "FILTER_NAME" => "arrFilterCatalogSearch",
        "HIDE_NOT_AVAILABLE" => "N",
        "IBLOCK_ID" => "8",
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
        "PAGER_TEMPLATE" => defined("IS_MOBILE") ? "modern_ajax" : "ajax_for_search",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => $GLOBALS["PAGE_ELEMENT_COUNT"],
        "PARTIAL_PRODUCT_PROPERTIES" => "Y",
        "PRICE_CODE" => $GLOBALS["CITY_PRICE_CODE"],
        "GEO_REGION_CITY_NAME" => $GLOBALS["GEO_REGION_CITY_NAME"],
        "REGION_PRICE_CODE_DEFAULT" => "Москва",
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPERTIES" => array(
        ),
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "",
        "PRODUCT_SUBSCRIPTION" => "N",
        "PROPERTY_CODE" => array(
            0 => "",
            1 => "PHOTOS",
            2 => "",
        ),
        "SECTION_CODE" => "",
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_URL" => "",
        "SECTION_USER_FIELDS" => array(
            0 => "",
            1 => "UF_LINK",
            2 => "",
        ),
        "SEF_MODE" => "N",
        "SET_BROWSER_TITLE" => "Y",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_STATUS_404" => "Y",
        "SET_TITLE" => "Y",
        "SHOW_404" => "Y",
        "SHOW_ALL_WO_SECTION" => "Y",
        "SHOW_CLOSE_POPUP" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "TEMPLATE_THEME" => "blue",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "COMPONENT_TEMPLATE" => "catalog",
        "MESSAGE_NOT_FOUND" => "Товаров не найдено, попробуйте изменить запрос.",
        "OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "OFFERS_PROPERTY_CODE" => array(
            0 => "VENDOR_CODE",
            1 => "",
        ),
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_FIELD2" => "id",
        "OFFERS_SORT_ORDER2" => "desc",
        "OFFERS_CART_PROPERTIES" => array(
            0 => "VENDOR_CODE",
        ),
        "FILE_404" => ""
    ),
    false
);?>

<?if (!defined("IS_MOBILE")):?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "gift-wraps-cart-popup",
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
            "CACHE_TYPE" => "A",
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
        false,
        array("HIDE_ICONS" => "Y")
    );?>
<?endif?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>