<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "интернет магазин швейных машинок Москва, швейная машина купить в интернет магазин, швейная фурнитура интернет магазин розница, швейный дом интернет магазин, интернет магазин тканей, Интернет магазин оверлоков, Интернет магазин Санкт - Петербург, Интернет гладильной техники, Интернет магазин швейной техники");
$APPLICATION->SetPageProperty("description", "ТекстильТорг - Интернет-магазин швейных машинок, оверлоков, гладильной техники, ткани и аксессуаров в  Москве и Санкт-Петербурге.  Огромный выбор товаров, отличный сервис.");
$APPLICATION->SetTitle("Топ покупок");

if (defined("CATALOG_404"))
{
	\Bitrix\Iblock\Component\Tools::process404("", true, true, true);
}
?>
<?$arrFilterCatalog = ["PROPERTY_LEADER_VALUE" => "Да"];?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"bestsellers", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
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
		"PAGER_TEMPLATE" => "modern_ajax",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "16",
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
			0 => "CHAR_PROSHIVAEMYE_MATERIALY",
			1 => "CHAR_TIP_MASHINY",
			2 => "CHAR_MAKSIMALNAYA_SKOROST_SHITYA",
			3 => "CHAR_REGULIROVKA_DAVLENIYA_LAPKI_NA_TKAN",
			4 => "CHAR_DIFFERENCIALNAYA_PODACHA_MATERIALA",
			5 => "CHAR_KOLICHESTVO_NITEJ",
			6 => "CHAR_KOLICHESTVO_SHVOV",
			7 => "CHAR_KONTROL_NATYAZHENIYA_NITEJ",
			8 => "CHAR_MUSOROSBORNIK",
			9 => "CHAR_OBREZKA_NITEJ",
			10 => "GUARANTEE",
			11 => "",
		),
		"SECTION_ID" => "97",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "UF_ACTION",
			1 => "UF_TEXT_FOR_TOPSELL",
			2 => "UF_LINK",
			3 => "",
			4 => "UF_TEXT_FOR_BOTTSELL",
		),
		"SEF_MODE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "N",
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
		"COMPONENT_TEMPLATE" => "bestsellers",
		"MESSAGE_NOT_FOUND" => "Товаров не найдено, попробуйте изменить параметры фильтра.",
		"CITY_SHOPS" => "Москва,Санкт-Петербург,Нижний Новгород,Ростов-на-Дону,Екатеринбург",
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "VENDOR_CODE",
			1 => "CHAR_TIP_MASHINY",
			2 => "CHAR_KOLICHESTVO_NITEJ",
			3 => "CHAR_PROSHIVAEMYE_MATERIALY",
			4 => "CHAR_REGULIROVKA_DAVLENIYA_LAPKI_NA_TKAN",
			5 => "CHAR_KOLICHESTVO_SHVOV",
			6 => "CHAR_MAKSIMALNAYA_SKOROST_SHITYA",
			7 => "CHAR_DIFFERENCIALNAYA_PODACHA_MATERIALA",
			8 => "CHAR_KONTROL_NATYAZHENIYA_NITEJ",
			9 => "CHAR_MUSOROSBORNIK",
			10 => "CHAR_OBREZKA_NITEJ",
			11 => "",
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
		"SECTION_CODE" => ""
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>