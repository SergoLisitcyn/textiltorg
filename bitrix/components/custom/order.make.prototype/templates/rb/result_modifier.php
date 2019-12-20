<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// Получим товары в заказе
CModule::IncludeModule("sale"); // подключение модуля продаж

$arResult["PRODUCRS_BASKET"] = array();
$dbBasketItems = CSaleBasket::GetList(array(), array("ORDER_ID" => $arResult["ORDER"]["ID"]));
while ($arItem = $dbBasketItems->Fetch()) {
    $arResult["PRODUCRS_BASKET"][] = array(
        "PRODUCT_ID" => ($arItem["PRODUCT_XML_ID"]) ? $arItem["PRODUCT_XML_ID"] : $arItem["PRODUCT_ID"],
        "QUANTITY" => $arItem["QUANTITY"],
        "PRICE" => round($arItem["PRICE"], 2)
    );
}

$arResult["PRODUCRS_BASKET_ID"] = array();
$arResult["PRODUCTS_SUMM"] = 0;

foreach ($arResult["PRODUCRS_BASKET"] as $arProduct)
{
    for ($i = 0; $i < $arProduct["QUANTITY"]; $i++)
    {
        $arResult["PRODUCRS_BASKET_ID"][] = $arProduct["PRODUCT_ID"];
        $arResult["PRODUCTS_SUMM"] += $arProduct["PRICE"];
    }
}
