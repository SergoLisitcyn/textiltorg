<?php
/*
 * Логика фильтраций от FISRU
 *
 * Информация в объявлениях обновляется в соответствии с данными из XML-файла при каждом цикле автозагрузки.
 * В Вашей учетной записи циклы автозагрузки производятся ежедневно в соответствии с графиком: 03:35,07:35,11:35,15:35,19:35
 * */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsXml("FISRU");
$arElementsXml = $obBitrixMarket->GetElementsForAvito(NULL); // NULL - игнорировать NO_EXPORT_MARKET(Не выгружать в Yandex.Market)

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