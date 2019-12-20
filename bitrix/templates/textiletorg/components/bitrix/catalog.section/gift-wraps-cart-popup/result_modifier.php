<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

foreach ($arResult["ITEMS"] as $nItem => $arItem)
{
    //resize picture
    $arPictures = array(
        $arItem["PREVIEW_PICTURE"]["ID"],
        $arItem["DETAIL_PICTURE"]["ID"]
    );

    $arItem["RESIZE_PICTURE"]["BIG"] = Helper::Resize(array($arItem["DETAIL_PICTURE"]["ID"]), 210, 210, true);
    $arItem["RESIZE_PICTURE"]["SMALL"] = Helper::Resize(array($arItem["PREVIEW_PICTURE"]["ID"]), 70, 20, true);

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
    }

    $arResult["ITEMS"][$nItem] = $arItem;
}