<?
define("HIDE_PAGE_TITLE", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (defined("CATALOG_404")) {
	\Bitrix\Iblock\Component\Tools::process404("", true, true, true);
}
if(!empty($_GET["TAG"])) {
	$arSelect = Array("ID", "IBLOCK_ID", "*");
	$arFilter = Array("IBLOCK_ID"=>52, "ACTIVE"=>"Y", "CODE"=>$_GET["TAG"]);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while($ob = $res->GetNextElement()) {
		$arTag = array_merge($ob->GetFields(), $ob->GetProperties());
	}
	if(empty($arTag["ID"])) {
		$APPLICATION->SetTitle("Страница тегов");
		$APPLICATION->SetPageProperty("title", "Страница тегов");
		//$APPLICATION->SetPageProperty("keywords", "Товар не найден");
		//$APPLICATION->SetPageProperty("description", "Товар не найден");
		$APPLICATION->AddChainItem("Страница тегов", "");
		
		echo '<h1 class="catalog-action-title">Продукты не найдены</h1>';
		echo '<div class="section-title-action-description"><p></p><p></p></div>';
	} else {
		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arTag['IBLOCK_ID'], $arTag['ID']);
		$arTag['IPROPERTY_VALUES'] = $ipropValues->getValues();

		$regionName = $GLOBALS["GEO_REGION_CITY_NAME"];
		$arCitysEnd = array(
			"Москва" => "Москве",
			"Санкт-Петербург" => "Санкт-Петербурге",
			"Екатеринбург" => "Екатеринбурге",
			"Нижний Новгород" => "Нижнем Новгороде",
			"Ростов-на-Дону" => "Ростове-на-Дону",
		);
		if(isset($arCitysEnd[$regionName]))
			$city = $arCitysEnd[$regionName];
		else
			$city = "Москве";
		$APPLICATION->SetTitle(str_replace("#CITY#", $city, $arTag['IPROPERTY_VALUES']['ELEMENT_META_TITLE']));
		$APPLICATION->SetPageProperty("title", str_replace("#CITY#", $city, $arTag['IPROPERTY_VALUES']['ELEMENT_META_TITLE']));
		$APPLICATION->SetPageProperty("keywords", $arTag['IPROPERTY_VALUES']['ELEMENT_META_KEYWORDS']);
		$APPLICATION->SetPageProperty("description", str_replace("#CITY#", $city, $arTag['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION']));
		$APPLICATION->AddChainItem(str_replace("#CITY#", $city, $arTag['IPROPERTY_VALUES']['ELEMENT_META_TITLE']), $arTag["DETAIL_PAGE_URL"]);

		$arrFilterCatalog = array("%TAGS"=>$arTag['NAME']);

		echo '<h1 class="catalog-action-title">'.$arTag['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'].'</h1>';
		echo '<div id="seo-source-tag" class="section-title-action-description"><p></p><p>'.$arTag['PREVIEW_TEXT'].'</p></div>';

		if(!defined("IS_MOBILE")) {	
			$APPLICATION->IncludeComponent(
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
			);
		} else {
			$APPLICATION->IncludeComponent(
				"custom:catalog.sort.prototype",
				"mobile",
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
						)
					),
					"FILTER_NAME" => "arrFilterCatalog",
					"PRICE_ID" => $GLOBALS["PRICE_ID"]
				),
				false,
				array(
					"HIDE_ICONS" => "Y"
				)
			);
			$APPLICATION->IncludeComponent(
				"bitrix:catalog.smart.filter",
				"catalog",
				array(
					"CACHE_GROUPS" => "Y",
					"CACHE_TIME" => "36000000",
					"CACHE_TYPE" => "A",
					"CONVERT_CURRENCY" => "N",
					"DISPLAY_ELEMENT_COUNT" => "Y",
					"FILTER_NAME" => "arrFilterCatalog",
					"FILTER_VIEW_MODE" => "vertical",
					"HIDE_NOT_AVAILABLE" => "N",
					"IBLOCK_ID" => "8",
					"IBLOCK_TYPE" => "catalog",
					"INSTANT_RELOAD" => "N",
					"PAGER_PARAMS_NAME" => "arrPager",
					"POPUP_POSITION" => "left",
					"PRICE_CODE" => $GLOBALS["CITY_FILTER_PRICE_CODE"],
					"SAVE_IN_SESSION" => "N",
					"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
					"SECTION_CODE_PATH" => "",
					"SECTION_DESCRIPTION" => "-",
					"SECTION_ID" => "",
					"SECTION_TITLE" => "-",
					"SEF_MODE" => "N",
					"SEF_RULE" => "",
					"SMART_FILTER_PATH" => "",
					"TEMPLATE_THEME" => "red",
					"XML_EXPORT" => "N",
					"COMPONENT_TEMPLATE" => "catalog",
					"HELP_SECTION_CODE" => $_REQUEST["SECTION_CODE"],
				),
				false
			);
		}
		$APPLICATION->IncludeComponent(
			"bitrix:catalog.section", 
			"catalog", 
			array(
				"TAGS_PAGE" => "Y",
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
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "Y",
				"CACHE_TIME" => "36000000",
				"CACHE_TYPE" => "A",
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
					0 => "UF_ACTION",
					1 => "UF_LINK",
					2 => "UF_H1_TITLE",
					3 => "UF_ACTION_MORE",
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
				"HIDE_FILTER" => "N"
			),
			false
		);
	}
}?>
<script>
	$(function(){
		var content = $("#seo-source-tag").html();
		$("#seo-source-tag").remove();
		$("#seo-target").append(content);
	});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>