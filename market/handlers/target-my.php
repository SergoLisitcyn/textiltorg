<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsForTorgMail();

$arShopXML = array(
	"name" => BitrixMarket::SHOP_NAME,
	"company" => BitrixMarket::SHOP_COMPANY,
	"url" => $obBitrixMarket->GetFullShopUrl(false),
	"currencies" => array(
		"currency" => array(
			"@id" => $obBitrixMarket->GetCurrency(),
			"@rate" => "1"
		)
	),
	"categories" => array(
		"category" => $arSectionsXml
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
	"torg_price" => array(
		"@date" => date("Y-m-d H:i"),
		"shop" => $arShopXML
	)
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);