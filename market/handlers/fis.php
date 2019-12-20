<?php
/*
 * Выгрузка на площадку FIS.RU
 * используется фильтр по пользовательскому полю - UF_IS_FISRU(Выгружать в fis.ru),
 * UF_YM_ID(ID раздела для Yandex.Market), игнорирует NO_EXPORT_MARKET(Не выгружать в Yandex.Market)
 * */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsXml(true, NULL);

foreach($arElementsXml as &$item){
    foreach($arElementsXml as &$check){
        if($item['name'] == $check['name'] . ' во'){
            $check['price'] = $item['price'];
            $check['url'] = $item['url'];
            $item = '';
        }
    }
}

// echo "<pre>";
// var_dump($arElementsXml);
// die;

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
	),
	"delivery-options" => array(
		"option" => array(
			"@cost" => BitrixMarket::DEFAULT_PRICE_DELIVERY,
			"@days" => BitrixMarket::DEFAULT_DAYS_DELIVERY,
		)
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
	"yml_catalog" => array(
		"@date" => date("Y-m-d H:i"),
		"shop" => $arShopXML
	)
);

header('Content-type: text/xml');
$xml = new ArrayToXML();
print $xml->buildXML($arXml, false);