<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

//Швейные машины - 19
//Швейные машины уценка - 977
//Оверлоки - 20
//Оверлоки уценка - 1001
//Вязальные машины - 83
//Вышивальные машины - 23
//Вышивальные машины - 1023
//Швейно-вышивальные машины - 24
//Швейно-вышивальные машины - 1030
//Манекены - 21
//Ткацкие станки - 85
//Кеттельные машины - 90
//Швейно-вышивальные машины - 24

$excludeRootCat = true;
$arrTargetCategory = array(19,20,83,23,21,977,1001,1023);
$arrResultCategory = $obBitrixMarket->GetSubCategory($arrTargetCategory);
$arSectionsXml = $obBitrixMarket->GetSectionsXml($excludeRootCat, $arrResultCategory);
$arElementsXml = $obBitrixMarket->GetElementsXml(false, "ALL", false, array("SECTION_ID" => $arrResultCategory));

foreach($arElementsXml as &$item){
    foreach($arElementsXml as &$check){
        if($item['name'] == $check['name'] . ' во'){
            $check['price'] = $item['price'];
            $check['url'] = $item['url'];
            $item = '';
        }
    }
}

$arShopXML = array(
    "name" => BitrixMarket::SHOP_NAME,
    "company" => BitrixMarket::SHOP_COMPANY,
    "url" => $obBitrixMarket->GetFullShopUrl(true),
    "currencies" => array(
        "currency" => array(
            "@id" => $obBitrixMarket->GetCurrency(),
            "@rate" => "1"
        )
    ),
    "categories" => array(
        "category" => $arSectionsXml
    ),
    "delivery-options" => array(
        "option" => array(
            "@cost" => BitrixMarket::DEFAULT_PRICE_DELIVERY,
            "@days" => BitrixMarket::DEFAULT_DAYS_DELIVERY,
        )
    )
);

if (!empty($arHandler["CPA"]))
{
    $arShopXML["cpa"] = "1";
}

$arShopXML["offers"] = array(
    "offer" => $arElementsXml
);

$arXml = array(
    "yml_catalog" => array(
        "@date" => date("Y-m-d H:i"),
        "shop" => $arShopXML
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);