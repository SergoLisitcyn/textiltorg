<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Ayers\Delivery\CalcPrice;

foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["RESIZE_PICTURE"] = Helper::Resize($arPictures, 260, 260, true);
    if ($arItem["RESIZE_PICTURE"] == NULL)
        $arItem["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/noimage-260x260.jpg";

    //price
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
                    array($arItem)
                );

                if (!empty($arOptimalDelivery4Items) && $arOptimalDelivery4Items['PRICE']['FORMAT'] != $arItem["REGION_PRICE"]["DISCOUNT_VALUE"])
                {
                    $arItem["REGION_PRICE"]["DISCOUNT_VALUE"] = $arOptimalDelivery4Items['PRICE']['FORMAT'];
                }
            }
        }
    }

    // url
    $arItem["DETAIL_PAGE_URL"] = Helper::RemoveOneLavelUrl($arItem["DETAIL_PAGE_URL"]);

    $arResult["ITEMS"][$nItem] = $arItem;
}