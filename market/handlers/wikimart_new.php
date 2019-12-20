<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsAll();
$arElementsXml = $obBitrixMarket->GetElementsXmlWikimartNew();

$arXml = array(
    "yml_catalog" => array(
        "@date" => date("Y-m-d H:i"),
        "@xmlns:w" => "http://wikimart.ru/spec/wml",
        "offers" => array(
            "offer" => $arElementsXml
        )
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);