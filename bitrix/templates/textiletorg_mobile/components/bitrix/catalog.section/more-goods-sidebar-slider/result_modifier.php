<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

// count comments
CModule::IncludeModule("highloadblock");
use Bitrix\Highloadblock as HL;

$arIDs = array();
$arCommentsCount = array();
foreach ($arResult["ITEMS"] as $nItem => $arItem)
    $arIDs[] = $arItem["ID"];

$hlBlock = HL\HighloadBlockTable::getById(6)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlBlock);

$entityDataClass = $entity->getDataClass();
$entityTableName = $hlBlock["TABLE_NAME"];

$tableID = 'tbl_'.$entityTableName;
$rsData = $entityDataClass::getList(array(
    "select" => array(
        "UF_ELEMENT"
    ),
    "filter" => array(
        "UF_ELEMENT" => $arIDs,
        "UF_ACTIVE" => true
    ),
    "order" => array(
        "UF_DATE"=>"DESC",
        "ID"=>"DESC"
    )
));

$rsData = new CDBResult($rsData, $tableID);

while ($arItem = $rsData->Fetch())
    $arCommentsCount[$arItem["UF_ELEMENT"]] += 1;


foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["RESIZE_PICTURE"] = Helper::Resize($arPictures, 220, 180, true);
    if ($arItem["RESIZE_PICTURE"] == NULL)
        $arItem["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/noimage-220x180.jpg";


    if ($arItem["OFFERS"])
    {
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
            $minPrice = array();
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

                if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arItem["REGION_PRICE"]["DISCOUNT_VALUE"])
                {
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
                }
            }
        }
    }

    // rating

    $rating = (intval($arItem["PROPERTIES"]["RATING"]["VALUE"]))? intval($arItem["PROPERTIES"]["RATING"]["VALUE"]) : 0;

    switch ($rating)
    {
        case "1":
            $class = "r1";
            break;
        case "2":
            $class = "r2";
            break;
        case "3":
            $class = "r3";
            break;
        case "4":
            $class = "r4";
            break;
        case "5":
            $class = "r5";
            break;

        default:
            $class = "r0";
            break;
    }

    $arItem["RATING"] = array(
        "CLASS" => $class,
        "COUNT" => $rating,
        "VOTES" => intval($arItem["PROPERTIES"]["VOTES"]["VALUE"])
    );

    // url
    $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

    //reviews
    $arItem["COMMENTS_COUNT"] = $arCommentsCount[$arItem["ID"]];

    $arResult["ITEMS"][$nItem] = $arItem;
}