<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

//Швейные машины - 19
//Оверлоки - 20
//Вязальные машины - 83
//Вышивальные машины - 23
//Манекены - 21
//Ткацкие станки - 85
//Кеттельные машины - 90
//Швейно-вышивальные машины - 24
$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arrTargetCategory = array(19,20,83,23,21);
$arrResultCategory = $obBitrixMarket->GetSubCategory($arrTargetCategory);
$arElementsXml = $obBitrixMarket->GetElementsXml(true, "ALL", false, array("SECTION_ID" => $arrResultCategory));

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