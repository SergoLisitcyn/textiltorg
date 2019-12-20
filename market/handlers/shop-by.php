<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$obBitrixMarket = new BitrixMarket(REGION, HANDLER, $arHandler);

$arSectionsXml = $obBitrixMarket->GetSectionsXml();
$arElementsXml = $obBitrixMarket->GetElementsXml();
$arElementsXml = $obBitrixMarket->GetDeliveryPriceRB($arElementsXml, "BYN");

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
			"@cost" => BitrixMarket::DEFAULT_PRICE_DELIVERY_RB,
			"@days" => BitrixMarket::DEFAULT_DAYS_DELIVERY_RB,
		)
	)
);

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