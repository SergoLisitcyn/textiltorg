<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsForAvito();

$arXml = array(
    "Ads" => array(
        "@formatVersion" => "3",
        "@target" => "Avito.ru",
        "Ad" => $arElementsXml
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);