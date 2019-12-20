<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "Узнать адреса магазинов \"ТекстильТорг\".");
$APPLICATION->SetPageProperty("keywords", "Адреса магазинов сеть текстильторг");
$APPLICATION->SetPageProperty("description", "Наши магазины в Москве, Санкт-Петербурге и других городах | Сеть магазинов \"ТекстильТорг\"");
$APPLICATION->SetTitle("Наши магазины");
?><?if (defined("IS_MOBILE") || $_GET["test"] == 1):?>
	<?$APPLICATION->IncludeComponent(
	"custom:region-select.prototype",
	"mobile-footer",
Array(),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> 
	<?$APPLICATION->IncludeComponent(
	"custom:contacts.prototype",
	"mobile",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "mobile",
		"IBLOCK_ID" => "41"
	)
);?>
<?else:?>
	<?$APPLICATION->IncludeComponent(
	"custom:region-select.prototype",
	"footer",
	Array(
		"DEFAULT_REGION_CITY_NAME" => "Москва",
		"DEFAULT_REGION_ID" => "19"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> 
	<?$APPLICATION->IncludeComponent(
	"custom:contacts.prototype",
	".default",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => "41"
	)
);?>
<?endif?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>