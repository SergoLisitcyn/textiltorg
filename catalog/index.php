<?
define("HIDE_PAGE_TITLE", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "интернет магазин швейных машинок Москва, швейная машина купить в интернет магазин, швейная фурнитура интернет магазин розница, швейный дом интернет магазин, интернет магазин тканей, Интернет магазин оверлоков, Интернет магазин Санкт - Петербург, Интернет гладильной техники, Интернет магазин швейной техники");
$APPLICATION->SetPageProperty("description", "ТекстильТорг - Интернет-магазин швейных машинок, оверлоков, гладильной техники, ткани и аксессуаров в  Москве и Санкт-Петербурге.  Огромный выбор товаров, отличный сервис.");
$APPLICATION->SetTitle("Интернет - магазин швейной, гладильной техники, тканей и аксессуаров | Текстильторг");

if (defined("CATALOG_404")) {
    \Bitrix\Iblock\Component\Tools::process404("", true, true, true);
}
$sectionCode = $_REQUEST["SECTION_CODE"];
if($sectionCode == 'aksessuary-dlya-vyshivaniya' || $sectionCode == 'podarochnye-karty' || $sectionCode == 'aksessuary-dlya-shitya'  || $sectionCode == 'aksessuary-dlya-vyazaniya' || $sectionCode == 'aksessuary-dlya-glazheniya') {
    $classItem = 'aksessuary';
    $classItemButton = 'item-catolog-button';
}
?>

	<div class="catalog">

    <div class="left-block">

        <? $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "sect",
                "AREA_FILE_SUFFIX" => "right_sidebar",
                "COMPONENT_TEMPLATE" => ".default",
                "EDIT_TEMPLATE" => ""
            ),
            false,
            array("HIDE_ICONS" => "Y")
        ); ?>
    </div>


    <div class="right-block <? echo $classItem; ?>">

        <div class = "n_c_h_buttons  <? echo $classItemButton; ?>" >
            <div id = "n_c_h_catalog" class = "n_c_h_el n_c_h_active">  <div class = "n_c_h_catalog_img">  </div> <div class = "n_c_h_text"> КАТАЛОГ ШВЕЙНЫХ МАШИН </div>  </div>
            <div id = "n_c_h_vibor" class = "n_c_h_el">  <div class = "n_c_h_vibor_img">  </div> <div class = "n_c_h_text"> КАК ВЫБРАТЬ ШВЕЙНУЮ МАШИНУ </div>  </div>
            <div id = "n_c_h_proiz" class = "n_c_h_el">  <div class = "n_c_h_proiz_img">  </div> <div class = "n_c_h_text"> ПРОИЗВОДИТЕЛИ ШВЕЙНЫХ МАШИН </div>  </div>
        </div>

            <? $APPLICATION->IncludeComponent(
                "custom:catalog.sort.prototype",
                "",
                array(
                    "ITEMS" => array(
                        array(
                            "TITLE" => "Сортировать",
                            "CODE" => "sort",
                            "OPTIONS" => array(
                                "price-asc" => "Сначала дешевле",
                                "price-desc" => "Сначала дороже",
                                "rating" => "По рейтингу",
                                "rating-price" => "По рейтингу и цене",
                                "availability" => "По наличию",
                            ),
                            "DEFAULT" => "price-asc"
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
                    "FILTER_NAME" => "arrFilterCatalog",
                    "PRICE_ID" => $GLOBALS["PRICE_ID"]
                ),
                false,
                array(
                    "HIDE_ICONS" => "Y"
                )
            ); ?>


        <? $APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"catalog", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "N",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => $GLOBALS["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_FIELD2" => $GLOBALS["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER" => $GLOBALS["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_ORDER2" => $GLOBALS["ELEMENT_SORT_ORDER2"],
		"FILTER_NAME" => "arrFilterCatalog",
		"HIDE_NOT_AVAILABLE" => "N",
		"IBLOCK_ID" => "8",
		"IBLOCK_TYPE" => "catalog",
		"INCLUDE_SUBSECTIONS" => "A",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "4",
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
		"PAGER_TEMPLATE" => "modern_ajax",
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
		"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "UF_ACTION",
			1 => "UF_H1_TITLE",
			2 => "UF_ACTION_MORE",
			3 => "UF_LINK",
			4 => "",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
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
		"MESSAGE_NOT_FOUND" => "Товаров не найдено, попробуйте изменить параметры фильтра.",
		"CITY_SHOPS" => "Москва,Санкт-Петербург,Нижний Новгород,Ростов-на-Дону,Екатеринбург,Новосибирск,Казань",
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
		"FILE_404" => "",
		"HIDE_FILTER" => "N",
		"CUSTOM_FILTER" => "",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"PROPERTY_CODE_MOBILE" => "",
		"PRODUCT_ROW_VARIANTS" => "[{\"VARIANT\":2,\"BIG_DATA\":false},{\"VARIANT\":2,\"BIG_DATA\":false},{\"VARIANT\":2,\"BIG_DATA\":false},{\"VARIANT\":2,\"BIG_DATA\":false},{\"VARIANT\":2,\"BIG_DATA\":false},{\"VARIANT\":2,\"BIG_DATA\":false}]",
		"ENLARGE_PRODUCT" => "STRICT",
		"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"PRODUCT_DISPLAY_MODE" => "N",
		"SHOW_SLIDER" => "Y",
		"LABEL_PROP_MOBILE" => "",
		"LABEL_PROP_POSITION" => "top-left",
		"SHOW_MAX_QUANTITY" => "N",
		"RCM_TYPE" => "personal",
		"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
		"SHOW_FROM_SECTION" => "N",
		"DISPLAY_COMPARE" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"LAZY_LOAD" => "N",
		"LOAD_ON_SCROLL" => "N",
		"COMPATIBLE_MODE" => "Y",
		"COMPARE_PATH" => ""
	),
	false
); ?>
    </div>
</div>

<? SetTitle($h1); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>