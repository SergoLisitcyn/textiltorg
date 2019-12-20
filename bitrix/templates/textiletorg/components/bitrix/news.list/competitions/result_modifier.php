<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

foreach ($arResult["ITEMS"] as $n => $arItem)
{
    $arDates = array();

    if ($arItem["PROPERTIES"]["DATE_START"]["VALUE"])
    {
        $arDates[] = $arItem["PROPERTIES"]["DATE_START"]["VALUE"];
    }

    if ($arItem["PROPERTIES"]["DATE_END"]["VALUE"])
    {
        $arDates[] = $arItem["PROPERTIES"]["DATE_END"]["VALUE"];
    }

    $arItem["DATES"] = implode(" - ", $arDates);

    if ($arItem["PROPERTIES"]["IS_END"]["VALUE_XML_ID"] == "Y")
    {
        $arResult["ITEMS_ARCHIVE"][] = $arItem;
    }
    else
    {
        $arResult["ITEMS_ACTIVE"][] = $arItem;
    }
}
?>