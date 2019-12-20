<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule("sale");

$arResult["PRODUCRS_BASKET"] = array();
$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $arResult["ORDER"]["ID"]));
while ($arItem = $dbBasketItems->Fetch()) {
    $arResult["PRODUCRS_BASKET"][] = array(
        "ORIGIN_PRODUCT_ID" => $arItem["PRODUCT_ID"],
        "PRODUCT_ID" => (!empty($arItem["PRODUCT_XML_ID"])) ? $arItem["PRODUCT_XML_ID"] : $arItem["PRODUCT_ID"],
        "QUANTITY" => $arItem["QUANTITY"],
        "PRICE" => round($arItem["PRICE"], 2)
    );
}

$arResult["PRODUCRS_BASKET_XML_ID"] = array();
$arResult["PRODUCTS_SUMM"] = 0;
$arResult["GDESLON_CODES"] = "";

$arCartItemsId = array();

foreach ($arResult["PRODUCRS_BASKET"] as $arProduct)
{
    $productXmlId = (!empty($arProduct["PRODUCT_XML_ID"])) ? $arProduct["PRODUCT_XML_ID"]: $arProduct["PRODUCT_ID"];
    $gdeslonCodes .= $productXmlId .':'. intval($arProduct["PRICE"] ) . ',';
    for($i=1; $i<$arProduct["QUANTITY"]; $i++) { // Add the same
        $gdeslonCodes .= $productXmlId .':'. intval($arProduct["PRICE"] ) . ',';
    }

    for ($i = 0; $i < $arProduct["QUANTITY"]; $i++)
    {
        $arResult["PRODUCRS_BASKET_XML_ID"][] = $arProduct["PRODUCT_ID"];
        $arResult["PRODUCTS_SUMM"] += $arProduct["PRICE"];
        $arCartItemsId[] = $arProduct["ORIGIN_PRODUCT_ID"];
    }
}
$arResult["MRC"] = Helper::isMrc($arCartItemsId);
$arResult["GDESLON_CODES"] = rtrim($gdeslonCodes,',');
