<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

//Швейные машины - 19
//Швейные машины уценка - 977
//Оверлоки - 20
//Оверлоки уценка - 1001
//Вышивальные машины - 23
//Вышивальные машины - 1023
//Швейно-вышивальные машины - 24
//Швейно-вышивальные машины - 1030
//Вязальные машины - 83

$obBitrixMarket->GetSectionsXml();
$arrTargetCategory = array(20, 19, 83, 24, 23,977,1001,1023,1030);
$arrTargetProducts = array();
$arElementsXml = $obBitrixMarket->GetElementsXmlGoogleMS($arrTargetCategory, $arrTargetProducts);

foreach($arElementsXml as &$item){
    foreach($arElementsXml as &$check){
        if($item['title'] == $check['title'] . ' во'){
            $check['g:price'] = $item['g:price'];
            $check['link'] = $item['link'];
            $item = '';
        }
    }
}

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