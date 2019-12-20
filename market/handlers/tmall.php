<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$targetProducts = array(72391000001,72406000001,72405000001,72400000001,72388000001,72403000001,2170,2846,638,2842,11391000001,1078,1699,12589,11393000001,2845,1507,11617,11612,72389000001);

$arSectionsXml = $obBitrixMarket->GetSectionsXml(true);
$arElementsXml = $obBitrixMarket->GetElementsXmlTmall(false, "ALL", false, array("XML_ID" => $targetProducts));

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