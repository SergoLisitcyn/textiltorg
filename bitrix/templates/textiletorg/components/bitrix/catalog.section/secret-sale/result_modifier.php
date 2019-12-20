<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

if (!empty($arResult["SECTION_PAGE_URL"]))
{
    $mainPage = Helper::RemoveOneLavelUrl($arResult["SECTION_PAGE_URL"]);
    if ($mainPage != Helper::GetRequestUrl())
    {
        LocalRedirect(rtrim($mainPage, '/') . Helper::GetRequestFilterUrl());
    }
}


/*
// is export
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

$arIDs = array();
$arCommentsCount = array();
foreach ($arResult["ITEMS"] as $nItem => $arItem)
    $arIDs[] = $arItem["ID"];

// count comments
$arFilter = array(
    "ACTIVE" => "Y",
    "IBLOCK_ID" => (SITE_ID == "tp") ? "39" : "38",
    "PROPERTY_ELEMENT" => $arIDs
);
$arSelect = array("ID", "IBLOCK_ID", "PROPERTY_ELEMENT");
$rsData = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
while ($arItem = $rsData->GetNext()) {
    $arCommentsCount[$arItem["PROPERTY_ELEMENT_VALUE"]] += 1;
}
*/
// actions good
$rsElementsActions = CIBlockElement::GetList(
    array(
        "SORT" => "ASC"
    ),
    array(
        "IBLOCK_ID" => 1,
        "ACTIVE" => "Y",
        "PROPERTY_PRODUCTS" => $arIDs
    ),
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_ICON_GOOD", "PROPERTY_PRODUCTS")
);

$arElementsActions = array();
while ($arElementActions = $rsElementsActions->GetNext())
{
    if ($arElementActions["PROPERTY_ICON_GOOD_VALUE"])
    {
        $arElementActions["ICON_GOOD"] = CFile::GetPath($arElementActions["PROPERTY_ICON_GOOD_VALUE"]);
    }

    $arElementsActions[] = $arElementActions;
}

// Распихаем акции по товарам
$rsActions = CIBlockElement::GetList(
    array("SORT" => "ASC"),
    array("IBLOCK_ID" => 1, "ACTIVE" => "Y", "PROPERTY_PRODUCTS" => $arIDs),
    false,
    false
);
$arAcrions = array();
while($ob = $rsActions->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $arAcrions[] = array_merge($arFields, $arProps);
}

foreach ($arResult["ITEMS"] as $key => $arItem) {
    $arItemActions = array();
    foreach ($arAcrions as $action) {
        if (in_array($arItem["ID"], $action["PRODUCTS"]["VALUE"])) {
            $arItemActions[] = array(
                "NAME" => $action["NAME"],
                "URL" => $action["DETAIL_PAGE_URL"]
            );
        }
    }
    $arResult["ITEMS"][$key]["ACTIONS"] = $arItemActions;
}
/*
// overviews
$rsElementsOverview = CIBlockElement::GetList(
    array(
        "SORT" => "ASC"
    ),
    array(
        "IBLOCK_ID" => 5,
        "ACTIVE" => "Y",
        "PROPERTY_PRODUCTS" => $arIDs
    ),
    false,
    false,
    array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_PRODUCTS")
);

$arElementsOverview = array();
while ($arElementOverview = $rsElementsOverview->GetNext())
{
    if ($arElementOverview["PROPERTY_ICON_GOOD_VALUE"])
    {
        $arElementOverview["ICON_GOOD"] = CFile::GetPath($arElementOverview["PROPERTY_ICON_GOOD_VALUE"]);
    }

    $arElementsOverview[] = $arElementOverview;
}
*/
// items
foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["RESIZE_PICTURE"] = Helper::Resize($arPictures, 220, 180, true);
    if ($arItem["RESIZE_PICTURE"] == NULL)
        $arItem["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-200.png";

    // more photos
    if ($arItem["PROPERTIES"]["PHOTOS"]["VALUE"])
    {
        $arItem["PHOTOS"] = array();
        array_unshift($arItem["PROPERTIES"]["PHOTOS"]["VALUE"], $arItem["DETAIL_PICTURE"]["ID"]);

        foreach ($arItem["PROPERTIES"]["PHOTOS"]["VALUE"] as $photo)
            $arItem["PHOTOS"][] = array(
                "PREVIEW" => Helper::Resize(array($photo), 46, 46),
                "DETAIL" => Helper::Resize(array($photo), 220, 180)
            );
    }

    //anonce
    $arItem["PREVIEW_TEXT"] = Helper::Truncate($arItem["PREVIEW_TEXT"], 500);

    if ($arItem["OFFERS"])
    {
        $minPrice = array();
        // offers
        foreach ($arItem["OFFERS"] as $nOffer => $arOffer)
        {
            //resize picture
            $arPictures = array(
                $arOffer["PREVIEW_PICTURE"]["ID"],
                $arOffer["DETAIL_PICTURE"]["ID"]
            );

            $arOffer["RESIZE_PICTURE"] = Helper::Resize($arPictures, 210, 210, true);
            if ($arOffer["RESIZE_PICTURE"] == NULL)
                $arOffer["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-200.png";

            //price
            $arOffer["REGION_PRICE"] = ($arOffer["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
                $arOffer["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
                $arOffer["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

            if (isset($arResult["REGION_PRICE"]))
            {
                $arOffer["REGION_PRICE"]["DEFAULT_VALUE"] = $arOffer["REGION_PRICE"]["DISCOUNT_VALUE"];
            }

            // min price

            if (empty($minPrice) || $minPrice["DEFAULT_VALUE"] > $arOffer["REGION_PRICE"]["DEFAULT_VALUE"])
                $minPrice = $arOffer["REGION_PRICE"];

            $arItem["OFFERS"][$nOffer] = $arOffer;
        }

        $arItem["CATALOG_MEASURE_NAME"] = $arOffer["CATALOG_MEASURE_NAME"];
        $arItem["REGION_PRICE"] = $minPrice;
    }
    else
    {
        $arItem["REGION_PRICE"] = ($arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
        $arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
        $arItem["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

        if (isset($arItem["REGION_PRICE"]))
        {
            $arItem["REGION_PRICE"]["DEFAULT_VALUE"] = $arItem["REGION_PRICE"]["DISCOUNT_VALUE"];
        }
    }

    if ($arItem["REGION_PRICE"]["CURRENCY"] == "BYN")
    {
        $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arItem["REGION_PRICE"]["DISCOUNT_VALUE"], 2, ',', ' ');
        $arItem["REGION_PRICE"]["VALUE"] = number_format($arItem["REGION_PRICE"]["VALUE"], 2, ',', ' ');
    }
    else
    {
        $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = number_format($arItem["REGION_PRICE"]["DISCOUNT_VALUE"], 0, '.', ' ');
        $arItem["REGION_PRICE"]["VALUE"] = number_format($arItem["REGION_PRICE"]["VALUE"], 0, '.', ' ');

        // delivery price calc
        if (Loader::includeModule('ayers.delivery'))
        {
            $isInShops = CalcPrice::IsInShops();

            if (!$isInShops && SITE_ID == 's1')
            {
                $optimalCompany = CalcPrice::GetOptimalCompany4City($arParams["GEO_REGION_CITY_NAME"]);

                $arOptimalDelivery4Items = CalcPrice::GetOptimalDelivery4Items(
                    $optimalCompany,
                    $arParams["GEO_REGION_CITY_NAME"],
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"],
                    array($arItem),
                    true
                );

                if (!empty($arOptimalDelivery4Items['DELIVERY']['STANDART']['PRICE']) &&  !empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]))
                {
                    $arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"] += $arOptimalDelivery4Items['DELIVERY']['STANDART']['PRICE'];
                    $arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"] = CalcPrice::Format($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]);
                }

                if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arItem["REGION_PRICE"]["DISCOUNT_VALUE"])
                {
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
                }
            }
        }
    }

    //compare
    $arItem["ADD_COMPARE_URL"] = "/compare/?action=ADD_TO_COMPARE_LIST&id=".$arItem["ID"];
    $arItem["DELETE_COMPARE_URL"] = "/compare/?action=DELETE_FROM_COMPARE_RESULT&ID=".$arItem["ID"];

    // properties icons
    $arItem["IS_ICONS"] = false;
    $arPropertiesIcons = array(
        "PRODUCT_CERTIFIED",
        "SUITABLE_ALL_TYPES_FABRIC",
        "GIFT_COUPON_25",
        "EXPERT_ADVICE",
        "PARTICIPANT_ACTION_DISPOSAL"
    );

    foreach ($arPropertiesIcons as $code)
        if ($arItem["PROPERTIES"][$code]["VALUE_XML_ID"] == "Y")
            $arItem["IS_ICONS"] = true;

    // rating

    $rating = (intval($arItem["PROPERTIES"]["RATING"]["VALUE"]))? intval($arItem["PROPERTIES"]["RATING"]["VALUE"]) : 0;

    switch ($rating)
    {
        case "1":
            $class = "one";
            break;
        case "2":
            $class = "two";
            break;
        case "3":
            $class = "three";
            break;
        case "4":
            $class = "four";
            break;
        case "5":
            $class = "five";
            break;

        default:
            $class = "zero";
            break;
    }

    $arItem["RATING"] = array(
        "CLASS" => $class,
        "COUNT" => $rating,
        "VOTES" => intval($arItem["PROPERTIES"]["VOTES"]["VALUE"])
    );

    //quantity
    if ($arItem["OFFERS"])
    {
        $quantity = 0;
        foreach ($arItem["OFFERS"] as $arOffer)
        {
            $quantity += $arOffer["CATALOG_QUANTITY"];
        }
        $arItem["CATALOG_QUANTITY"] = $quantity;
    }
    else
        $quantity = intval($arItem["CATALOG_QUANTITY"]);

    if ($quantity != 0)
        $arItem["QUANTITY_TEXT"] = "много";

    if ($arItem["OFFERS"])
    {
        $reserved = 0;
        foreach ($arItem["OFFERS"] as $arOffer)
            $reserved += $arOffer["CATALOG_QUANTITY_RESERVED"];
    }
    else
        $reserved = intval($arItem["CATALOG_QUANTITY_RESERVED"]);

    if ($reserved != 0)
        $arItem["RESERVED_TEXT"] = "много";

    // credit
    if ($arItem["PROPERTIES"]["PAYMENT_INSTALLMENTS"]["VALUE_XML_ID"] != "Y" && $arItem["REGION_PRICE"]["DEFAULT_VALUE"] >= 3000)
    {
        $arItem["CREDIT"] = round($arItem["REGION_PRICE"]["DEFAULT_VALUE"] * 0.048);
        $arItem["CREDIT"] = number_format($arItem["CREDIT"], 0, ".", " ");
    }

    // url
    $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

    // count comments
    $arItem["COMMENTS_COUNT"] = ($arCommentsCount[$arItem["ID"]]) ? $arCommentsCount[$arItem["ID"]] : 0;

    // actions good
    $arItem["GOOD_ACTIONS"] = array();
    foreach ($arElementsActions as $arElementActions)
    {
        if ($arElementActions["PROPERTY_PRODUCTS_VALUE"] == $arItem["ID"] && $arElementActions["ICON_GOOD"])
        {
            $arItem["GOOD_ACTIONS"][] = $arElementActions;
        }
    }

    // overviews
    $arItem["OVERVIEWS"] = array();

    foreach ($arElementsOverview as $arElementOverview)
    {
        if ($arElementOverview["PROPERTY_PRODUCTS_VALUE"] == $arItem["ID"])
        {
            $arItem["OVERVIEWS"]["COUNT"]++;
            $arItem["OVERVIEWS"]["TEMPLATE"] .= '<li><a href="'.$arElementOverview["DETAIL_PAGE_URL"].'" target="_blank">'.$arElementOverview["NAME"].'</a></li>';
        }
    }

    if ($arItem["OVERVIEWS"])
    {
        $arItem["OVERVIEWS"]["HELP"] = str_replace(
            "#OVERVIEWS#",
            $arItem["OVERVIEWS"]["TEMPLATE"],
            '<div class="overviews"><strong>Для этого товара есть статьи:</strong><ul>#OVERVIEWS#</ul></div>'
        );
    }

    $arResult["ITEMS"][$nItem] = $arItem;
	
}

/*
$arResult["UF_YM_ID"] = 0;
$arResult["UF_TEXT_FOR_FEEDBACK"] = "";
$db_list = CIBlockSection::GetList(Array(), Array('IBLOCK_ID' => $arParams["IBLOCK_ID"], "ID" => $arParams["SECTION_ID"]), false, array("IBLOCK_ID", "ID", "UF_YM_ID", "UF_TEXT_FOR_FEEDBACK"));
while($ar_result = $db_list->GetNext())
{
		$arResult["UF_YM_ID"] = $ar_result["UF_YM_ID"];
		$arResult["UF_TEXT_FOR_FEEDBACK"] = $ar_result["UF_TEXT_FOR_FEEDBACK"];
}

// Получим дерево разделов
$arResult["SECTION_CHINE"] = array();
if ($arParams["SECTION_ID"] > 0) {
	$nav = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $arParams["SECTION_ID"]);
	while($arSectionPath = $nav->GetNext()){
		$arResult["SECTION_CHINE"][] = $arSectionPath["NAME"];
	}
}

// Подключение описание для раздела
$sectionPage = $arResult["SECTION_PAGE_URL"];

$sectionPage = mb_substr($sectionPage, 1);
$arResult["FILE_SECTION_PATH"] = "/include/sections/".str_replace("/", "-", $sectionPage);
$arResult["FILE_SECTION_PATH"] .= $transliteCity = Cutil::translit($_SESSION["GEO_REGION_CITY_NAME"], "ru", array("replace_space" => "-", "replace_other" => "-"));
$arResult["FILE_SECTION_PATH"] .= ".php";
$arResult["FILE_SECTION_EXISTS"] = file_exists($_SERVER["DOCUMENT_ROOT"].$arResult["FILE_SECTION_PATH"]);

// Если нет описания для раздела (из включаемой области, то заменим макросы в DESCRIPTION
if (!$arResult["FILE_SECTION_EXISTS"]) {
    $arMascros = array(
        "REGION" => $_SESSION["GEO_REGION_CITY_NAME"],
        "SECTION" => $arResult["NAME"]
    );
    $arResult["DESCRIPTION"] = ReplaceMacrosInText($arResult["DESCRIPTION"], $arMascros);
}

//Скрыть описание
if ($arResult['DESCRIPTION'])
{
    if (preg_match('/\[DELIMITER\]/', $arResult['DESCRIPTION']))
    {
        $arResult['DESCRIPTION'] = preg_replace('/\[DELIMITER\]([^\[DELIMITER\]]+)$/', '<div class="desc-delimiter">$1</div><a href="#open-delimiter">Читать далее...</a>', $arResult['DESCRIPTION']);
    }
}

// Убрать описание из пагинации
$arResult["IS_HIDE_DESC"] = (intval($arResult["NAV_RESULT"]->NavPageNomer) >= 2)? true: false;
