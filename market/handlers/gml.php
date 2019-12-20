<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsXmlGml();

// echo "<pre>";
// var_dump($arElementsXml);
// die;

$arXml = array(
    "feed" => array(
        "@xmlns" => "http://www.w3.org/2005/Atom",
        "@xmlns:g" => "http://base.google.com/ns/1.0",
        "title" => BitrixMarket::SHOP_NAME,
        "link" => array(
            "@rel" => "self",
            "%" => $obBitrixMarket->GetFullShopUrl(false),
        ),
        "updated" => date("Y-m-d\TH:i:s\Z"),
        "entry" => $arElementsXml
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);