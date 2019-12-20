<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if ($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])
{
    // overviews
    $rsElements= CIBlockElement::GetList(
        array(
            "SORT" => "ASC"
        ),
        array(
            "IBLOCK_ID" => 8,
            "ID" => $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"],
            "ACTIVE" => "Y",
        ),
        false,
        false,
        array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL")
    );

    $arResult["PRODUCTS"] = array();
    while ($arElement = $rsElements->GetNext())
    {
        $arResult["PRODUCTS"][] = $arElement;
    }
}