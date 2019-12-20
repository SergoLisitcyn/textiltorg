<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once(__DIR__."/BitrixMarket.php");
require_once(__DIR__."/ArrayToXML.php");

$obBitrixMarket = new BitrixMarket("RND");
//Швейные машины - 19
//Оверлоки - 20
//Вязальные машины - 83
//Вышивальные машины - 23

//Манекены - 21
//Ткацкие станки - 85
//Кеттельные машины - 90
//Швейно-вышивальные машины - 24
$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arrTargetCategory = array(19,20,83,23);
$arrResultCategory = $obBitrixMarket->GetSubCategory($arrTargetCategory);
$arElementsXml = $obBitrixMarket->GetElementsForAvito("ALL", array("PROPERTY_NO_EXPORT_AVITO" => false, "SECTION_ID" => $arrResultCategory)); // ALL - игнорировать NO_EXPORT_MARKET(Не выгружать в Yandex.Market)

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
?>