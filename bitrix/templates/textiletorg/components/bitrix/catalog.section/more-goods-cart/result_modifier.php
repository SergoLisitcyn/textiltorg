<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

$arItems = array();
foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["TITLE_NAME"] = Helper::Truncate($arItem["NAME"], 25);

    $arItem["RESIZE_PICTURE"] = Helper::Resize($arPictures, 100, 100, true);
    if ($arItem["RESIZE_PICTURE"] == NULL)
        $arItem["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/no-img-200.png";

    //price
    if (count($arItem['OFFERS']) > 0) {
        foreach ($arItem['OFFERS'] as $offer) {
            if (count($offer['PRICES']) > 0) {
                $arItem['PRICES'] =  $offer['PRICES'];
                break;
            }
        }
    }
    $arItem["REGION_PRICE"] = ($arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]]) ?
    $arItem["PRICES"][$arParams["GEO_REGION_CITY_NAME"]] :
    $arItem["PRICES"][$arParams["REGION_PRICE_CODE_DEFAULT"]];

    if (isset($arItem["REGION_PRICE"]))
    {
        $arItem["REGION_PRICE"]["DEFAULT_VALUE"] = $arItem["REGION_PRICE"]["DISCOUNT_VALUE"];
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

                if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arItem["REGION_PRICE"]["DISCOUNT_VALUE"])
                {
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
                }
            }
        }
    }

    //anonce
    $arItem["PREVIEW_TEXT"] = strip_tags($arItem["PREVIEW_TEXT"]);
    $arItem["PREVIEW_TEXT"] = preg_replace("/[a-z]{1}\s\{.+\}/i", "", $arItem["PREVIEW_TEXT"]);
    $arItem["PREVIEW_TEXT"] = preg_replace("/\&nbsp\;/", " ", $arItem["PREVIEW_TEXT"]);
    $arItem["PREVIEW_TEXT"] = preg_replace("/\s{2,}/i", " ", $arItem["PREVIEW_TEXT"]);

    // url
    $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

    $arItems[$arItem["ID"]] = $arItem;
}

$arResult["ITEMS"] = array();
foreach ($GLOBALS[$arParams["FILTER_NAME"]]["ID"] as $id)
{
    if (!empty($arItems[$id]))
    {
        $arResult["ITEMS"][] = $arItems[$id];
    }
}

// Товары обязательные для эксплуатации

if (!empty($arParams["ELEMENTS_CART_ID"]))
{

    $rsElements = CIBlockElement::GetList(
        array(
            "SORT" => "ASC"
        ),
        array(
            "IBLOCK_ID" => 8,
            "ID" => $arParams["ELEMENTS_CART_ID"]
        ),
        false,
        false,
        array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID")
    );

    $arCartGoodSections = array();
    while($arElement = $rsElements->GetNext())
    {
        $arCartGoodSections[] = $arElement["IBLOCK_SECTION_ID"];
    }

    if (!empty($arCartGoodSections))
    {
        $rsElements = CIBlockElement::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "IBLOCK_ID" => 32,
                "ACTIVE" => "Y",
                "PROPERTY_SECTIONS" => $arCartGoodSections,
                "!PROPERTY_TITLE" => ""
            ),
            false,
            false,
            array("ID", "NAME", "IBLOCK_ID", "DETAIL_TEXT", "PROPERTY_SECTIONS", "PROPERTY_TITLE")
        );

        while($arElement = $rsElements->GetNext())
        {
            $arResult["HELP_ARTICLES"][$arElement["ID"]] = $arElement;
        }
    }
}

$currentHelpArticle = current($arResult["HELP_ARTICLES"]);
$currentHelpTitle = ($currentHelpArticle["PROPERTY_TITLE_VALUE"])? $currentHelpArticle["PROPERTY_TITLE_VALUE"]: $currentHelpArticle["NAME"];

$arResult["TITLE"] = (count($arParams["ELEMENTS_CART_ID"]) == 1 && $currentHelpTitle) ?
    $currentHelpTitle:
    "Не забудьте! Товары, нужные для эксплуатации выбранных товаров";