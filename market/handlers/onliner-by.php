<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$obBitrixMarket->GetSectionsAll();
$arElementsXml = $obBitrixMarket->GetElementsXmlForOnlinerby();

$arXml = array(
    "price-list" => array(
        "@version" => "1.0",
        "items-list" => $arElementsXml
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);