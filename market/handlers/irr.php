<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

//Швейные машины - 19
//Оверлоки - 20
//Вышивальные машины - 23
//Швейно-вышивальные машины - 24
//Вязальные машины - 83
//Ткацкие станки - 85
//Кеттельные машины - 90
//Отпариватели - 132

//Манекены - 21
//Паровые швабры - 197
//Пароочистители - 172
//Пылесосы - 183
//Сушилки для белья - 154
//Гладильные системы - 121
//Гладильные доски - 105
//Гладильные прессы - 98
//Парогенераторы - 141

$arrTargetCategory = array(20, 19, 83, 24, 23, 85, 90, 132, 21, 197, 197, 172, 183, 154, 121, 105, 98, 141);
$arElementsXml = $obBitrixMarket->GetElementsXmlIrr($arrTargetCategory);

// echo "<pre>";
// var_dump($arElementsXml);
// die;

$arXml = array(
    "users" => array(
        "user" => array(
            "@deactivate-untouched" => "false",
            "match" => array(
                "user-id" => "123"
            ),
            "store-ad" => $arElementsXml
        )
    )
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);