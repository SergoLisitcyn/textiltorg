<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsXmlCriteo();

// echo "<pre>";
// var_dump($arElementsXml);
// die;

$arXml = array(
    "rss" => array(
        "@xmlns:g" => "http://base.google.com/ns/1.0",
        "@version" => "2.0",
        "channel" => array(
            "title" => BitrixMarket::SHOP_NAME,
            "link" => array(
                "@rel" => "self",
                "%" => $obBitrixMarket->GetFullShopUrl(false),
            ),
            "description" => "ТекстильТорг — магазин швейной и мелкой бытовой техники",
            "item" => $arElementsXml
        ),
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);