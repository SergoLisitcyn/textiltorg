<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
$GOOD_ID = explode(",", $_REQUEST["id"]);

if (empty($GOOD_ID) || !is_array($GOOD_ID))
{
    die;
}
?>

<?

$APPLICATION->IncludeComponent(
    "custom:region.prototype",
    "",
    array(
        "HOUSE_REGIONS" => $GLOBALS["REGION_HOUSE_REGIONS"],
        "DEFAULT_REGION" => $GLOBALS["REGION_DEFAULT_REGION"],
        "COUNTRY_NAME_ORIG"=> $GLOBALS["REGION_COUNTRY_NAME_ORIG"],
        "CTX_PARAM" => array(
            "MSK" => "Москва",
            "SPB" => "Санкт-Петербург",
            "EKB" => "Екатеринбург",
            "N_NOW" => "Нижний Новгород",
            "RNB" => "Ростов-на-Дону",
			"NSK" => "Новосибирск",
			"KZN" => "Казань"
        ),
        "CITIES_PRICE" => array(
            "Москва" => 1,
            "Санкт-Петербург" => 2,
            "Екатеринбург" => 4,
            "Нижний Новгород" => 5,
            "Ростов-на-Дону" => 6,
			"Новосибирск" => 12,
			"Казань" => 13,
            "Минск" => 7
        )
    ),
    false,
    array(
        "HIDE_ICONS" => "Y"
    )
);
?>



<?$APPLICATION->IncludeComponent(
    "custom:catalog.propfilter.prototype",
    "",
    array(
        "IBLOCK_ID" => 8,
        "ELEMENT_ID" => $GOOD_ID,
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
if (SITE_ID == 'by')
{
    $GLOBALS["arrFilterMoreGoodsCart"][] = array(
        "CATALOG_CURRENCY_11" => "BYN",
        "PROPERTY_VIEW_SITE_RB_VALUE" => "Да"
    );
}

foreach ($GOOD_ID as $good)
{
    $rsGoods = CIBlockElement::GetList(
        array(
            "SORT" => "ASC"
        ),
        array(
            "IBLOCK_ID" => $IBLOCK_ID,
            "ID" => $good,
        ),
        false,
        false,
        array('IBLOCK_SECTION_ID')
    );

    while($arGood = $rsGoods->GetNext())
    {
        // Дополним фильтр свойством у секций "Не забудьте куптить"
        if (isset($arGood['IBLOCK_SECTION_ID']) && !empty($arGood['IBLOCK_SECTION_ID']))
        {
            $nav = CIBlockSection::GetNavChain(false, intval($arGood['IBLOCK_SECTION_ID']));
            $arSectionIds = array();
            while($arSectionPath = $nav->GetNext())
            {
                $arSectionIds[] = $arSectionPath["ID"];
            }

            $arMoreCoodsIds = array();
            $arFilter = Array('IBLOCK_ID' => 8, 'ID' => $arSectionIds);
            $db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, false, array("ID", "IBLOCK_ID", "UF_DP"));
            while($ar_result = $db_list->GetNext())
            {
                if (!empty($ar_result["UF_DP"]))
                {
                    $arMoreCoodsIds = array_merge($arMoreCoodsIds, $ar_result["UF_DP"]);
                }
            }
        }

        if (count($arMoreCoodsIds) > 0)
        {
            if (empty($GLOBALS["arrFilterMoreGoodsCart"]["ID"]))
            {
                $arMergeFilterIds = $arMoreCoodsIds;
            }
            else
            {
                $arMergeFilterIds = array_merge($arMoreCoodsIds, $GLOBALS["arrFilterMoreGoodsCart"]["ID"]);
            }
            $arMergeFilterIds = array_unique($arMergeFilterIds);
            $GLOBALS["arrFilterMoreGoodsCart"]["ID"] = $arMergeFilterIds;
        }
    }
}
?>

<?
    if (!empty($_POST["id"])){
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "more-goods-cart",
            array(
                "ACTION_VARIABLE" => "action_catalog",
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
                "ELEMENT_SORT_FIELD" => "SORT",
                "ELEMENT_SORT_FIELD2" => "NAME",
                "ELEMENT_SORT_ORDER" => "ASC",
                "ELEMENT_SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "arrFilterMoreGoodsCart",
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
                "PAGER_TEMPLATE" => "modern",
                "PAGER_TITLE" => "Товары",
                "PAGE_ELEMENT_COUNT" => "20",
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
                "SET_META_DESCRIPTION" => "N",
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
                "COMPONENT_TEMPLATE" => "catalog",
                "ELEMENTS_CART_ID" => $GOOD_ID,
                "OFFERS_FIELD_CODE" => array(
                    0 => "ID",
                    1 => "",
                ),
                "OFFERS_PROPERTY_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_FIELD2" => "id",
                "OFFERS_SORT_ORDER2" => "desc",
                "OFFERS_CART_PROPERTIES" => array()
            ),
            false,
            array("HIDE_ICONS" => "Y")
        );
    }
?>