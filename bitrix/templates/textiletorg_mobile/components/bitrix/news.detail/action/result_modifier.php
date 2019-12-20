<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult["PRODUCTS"] = array();

if ($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])
{
    if (CModule::IncludeModule("iblock"))
    {
        $rsElements = CIBlockElement::GetList(
            array(
                "SORT" => "ASC",
                "NAME" => "ASC"
            ),
            array(
                "IBLOCK_ID" => 8,
                "ID" => $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"],
                "ACTIVE"=>"Y",
            ),
            false,
            false,
            array("ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "DETAIL_PICTURE", "CATALOG_GROUP_1")
        );

        while($arElement = $rsElements->GetNext())
        {
            //resize picture
            $arPictures = array(
                $arElement["PREVIEW_PICTURE"],
                $arElement["DETAIL_PICTURE"]
            );

            $arElement["RESIZE_PICTURE"] = Helper::Resize($arPictures, 26, 26);
            if ($arElement["RESIZE_PICTURE"] == NULL)
                $arElement["RESIZE_PICTURE"]["SRC"] = SITE_TEMPLATE_PATH."/img/noimage-26x26.jpg";

            // price
            $arElement["PRINT_PRICE"] = number_format($arElement["CATALOG_PRICE_1"], 0, ",", " ");

            $arResult["PRODUCTS"][] = $arElement;
        }
    }
}